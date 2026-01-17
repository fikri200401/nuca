<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2)->default(50000); // minimal DP
            $table->string('proof_of_payment')->nullable(); // upload bukti transfer
            $table->enum('status', ['pending', 'approved', 'rejected', 'expired'])->default('pending');
            $table->timestamp('deadline_at'); // 24 jam dari booking
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            
            $table->index('status');
            $table->index('deadline_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deposits');
    }
};
