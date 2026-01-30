<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function index()
    {
        $vouchers = Voucher::orderBy('valid_from', 'desc')->paginate(15);
        return view('admin.vouchers.index', compact('vouchers'));
    }

    public function create()
    {
        return view('admin.vouchers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:vouchers,code|max:50',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:nominal,percentage',
            'value' => 'required|numeric|min:0',
            'min_transaction' => 'required|numeric|min:0',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after:valid_from',
            'is_single_use' => 'boolean',
            'max_usage' => 'nullable|integer|min:1',
            'show_on_landing' => 'boolean',
        ]);

        Voucher::create($request->all());

        return redirect()
            ->route('admin.vouchers.index')
            ->with('success', 'Voucher berhasil ditambahkan.');
    }

    public function edit(Voucher $voucher)
    {
        return view('admin.vouchers.edit', compact('voucher'));
    }

    public function update(Request $request, Voucher $voucher)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:vouchers,code,' . $voucher->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:nominal,percentage',
            'value' => 'required|numeric|min:0',
            'min_transaction' => 'required|numeric|min:0',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after:valid_from',
            'is_single_use' => 'boolean',
            'max_usage' => 'nullable|integer|min:1',
            'show_on_landing' => 'boolean',
        ]);

        $voucher->update($request->all());

        return redirect()
            ->route('admin.vouchers.index')
            ->with('success', 'Voucher berhasil diupdate.');
    }

    public function destroy(Voucher $voucher)
    {
        if ($voucher->usages()->exists()) {
            return back()->withErrors(['error' => 'Voucher tidak bisa dihapus karena sudah digunakan.']);
        }

        $voucher->delete();

        return redirect()
            ->route('admin.vouchers.index')
            ->with('success', 'Voucher berhasil dihapus.');
    }

    public function toggleStatus(Voucher $voucher)
    {
        $voucher->update(['is_active' => !$voucher->is_active]);

        return back()->with('success', 'Status voucher berhasil diubah.');
    }

    /**
     * View voucher usage statistics
     */
    public function usage(Voucher $voucher)
    {
        $usages = $voucher->usages()
            ->with(['user', 'booking'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.vouchers.usage', compact('voucher', 'usages'));
    }
}
