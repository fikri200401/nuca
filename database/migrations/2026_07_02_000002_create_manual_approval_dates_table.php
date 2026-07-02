<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tanggal janji temu yang WAJIB approval manual: booking online untuk
     * tanggal ini ditahan (pending_approval) meski auto-approval global aktif.
     * Dipakai saat hari tertentu diprediksi ramai walk-in.
     */
    public function up(): void
    {
        Schema::create('manual_approval_dates', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manual_approval_dates');
    }
};
