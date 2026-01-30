<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

// Simulate authenticated request
$user = App\Models\User::where('role', 'customer')->first();
Auth::login($user);

echo "Testing booking creation...\n";
echo "User: {$user->name} (ID: {$user->id})\n\n";

$data = [
    'treatment_id' => 1,
    'doctor_id' => 1,
    'booking_date' => '2025-12-31',
    'booking_time' => '10:00',
    'notes' => 'Test booking'
];

echo "Request data:\n";
print_r($data);
echo "\n";

try {
    $bookingService = new App\Services\BookingService(new App\Services\WhatsAppService());
    $result = $bookingService->createBooking($user->id, $data);
    
    echo "Result:\n";
    print_r($result);
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\nTrace:\n" . $e->getTraceAsString() . "\n";
}
