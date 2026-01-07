<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Treatment;
use Illuminate\Http\Request;

class TreatmentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

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
        if ($treatment->bookings()->exists()) {
            return back()->withErrors(['error' => 'Treatment tidak bisa dihapus karena ada booking terkait.']);
        }

        $treatment->delete();

        return redirect()
            ->route('admin.treatments.index')
            ->with('success', 'Treatment berhasil dihapus.');
    }

    public function toggleStatus(Treatment $treatment)
    {
        $treatment->update(['is_active' => !$treatment->is_active]);

        return back()->with('success', 'Status treatment berhasil diubah.');
    }
}
