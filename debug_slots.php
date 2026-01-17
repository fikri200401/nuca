<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Doctor;
use App\Models\Treatment;
use App\Models\Booking;
use Carbon\Carbon;

$date = '2025-12-31';
$treatmentId = 1; // Facial Basic

echo "=== DEBUG AVAILABLE SLOTS ===\n\n";

// 1. Cek day of week
$dayOfWeek = strtolower(Carbon::parse($date)->format('l'));
echo "Date: {$date}\n";
echo "Day of Week: {$dayOfWeek}\n\n";

// 2. Cek treatment
$treatment = Treatment::find($treatmentId);
echo "Treatment: {$treatment->name}\n";
echo "Duration: {$treatment->duration_minutes} minutes\n\n";

// 3. Cek doctors available on that day
$doctors = Doctor::active()
    ->whereHas('schedules', function($query) use ($dayOfWeek) {
        $query->where('day_of_week', $dayOfWeek);
    })
    ->with(['schedules' => function($query) use ($dayOfWeek) {
        $query->where('day_of_week', $dayOfWeek);
    }])
    ->get();

echo "Doctors available on {$dayOfWeek}: {$doctors->count()}\n\n";

foreach ($doctors as $doctor) {
    echo "Dr. {$doctor->name}\n";
    
    foreach ($doctor->schedules as $schedule) {
        echo "  Schedule: {$schedule->start_time} - {$schedule->end_time}\n";
        
        // Generate slots
        $currentTime = Carbon::parse($schedule->start_time);
        $endTime = Carbon::parse($schedule->end_time);
        
        echo "  Generating slots...\n";
        
        $slotCount = 0;
        while ($currentTime->copy()->addMinutes($treatment->duration_minutes)->lte($endTime)) {
            $slotStart = $currentTime->format('H:i');
            $slotEnd = $currentTime->copy()->addMinutes($treatment->duration_minutes)->format('H:i');
            
            // Check availability
            $isAvailable = $doctor->isAvailable($date, $slotStart, $slotEnd);
            
            echo "    Slot {$slotStart}-{$slotEnd}: " . ($isAvailable ? 'AVAILABLE' : 'BLOCKED') . "\n";
            
            if (!$isAvailable) {
                // Debug why blocked
                $dateOnly = date('Y-m-d', strtotime($date));
                
                echo "      Checking conflicts for date: {$dateOnly}\n";
                
                $conflictingBookings = $doctor->bookings()
                    ->whereDate('booking_date', $dateOnly)
                    ->whereIn('status', ['auto_approved', 'waiting_deposit', 'deposit_confirmed'])
                    ->get();
                
                echo "      Found {$conflictingBookings->count()} bookings on this date\n";
                
                if ($conflictingBookings->count() > 0) {
                    echo "      Conflicting bookings:\n";
                    foreach ($conflictingBookings as $booking) {
                        echo "        - {$booking->booking_time} to {$booking->end_time} (Status: {$booking->status})\n";
                        
                        // Check overlap logic
                        $bookingStart = substr($booking->booking_time, 0, 5);
                        $bookingEnd = substr($booking->end_time, 0, 5);
                        
                        $overlaps = false;
                        if ($bookingStart <= $slotStart && $bookingEnd > $slotStart) {
                            $overlaps = true;
                            echo "          Overlap: existing booking covers slot start\n";
                        }
                        if ($bookingStart < $slotEnd && $bookingEnd >= $slotEnd) {
                            $overlaps = true;
                            echo "          Overlap: existing booking covers slot end\n";
                        }
                        if ($bookingStart >= $slotStart && $bookingEnd <= $slotEnd) {
                            $overlaps = true;
                            echo "          Overlap: slot completely covers existing booking\n";
                        }
                        
                        if (!$overlaps) {
                            echo "          No actual overlap detected!\n";
                        }
                    }
                } else {
                    echo "      No bookings found, but still marked as BLOCKED - checking query issue\n";
                    
                    // Test raw query
                    $allBookings = $doctor->bookings()->get();
                    echo "      Total bookings for this doctor: {$allBookings->count()}\n";
                    foreach ($allBookings as $b) {
                        echo "        - Date: {$b->booking_date} (raw), Time: {$b->booking_time}-{$b->end_time}\n";
                    }
                }
            }
            
            $slotCount++;
            $currentTime->addMinutes(30);
        }
        
        echo "  Total slots generated: {$slotCount}\n";
    }
    echo "\n";
}

// 4. Check existing bookings on that date
echo "=== EXISTING BOOKINGS ON {$date} ===\n";
$bookings = Booking::where('booking_date', $date)
    ->with(['treatment', 'doctor'])
    ->get();

echo "Total: {$bookings->count()}\n\n";
foreach ($bookings as $booking) {
    echo "- {$booking->booking_time} to {$booking->end_time}: {$booking->treatment->name} with Dr. {$booking->doctor->name} (Status: {$booking->status})\n";
}
