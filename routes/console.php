<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled Commands
|--------------------------------------------------------------------------
|
| Here you may define all of your scheduled commands. Laravel will
| automatically handle the scheduling of these commands.
|
*/

// Update lease statuses daily at 6:00 AM
Schedule::command('leases:update-statuses')
    ->dailyAt('06:00')
    ->description('Update lease statuses based on expiration dates')
    ->onOneServer(); // Prevent multiple servers from running this simultaneously

// Optional: Run more frequently during testing/development
// Schedule::command('leases:update-statuses')->hourly();
