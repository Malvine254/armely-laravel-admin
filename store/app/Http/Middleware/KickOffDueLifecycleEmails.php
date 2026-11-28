<?php

namespace App\Http\Middleware;

use App\Jobs\ProcessPriceDropAlertsJob;
use App\Jobs\ProcessReminderSubscriptionsJob;
use App\Models\AppSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Safety net for hosts where the `schedule:run` cron is missing or stalled.
 *
 * Reminder and price-drop emails only ever ran from the scheduler, so a host
 * without a working cron never sent a single one. When a campaign is overdue
 * (well past its normal interval) this runs it once, after the response has
 * been sent to the visitor. With a healthy cron it never fires, because the
 * scheduler keeps last_run_at fresh.
 */
class KickOffDueLifecycleEmails
{
    private const CAMPAIGNS = [
        // AppSetting key => [job class, minutes after which the run is overdue]
        'lifecycle.reminders.last_run_at' => [ProcessReminderSubscriptionsJob::class, 45],
        'lifecycle.price_drop.last_run_at' => [ProcessPriceDropAlertsJob::class, 90],
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (!config('lifecycle.http_fallback_enabled', true) || app()->runningUnitTests()) {
            return $response;
        }

        try {
            // Check at most once a minute across all visitors.
            if (!Cache::add('lifecycle.http_fallback.tick', true, now()->addMinute())) {
                return $response;
            }

            foreach (self::CAMPAIGNS as $settingKey => [$jobClass, $overdueAfterMinutes]) {
                if (!$this->isOverdue($settingKey, $overdueAfterMinutes)) {
                    continue;
                }

                // One fallback run per campaign at a time, across all workers.
                if (!Cache::add("lifecycle.http_fallback.lock.{$jobClass}", true, now()->addMinutes(20))) {
                    continue;
                }

                app()->terminating(function () use ($jobClass): void {
                    try {
                        ignore_user_abort(true);
                        @set_time_limit(600);
                        $jobClass::dispatchSync();
                    } catch (\Throwable $error) {
                        Log::warning('Lifecycle email fallback run failed', ['job' => $jobClass, 'error' => $error->getMessage()]);
                    } finally {
                        Cache::forget("lifecycle.http_fallback.lock.{$jobClass}");
                    }
                });
            }
        } catch (\Throwable $error) {
            Log::debug('Lifecycle email fallback check skipped', ['error' => $error->getMessage()]);
        }

        return $response;
    }

    private function isOverdue(string $settingKey, int $overdueAfterMinutes): bool
    {
        $lastRun = AppSetting::getValue($settingKey, null);
        if (!$lastRun) {
            return true;
        }

        try {
            return Carbon::parse((string) $lastRun)->lessThan(now()->subMinutes($overdueAfterMinutes));
        } catch (\Throwable) {
            return true;
        }
    }
}
