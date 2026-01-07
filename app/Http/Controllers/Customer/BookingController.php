<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Treatment;
use App\Models\Doctor;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /**
     * Show booking form
     */
    public function create()
    {
        $treatments = Treatment::active()->get();
        return view('customer.booking.create', compact('treatments'));
    }

    /**
     * Get available slots (AJAX)
     */
    public function getAvailableSlots(Request $request)
    {
        $request->validate([
            'treatment_id' => 'required|exists:treatments,id',
            'date' => 'required|date|after_or_equal:today',
            'doctor_id' => 'nullable|exists:doctors,id',
        ]);

        try {
            $slots = $this->bookingService->getAvailableSlots(
                $request->treatment_id,
                $request->date,
                $request->doctor_id
            );

            return response()->json([
                'success' => true,
                'slots' => $slots,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get available doctors for specific slot (AJAX)
     */
    public function getAvailableDoctors(Request $request)
    {
        $request->validate([
            'treatment_id' => 'required|exists:treatments,id',
            'date' => 'required|date',
            'time' => 'required',
        ]);

        try {
            $doctors = $this->bookingService->getAvailableDoctors(
                $request->treatment_id,
                $request->date,
                $request->time
            );

            return response()->json([
                'success' => true,
                'doctors' => $doctors,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store booking
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'treatment_id' => 'required|exists:treatments,id',
                'doctor_id' => 'required|exists:doctors,id',
                'booking_date' => 'required|date|after_or_equal:today',
                'booking_time' => 'required',
                'voucher_code' => 'nullable|string',
                'notes' => 'nullable|string|max:500',
            ]);

            $result = $this->bookingService->createBooking(Auth::id(), [
                'treatment_id' => $request->treatment_id,
                'doctor_id' => $request->doctor_id,
                'booking_date' => $request->booking_date,
                'booking_time' => $request->booking_time,
                'voucher_code' => $request->voucher_code,
                'notes' => $request->notes,
            ]);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Booking berhasil dibuat!',
                    'booking_id' => $result['booking']->id,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 422);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', $e->validator->errors()->all()),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Booking creation error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show booking list
     */
    public function index()
    {
        $bookings = Booking::where('user_id', Auth::id())
            ->with(['treatment', 'doctor', 'deposit'])
            ->orderBy('booking_date', 'desc')
            ->orderBy('booking_time', 'desc')
            ->paginate(10);

        return view('customer.booking.index', compact('bookings'));
    }

    /**
     * Show booking detail
     */
    public function show($id)
    {
        $booking = Booking::where('user_id', Auth::id())
            ->with(['treatment', 'doctor', 'deposit', 'feedback', 'beforeAfterPhotos'])
            ->findOrFail($id);

        return view('customer.booking.show', compact('booking'));
    }

    /**
     * Upload deposit proof
     */
    public function uploadDepositProof(Request $request, $id)
    {
        $booking = Booking::where('user_id', Auth::id())
            ->findOrFail($id);

        if (!$booking->deposit || $booking->deposit->status !== 'pending') {
            return back()->withErrors(['error' => 'Deposit tidak dapat diupload.']);
        }

        $request->validate([
            'proof_of_payment' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Upload file
        $path = $request->file('proof_of_payment')->store('deposits', 'public');

        $booking->deposit->update([
            'proof_of_payment' => $path,
        ]);

        return back()->with('success', 'Bukti pembayaran berhasil diupload. Menunggu verifikasi admin.');
    }
}
