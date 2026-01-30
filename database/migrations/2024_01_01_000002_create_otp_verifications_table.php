<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('otp_verifications', function (Blueprint $table) {
            $table->id();
            $table->string('whatsapp_number');
            $table->string('otp_code');
            $table->enum('purpose', ['register', 'login', 'reset_password']);
            $table->timestamp('expires_at');
            $table->integer('attempts')->default(0);
            $table->boolean('verified')->default(false);
            $table->timestamp('last_resend_at')->nullable(); // untuk cooldown kirim ulang
            $table->timestamps();
            
            $table->index(['whatsapp_number', 'purpose']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otp_verifications');
    }
};
