<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Deposit;
use App\Models\Doctor;
use App\Models\Treatment;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BookingService
{
    protected $whatsappService;

    protected $bookingQueueService;

    public function __construct(
        WhatsAppService $whatsappService,
        BookingQueueService $bookingQueueService
    ) {
        $this->whatsappService = $whatsappService;
        $this->bookingQueueService = $bookingQueueService;
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
            ->whereHas('schedules', function ($query) use ($dayOfWeek) {
                $query->where('day_of_week', $dayOfWeek)
                    ->where('is_active', true);
            })
            ->when($doctorId, function ($query) use ($doctorId) {
                return $query->where('id', $doctorId);
            })
            ->with(['schedules' => function ($query) use ($dayOfWeek) {
                $query->where('day_of_week', $dayOfWeek)
                    ->where('is_active', true);
            }])
            ->get();

        $allSlots = [];
        $slotDetails = [];

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
                    $timeKey = $slot['time'];
                    if (! isset($slotDetails[$timeKey])) {
                        $slotDetails[$timeKey] = $slot;
                    } else {
                        // If slot already exists and this one is available, mark as available
                        if ($slot['available'] && ! $slot['isPast']) {
                            $slotDetails[$timeKey]['available'] = true;
                        }
                    }
                }
            }
        }

        // Sort by time
        ksort($slotDetails);

        // Return array of slot objects
        return array_values($slotDetails);
    }

    /**
     * Generate time slots based on treatment duration
     */
    protected function generateTimeSlots($startTime, $endTime, $duration, $doctor, $date)
    {
        $slots = [];

        // Extract time from datetime if necessary
        $startTimeStr = is_string($startTime) ? $startTime : $startTime->format('H:i:s');
        $endTimeStr = is_string($endTime) ? $endTime : $endTime->format('H:i:s');

        $currentTime = Carbon::createFromTimeString($startTimeStr);
        $endTime = Carbon::createFromTimeString($endTimeStr);
        $bookingDate = Carbon::parse($date);
        $now = Carbon::now();

        // Get max booking time from settings (default 20:00 / 8 PM)
        $maxBookingTime = \App\Models\Setting::get('max_booking_time', '20:00');
        $maxTime = Carbon::createFromTimeString($maxBookingTime);

        while ($currentTime->copy()->addMinutes($duration)->lte($endTime)) {
            $slotStart = $currentTime->format('H:i');
            $slotEnd = $currentTime->copy()->addMinutes($duration)->format('H:i');

            // Check if slot exceeds max booking time (slot bisa dimulai sampai max booking time)
            $slotStartTime = Carbon::createFromTimeString($slotStart);
            if ($slotStartTime->gt($maxTime)) {
                // Skip slots that start AFTER max booking time (20:00)
                $currentTime->addMinutes(30);

                continue;
            }

            // Disable slot if it's today and the time has already passed
            $slotDateTime = $bookingDate->copy()->setTimeFromTimeString($slotStart);
            $isPast = false;
            if ($bookingDate->isToday() && $slotDateTime->lte($now)) {
                $isPast = true;
            }

            // Existing reservations do not hide a slot. Customers can still
            // choose it and join the doctor/date/time waiting list.
            $isAvailable = $doctor->isScheduledAt($date, $slotStart, $slotEnd) && ! $isPast;

            $slots[] = [
                'time' => $slotStart,
                'end_time' => $slotEnd,
                'available' => $isAvailable,
                'isPast' => $isPast,
            ];

            // Move to next slot (setiap 30 menit)
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
            // Serialise queue writes per doctor so simultaneous reservations
            // cannot race when deciding who holds the slot.
            $doctor = Doctor::query()
                ->whereKey($data['doctor_id'])
                ->lockForUpdate()
                ->firstOrFail();

            // Tolak booking pelanggan pada hari/tanggal klinik tutup.
            // Entri manual admin boleh menembus (is_manual_entry).
            if (! ($data['is_manual_entry'] ?? false)) {
                $closedReason = $this->getClosedReason($data['booking_date']);
                if ($closedReason) {
                    throw new \Exception($closedReason);
                }
            }

            // Calculate end time based on treatment duration
            $startTime = Carbon::parse($data['booking_time']);
            $endTime = $startTime->copy()->addMinutes($treatment->duration_minutes);

            if (! ($data['is_manual_entry'] ?? false)) {
                $appointment = Carbon::parse($data['booking_date'].' '.$data['booking_time']);
                if ($appointment->lte(now())) {
                    throw new \Exception('Waktu booking sudah lewat. Silakan pilih jadwal berikutnya.');
                }
            }

            // Validate working hours only. An occupied slot remains bookable
            // because the new request will be placed in its waiting list.
            if (! $doctor->isScheduledAt($data['booking_date'], $data['booking_time'], $endTime->format('H:i'))) {
                throw new \Exception('Dokter tidak memiliki jadwal pada waktu tersebut.');
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
                'queue_status' => 'waiting',
                'queue_entered_at' => now(),
                'total_price' => $totalPrice,
                'discount_amount' => $discountAmount,
                'final_price' => $finalPrice,
                'customer_notes' => $data['notes'] ?? null,
                'admin_notes' => $data['admin_notes'] ?? null,
                'is_manual_entry' => $data['is_manual_entry'] ?? false,
            ]);

            // === Penentuan status booking berdasarkan Setting Booking ===
            $depositEnabled = \App\Models\Setting::get('deposit_enabled', true);
            $thresholdDays = (int) \App\Models\Setting::get('deposit_threshold_days', 7);
            $depositAmount = (float) \App\Models\Setting::get('min_deposit', 50000);
            $depositDeadlineHours = (int) \App\Models\Setting::get('deposit_deadline_hours', 24);

            $bookingDate = Carbon::parse($data['booking_date']);

            // Apakah booking ditahan (pending_approval)?
            // - Entri manual admin: tidak pernah ditahan (admin = pihak yang meng-ACC).
            // - Jadwal HARI INI: dikendalikan checkbox "Auto Approval Booking" (booking_auto_approval).
            // - Jadwal ke depan: dikendalikan daftar "Auto-Approval OFF per Tanggal".
            if ($data['is_manual_entry'] ?? false) {
                $held = false;
            } elseif ($bookingDate->isToday()) {
                $held = ! \App\Models\Setting::get('booking_auto_approval', true);
            } else {
                $held = $this->requiresManualApproval($data['booking_date']);
            }

            $daysDifference = now()->diffInDays($bookingDate, false);
            $needsDeposit = $depositEnabled && $daysDifference >= $thresholdDays;

            if ($held) {
                // Ditahan (Menunggu Konfirmasi): jadwal hari ini saat auto-approve OFF,
                // atau tanggal ke depan yang ada di daftar Auto-Approval OFF per Tanggal.
                $booking->update(['status' => 'pending_approval']);
            } elseif ($needsDeposit) {
                // Booking >= ambang hari, butuh DP
                $booking->update(['status' => 'waiting_deposit']);

                $deposit = Deposit::create([
                    'booking_id' => $booking->id,
                    'amount' => $depositAmount, // Minimal DP (dari Setting Booking)
                    'status' => 'pending',
                    'deadline_at' => now()->addHours($depositDeadlineHours),
                ]);

            } else {
                // Auto approve
                $booking->update(['status' => 'auto_approved']);
            }

            $this->bookingQueueService->reconcileLocked(
                $doctor->id,
                Carbon::parse($data['booking_date'])->toDateString()
            );

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

            $booking = $booking->fresh(['user', 'treatment', 'doctor', 'deposit']);
            $this->sendCurrentBookingNotification($booking);

            return [
                'success' => true,
                'booking' => $booking,
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
        $snapshot = Booking::findOrFail($bookingId);
        DB::beginTransaction();

        try {
            $newDoctorId = (int) ($doctorId ?: $snapshot->doctor_id);
            $doctorIds = collect([$snapshot->doctor_id, $newDoctorId])->unique()->sort()->values();
            $lockedDoctors = Doctor::query()
                ->whereIn('id', $doctorIds)
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');
            $booking = Booking::query()->whereKey($bookingId)->lockForUpdate()->firstOrFail();
            $treatment = $booking->treatment;

            $oldDoctorId = $booking->doctor_id;
            $oldDate = $booking->booking_date->toDateString();
            $doctor = $lockedDoctors->get($newDoctorId);

            if (! $doctor) {
                throw new \Exception('Dokter tidak ditemukan.');
            }

            // Calculate end time
            $startTime = Carbon::parse($newTime);
            $endTime = $startTime->copy()->addMinutes($treatment->duration_minutes);

            if (Carbon::parse($newDate.' '.$newTime)->lte(now())) {
                throw new \Exception('Waktu reschedule sudah lewat. Silakan pilih jadwal berikutnya.');
            }

            if (! $doctor->isScheduledAt($newDate, $newTime, $endTime->format('H:i'))) {
                throw new \Exception('Dokter tidak memiliki jadwal pada waktu reschedule tersebut.');
            }

            $booking->update([
                'booking_date' => $newDate,
                'booking_time' => $newTime,
                'end_time' => $endTime->format('H:i:s'),
                'doctor_id' => $doctor->id,
                'queue_status' => 'waiting',
                'queue_entered_at' => now(),
                'queue_confirmed_at' => null,
            ]);

            $activated = collect();
            $activated = $activated->merge(
                $this->bookingQueueService->reconcileLocked($oldDoctorId, $oldDate)
            );

            if ($oldDoctorId !== $newDoctorId || $oldDate !== Carbon::parse($newDate)->toDateString()) {
                $activated = $activated->merge(
                    $this->bookingQueueService->reconcileLocked($newDoctorId, $newDate)
                );
            }

            DB::commit();

            $booking = $booking->fresh(['user', 'treatment', 'doctor', 'deposit']);
            $this->sendActivationNotifications($activated, [$booking->id]);
            $this->sendCurrentBookingNotification($booking);

            return [
                'success' => true,
                'booking' => $booking,
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
        $snapshot = Booking::findOrFail($bookingId);
        $result = DB::transaction(function () use ($bookingId, $adminNotes, $snapshot) {
            Doctor::query()->whereKey($snapshot->doctor_id)->lockForUpdate()->firstOrFail();
            $booking = Booking::query()->whereKey($bookingId)->lockForUpdate()->firstOrFail();

            $booking->update([
                'status' => 'cancelled',
                'admin_notes' => $adminNotes,
            ]);
            $this->bookingQueueService->release($booking);

            return [
                'booking' => $booking->fresh(),
                'activated' => $this->bookingQueueService->reconcileLocked(
                    $booking->doctor_id,
                    $booking->booking_date->toDateString()
                ),
            ];
        });

        $this->sendActivationNotifications($result['activated']);

        return [
            'success' => true,
            'booking' => $result['booking'],
        ];
    }

    /**
     * Complete booking
     */
    public function completeBooking($bookingId)
    {
        $snapshot = Booking::findOrFail($bookingId);
        $result = DB::transaction(function () use ($bookingId, $snapshot) {
            Doctor::query()->whereKey($snapshot->doctor_id)->lockForUpdate()->firstOrFail();
            $booking = Booking::query()->whereKey($bookingId)->lockForUpdate()->firstOrFail();

            if (! $booking->isSlotHolder()) {
                throw new \Exception('Hanya pemegang slot yang dapat ditandai selesai.');
            }

            $booking->update(['status' => 'completed']);
            $this->bookingQueueService->release($booking);

            return [
                'booking' => $booking->fresh(),
                'activated' => $this->bookingQueueService->reconcileLocked(
                    $booking->doctor_id,
                    $booking->booking_date->toDateString()
                ),
            ];
        });

        $this->sendActivationNotifications($result['activated']);

        return [
            'success' => true,
            'booking' => $result['booking'],
        ];
    }

    /**
     * Setujui booking yang sedang ditahan (pending_approval).
     * Persetujuan manual langsung mengonfirmasi booking (auto_approved).
     */
    public function approveBooking($bookingId)
    {
        $snapshot = Booking::findOrFail($bookingId);
        $result = DB::transaction(function () use ($bookingId, $snapshot) {
            Doctor::query()->whereKey($snapshot->doctor_id)->lockForUpdate()->firstOrFail();
            $booking = Booking::query()->whereKey($bookingId)->lockForUpdate()->firstOrFail();

            if ($booking->status !== 'pending_approval') {
                return [
                    'success' => false,
                    'message' => 'Booking ini tidak sedang menunggu persetujuan.',
                ];
            }

            $booking->update(['status' => 'auto_approved']);
            $activated = $this->bookingQueueService->reconcileLocked(
                $booking->doctor_id,
                $booking->booking_date->toDateString()
            );

            return [
                'success' => true,
                'booking' => $booking->fresh(['user', 'treatment', 'doctor', 'deposit']),
                'activated' => $activated,
            ];
        });

        if (! $result['success']) {
            return $result;
        }

        $this->sendActivationNotifications($result['activated'], [$result['booking']->id]);
        $this->sendCurrentBookingNotification($result['booking']);

        return [
            'success' => true,
            'booking' => $result['booking'],
            'waitlisted' => $result['booking']->isWaitingForSlot(),
        ];
    }

    /**
     * Tolak booking yang sedang ditahan (pending_approval).
     */
    public function rejectBooking($bookingId, $reason = null)
    {
        $snapshot = Booking::findOrFail($bookingId);
        $result = DB::transaction(function () use ($bookingId, $reason, $snapshot) {
            Doctor::query()->whereKey($snapshot->doctor_id)->lockForUpdate()->firstOrFail();
            $booking = Booking::query()->whereKey($bookingId)->lockForUpdate()->firstOrFail();

            if ($booking->status !== 'pending_approval') {
                return [
                    'success' => false,
                    'message' => 'Booking ini tidak sedang menunggu persetujuan.',
                ];
            }

            $booking->update([
                'status' => 'cancelled',
                'admin_notes' => $reason ?: 'Booking ditolak oleh admin.',
            ]);
            $this->bookingQueueService->release($booking);

            return [
                'success' => true,
                'booking' => $booking->fresh(['user']),
                'activated' => $this->bookingQueueService->reconcileLocked(
                    $booking->doctor_id,
                    $booking->booking_date->toDateString()
                ),
            ];
        });

        if (! $result['success']) {
            return $result;
        }

        $this->sendActivationNotifications($result['activated']);
        $this->whatsappService->sendBookingRejected($result['booking'], $reason);

        return [
            'success' => true,
            'booking' => $result['booking'],
        ];
    }

    /**
     * Approve a submitted deposit and let the queue decide whether this
     * booking can hold the requested slot immediately.
     */
    public function approveDeposit(int $depositId, int $adminId): array
    {
        $snapshot = Deposit::with('booking:id,doctor_id')->findOrFail($depositId);

        $result = DB::transaction(function () use ($depositId, $adminId, $snapshot) {
            Doctor::query()->whereKey($snapshot->booking->doctor_id)->lockForUpdate()->firstOrFail();
            $deposit = Deposit::query()->whereKey($depositId)->lockForUpdate()->firstOrFail();
            $booking = Booking::query()->whereKey($deposit->booking_id)->lockForUpdate()->firstOrFail();

            if (! $deposit->isSubmitted()) {
                return [
                    'success' => false,
                    'message' => 'Hanya deposit yang menunggu verifikasi yang dapat disetujui.',
                ];
            }

            $deposit->approve($adminId);
            $booking->update(['status' => 'deposit_confirmed']);

            return [
                'success' => true,
                'booking' => $booking->fresh(['user', 'treatment', 'doctor', 'deposit']),
                'activated' => $this->bookingQueueService->reconcileLocked(
                    $booking->doctor_id,
                    $booking->booking_date->toDateString()
                ),
            ];
        });

        if (! $result['success']) {
            return $result;
        }

        $booking = $result['booking']->fresh(['user', 'treatment', 'doctor', 'deposit']);
        $this->sendActivationNotifications($result['activated'], [$booking->id]);

        if ($booking->isSlotHolder()) {
            $this->whatsappService->sendDepositApproved($booking);
        } else {
            $this->whatsappService->sendDepositApprovedWaitingList($booking);
        }

        return [
            'success' => true,
            'booking' => $booking,
            'waitlisted' => $booking->isWaitingForSlot(),
        ];
    }

    /**
     * Expire a deposit and release its place, promoting the next eligible
     * request automatically.
     */
    public function expireDeposit(int $depositId): array
    {
        $snapshot = Deposit::with('booking:id,doctor_id')->findOrFail($depositId);

        $result = DB::transaction(function () use ($depositId, $snapshot) {
            Doctor::query()->whereKey($snapshot->booking->doctor_id)->lockForUpdate()->firstOrFail();
            $deposit = Deposit::query()->whereKey($depositId)->lockForUpdate()->firstOrFail();
            $booking = Booking::query()->whereKey($deposit->booking_id)->lockForUpdate()->firstOrFail();

            if ($deposit->status !== 'pending') {
                return [
                    'success' => false,
                    'message' => 'Deposit ini tidak lagi menunggu pembayaran.',
                ];
            }

            $deposit->update(['status' => 'expired']);
            $booking->update(['status' => 'expired']);
            $this->bookingQueueService->release($booking);

            return [
                'success' => true,
                'booking' => $booking->fresh(['user']),
                'activated' => $this->bookingQueueService->reconcileLocked(
                    $booking->doctor_id,
                    $booking->booking_date->toDateString()
                ),
            ];
        });

        if (! $result['success']) {
            return $result;
        }

        $this->sendActivationNotifications($result['activated']);
        $this->whatsappService->sendDepositExpired($result['booking']);

        return $result;
    }

    /**
     * Notify customers whose waiting-list entry has just become the holder.
     */
    public function sendActivationNotifications(Collection $bookings, array $exceptIds = []): void
    {
        $bookings
            ->unique('id')
            ->reject(fn (Booking $booking) => in_array($booking->id, $exceptIds, true))
            ->each(function (Booking $booking) {
                $this->whatsappService->sendBookingConfirmation(
                    $booking->fresh(['user', 'treatment', 'doctor'])
                );
            });
    }

    private function sendCurrentBookingNotification(Booking $booking): void
    {
        if ($booking->status === 'pending_approval') {
            $this->whatsappService->sendBookingPendingApproval($booking);

            return;
        }

        if ($booking->status === 'waiting_deposit' && $booking->deposit) {
            $this->whatsappService->sendDepositWaiting($booking, $booking->deposit);

            return;
        }

        if (in_array($booking->status, BookingQueueService::CONFIRMED_BOOKING_STATUSES, true)) {
            if ($booking->isSlotHolder()) {
                $this->whatsappService->sendBookingConfirmation($booking);
            } else {
                $this->whatsappService->sendBookingWaitingList($booking);
            }
        }
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

        return Doctor::active()
            ->whereHas('schedules', function ($query) use ($dayOfWeek, $startTime, $endTime) {
                $query->where('day_of_week', $dayOfWeek)
                    ->where('start_time', '<=', $startTime)
                    ->where('end_time', '>=', $endTime);
            })
            ->get()
            ->filter(fn ($doctor) => $doctor->isScheduledAt($date, $startTime, $endTime))
            ->map(function ($doctor) use ($date, $startTime) {
                return array_merge([
                    'id' => $doctor->id,
                    'name' => $doctor->name,
                    'specialization' => $doctor->specialization,
                ], $this->bookingQueueService->getSlotStats(
                    $doctor->id,
                    $date,
                    $startTime
                ));
            })
            ->values();
    }

    /**
     * Kembalikan alasan (string) jika klinik tutup pada tanggal tsb, atau null jika buka.
     * Sumber: hari tutup rutin (setting closed_weekdays) + tanggal libur khusus (tabel).
     */
    public function getClosedReason($date): ?string
    {
        $carbon = Carbon::parse($date);
        $weekdayEn = strtolower($carbon->format('l'));

        // 1) Hari tutup rutin (mis. tiap Minggu)
        $closedWeekdays = \App\Models\Setting::get('closed_weekdays', []);
        if (! is_array($closedWeekdays)) {
            $closedWeekdays = [];
        }
        if (in_array($weekdayEn, $closedWeekdays, true)) {
            return 'Klinik libur setiap hari '.self::weekdayLabelId($weekdayEn).'.';
        }

        // 2) Tanggal libur khusus (one-off)
        $closed = \App\Models\ClinicClosedDate::whereDate('date', $carbon->toDateString())->first();
        if ($closed) {
            return $closed->note
                ? ('Klinik libur pada tanggal ini: '.$closed->note.'.')
                : 'Klinik libur pada tanggal ini.';
        }

        return null;
    }

    /**
     * Apakah tanggal janji temu ini di-set wajib approval manual
     * (auto-approval dimatikan khusus tanggal ini)?
     */
    public function requiresManualApproval($date): bool
    {
        return \App\Models\ManualApprovalDate::whereDate('date', Carbon::parse($date)->toDateString())->exists();
    }

    /**
     * Nama hari (Inggris -> Indonesia) untuk pesan & tampilan.
     */
    public static function weekdayLabelId(string $en): string
    {
        return [
            'monday' => 'Senin',
            'tuesday' => 'Selasa',
            'wednesday' => 'Rabu',
            'thursday' => 'Kamis',
            'friday' => 'Jumat',
            'saturday' => 'Sabtu',
            'sunday' => 'Minggu',
        ][strtolower($en)] ?? ucfirst($en);
    }
}
