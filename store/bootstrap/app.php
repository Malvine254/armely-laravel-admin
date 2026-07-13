<?php

use App\Models\AppSetting;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Carbon;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'active.user' => \App\Http\Middleware\EnsureUserIsActive::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\KickOffDuePriceSync::class,
        ]);

        $middleware->api(append: [
            \App\Http\Middleware\KickOffDuePriceSync::class,
        ]);

        // API routes are token-based and should never redirect to a web login route.
        $middleware->redirectGuestsTo(function ($request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return null;
            }

            return '/login';
        });

        // Allow admin routes to bypass maintenance mode
        $middleware->preventRequestsDuringMaintenance(except: [
            'api/v1/auth/login',
            'api/v1/admin/*',
            'admin/*',
        ]);
    })
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->job(\App\Jobs\CheckExpiringQuotesJob::class)
            ->hourly()
            ->name('check-expiring-quotes')
            ->withoutOverlapping();

        // Evaluate schedule every minute and dispatch only on exact configured
        // HH:MM in the configured timezone.
        $priceSyncTime = '18:00';
        $priceSyncTimezone = 'America/Chicago';

        try {
            $configuredTime = (string) AppSetting::getValue('price_sync.time', '18:00');
            if (preg_match('/^\d{2}:\d{2}$/', $configuredTime)) {
                $priceSyncTime = $configuredTime;
            }

            $configuredTimezone = (string) AppSetting::getValue('price_sync.timezone', 'America/Chicago');
            if (in_array($configuredTimezone, timezone_identifiers_list(), true)) {
                $priceSyncTimezone = $configuredTimezone;
            }
        } catch (\Throwable) {
            // Use defaults when app settings are not available.
        }

        $schedule->command('price-sync:dispatch-scheduled')
            ->dailyAt($priceSyncTime)
            ->timezone($priceSyncTimezone)
            ->name('dispatch-scheduled-price-sync')
            ->withoutOverlapping();

        $schedule->command('price-sync:dispatch-scheduled')
            ->everyMinute()
            ->name('dispatch-scheduled-price-sync-fallback')
            ->withoutOverlapping();

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

        // Import flat file then sync Excel catalog files against TD SYNNEX API daily.
        $schedule->command('tdsynnex:sync-catalog')
            ->daily()
            ->name('tdsynnex-sync-catalog')
            ->withoutOverlapping();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e, $request) {
            // Handle unauthenticated requests
            if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                if ($request->is('api/*') || $request->expectsJson()) {
                    return response()->json([
                        'message' => 'Unauthenticated',
                        'success' => false,
                    ], 401);
                }
            }
        });
    })->create();
