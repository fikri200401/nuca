<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            TreatmentSeeder::class,
            DoctorSeeder::class,
            SettingSeeder::class,
            VoucherSeeder::class,
            BookingSeeder::class,
            FeedbackSeeder::class,
            ArticleSeeder::class,
        ]);
    }
}
