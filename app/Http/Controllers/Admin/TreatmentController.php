<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Treatment;
use Illuminate\Http\Request;

class TreatmentController extends Controller
{
    public function index()
    {
        $treatments = Treatment::orderBy('name')->paginate(15);
        return view('admin.treatments.index', compact('treatments'));
    }

    public function create()
    {
        return view('admin.treatments.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:15',
            'price' => 'required|numeric|min:0',
            'is_popular' => 'boolean',
        ]);

        Treatment::create($request->all());

        return redirect()
            ->route('admin.treatments.index')
            ->with('success', 'Treatment berhasil ditambahkan.');
    }

    public function edit(Treatment $treatment)
    {
        return view('admin.treatments.edit', compact('treatment'));
    }

    public function update(Request $request, Treatment $treatment)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:15',
            'price' => 'required|numeric|min:0',
            'is_popular' => 'boolean',
        ]);

        $treatment->update($request->all());

        return redirect()
            ->route('admin.treatments.index')
            ->with('success', 'Treatment berhasil diupdate.');
    }

    public function destroy(Treatment $treatment)
    {
        try {
            // Cek apakah ada booking terkait
            $bookingCount = $treatment->bookings()->count();
            
            if ($bookingCount > 0) {
                return redirect()
                    ->route('admin.treatments.index')
                    ->withErrors(['error' => "Treatment tidak bisa dihapus karena ada {$bookingCount} booking terkait. Nonaktifkan treatment sebagai gantinya."]);
            }

            $treatment->delete();

            return redirect()
                ->route('admin.treatments.index')
                ->with('success', 'Treatment berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.treatments.index')
                ->withErrors(['error' => 'Gagal menghapus treatment: ' . $e->getMessage()]);
        }
    }

    public function toggleStatus(Treatment $treatment)
    {
        $treatment->update(['is_active' => !$treatment->is_active]);

        return back()->with('success', 'Status treatment berhasil diubah.');
    }
}
