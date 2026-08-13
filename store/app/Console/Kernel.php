<?php

namespace App\Console;

use App\Models\AppSetting;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Carbon;

class Kernel extends ConsoleKernel
{
    private function settingValue(string $key, mixed $default = null): mixed
    {
        try {
            return AppSetting::getValue($key, $default);
        } catch (\Throwable) {
            return $default;
        }
    }

    private function scheduledPriceSyncTime(): string
    {
        $time = (string) $this->settingValue('price_sync.time', '18:00');

        return preg_match('/^\d{2}:\d{2}$/', $time)
            ? $time
            : '18:00';
    }

    private function scheduledPriceSyncTimezone(): string
    {
        $timezone = (string) $this->settingValue('price_sync.timezone', 'America/Chicago');

        return in_array($timezone, timezone_identifiers_list(), true)
            ? $timezone
            : 'America/Chicago';
    }

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Check for expiring quotes every hour
        $schedule->job(\App\Jobs\CheckExpiringQuotesJob::class)
            ->hourly()
            ->name('check-expiring-quotes')
            ->withoutOverlapping();

        $scheduleTime = $this->scheduledPriceSyncTime();
        $scheduleTimezone = $this->scheduledPriceSyncTimezone();

        $schedule->command('price-sync:dispatch-scheduled')
            ->dailyAt($scheduleTime)
            ->timezone($scheduleTimezone)
            ->name('dispatch-scheduled-price-sync')
            ->withoutOverlapping();

        // Keep a secondary every-minute evaluation available for compatibility with
        // alternate schedule runners and to recover quickly if the exact cron missed.
        $schedule->command('price-sync:dispatch-scheduled')
            ->everyMinute()
            ->name('dispatch-scheduled-price-sync-fallback')
            ->withoutOverlapping();

        // Midnight: compare live_* shadow columns against main columns and apply any changes
        // to base_price, retail_price, quantity, is_available. This is the only time
        // displayed prices/availability are updated.
        $schedule->job(\App\Jobs\NightlyPriceSyncJob::class, 'products-sync', 'database')
            ->dailyAt('00:00')
            ->name('nightly-price-sync')
            ->withoutOverlapping();

        $schedule->job(\App\Jobs\ProcessPriceDropAlertsJob::class, 'default', 'database')
            ->hourly()
            ->name('process-price-drop-alerts')
            ->withoutOverlapping();

        $schedule->job(\App\Jobs\ProcessReminderSubscriptionsJob::class, 'default', 'database')
            ->everyThirtyMinutes()
            ->name('process-reminder-subscriptions')
            ->withoutOverlapping();

        $schedule->job(\App\Jobs\ProcessInvoicePaymentRemindersJob::class, 'default', 'database')
            ->everySixHours()
            ->name('process-invoice-payment-reminders')
            ->withoutOverlapping();

        $schedule->command('storefront:rebuild-assortment')
            ->dailyAt('01:00')
            ->timezone($scheduleTimezone)
            ->name('rebuild-storefront-assortment')
            ->withoutOverlapping();

        // Update order statuses every 30 minutes
        $schedule->call(function () {
            $orders = \App\Models\Order::whereIn('status', ['pending', 'processing', 'shipped'])
                ->get();
            
            foreach ($orders as $order) {
                \App\Jobs\UpdateOrderStatusJob::dispatch($order);
            }
        })
        ->everyThirtyMinutes()
        ->name('update-order-statuses')
        ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
