<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('deposits', function (Blueprint $table) {
                $table->string('status')->default('pending')->change();
            });

            return;
        }

        DB::statement("ALTER TABLE deposits MODIFY COLUMN status ENUM('pending', 'submitted', 'approved', 'rejected', 'expired') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('deposits', function (Blueprint $table) {
                $table->string('status')->default('pending')->change();
            });

            return;
        }

        DB::statement("ALTER TABLE deposits MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'expired') NOT NULL DEFAULT 'pending'");
    }
};
