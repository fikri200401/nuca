<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Doctor;
use App\Models\Treatment;
use App\Models\Deposit;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BookingService
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * Get available time slots for a specific date and treatment
     */
    public function getAvailableSlots($treatmentId, $date, $doctorId = null)
    {
        $treatment = Treatment::findOrFail($treatmentId);
        $dayOfWeek = strtolower(Carbon::parse($date)->format('l')); // Get day name: monday, tuesday, etc

        // Get doctors available on this day
        $doctors = Doctor::active()
            ->whereHas('schedules', function($query) use ($dayOfWeek) {
                $query->where('day_of_week', $dayOfWeek);
            })
            ->when($doctorId, function($query) use ($doctorId) {
                return $query->where('id', $doctorId);
            })
            ->with(['schedules' => function($query) use ($dayOfWeek) {
                $query->where('day_of_week', $dayOfWeek);
            }])
            ->get();

        $allSlots = [];

        foreach ($doctors as $doctor) {
            foreach ($doctor->schedules as $schedule) {
                $slots = $this->generateTimeSlots(
                    $schedule->start_time,
                    $schedule->end_time,
                    $treatment->duration_minutes,
                    $doctor,
                    $date
                );

                foreach ($slots as $slot) {
                    if ($slot['available'] && !in_array($slot['time'], $allSlots)) {
                        $allSlots[] = $slot['time'];
                    }
                }
            }
        }

        sort($allSlots);
        return $allSlots;
    }

    /**
     * Generate time slots based on treatment duration
     */
    protected function generateTimeSlots($startTime, $endTime, $duration, $doctor, $date)
    {
        $slots = [];
        $currentTime = Carbon::parse($startTime);
        $endTime = Carbon::parse($endTime);
<<<<<<< HEAD
=======
        
        // Get max booking time from settings (default 18:00)
        $maxBookingTime = \App\Models\Setting::get('max_booking_time', '18:00');
        $maxTime = Carbon::parse($maxBookingTime);
>>>>>>> 37f6b61 (upload project)

        while ($currentTime->copy()->addMinutes($duration)->lte($endTime)) {
            $slotStart = $currentTime->format('H:i');
            $slotEnd = $currentTime->copy()->addMinutes($duration)->format('H:i');
<<<<<<< HEAD
=======
            
            // Check if slot exceeds max booking time (slot bisa dimulai sampai max booking time)
            $slotStartTime = Carbon::parse($slotStart);
            if ($slotStartTime->gt($maxTime)) {
                // Skip slots that start AFTER max booking time
                $currentTime->addMinutes(30);
                continue;
            }
>>>>>>> 37f6b61 (upload project)

            // Check if slot is available
            $isAvailable = $doctor->isAvailable($date, $slotStart, $slotEnd);

            $slots[] = [
                'time' => $slotStart,
                'end_time' => $slotEnd,
                'available' => $isAvailable,
            ];

            // Move to next slot (bisa dikonfigurasi, misal setiap 30 menit)
            $currentTime->addMinutes(30);
        }

        return $slots;
    }

    /**
     * Create booking
     */
    public function createBooking($userId, $data)
    {
        DB::beginTransaction();

        try {
            $treatment = Treatment::findOrFail($data['treatment_id']);
            $doctor = Doctor::findOrFail($data['doctor_id']);

            // Calculate end time based on treatment duration
            $startTime = Carbon::parse($data['booking_time']);
            $endTime = $startTime->copy()->addMinutes($treatment->duration_minutes);

            // Check availability
            if (!$doctor->isAvailable($data['booking_date'], $data['booking_time'], $endTime->format('H:i'))) {
                throw new \Exception('Slot tidak tersedia. Silakan pilih waktu lain.');
            }

            // Calculate price with discounts
            $totalPrice = $treatment->price;
            $discountAmount = 0;

            // Apply member discount if applicable
            $user = \App\Models\User::find($userId);
            if ($user->is_member && $user->member_discount > 0) {
                $discountAmount += ($totalPrice * $user->member_discount) / 100;
            }

            // Apply voucher if provided
            if (isset($data['voucher_code'])) {
                $voucher = \App\Models\Voucher::where('code', $data['voucher_code'])->first();
                if ($voucher && $voucher->canBeUsedBy($userId, $totalPrice)) {
                    $voucherDiscount = $voucher->calculateDiscount($totalPrice);
                    $discountAmount += $voucherDiscount;
                }
            }

            $finalPrice = $totalPrice - $discountAmount;

            // Create booking
            $booking = Booking::create([
                'user_id' => $userId,
                'treatment_id' => $treatment->id,
                'doctor_id' => $doctor->id,
                'booking_date' => $data['booking_date'],
                'booking_time' => $data['booking_time'],
                'end_time' => $endTime->format('H:i:s'),
                'total_price' => $totalPrice,
                'discount_amount' => $discountAmount,
                'final_price' => $finalPrice,
                'customer_notes' => $data['notes'] ?? null,
                'admin_notes' => $data['admin_notes'] ?? null,
                'is_manual_entry' => $data['is_manual_entry'] ?? false,
            ]);

            // Check if deposit is required
            $bookingDate = Carbon::parse($data['booking_date']);
            $daysDifference = now()->diffInDays($bookingDate, false);

            if ($daysDifference >= 7) {
                // Booking 7 hari atau lebih, butuh DP
                $booking->update(['status' => 'waiting_deposit']);

                $deposit = Deposit::create([
                    'booking_id' => $booking->id,
                    'amount' => 50000, // Minimal DP
                    'status' => 'pending',
                    'deadline_at' => now()->addHours(24),
                ]);

                // Send notification for deposit
                $this->whatsappService->sendDepositWaiting($booking, $deposit);
            } else {
                // Auto approve
                $booking->update(['status' => 'auto_approved']);

                // Send confirmation
                $this->whatsappService->sendBookingConfirmation($booking);
            }

            // Record voucher usage if applicable
            if (isset($voucher) && $voucher) {
                \App\Models\VoucherUsage::create([
                    'voucher_id' => $voucher->id,
                    'user_id' => $userId,
                    'booking_id' => $booking->id,
                    'discount_amount' => $voucherDiscount ?? 0,
                ]);

                $voucher->incrementUsage();
            }

            DB::commit();

            return [
                'success' => true,
                'booking' => $booking->load(['treatment', 'doctor', 'deposit']),
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Reschedule booking (admin only)
     */
    public function rescheduleBooking($bookingId, $newDate, $newTime, $doctorId = null)
    {
        DB::beginTransaction();

        try {
            $booking = Booking::findOrFail($bookingId);
            $treatment = $booking->treatment;
            
            // Use current doctor if not specified
            $doctor = $doctorId ? Doctor::findOrFail($doctorId) : $booking->doctor;

            // Calculate end time
            $startTime = Carbon::parse($newTime);
            $endTime = $startTime->copy()->addMinutes($treatment->duration_minutes);

            // Check availability
            if (!$doctor->isAvailable($newDate, $newTime, $endTime->format('H:i'))) {
                throw new \Exception('Slot tidak tersedia untuk reschedule.');
            }

            // Update booking
            $booking->update([
                'booking_date' => $newDate,
                'booking_time' => $newTime,
                'end_time' => $endTime->format('H:i:s'),
                'doctor_id' => $doctor->id,
            ]);

            DB::commit();

            // Send notification
            $this->whatsappService->sendBookingConfirmation($booking->fresh());

            return [
                'success' => true,
                'booking' => $booking->fresh(),
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Cancel booking
     */
    public function cancelBooking($bookingId, $adminNotes = null)
    {
        $booking = Booking::findOrFail($bookingId);
        
        $booking->update([
            'status' => 'cancelled',
            'admin_notes' => $adminNotes,
        ]);

        return [
            'success' => true,
            'booking' => $booking,
        ];
    }

    /**
     * Complete booking
     */
    public function completeBooking($bookingId)
    {
        $booking = Booking::findOrFail($bookingId);
        
        $booking->update(['status' => 'completed']);

        return [
            'success' => true,
            'booking' => $booking,
        ];
    }

    /**
     * Get available doctors for a specific slot
     */
    public function getAvailableDoctors($treatmentId, $date, $time)
    {
        $treatment = Treatment::findOrFail($treatmentId);
        $dayOfWeek = strtolower(Carbon::parse($date)->format('l')); // Get day name: monday, tuesday, etc
        
        $startTime = $time;
        $endTime = Carbon::parse($time)->addMinutes($treatment->duration_minutes)->format('H:i');

        $doctors = Doctor::active()
            ->whereHas('schedules', function($query) use ($dayOfWeek, $startTime, $endTime) {
                $query->where('day_of_week', $dayOfWeek)
                      ->where('start_time', '<=', $startTime)
                      ->where('end_time', '>=', $endTime);
            })
            ->get()
            ->filter(function($doctor) use ($date, $startTime, $endTime) {
                return $doctor->isAvailable($date, $startTime, $endTime);
            })
            ->values();

        return $doctors;
    }
}
