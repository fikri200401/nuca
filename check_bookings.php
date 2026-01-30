<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Booking;

echo "Total Bookings: " . Booking::count() . PHP_EOL . PHP_EOL;

$bookings = Booking::with(['treatment', 'doctor'])->get();

foreach ($bookings as $booking) {
    echo "ID: {$booking->id}" . PHP_EOL;
    echo "Code: {$booking->booking_code}" . PHP_EOL;
    echo "Date: {$booking->booking_date}" . PHP_EOL;
    echo "Time: {$booking->booking_time} - {$booking->end_time}" . PHP_EOL;
    echo "Treatment: {$booking->treatment->name}" . PHP_EOL;
    echo "Doctor: {$booking->doctor->name}" . PHP_EOL;
    echo "Status: {$booking->status}" . PHP_EOL;
    echo "---" . PHP_EOL;
}
