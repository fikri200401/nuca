<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NoShowNote;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoShowNoteController extends Controller
{
    /**
     * Store no-show note
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'booking_id' => 'nullable|exists:bookings,id',
            'note' => 'required|string',
        ]);

        NoShowNote::create([
            'user_id' => $request->user_id,
            'booking_id' => $request->booking_id,
            'note' => $request->note,
            'created_by' => Auth::id(),
        ]);

        return back()->with('success', 'Catatan no-show berhasil ditambahkan.');
    }

    /**
     * Delete no-show note
     */
    public function destroy(NoShowNote $noShowNote)
    {
        $noShowNote->delete();

        return back()->with('success', 'Catatan no-show berhasil dihapus.');
    }
}
