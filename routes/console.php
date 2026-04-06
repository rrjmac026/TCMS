<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('tenants:recalculate-storage')->hourly();

// Run once daily at 8 AM — checks all tenants and notifies those expiring soon
Schedule::command('subscriptions:notify-expiring')->dailyAt('08:00');