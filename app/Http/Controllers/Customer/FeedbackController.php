<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    public function __construct()
    {
    }

    /**
     * Show feedback form
     */
    public function create($bookingId)
    {
        $booking = Booking::where('user_id', Auth::id())
            ->where('status', 'completed')
            ->whereDoesntHave('feedback')
            ->with(['treatment', 'doctor'])
            ->findOrFail($bookingId);

        return view('customer.feedback.create', compact('booking'));
    }

    /**
     * Store feedback
     */
    public function store(Request $request, $bookingId)
    {
        $booking = Booking::where('user_id', Auth::id())
            ->where('status', 'completed')
            ->whereDoesntHave('feedback')
            ->findOrFail($bookingId);

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        Feedback::create([
            'booking_id' => $booking->id,
            'user_id' => Auth::id(),
            'treatment_id' => $booking->treatment_id,
            'doctor_id' => $booking->doctor_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return redirect()
            ->route('customer.bookings.show', $booking->id)
            ->with('success', 'Terima kasih atas feedback Anda!');
    }
}
