<?php

use Illuminate\Support\Facades\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled Tasks
|--------------------------------------------------------------------------
| Auto-expire deposits dan send booking reminders
*/

// Auto-expire deposits setiap 1 menit
Schedule::command('deposits:expire')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();

// Send booking reminders setiap jam (cek booking besok)
Schedule::command('bookings:send-reminders')
    ->hourly()
    ->withoutOverlapping()
    ->runInBackground();
