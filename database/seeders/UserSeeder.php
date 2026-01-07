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

        // Create Sample Customer
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
    }
}
