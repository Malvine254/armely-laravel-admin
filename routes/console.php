<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

match (config('mela.knowledge.schedule')) {
    'daily' => Schedule::command('mela:index')->dailyAt('03:15')->withoutOverlapping(120),
    'weekly' => Schedule::command('mela:index')->weeklyOn(0, '03:15')->withoutOverlapping(120),
    default => null,
};

Schedule::command('mela:index-scheduled')->everyMinute()->withoutOverlapping(30);
Schedule::command('mela:prune')->dailyAt('03:45');
