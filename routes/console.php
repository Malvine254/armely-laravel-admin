<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// The admin recurring schedule and deployed page changes share one index runner.

Schedule::command('mela:index-scheduled')->everyMinute()->withoutOverlapping(30);
Schedule::command('mela:prune')->dailyAt('03:45');
