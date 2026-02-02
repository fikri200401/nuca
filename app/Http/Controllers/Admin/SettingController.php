<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'fonnte_api_key' => 'nullable|string',
            'fonnte_device' => 'nullable|string',
            'whatsapp_enabled' => 'boolean',
            'address' => 'nullable|string',
            'google_maps_url' => 'nullable|url',
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
