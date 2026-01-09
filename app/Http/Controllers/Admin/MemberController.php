<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $query = User::customers();

        if ($request->filled('is_member')) {
            if ($request->is_member == '1') {
                $query->where('is_member', true);
            } else {
                $query->where('is_member', false);
            }
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('whatsapp_number', 'like', '%' . $request->search . '%')
                  ->orWhere('member_number', 'like', '%' . $request->search . '%');
            });
        }

        $members = $query->withCount('bookings')->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.members.index', compact('members'));
    }

    public function show(User $member)
    {
        $member->load(['bookings.treatment', 'feedbacks', 'noShowNotes']);
        
        $stats = [
            'total_bookings' => $member->bookings()->count(),
            'completed_bookings' => $member->bookings()->completed()->count(),
            'cancelled_bookings' => $member->bookings()->where('status', 'cancelled')->count(),
            'no_show_count' => $member->noShowNotes()->count(),
        ];

        return view('admin.members.show', compact('member', 'stats'));
    }

    /**
     * Activate member
     */
    public function activateMember(User $member)
    {
        if ($member->is_member) {
            return back()->withErrors(['error' => 'User sudah menjadi member.']);
        }

        // Generate member number
        $memberNumber = 'MBR-' . strtoupper(Str::random(8));

        $member->update([
            'is_member' => true,
            'member_number' => $memberNumber,
            'member_discount' => 10, // Default 10%
        ]);

        return back()->with('success', 'Member berhasil diaktifkan. Nomor member: ' . $memberNumber);
    }

    /**
     * Deactivate member
     */
    public function deactivateMember(User $member)
    {
        if (!$member->is_member) {
            return back()->withErrors(['error' => 'User bukan member.']);
        }

        $member->update([
            'is_member' => false,
            'member_discount' => 0,
        ]);

        return back()->with('success', 'Member berhasil dinonaktifkan.');
    }

    /**
     * Update member discount
     */
    public function updateDiscount(Request $request, User $member)
    {
        $request->validate([
            'member_discount' => 'required|numeric|min:0|max:100',
        ]);

        if (!$member->is_member) {
            return back()->withErrors(['error' => 'User bukan member.']);
        }

        $member->update([
            'member_discount' => $request->member_discount,
        ]);

        return back()->with('success', 'Diskon member berhasil diupdate.');
    }
}
