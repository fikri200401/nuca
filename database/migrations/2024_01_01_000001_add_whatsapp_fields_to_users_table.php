<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('whatsapp_number')->unique()->after('email');
            $table->string('member_number')->unique()->nullable()->after('whatsapp_number');
            $table->string('username')->unique()->nullable()->after('member_number');
            $table->date('birth_date')->nullable()->after('username');
            $table->enum('gender', ['male', 'female'])->nullable()->after('birth_date');
            $table->text('address')->nullable()->after('gender');
            $table->boolean('is_member')->default(false)->after('address');
            $table->decimal('member_discount', 5, 2)->default(0)->after('is_member');
            $table->enum('role', ['customer', 'admin', 'owner'])->default('customer')->after('member_discount');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'whatsapp_number', 'member_number', 'username', 
                'birth_date', 'gender', 'address', 
                'is_member', 'member_discount', 'role'
            ]);
        });
    }
};
