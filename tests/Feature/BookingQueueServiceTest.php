<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Setting;
use App\Models\Treatment;
use App\Models\User;
use App\Services\BookingQueueService;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BookingQueueServiceTest extends TestCase
{
    use RefreshDatabase;

    private BookingQueueService $queueService;

    private User $customer;

    private Treatment $treatment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->queueService = app(BookingQueueService::class);
        $this->customer = User::create([
            'name' => 'Queue Customer',
            'email' => 'queue@example.test',
            'whatsapp_number' => '081200000001',
            'password' => 'password',
            'role' => 'customer',
        ]);
        $this->treatment = Treatment::create([
            'name' => 'Queue Treatment',
            'description' => 'Test treatment',
            'duration_minutes' => 60,
            'price' => 100000,
            'is_active' => true,
        ]);
    }

    public function test_queue_counts_are_separated_by_doctor_date_and_time(): void
    {
        $doctorA = $this->doctor('Dokter A');
        $doctorB = $this->doctor('Dokter B');
        $date = now()->addWeek()->toDateString();

        foreach (range(1, 3) as $position) {
            $this->booking($doctorA, $date, '08:00', 'auto_approved', $position);
        }

        foreach (range(1, 5) as $position) {
            $this->booking($doctorB, $date, '08:00', 'auto_approved', $position);
        }

        $this->reconcile($doctorA, $date);
        $this->reconcile($doctorB, $date);

        $this->assertSame(4, $this->queueService->getSlotStats($doctorA->id, $date, '08:00')['next_queue_position']);
        $this->assertSame(6, $this->queueService->getSlotStats($doctorB->id, $date, '08:00')['next_queue_position']);
    }

    public function test_next_approved_booking_is_promoted_after_slot_holder_is_cancelled(): void
    {
        $doctor = $this->doctor('Dokter Promosi');
        $date = now()->addWeek()->toDateString();
        $first = $this->booking($doctor, $date, '09:00', 'auto_approved', 1);
        $second = $this->booking($doctor, $date, '09:00', 'auto_approved', 2);

        $this->reconcile($doctor, $date);

        $this->assertSame('confirmed', $first->fresh()->queue_status);
        $this->assertSame('waiting', $second->fresh()->queue_status);

        $first->update(['status' => 'cancelled', 'queue_status' => 'released']);
        $promoted = $this->reconcile($doctor, $date);

        $this->assertTrue($promoted->contains('id', $second->id));
        $this->assertSame('confirmed', $second->fresh()->queue_status);
    }

    public function test_approved_booking_cannot_skip_an_earlier_unapproved_request_at_the_same_time(): void
    {
        $doctor = $this->doctor('Dokter FIFO');
        $date = now()->addWeek()->toDateString();
        $first = $this->booking($doctor, $date, '10:00', 'pending_approval', 1);
        $second = $this->booking($doctor, $date, '10:00', 'auto_approved', 2);

        $this->reconcile($doctor, $date);

        $this->assertSame('waiting', $first->fresh()->queue_status);
        $this->assertSame('waiting', $second->fresh()->queue_status);

        $first->update(['status' => 'auto_approved']);
        $this->reconcile($doctor, $date);

        $this->assertSame('confirmed', $first->fresh()->queue_status);
        $this->assertSame('waiting', $second->fresh()->queue_status);
    }

    public function test_overlapping_start_times_cannot_both_hold_a_doctor_slot(): void
    {
        $doctor = $this->doctor('Dokter Overlap');
        $date = now()->addWeek()->toDateString();
        $first = $this->booking($doctor, $date, '08:00', 'auto_approved', 1, '09:00');
        $overlap = $this->booking($doctor, $date, '08:30', 'auto_approved', 2, '09:30');

        $this->reconcile($doctor, $date);

        $this->assertSame('confirmed', $first->fresh()->queue_status);
        $this->assertSame('waiting', $overlap->fresh()->queue_status);

        $first->update(['status' => 'cancelled', 'queue_status' => 'released']);
        $this->reconcile($doctor, $date);

        $this->assertSame('confirmed', $overlap->fresh()->queue_status);
    }

    public function test_booking_service_keeps_occupied_time_bookable_and_promotes_fifo_automatically(): void
    {
        Setting::set('whatsapp_enabled', false, 'boolean');
        Setting::set('deposit_enabled', false, 'boolean');

        $doctor = $this->doctor('Dokter Integrasi');
        $date = now()->addDays(2);
        DoctorSchedule::create([
            'doctor_id' => $doctor->id,
            'day_of_week' => strtolower($date->format('l')),
            'start_time' => '08:00',
            'end_time' => '16:00',
            'is_active' => true,
        ]);

        $secondCustomer = User::create([
            'name' => 'Second Queue Customer',
            'email' => 'queue-second@example.test',
            'whatsapp_number' => '081200000002',
            'password' => 'password',
            'role' => 'customer',
        ]);
        $bookingService = app(BookingService::class);
        $payload = [
            'treatment_id' => $this->treatment->id,
            'doctor_id' => $doctor->id,
            'booking_date' => $date->toDateString(),
            'booking_time' => '09:00',
        ];

        $firstResult = $bookingService->createBooking($this->customer->id, $payload);
        $secondResult = $bookingService->createBooking($secondCustomer->id, $payload);

        $this->assertTrue($firstResult['success']);
        $this->assertTrue($secondResult['success']);
        $this->assertSame('confirmed', $firstResult['booking']->queue_status);
        $this->assertSame('waiting', $secondResult['booking']->queue_status);
        $this->assertTrue($secondResult['booking']->isWaitingForSlot());
        $this->assertSame(3, $this->queueService->getSlotStats($doctor->id, $date->toDateString(), '09:00')['next_queue_position']);

        $bookingService->cancelBooking($firstResult['booking']->id, 'Test cancellation');

        $this->assertSame('confirmed', $secondResult['booking']->fresh()->queue_status);
    }

    public function test_past_waiting_list_is_released_instead_of_promoted(): void
    {
        $doctor = $this->doctor('Dokter Jadwal Lewat');
        $date = now()->subDay()->toDateString();
        $booking = $this->booking($doctor, $date, '09:00', 'auto_approved', 1);

        $promoted = $this->reconcile($doctor, $date);

        $this->assertTrue($promoted->isEmpty());
        $this->assertSame('released', $booking->fresh()->queue_status);
    }

    private function doctor(string $name): Doctor
    {
        return Doctor::create([
            'name' => $name,
            'specialization' => 'General',
            'is_active' => true,
        ]);
    }

    private function booking(
        Doctor $doctor,
        string $date,
        string $time,
        string $status,
        int $order,
        ?string $endTime = null
    ): Booking {
        $enteredAt = Carbon::parse($date)->subDays(2)->addMinutes($order);

        return Booking::create([
            'user_id' => $this->customer->id,
            'treatment_id' => $this->treatment->id,
            'doctor_id' => $doctor->id,
            'booking_date' => $date,
            'booking_time' => $time,
            'end_time' => $endTime ?: Carbon::parse($time)->addHour()->format('H:i'),
            'status' => $status,
            'queue_status' => 'waiting',
            'queue_entered_at' => $enteredAt,
            'total_price' => 100000,
            'discount_amount' => 0,
            'final_price' => 100000,
            'created_at' => $enteredAt,
            'updated_at' => $enteredAt,
        ]);
    }

    private function reconcile(Doctor $doctor, string $date)
    {
        return DB::transaction(function () use ($doctor, $date) {
            Doctor::query()->whereKey($doctor->id)->lockForUpdate()->firstOrFail();

            return $this->queueService->reconcileLocked($doctor->id, $date);
        });
    }
}
