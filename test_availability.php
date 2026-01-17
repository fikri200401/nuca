<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Doctor;

$doctor = Doctor::find(1);
$date = '2025-12-31';

// Test slot 10:00-11:00 yang harusnya available
$result = $doctor->isAvailable($date, '10:00', '11:00');

echo "Testing: Dr. {$doctor->name}, Date: {$date}, Time: 10:00-11:00\n";
echo "Result: " . ($result ? 'AVAILABLE' : 'BLOCKED') . "\n\n";

// Manual check
$dateOnly = date('Y-m-d', strtotime($date));
$bookings = $doctor->bookings()
    ->whereDate('booking_date', $dateOnly)
    ->whereIn('status', ['auto_approved', 'waiting_deposit', 'deposit_confirmed'])
    ->get();

echo "Found {$bookings->count()} bookings:\n";
foreach ($bookings as $booking) {
    $bookingStart = substr($booking->booking_time, 0, 5);
    $bookingEnd = substr($booking->end_time, 0, 5);
    
    echo "- {$bookingStart} to {$bookingEnd}\n";
    
    // Check overlap logic
    $startTime = '10:00';
    $endTime = '11:00';
    
    $overlaps = ($startTime < $bookingEnd && $endTime > $bookingStart);
    echo "  Checking: {$startTime} < {$bookingEnd} && {$endTime} > {$bookingStart}\n";
    echo "  Result: " . ($overlaps ? 'OVERLAPS' : 'NO OVERLAP') . "\n";
}
