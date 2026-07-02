<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\ClinicClosedDate;
use App\Models\ManualApprovalDate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        $closedDates = ClinicClosedDate::orderBy('date')->get();
        $manualApprovalDates = ManualApprovalDate::orderBy('date')->get();

        return view('admin.settings.index', compact('settings', 'closedDates', 'manualApprovalDates'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'fonnte_api_key' => 'nullable|string',
            'fonnte_device' => 'nullable|string',
            'whatsapp_enabled' => 'boolean',
            'address' => 'nullable|string',
            'google_maps_url' => 'nullable|url',
            'hero_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
        ]);

        // Update or create settings
        Setting::updateOrCreate(
            ['key' => 'fonnte_api_key'],
            ['value' => $request->input('fonnte_api_key')]
        );

        Setting::updateOrCreate(
            ['key' => 'fonnte_device'],
            ['value' => $request->input('fonnte_device')]
        );

        Setting::updateOrCreate(
            ['key' => 'whatsapp_enabled'],
            ['value' => $request->input('whatsapp_enabled', 0)]
        );

        Setting::updateOrCreate(
            ['key' => 'address'],
            ['value' => $request->input('address')]
        );

        Setting::updateOrCreate(
            ['key' => 'google_maps_url'],
            ['value' => $request->input('google_maps_url')]
        );

        // Handle hero image upload
        if ($request->hasFile('hero_image')) {
            // Delete old hero image if exists
            $oldPath = Setting::get('hero_image');
            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('hero_image')->store('hero', 'public');

            Setting::updateOrCreate(
                ['key' => 'hero_image'],
                ['value' => $path]
            );
        }

        // Handle hero image removal
        if ($request->input('remove_hero_image') === '1') {
            $oldPath = Setting::get('hero_image');
            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
            Setting::updateOrCreate(
                ['key' => 'hero_image'],
                ['value' => null]
            );
        }

        // Update .env file (optional)
        $this->updateEnvFile([
            'FONNTE_API_KEY' => $request->input('fonnte_api_key'),
            'FONNTE_DEVICE' => $request->input('fonnte_device'),
        ]);

        return redirect()
            ->route('admin.settings.index')
            ->with('success', 'Konfigurasi berhasil disimpan.');
    }

    /**
     * Simpan Kebijakan Booking (auto-approval & DP).
     * type di-set eksplisit agar Setting::get() melakukan cast yang benar.
     */
    public function saveBookingPolicy(Request $request)
    {
        $request->validate([
            'booking_auto_approval' => 'boolean',
            'deposit_enabled' => 'boolean',
            'deposit_threshold_days' => 'nullable|integer|min:0|max:365',
            'min_deposit' => 'nullable|numeric|min:0',
            'deposit_deadline_hours' => 'nullable|integer|min:1|max:720',
        ]);

        Setting::updateOrCreate(
            ['key' => 'booking_auto_approval'],
            ['value' => $request->input('booking_auto_approval', 0) ? '1' : '0', 'type' => 'boolean']
        );

        Setting::updateOrCreate(
            ['key' => 'deposit_enabled'],
            ['value' => $request->input('deposit_enabled', 0) ? '1' : '0', 'type' => 'boolean']
        );

        Setting::updateOrCreate(
            ['key' => 'deposit_threshold_days'],
            ['value' => (int) $request->input('deposit_threshold_days', 7), 'type' => 'number']
        );

        Setting::updateOrCreate(
            ['key' => 'min_deposit'],
            ['value' => (int) $request->input('min_deposit', 50000), 'type' => 'number']
        );

        Setting::updateOrCreate(
            ['key' => 'deposit_deadline_hours'],
            ['value' => (int) $request->input('deposit_deadline_hours', 24), 'type' => 'number']
        );

        return back()->with('success', 'Kebijakan booking berhasil disimpan.');
    }

    /**
     * Toggle shop open/close status
     */
    public function toggleShopStatus(Request $request)
    {
        $currentStatus = Setting::get('is_shop_open', true);
        $newStatus = !$currentStatus;

        Setting::updateOrCreate(
            ['key' => 'is_shop_open'],
            ['value' => $newStatus ? '1' : '0', 'type' => 'boolean']
        );

        return response()->json([
            'success' => true,
            'is_open' => $newStatus,
            'message' => $newStatus ? 'Toko dibuka' : 'Toko ditutup'
        ]);
    }

    /**
     * Simpan hari tutup rutin (mis. tiap Minggu)
     */
    public function saveClosedWeekdays(Request $request)
    {
        $request->validate([
            'closed_weekdays' => 'nullable|array',
            'closed_weekdays.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
        ]);

        Setting::updateOrCreate(
            ['key' => 'closed_weekdays'],
            [
                'value' => json_encode(array_values($request->input('closed_weekdays', []))),
                'type' => 'json',
            ]
        );

        return back()->with('success', 'Hari tutup rutin berhasil disimpan.');
    }

    /**
     * Tambah tanggal libur khusus
     */
    public function storeClosedDate(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'note' => 'nullable|string|max:255',
        ]);

        ClinicClosedDate::updateOrCreate(
            ['date' => $request->date],
            ['note' => $request->note]
        );

        return back()->with('success', 'Tanggal libur berhasil ditambahkan.');
    }

    /**
     * Hapus tanggal libur khusus
     */
    public function destroyClosedDate(ClinicClosedDate $closedDate)
    {
        $closedDate->delete();

        return back()->with('success', 'Tanggal libur berhasil dihapus.');
    }

    /**
     * Tambah tanggal (janji temu) yang wajib approval manual.
     * Juga dipakai tombol cepat "matikan auto-approval hari ini".
     */
    public function storeManualApprovalDate(Request $request)
    {
        $request->validate([
            'date' => 'required|date|after:today',
            'note' => 'nullable|string|max:255',
        ], [
            'date.after' => 'Tanggal harus setelah hari ini (mulai besok).',
        ]);

        ManualApprovalDate::updateOrCreate(
            ['date' => $request->date],
            ['note' => $request->note]
        );

        return back()->with('success', 'Tanggal wajib approval manual berhasil ditambahkan.');
    }

    /**
     * Hapus tanggal wajib approval manual
     */
    public function destroyManualApprovalDate(ManualApprovalDate $manualApprovalDate)
    {
        $manualApprovalDate->delete();

        return back()->with('success', 'Tanggal wajib approval manual berhasil dihapus.');
    }

    /**
     * Test WhatsApp connection
     */
    public function testConnection(Request $request)
    {
        $apiKey = $request->input('api_key');
        
        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'API Key tidak boleh kosong'
            ]);
        }

        try {
            $response = \Http::withHeaders([
                'Authorization' => $apiKey
            ])->post('https://api.fonnte.com/validate');

            if ($response->successful()) {
                $data = $response->json();
                return response()->json([
                    'success' => true,
                    'message' => 'Koneksi berhasil!',
                    'data' => $data
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Koneksi gagal: ' . $response->body()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update .env file
     */
    private function updateEnvFile(array $data)
    {
        $envFile = base_path('.env');
        
        if (!file_exists($envFile)) {
            return;
        }

        $env = file_get_contents($envFile);

        foreach ($data as $key => $value) {
            $value = $value ?? '';
            
            if (strpos($env, $key . '=') !== false) {
                // Update existing key
                $env = preg_replace(
                    "/^{$key}=.*/m",
                    "{$key}={$value}",
                    $env
                );
            } else {
                // Add new key
                $env .= "\n{$key}={$value}";
            }
        }

        file_put_contents($envFile, $env);
    }
}
