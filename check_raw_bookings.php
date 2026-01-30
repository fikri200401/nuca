<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$results = DB::select('SELECT id, booking_date, booking_time, end_time, status FROM bookings');

echo "Raw database content:\n\n";
foreach ($results as $row) {
    print_r($row);
}
