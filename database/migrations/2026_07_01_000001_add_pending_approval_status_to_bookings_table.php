<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Tambahkan status 'pending_approval' pada enum bookings.status.
     * Status ini dipakai ketika auto-approval dimatikan admin - booking
     * masuk antrean menunggu persetujuan manual.
     *
     * Catatan: 'no-show' turut disertakan agar data lama (fitur no-show)
     * tidak hilang saat ALTER enum.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM(
            'pending_approval',
            'auto_approved',
            'waiting_deposit',
            'deposit_confirmed',
            'deposit_rejected',
            'expired',
            'completed',
            'cancelled',
            'no-show'
        ) NOT NULL DEFAULT 'auto_approved'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM(
            'auto_approved',
            'waiting_deposit',
            'deposit_confirmed',
            'deposit_rejected',
            'expired',
            'completed',
            'cancelled',
            'no-show'
        ) NOT NULL DEFAULT 'auto_approved'");
    }
};
