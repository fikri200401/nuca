<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin
        User::create([
            'name' => 'Admin Klinik',
            'email' => 'admin@klinik.com',
            'whatsapp_number' => '081234567890',
            'username' => 'admin',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create Owner
        User::create([
            'name' => 'Owner Klinik',
            'email' => 'owner@klinik.com',
            'whatsapp_number' => '081234567891',
            'username' => 'owner',
            'password' => Hash::make('password'),
            'role' => 'owner',
        ]);

        // Create Sample Customer (Member VIP)
        User::create([
            'name' => 'Customer Demo',
            'email' => 'customer@demo.com',
            'whatsapp_number' => '081234567892',
            'username' => 'customer',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'is_member' => true,
            'member_number' => 'MBR-DEMO001',
            'member_discount' => 10,
        ]);

        // Create Additional Customers (Non-Member)
        User::create([
            'name' => 'Siti Nurhaliza',
            'email' => 'siti@gmail.com',
            'whatsapp_number' => '081298765432',
            'username' => 'siti',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'is_member' => false,
        ]);

        User::create([
            'name' => 'Dewi Lestari',
            'email' => 'dewi@gmail.com',
            'whatsapp_number' => '081387654321',
            'username' => 'dewi',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'is_member' => true,
            'member_number' => 'MBR-002',
            'member_discount' => 10,
        ]);

        User::create([
            'name' => 'Rina Kusuma',
            'email' => 'rina@gmail.com',
            'whatsapp_number' => '081276543210',
            'username' => 'rina',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'is_member' => false,
        ]);

        User::create([
            'name' => 'Maya Putri',
            'email' => 'maya@gmail.com',
            'whatsapp_number' => '081365432109',
            'username' => 'maya',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'is_member' => true,
            'member_number' => 'MBR-003',
            'member_discount' => 15, // VIP Member
        ]);

        echo "✓ Created 3 staff users (admin, owner)\n";
        echo "✓ Created 5 customer users (3 members, 2 non-members)\n";
        echo "✓ Default password for all users: 'password'\n";
    }
}
