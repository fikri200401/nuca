<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Treatment;
use App\Models\Doctor;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
        $isShopOpen = \App\Models\Setting::get('is_shop_open', true);

        return view('customer.booking.create', compact('treatments', 'isShopOpen'));
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
            // Check if shop is open
            $isShopOpen = \App\Models\Setting::get('is_shop_open', true);
            
            $slots = $this->bookingService->getAvailableSlots(
                $request->treatment_id,
                $request->date,
                $request->doctor_id
            );

            return response()->json([
                'success' => true,
                'slots' => $slots,
                'is_shop_open' => $isShopOpen,
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
            // Check if shop is open
            $isShopOpen = \App\Models\Setting::get('is_shop_open', true);
            if (!$isShopOpen) {
                return response()->json([
                    'success' => false,
                    'message' => 'Maaf, klinik sedang tutup. Reservasi online sementara tidak tersedia.',
                ], 422);
            }

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
            Log::error('Booking creation error: ' . $e->getMessage(), [
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

        if (!$booking->deposit || !in_array($booking->deposit->status, ['pending', 'rejected'])) {
            return back()->withErrors(['error' => 'Deposit tidak dapat diupload.']);
        }

        $request->validate([
            'proof_of_payment' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Upload file
        $path = $request->file('proof_of_payment')->store('deposits', 'public');

        // Update deposit with proof and change status to submitted
        $booking->deposit->update([
            'proof_of_payment' => $path,
            'status' => 'submitted',
            'rejection_reason' => null, // Clear rejection reason if re-uploading
        ]);

        return back()->with('success', 'Bukti pembayaran berhasil diupload. Menunggu verifikasi admin.');
    }

    /**
     * Check voucher validity (AJAX)
     */
    public function checkVoucher(Request $request)
    {
        $request->validate([
            'voucher_code' => 'required|string',
            'treatment_id' => 'required|exists:treatments,id',
        ]);

        try {
            $voucher = \App\Models\Voucher::where('code', $request->voucher_code)->first();

            if (!$voucher) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Kode voucher tidak ditemukan.',
                ], 404);
            }

            // Check if voucher is valid
            if (!$voucher->isValid()) {
                $reason = 'Voucher tidak aktif atau sudah kadaluarsa.';
                if ($voucher->max_usage && $voucher->usage_count >= $voucher->max_usage) {
                    $reason = 'Voucher sudah mencapai batas penggunaan.';
                }
                
                return response()->json([
                    'valid' => false,
                    'message' => $reason,
                ], 422);
            }

            // Get treatment price
            $treatment = \App\Models\Treatment::findOrFail($request->treatment_id);
            $totalPrice = $treatment->price;

            // Apply member discount if applicable
            $user = Auth::user();
            $memberDiscount = 0;
            if ($user->is_member && $user->member_discount > 0) {
                $memberDiscount = ($totalPrice * $user->member_discount) / 100;
            }

            $priceAfterMemberDiscount = $totalPrice - $memberDiscount;

            // Check if can be used by this user
            if (!$voucher->canBeUsedBy($user->id, $priceAfterMemberDiscount)) {
                $reason = 'Voucher tidak dapat digunakan.';
                
                if ($priceAfterMemberDiscount < $voucher->min_transaction) {
                    $reason = 'Minimal transaksi Rp ' . number_format($voucher->min_transaction, 0, ',', '.') . ' untuk menggunakan voucher ini.';
                }
                
                if ($voucher->is_single_use) {
                    $hasUsed = $voucher->usages()->where('user_id', $user->id)->exists();
                    if ($hasUsed) {
                        $reason = 'Anda sudah pernah menggunakan voucher ini.';
                    }
                }

                return response()->json([
                    'valid' => false,
                    'message' => $reason,
                ], 422);
            }

            // Calculate discount
            $voucherDiscount = $voucher->calculateDiscount($priceAfterMemberDiscount);
            $finalPrice = $priceAfterMemberDiscount - $voucherDiscount;

            return response()->json([
                'valid' => true,
                'message' => 'Voucher berhasil diterapkan!',
                'voucher' => [
                    'code' => $voucher->code,
                    'name' => $voucher->name,
                    'type' => $voucher->type,
                    'value' => $voucher->value,
                    'formatted_value' => $voucher->formatted_value,
                ],
                'price_breakdown' => [
                    'base_price' => $totalPrice,
                    'member_discount' => $memberDiscount,
                    'voucher_discount' => $voucherDiscount,
                    'final_price' => $finalPrice,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'valid' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}

