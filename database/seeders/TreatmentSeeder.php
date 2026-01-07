<?php

namespace Database\Seeders;

use App\Models\Treatment;
use Illuminate\Database\Seeder;

class TreatmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $treatments = [
            [
                'name' => 'Facial Basic',
                'description' => 'Perawatan wajah dasar untuk menjaga kesehatan kulit',
                'duration_minutes' => 60,
                'price' => 150000,
                'is_popular' => true,
            ],
            [
                'name' => 'Facial Acne',
                'description' => 'Perawatan khusus untuk kulit berjerawat',
                'duration_minutes' => 90,
                'price' => 250000,
                'is_popular' => true,
            ],
            [
                'name' => 'Whitening Treatment',
                'description' => 'Perawatan pemutihan kulit',
                'duration_minutes' => 120,
                'price' => 350000,
                'is_popular' => true,
            ],
            [
                'name' => 'Chemical Peeling',
                'description' => 'Pengelupasan kulit mati untuk regenerasi kulit baru',
                'duration_minutes' => 90,
                'price' => 400000,
                'is_popular' => false,
            ],
            [
                'name' => 'Microdermabrasion',
                'description' => 'Eksfoliasi kulit dengan teknologi kristal mikro',
                'duration_minutes' => 75,
                'price' => 450000,
                'is_popular' => true,
            ],
            [
                'name' => 'Laser Hair Removal',
                'description' => 'Penghilangan bulu permanen dengan teknologi laser',
                'duration_minutes' => 45,
                'price' => 500000,
                'is_popular' => false,
            ],
        ];

        foreach ($treatments as $treatment) {
            Treatment::create($treatment);
        }
    }
}
