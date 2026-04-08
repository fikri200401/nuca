<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('description')->nullable();
            $table->json('permissions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Add role_id to users table (nullable, so existing users are unaffected)
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->constrained('roles')->nullOnDelete()->after('role');
        });

        // Seed default roles
        $now = now();
        $roles = [
            [
                'name'        => 'Super Admin',
                'slug'        => 'super-admin',
                'description' => 'Akses penuh tanpa batasan.',
                'permissions' => json_encode([
                    'dashboard'          => ['view' => true, 'add' => true, 'edit' => true, 'delete' => true],
                    'laporan_kunjungan'  => ['view' => true, 'add' => true, 'edit' => true, 'delete' => true],
                    'laporan_pendapatan' => ['view' => true, 'add' => true, 'edit' => true, 'delete' => true],
                    'manajemen_user'     => ['view' => true, 'add' => true, 'edit' => true, 'delete' => true],
                    'manajemen_role'     => ['view' => true, 'add' => true, 'edit' => true, 'delete' => true],
                    'treatments'         => ['view' => true, 'add' => true, 'edit' => true, 'delete' => true],
                    'doctors'            => ['view' => true, 'add' => true, 'edit' => true, 'delete' => true],
                    'bookings'           => ['view' => true, 'add' => true, 'edit' => true, 'delete' => true],
                    'deposits'           => ['view' => true, 'add' => true, 'edit' => true, 'delete' => true],
                    'vouchers'           => ['view' => true, 'add' => true, 'edit' => true, 'delete' => true],
                    'members'            => ['view' => true, 'add' => true, 'edit' => true, 'delete' => true],
                    'feedbacks'          => ['view' => true, 'add' => true, 'edit' => true, 'delete' => true],
                    'settings'           => ['view' => true, 'add' => true, 'edit' => true, 'delete' => true],
                ]),
                'is_active'   => true,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'name'        => 'Doctor',
                'slug'        => 'doctor',
                'description' => 'Dokter klinik. Akses terbatas pada jadwal dan booking.',
                'permissions' => json_encode([
                    'dashboard'          => ['view' => true,  'add' => false, 'edit' => false, 'delete' => false],
                    'laporan_kunjungan'  => ['view' => true,  'add' => false, 'edit' => false, 'delete' => false],
                    'laporan_pendapatan' => ['view' => false, 'add' => false, 'edit' => false, 'delete' => false],
                    'manajemen_user'     => ['view' => false, 'add' => false, 'edit' => false, 'delete' => false],
                    'manajemen_role'     => ['view' => false, 'add' => false, 'edit' => false, 'delete' => false],
                    'treatments'         => ['view' => true,  'add' => false, 'edit' => false, 'delete' => false],
                    'doctors'            => ['view' => true,  'add' => false, 'edit' => false, 'delete' => false],
                    'bookings'           => ['view' => true,  'add' => false, 'edit' => true,  'delete' => false],
                    'deposits'           => ['view' => false, 'add' => false, 'edit' => false, 'delete' => false],
                    'vouchers'           => ['view' => false, 'add' => false, 'edit' => false, 'delete' => false],
                    'members'            => ['view' => true,  'add' => false, 'edit' => false, 'delete' => false],
                    'feedbacks'          => ['view' => true,  'add' => false, 'edit' => false, 'delete' => false],
                    'settings'           => ['view' => false, 'add' => false, 'edit' => false, 'delete' => false],
                ]),
                'is_active'   => true,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'name'        => 'Frontdesk',
                'slug'        => 'frontdesk',
                'description' => 'Staf frontdesk. Mengelola booking dan deposit.',
                'permissions' => json_encode([
                    'dashboard'          => ['view' => true,  'add' => false, 'edit' => false, 'delete' => false],
                    'laporan_kunjungan'  => ['view' => true,  'add' => false, 'edit' => false, 'delete' => false],
                    'laporan_pendapatan' => ['view' => false, 'add' => false, 'edit' => false, 'delete' => false],
                    'manajemen_user'     => ['view' => false, 'add' => false, 'edit' => false, 'delete' => false],
                    'manajemen_role'     => ['view' => false, 'add' => false, 'edit' => false, 'delete' => false],
                    'treatments'         => ['view' => true,  'add' => false, 'edit' => false, 'delete' => false],
                    'doctors'            => ['view' => true,  'add' => false, 'edit' => false, 'delete' => false],
                    'bookings'           => ['view' => true,  'add' => true,  'edit' => true,  'delete' => false],
                    'deposits'           => ['view' => true,  'add' => true,  'edit' => true,  'delete' => false],
                    'vouchers'           => ['view' => true,  'add' => false, 'edit' => false, 'delete' => false],
                    'members'            => ['view' => true,  'add' => false, 'edit' => false, 'delete' => false],
                    'feedbacks'          => ['view' => true,  'add' => false, 'edit' => false, 'delete' => false],
                    'settings'           => ['view' => false, 'add' => false, 'edit' => false, 'delete' => false],
                ]),
                'is_active'   => true,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
        ];

        \DB::table('roles')->insert($roles);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });
        Schema::dropIfExists('roles');
    }
};
