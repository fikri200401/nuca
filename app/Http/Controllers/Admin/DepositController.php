<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Services\BookingService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepositController extends Controller
{
    protected $whatsappService;

    protected $bookingService;

    public function __construct(
        WhatsAppService $whatsappService,
        BookingService $bookingService
    ) {
        $this->whatsappService = $whatsappService;
        $this->bookingService = $bookingService;
    }

    public function index(Request $request)
    {
        $query = Deposit::with(['booking.user', 'booking.treatment']);

        // Filter by status tab
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // Deposit yang sudah diunggah adalah antrean utama untuk diverifikasi.
            $query->where('status', 'submitted');
        }

        // Search: booking code or customer name/WhatsApp
        if ($request->filled('search')) {
            $query->whereHas('booking', function ($q) use ($request) {
                $q->where('booking_code', 'like', '%'.$request->search.'%')
                    ->orWhereHas('user', function ($q2) use ($request) {
                        $q2->where('name', 'like', '%'.$request->search.'%')
                            ->orWhere('whatsapp_number', 'like', '%'.$request->search.'%');
                    });
            });
        }

        // Sort
        $sortDir = $request->sort === 'oldest' ? 'asc' : 'desc';
        $query->orderBy('deadline_at', $sortDir);

        $deposits = $query->paginate(20)->withQueryString();

        return view('admin.deposits.index', compact('deposits'));
    }

    public function show(Deposit $deposit)
    {
        $deposit->load(['booking.user', 'booking.treatment', 'booking.doctor', 'verifier']);

        return view('admin.deposits.show', compact('deposit'));
    }

    /**
     * Approve deposit
     */
    public function approve(Deposit $deposit)
    {
        $result = $this->bookingService->approveDeposit($deposit->id, Auth::id());

        if (! $result['success']) {
            return back()->withErrors(['error' => $result['message']]);
        }

        $message = $result['waitlisted']
            ? 'Deposit disetujui. Booking tetap berada di waiting list sampai slot tersedia.'
            : 'Deposit berhasil diapprove dan booking menjadi pemegang slot.';

        return back()->with('success', $message);
    }

    /**
     * Reject deposit
     */
    public function reject(Request $request, Deposit $deposit)
    {
        $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        if (! $deposit->isSubmitted()) {
            return back()->withErrors(['error' => 'Hanya deposit yang menunggu verifikasi yang dapat ditolak.']);
        }

        $deposit->reject(Auth::id(), $request->rejection_reason);

        // Update booking status
        $deposit->booking->update(['status' => 'deposit_rejected']);

        // Send notification
        $this->whatsappService->sendDepositRejected($deposit->booking, $deposit);

        return back()->with('success', 'Deposit berhasil direject.');
    }
}
