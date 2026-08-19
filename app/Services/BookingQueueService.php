<?php

namespace App\Services;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class BookingQueueService
{
    public const ACTIVE_QUEUE_STATUSES = ['waiting', 'confirmed'];

    public const CONFIRMED_BOOKING_STATUSES = ['auto_approved', 'deposit_confirmed'];

    public const TERMINAL_BOOKING_STATUSES = ['cancelled', 'expired', 'completed', 'no-show'];

    /**
     * Queue information shown before a customer submits a reservation.
     */
    public function getSlotStats(int $doctorId, string $date, string $time): array
    {
        $queueCount = Booking::query()
            ->where('doctor_id', $doctorId)
            ->whereDate('booking_date', Carbon::parse($date)->toDateString())
            ->whereTime('booking_time', $this->normalizeTime($time))
            ->whereIn('queue_status', self::ACTIVE_QUEUE_STATUSES)
            ->whereNotIn('status', self::TERMINAL_BOOKING_STATUSES)
            ->count();

        return [
            'queue_count' => $queueCount,
            'next_queue_position' => $queueCount + 1,
            'has_confirmed_booking' => Booking::query()
                ->where('doctor_id', $doctorId)
                ->whereDate('booking_date', Carbon::parse($date)->toDateString())
                ->whereTime('booking_time', $this->normalizeTime($time))
                ->where('queue_status', 'confirmed')
                ->exists(),
        ];
    }

    /**
     * Reconcile every queue belonging to one doctor on one date.
     *
     * The caller must lock the doctor row first. One request at each exact
     * start time is the queue head; only an approved head can hold the slot.
     * Confirmed holders are also checked for duration overlap.
     *
     * @return Collection<int, Booking> bookings newly promoted to slot holder
     */
    public function reconcileLocked(int $doctorId, string $date): Collection
    {
        $bookings = Booking::query()
            ->where('doctor_id', $doctorId)
            ->whereDate('booking_date', Carbon::parse($date)->toDateString())
            ->whereIn('queue_status', self::ACTIVE_QUEUE_STATUSES)
            ->orderByRaw('COALESCE(queue_entered_at, created_at)')
            ->orderBy('id')
            ->lockForUpdate()
            ->get();

        $bookings
            ->filter(fn (Booking $booking) => $this->mustRelease($booking))
            ->each(function (Booking $booking) {
                $booking->update([
                    'queue_status' => 'released',
                    'queue_confirmed_at' => null,
                ]);
            });

        $candidates = $bookings
            ->reject(fn (Booking $booking) => $this->mustRelease($booking))
            ->values();

        $heads = $candidates
            ->groupBy(fn (Booking $booking) => $this->normalizeTime($booking->getRawOriginal('booking_time') ?: $booking->booking_time))
            ->map->first()
            ->values();

        $headIds = $heads->pluck('id');

        $candidates
            ->reject(fn (Booking $booking) => $headIds->contains($booking->id))
            ->filter(fn (Booking $booking) => $booking->queue_status !== 'waiting')
            ->each(function (Booking $booking) {
                $booking->update([
                    'queue_status' => 'waiting',
                    'queue_confirmed_at' => null,
                ]);
            });

        $heads
            ->filter(fn (Booking $booking) => $booking->queue_status === 'confirmed')
            ->reject(fn (Booking $booking) => in_array($booking->status, self::CONFIRMED_BOOKING_STATUSES, true))
            ->each(function (Booking $booking) {
                $booking->update([
                    'queue_status' => 'waiting',
                    'queue_confirmed_at' => null,
                ]);
            });

        $confirmedHolders = collect();
        $newlyConfirmed = collect();

        $currentHolders = $heads
            ->filter(fn (Booking $booking) => $booking->queue_status === 'confirmed')
            ->filter(fn (Booking $booking) => in_array($booking->status, self::CONFIRMED_BOOKING_STATUSES, true))
            ->sortBy(fn (Booking $booking) => sprintf(
                '%s-%020d',
                optional($booking->queue_confirmed_at)->format('Y-m-d H:i:s.u') ?? '9999-12-31 23:59:59.999999',
                $booking->id
            ));

        foreach ($currentHolders as $booking) {
            if ($this->overlapsAny($booking, $confirmedHolders)) {
                $booking->update([
                    'queue_status' => 'waiting',
                    'queue_confirmed_at' => null,
                ]);

                continue;
            }

            $confirmedHolders->push($booking);
        }

        $eligibleHeads = $heads
            ->filter(fn (Booking $booking) => in_array($booking->status, self::CONFIRMED_BOOKING_STATUSES, true))
            ->reject(fn (Booking $booking) => $confirmedHolders->contains('id', $booking->id))
            ->sortBy(fn (Booking $booking) => sprintf(
                '%s-%020d',
                optional($booking->queue_entered_at)->format('Y-m-d H:i:s.u')
                    ?? optional($booking->created_at)->format('Y-m-d H:i:s.u')
                    ?? '9999-12-31 23:59:59.999999',
                $booking->id
            ));

        foreach ($eligibleHeads as $booking) {
            if ($this->overlapsAny($booking, $confirmedHolders)) {
                if ($booking->queue_status !== 'waiting') {
                    $booking->update([
                        'queue_status' => 'waiting',
                        'queue_confirmed_at' => null,
                    ]);
                }

                continue;
            }

            $booking->update([
                'queue_status' => 'confirmed',
                'queue_confirmed_at' => now(),
            ]);

            $booking->refresh();
            $confirmedHolders->push($booking);
            $newlyConfirmed->push($booking);
        }

        return $newlyConfirmed;
    }

    public function release(Booking $booking): void
    {
        $booking->update([
            'queue_status' => 'released',
            'queue_confirmed_at' => null,
        ]);
    }

    private function overlapsAny(Booking $candidate, Collection $holders): bool
    {
        return $holders->contains(function (Booking $holder) use ($candidate) {
            $candidateStart = $this->normalizeTime($candidate->getRawOriginal('booking_time') ?: $candidate->booking_time);
            $candidateEnd = $this->normalizeTime($candidate->getRawOriginal('end_time') ?: $candidate->end_time);
            $holderStart = $this->normalizeTime($holder->getRawOriginal('booking_time') ?: $holder->booking_time);
            $holderEnd = $this->normalizeTime($holder->getRawOriginal('end_time') ?: $holder->end_time);

            return $candidateStart < $holderEnd && $candidateEnd > $holderStart;
        });
    }

    private function mustRelease(Booking $booking): bool
    {
        if (in_array($booking->status, self::TERMINAL_BOOKING_STATUSES, true)) {
            return true;
        }

        $date = $booking->booking_date instanceof Carbon
            ? $booking->booking_date->toDateString()
            : Carbon::parse($booking->booking_date)->toDateString();
        $time = $this->normalizeTime($booking->getRawOriginal('booking_time') ?: $booking->booking_time);

        return Carbon::parse("{$date} {$time}")->lte(now());
    }

    private function normalizeTime(string $time): string
    {
        return Carbon::parse($time)->format('H:i:s');
    }
}
