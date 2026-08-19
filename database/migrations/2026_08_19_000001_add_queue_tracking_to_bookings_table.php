<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('queue_status', 20)->default('waiting')->after('status');
            $table->timestamp('queue_entered_at')->nullable()->after('queue_status');
            $table->timestamp('queue_confirmed_at')->nullable()->after('queue_entered_at');

            $table->index(
                ['doctor_id', 'booking_date', 'booking_time', 'queue_status'],
                'bookings_queue_slot_idx'
            );
        });

        $terminalStatuses = ['cancelled', 'expired', 'completed', 'no-show'];
        $confirmedStatuses = ['auto_approved', 'deposit_confirmed'];

        DB::table('bookings')->orderBy('id')->get()->each(function ($booking) use ($terminalStatuses) {
            $appointmentHasPassed = \Carbon\Carbon::parse(
                $booking->booking_date.' '.$booking->booking_time
            )->lte(now());

            DB::table('bookings')->where('id', $booking->id)->update([
                'queue_status' => in_array($booking->status, $terminalStatuses, true) || $appointmentHasPassed
                    ? 'released'
                    : 'waiting',
                'queue_entered_at' => $booking->created_at,
                'queue_confirmed_at' => null,
            ]);
        });

        DB::table('bookings')
            ->whereNotIn('status', $terminalStatuses)
            ->where('queue_status', 'waiting')
            ->orderBy('doctor_id')
            ->orderBy('booking_date')
            ->orderBy('booking_time')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get()
            ->groupBy(fn ($booking) => implode('|', [
                $booking->doctor_id,
                $booking->booking_date,
                substr((string) $booking->booking_time, 0, 5),
            ]))
            ->each(function ($slotBookings) use ($confirmedStatuses) {
                $head = $slotBookings->first();

                if ($head && in_array($head->status, $confirmedStatuses, true)) {
                    DB::table('bookings')->where('id', $head->id)->update([
                        'queue_status' => 'confirmed',
                        'queue_confirmed_at' => $head->updated_at ?? $head->created_at,
                    ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('bookings_queue_slot_idx');
            $table->dropColumn(['queue_status', 'queue_entered_at', 'queue_confirmed_at']);
        });
    }
};
