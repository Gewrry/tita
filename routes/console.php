<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Daily overdue check at 8:00 AM
Schedule::command('invoices:check-overdue')->dailyAt('08:00');

// Daily payment reminders at 9:00 AM
Schedule::command('invoices:send-reminders')->dailyAt('09:00');
