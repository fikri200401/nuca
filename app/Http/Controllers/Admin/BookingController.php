<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Treatment;
use App\Models\Doctor;
use App\Models\User;
use App\Services\BookingService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function index(Request $request)
    {
        $query = Booking::with(['user', 'treatment', 'doctor', 'deposit']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('booking_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('booking_date', '<=', $request->date_to);
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('booking_code', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($q2) use ($request) {
                      $q2->where('name', 'like', '%' . $request->search . '%')
                         ->orWhere('whatsapp_number', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $bookings = $query->orderBy('booking_date', 'desc')
            ->orderBy('booking_time', 'desc')
            ->paginate(20);

        return view('admin.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        $booking->load(['user', 'treatment', 'doctor', 'deposit', 'feedback', 'beforeAfterPhotos']);
        
        return view('admin.bookings.show', compact('booking'));
    }

    /**
     * Manual booking entry (from WhatsApp)
     */
    public function create()
    {
        $treatments = Treatment::active()->get();
        $doctors = Doctor::active()->get();
        $users = User::where('role', 'customer')
                     ->orderBy('name')
                     ->get();
        
        return view('admin.bookings.create', compact('treatments', 'doctors', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'treatment_id' => 'required|exists:treatments,id',
            'doctor_id' => 'required|exists:doctors,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required',
            'notes' => 'nullable|string',
        ]);

        $result = $this->bookingService->createBooking($request->user_id, [
            'treatment_id' => $request->treatment_id,
            'doctor_id' => $request->doctor_id,
            'booking_date' => $request->booking_date,
            'booking_time' => $request->booking_time,
            'admin_notes' => $request->notes, // Changed from 'notes' to 'admin_notes'
            'is_manual_entry' => true,
        ]);

        if ($result['success']) {
            return redirect()
                ->route('admin.bookings.show', $result['booking']->id)
                ->with('success', 'Booking manual berhasil dibuat!');
        }

        return back()
            ->withErrors(['error' => $result['message']])
            ->withInput();
    }

    /**
     * Reschedule booking
     */
    public function reschedule(Request $request, Booking $booking)
    {
        $request->validate([
            'booking_date' => 'required|date',
            'booking_time' => 'required',
            'doctor_id' => 'nullable|exists:doctors,id',
        ]);

        $result = $this->bookingService->rescheduleBooking(
            $booking->id,
            $request->booking_date,
            $request->booking_time,
            $request->doctor_id
        );

        if ($result['success']) {
            return back()->with('success', 'Booking berhasil direschedule.');
        }

        return back()->withErrors(['error' => $result['message']]);
    }

    /**
     * Cancel booking
     */
    public function cancel(Request $request, Booking $booking)
    {
        $request->validate([
            'admin_notes' => 'nullable|string',
        ]);

        $result = $this->bookingService->cancelBooking($booking->id, $request->admin_notes);

        return back()->with('success', 'Booking berhasil dibatalkan.');
    }

    /**
     * Complete booking
     */
    public function complete(Booking $booking)
    {
        $result = $this->bookingService->completeBooking($booking->id);

        return back()->with('success', 'Booking berhasil diselesaikan.');
    }

    /**
     * Mark booking as no-show and forfeit deposit (for testing)
     */
    public function markAsNoShow(Request $request, Booking $booking)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        // Update booking status to no-show
        $booking->update([
            'status' => 'no-show',
            'admin_notes' => $request->reason ?? 'Customer tidak datang (Testing)',
        ]);

        // Hanguskan deposit jika ada
        if ($booking->deposit && $booking->deposit->status === 'approved') {
            $booking->deposit->update([
                'status' => 'forfeited',
                'admin_notes' => 'DP hangus karena customer tidak datang',
            ]);

            // Create no-show note
            $booking->noShowNote()->create([
                'notes' => $request->reason ?? 'Customer tidak datang pada jadwal yang ditentukan',
                'recorded_by' => auth()->id(),
            ]);
        }

        return back()->with('success', 'Booking ditandai sebagai No-Show dan DP telah dihanguskan.');
    }

    /**
     * Update admin notes
     */
    public function updateNotes(Request $request, Booking $booking)
    {
        $request->validate([
            'admin_notes' => 'nullable|string',
        ]);

        $booking->update(['admin_notes' => $request->admin_notes]);

        return back()->with('success', 'Catatan berhasil diupdate.');
    }
}
