<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('bookings', function (Blueprint $table) {
                $table->string('status')->default('auto_approved')->change();
            });

            return;
        }

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
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('bookings', function (Blueprint $table) {
                $table->string('status')->default('auto_approved')->change();
            });

            return;
        }

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
