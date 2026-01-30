<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['nominal', 'percentage']);
            $table->decimal('value', 10, 2); // nominal atau persen
            $table->decimal('min_transaction', 10, 2)->default(0); // minimal transaksi
            $table->date('valid_from');
            $table->date('valid_until');
            $table->boolean('is_single_use')->default(true); // sekali pakai atau berkali-kali
            $table->integer('max_usage')->nullable(); // maksimal penggunaan total
            $table->integer('usage_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('show_on_landing')->default(false); // tampil di landing page
            $table->timestamps();
            
            $table->index(['valid_from', 'valid_until']);
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
