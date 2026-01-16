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
<<<<<<< HEAD
                'value' => 'Senin - Sabtu: 09:00 - 18:00, Minggu: 10:00 - 16:00',
=======
                'value' => 'Senin - Sabtu: 09:00 - 20:00, Minggu: 10:00 - 20:00',
>>>>>>> 37f6b61 (upload project)
                'type' => 'string',
                'description' => 'Jam operasional',
            ],
            [
<<<<<<< HEAD
=======
                'key' => 'clinic_open_time',
                'value' => '09:00',
                'type' => 'string',
                'description' => 'Jam buka klinik',
            ],
            [
                'key' => 'clinic_close_time',
                'value' => '20:00',
                'type' => 'string',
                'description' => 'Jam tutup klinik',
            ],
            [
                'key' => 'max_booking_time',
                'value' => '18:00',
                'type' => 'string',
                'description' => 'Jam maksimal booking (2 jam sebelum tutup)',
            ],
            [
>>>>>>> 37f6b61 (upload project)
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
            // WhatsApp API Configuration
            [
                'key' => 'fonnte_api_key',
                'value' => env('FONNTE_API_KEY', ''),
                'type' => 'string',
                'description' => 'Fonnte API Key untuk WhatsApp',
            ],
            [
                'key' => 'fonnte_device',
                'value' => env('FONNTE_DEVICE', ''),
                'type' => 'string',
                'description' => 'Fonnte Device / Nomor WhatsApp',
            ],
            [
                'key' => 'whatsapp_enabled',
                'value' => env('WHATSAPP_ENABLED', true) ? '1' : '0',
                'type' => 'boolean',
                'description' => 'Aktifkan notifikasi WhatsApp',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'type' => $setting['type'],
                    'description' => $setting['description'],
                ]
            );
        }
    }
}
