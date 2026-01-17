<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\DoctorSchedule;
use Illuminate\Database\Seeder;

class DoctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Doctor 1
        $doctor1 = Doctor::create([
            'name' => 'Dr. Sarah Wijaya',
            'specialization' => 'Dermatologi Estetik',
            'phone' => '081234567800',
            'email' => 'sarah@klinik.com',
            'bio' => 'Spesialis kulit dengan pengalaman 10 tahun di bidang dermatologi estetik',
        ]);

        // Schedule for Doctor 1 (Senin - Jumat)
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        foreach ($days as $day) {
            DoctorSchedule::create([
                'doctor_id' => $doctor1->id,
                'day_of_week' => $day,
                'start_time' => '09:00',
                'end_time' => '17:00',
            ]);
        }

        // Doctor 2
        $doctor2 = Doctor::create([
            'name' => 'Dr. Amanda Putri',
            'specialization' => 'Aesthetic Medicine',
            'phone' => '081234567801',
            'email' => 'amanda@klinik.com',
            'bio' => 'Dokter estetik bersertifikat internasional',
        ]);

        // Schedule for Doctor 2 (Rabu - Minggu)
        $days2 = ['wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        foreach ($days2 as $day) {
            DoctorSchedule::create([
                'doctor_id' => $doctor2->id,
                'day_of_week' => $day,
                'start_time' => '10:00',
                'end_time' => '18:00',
            ]);
        }

        // Doctor 3
        $doctor3 = Doctor::create([
            'name' => 'Dr. Lisa Hernandez',
            'specialization' => 'Skin Care Specialist',
            'phone' => '081234567802',
            'email' => 'lisa@klinik.com',
            'bio' => 'Ahli perawatan kulit dengan fokus pada anti-aging',
        ]);

        // Schedule for Doctor 3 (Seluruh hari)
        $allDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        foreach ($allDays as $day) {
            DoctorSchedule::create([
                'doctor_id' => $doctor3->id,
                'day_of_week' => $day,
                'start_time' => '08:00',
                'end_time' => '16:00',
            ]);
        }
    }
}
