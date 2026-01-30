<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('treatment_id')->constrained()->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained()->onDelete('cascade');
            $table->date('booking_date');
            $table->time('booking_time');
            $table->time('end_time'); // calculated based on treatment duration
            $table->enum('status', [
                'auto_approved', 
                'waiting_deposit', 
                'deposit_confirmed', 
                'deposit_rejected', 
                'expired', 
                'completed', 
                'cancelled'
            ])->default('auto_approved');
            $table->decimal('total_price', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('final_price', 10, 2);
            $table->boolean('is_manual_entry')->default(false); // booking via WhatsApp
            $table->text('admin_notes')->nullable();
            $table->text('customer_notes')->nullable();
            $table->timestamps();
            
            $table->index(['booking_date', 'booking_time']);
            $table->index(['doctor_id', 'booking_date']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
