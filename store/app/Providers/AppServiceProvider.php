<?php

namespace App\Providers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Check once per minute (non-blocking) whether a scheduled price sync is due,
        // and fire it in the background if so. This replaces the need for a cron job.
        $this->maybeFireScheduledPriceSync();
    }

    private function maybeFireScheduledPriceSync(): void
    {
        // Skip during CLI (artisan commands, queue workers, etc.)
        if (app()->runningInConsole()) {
            return;
        }

        // Throttle: only run this check once per minute across all web requests.
        if (Cache::has('price_sync_check_ran')) {
            return;
        }
        Cache::put('price_sync_check_ran', true, 55);

        try {
            $syncTime     = (string) \App\Models\AppSetting::getValue('price_sync.time', '18:00');
            $syncTimezone = (string) \App\Models\AppSetting::getValue('price_sync.timezone', 'Africa/Nairobi');

            if (!preg_match('/^\d{2}:\d{2}$/', $syncTime)) {
                return;
            }
            if (!in_array($syncTimezone, timezone_identifiers_list(), true)) {
                $syncTimezone = 'Africa/Nairobi';
            }

            $now           = \Carbon\Carbon::now($syncTimezone);
            $scheduledTime = \Carbon\Carbon::parse($now->format('Y-m-d') . ' ' . $syncTime, $syncTimezone);

            // Not yet time.
            if ($now->lt($scheduledTime)) {
                return;
            }

            // More than 4 hours past the scheduled time — missed window, skip today.
            if ($scheduledTime->diffInMinutes($now) > 240) {
                return;
            }

            // Already triggered today.
            $today        = $now->format('Y-m-d');
            $lastTriggered = (string) \App\Models\AppSetting::getValue('price_sync.last_triggered_date', '');
            if ($lastTriggered === $today) {
                return;
            }

            // Mark triggered before spawning so a second request in the same minute can't race.
            \App\Models\AppSetting::setValue('price_sync.last_triggered_date', $today);

            $phpBin  = PHP_BINARY;
            $artisan = base_path('artisan');

            if (PHP_OS_FAMILY === 'Windows') {
                $cmd = sprintf('start "" /B %s %s tdsynnex:refresh-live-prices',
                    escapeshellarg($phpBin), escapeshellarg($artisan));
                pclose(popen($cmd, 'r'));
            } else {
                $cmd = sprintf('%s %s tdsynnex:refresh-live-prices > /dev/null 2>&1 &',
                    escapeshellarg($phpBin), escapeshellarg($artisan));
                exec($cmd);
            }
        } catch (\Throwable) {
            // Never block a web request because of a background task failure.
        }
    }
}
