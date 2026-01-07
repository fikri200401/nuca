<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'clinic_name',
                'value' => 'Klinik Kecantikan Jelita',
                'type' => 'string',
                'description' => 'Nama klinik',
            ],
            [
                'key' => 'clinic_address',
                'value' => 'Jl. Raya Kecantikan No. 123, Jakarta',
                'type' => 'string',
                'description' => 'Alamat klinik',
            ],
            [
                'key' => 'clinic_phone',
                'value' => '021-1234567',
                'type' => 'string',
                'description' => 'Nomor telepon klinik',
            ],
            [
                'key' => 'clinic_whatsapp',
                'value' => '081234567890',
                'type' => 'string',
                'description' => 'Nomor WhatsApp klinik',
            ],
            [
                'key' => 'operating_hours',
                'value' => 'Senin - Sabtu: 09:00 - 18:00, Minggu: 10:00 - 16:00',
                'type' => 'string',
                'description' => 'Jam operasional',
            ],
            [
                'key' => 'about',
                'value' => 'Klinik Kecantikan Jelita adalah klinik terpercaya dengan dokter berpengalaman dan teknologi modern untuk perawatan kulit Anda.',
                'type' => 'string',
                'description' => 'Tentang klinik',
            ],
            [
                'key' => 'min_deposit',
                'value' => '50000',
                'type' => 'number',
                'description' => 'Minimal DP untuk booking',
            ],
            [
                'key' => 'deposit_deadline_hours',
                'value' => '24',
                'type' => 'number',
                'description' => 'Batas waktu pembayaran DP (jam)',
            ],
            [
                'key' => 'member_discount_default',
                'value' => '10',
                'type' => 'number',
                'description' => 'Diskon member default (%)',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}
