<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Doctor;
use App\Models\Treatment;
use App\Models\User;
use App\Models\Booking;

// Tanggal 31 Desember 2025 adalah hari Wednesday
$date = '2025-12-31';
$dayOfWeek = 'wednesday';

// Cek dokter yang ada jadwal di hari Wednesday
$doctors = Doctor::active()
    ->whereHas('schedules', function($q) use ($dayOfWeek) {
        $q->where('day_of_week', $dayOfWeek)->where('is_active', true);
    })
    ->with(['schedules' => function($q) use ($dayOfWeek) {
        $q->where('day_of_week', $dayOfWeek);
    }])
    ->get();

echo "Dokter tersedia di Wednesday: " . $doctors->count() . PHP_EOL;
foreach ($doctors as $doctor) {
    echo "Dr. {$doctor->name} - ";
    foreach ($doctor->schedules as $schedule) {
        echo "{$schedule->start_time}-{$schedule->end_time}";
    }
    echo PHP_EOL;
}

// Ambil beberapa treatment
$treatments = Treatment::take(3)->get();
echo PHP_EOL . "Treatments: " . $treatments->count() . PHP_EOL;

// Buat beberapa booking di tanggal 31 Desember 2025
$user = User::where('role', 'customer')->first();

if ($doctors->count() > 0 && $treatments->count() > 0 && $user) {
    // Booking 1: Pagi jam 09:00
    $booking1 = Booking::create([
        'user_id' => $user->id,
        'treatment_id' => $treatments[0]->id,
        'doctor_id' => $doctors[0]->id,
        'booking_date' => $date,
        'booking_time' => '09:00',
        'end_time' => '10:00',
        'total_price' => $treatments[0]->price,
        'discount_amount' => 0,
        'final_price' => $treatments[0]->price,
        'status' => 'auto_approved',
    ]);
    
    // Booking 2: Siang jam 13:00
    $booking2 = Booking::create([
        'user_id' => $user->id,
        'treatment_id' => $treatments[1]->id,
        'doctor_id' => $doctors[0]->id,
        'booking_date' => $date,
        'booking_time' => '13:00',
        'end_time' => '14:00',
        'total_price' => $treatments[1]->price,
        'discount_amount' => 0,
        'final_price' => $treatments[1]->price,
        'status' => 'auto_approved',
    ]);
    
    // Booking 3: Sore jam 15:00
    $booking3 = Booking::create([
        'user_id' => $user->id,
        'treatment_id' => $treatments[2]->id,
        'doctor_id' => $doctors[0]->id,
        'booking_date' => $date,
        'booking_time' => '15:00',
        'end_time' => '16:00',
        'total_price' => $treatments[2]->price,
        'discount_amount' => 0,
        'final_price' => $treatments[2]->price,
        'status' => 'auto_approved',
    ]);
    
    echo PHP_EOL . "Booking berhasil dibuat untuk tanggal 31 Desember 2025:" . PHP_EOL;
    echo "1. {$booking1->treatment->name} - {$booking1->booking_time} dengan Dr. {$booking1->doctor->name}" . PHP_EOL;
    echo "2. {$booking2->treatment->name} - {$booking2->booking_time} dengan Dr. {$booking2->doctor->name}" . PHP_EOL;
    echo "3. {$booking3->treatment->name} - {$booking3->booking_time} dengan Dr. {$booking3->doctor->name}" . PHP_EOL;
    
    echo PHP_EOL . "Slot yang sudah terisi: 09:00-10:00, 13:00-14:00, 15:00-16:00" . PHP_EOL;
    echo "Slot tersedia lainnya: 10:00, 10:30, 11:00, 11:30, 12:00, 12:30, 14:00, 14:30, 16:00, 16:30" . PHP_EOL;
} else {
    echo "Data tidak lengkap untuk membuat booking" . PHP_EOL;
    echo "Doctors: " . $doctors->count() . PHP_EOL;
    echo "Treatments: " . $treatments->count() . PHP_EOL;
    echo "User: " . ($user ? 'Found' : 'Not found') . PHP_EOL;
}
