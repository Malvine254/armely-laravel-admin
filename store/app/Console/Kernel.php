<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
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

        // Sync product prices every 30 minutes to keep prices current
        $schedule->job(\App\Jobs\SyncProductPricesJob::class)
            ->everyThirtyMinutes()
            ->name('sync-product-prices')
            ->withoutOverlapping();

        // Keep PriceAvailability catalog cached in local DB for fast storefront queries.
        $schedule->job(new \App\Jobs\SyncPriceAvailabilityCatalogJob(true), 'products-sync', 'database')
            ->hourly()
            ->name('sync-priceavailability-catalog')
            ->withoutOverlapping();

        // Backfill missing product images from Icecat/flat-file sources without blocking requests.
        $schedule->job(new \App\Jobs\EnrichPriceAvailabilityImagesJob(25, 0), 'products-sync', 'database')
            ->everyTwoHours()
            ->name('enrich-priceavailability-images')
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
