<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule daily inmate birthday notifications at 08:00 server time
Schedule::command('notify:inmate-birthdays')->dailyAt('08:00');

// Schedule auto-close of resolved tickets (configurable via TICKETS_AUTO_CLOSE_DAYS)
Schedule::command('tickets:auto-close-resolved')->dailyAt('02:00');
