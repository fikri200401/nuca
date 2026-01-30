<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('before_after_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->string('before_photo')->nullable();
            $table->string('after_photo')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('uploaded_by')->constrained('users'); // admin yang upload
            $table->timestamps();
            
            $table->index('booking_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('before_after_photos');
    }
};
