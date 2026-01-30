<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Doctor Schedules:\n";
foreach (DB::table('doctor_schedules')->get() as $schedule) {
    echo "Doctor ID: {$schedule->doctor_id}, Day: {$schedule->day_of_week}, Time: {$schedule->start_time} - {$schedule->end_time}\n";
}

echo "\nTesting date for today:\n";
$date = '2025-12-28';
$dayOfWeek = \Carbon\Carbon::parse($date)->dayOfWeek;
echo "Date: $date, Day of week (Carbon): $dayOfWeek (0=Sunday, 1=Monday...)\n";

echo "\nMatching schedules for day $dayOfWeek:\n";
foreach (DB::table('doctor_schedules')->where('day_of_week', $dayOfWeek)->get() as $schedule) {
    echo "Doctor ID: {$schedule->doctor_id}, Time: {$schedule->start_time} - {$schedule->end_time}\n";
}
