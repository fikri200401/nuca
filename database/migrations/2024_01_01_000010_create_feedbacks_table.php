<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('treatment_id')->constrained()->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained()->onDelete('cascade');
            $table->integer('rating'); // 1-5
            $table->text('comment')->nullable();
            $table->boolean('is_visible')->default(true); // admin bisa hide/show
            $table->timestamps();
            
            $table->index('rating');
            $table->index(['treatment_id', 'doctor_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedbacks');
    }
};
