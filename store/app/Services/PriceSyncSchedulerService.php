<?php

namespace App\Services;

use App\Models\AppSetting;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PriceSyncSchedulerService
{
    public function startBackgroundRun(string $scope = 'all', string $skusRaw = '', string $reason = 'manual'): array
    {
        $scope = $scope === 'specific' ? 'specific' : 'all';
        $scopeLabel = $scope === 'specific' ? 'Specific products' : 'All products in database';
        $startedAt = now();

        AppSetting::setValue('price_sync.run_state', [
            'status' => 'running',
            'message' => "Starting - {$scopeLabel}...",
            'output' => "Started at {$startedAt->format('Y-m-d H:i:s T')}\nScope: {$scopeLabel}\nStarted by: {$reason}",
            'started_at' => $startedAt->toDateTimeString(),
            'updated_at' => $startedAt->toDateTimeString(),
            'finished_at' => null,
        ]);

        $this->spawnRefreshCommand($scope, $skusRaw);

        return [
            'status' => 'running',
            'scope' => $scope,
            'scope_label' => $scopeLabel,
        ];
    }

    public function kickOffDueScheduledRun(): void
    {
        $fallbackEnabled = (bool) AppSetting::getValue('price_sync.enable_http_fallback', false);
        if (!$fallbackEnabled) {
            return;
        }

        if (!Cache::add('price_sync.schedule_tick', true, now()->addMinute())) {
            return;
        }

        try {
            $timezone = $this->resolveTimezone();
            $time = $this->resolveScheduledTime();

            [$hour, $minute] = array_map('intval', explode(':', $time));
            $now = Carbon::now($timezone);
            $scheduledAt = $now->copy()->setTime($hour, $minute, 0);
            $todayKey = $now->format('Y-m-d');

            if ($now->lessThan($scheduledAt)) {
                return;
            }

            if ((string) AppSetting::getValue('price_sync.last_triggered_date', '') === $todayKey) {
                return;
            }

            if ($this->isRunCurrentlyInProgress()) {
                return;
            }

            $this->startBackgroundRun('all', '', 'scheduled fallback');
            AppSetting::setValue('price_sync.last_triggered_date', $todayKey);
        } catch (\Throwable $e) {
            Log::warning('Price sync schedule fallback check failed', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Trigger scheduled price sync only when local time exactly matches configured HH:MM.
     * Returns true when a run is dispatched.
     */
    public function triggerExactScheduledRun(): bool
    {
        try {
            $timezone = $this->resolveTimezone();
            $time = $this->resolveScheduledTime();
            $now = Carbon::now($timezone);

            if ($now->format('H:i') !== $time) {
                return false;
            }

            $slotKey = $now->format('Y-m-d H:i');
            if ((string) AppSetting::getValue('price_sync.last_triggered_slot', '') === $slotKey) {
                return false;
            }

            if (!$this->acquireSlotLock($slotKey)) {
                return false;
            }

            if ($this->isRunCurrentlyInProgress()) {
                return false;
            }

            $this->startBackgroundRun('all', '', 'scheduled exact');
            AppSetting::setValue('price_sync.last_triggered_slot', $slotKey);
            AppSetting::setValue('price_sync.last_triggered_date', $now->format('Y-m-d'));

            return true;
        } catch (\Throwable $e) {
            Log::warning('Price sync exact scheduler check failed', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    private function resolveTimezone(): string
    {
        $timezone = (string) AppSetting::getValue('price_sync.timezone', 'America/Chicago');

        return in_array($timezone, timezone_identifiers_list(), true)
            ? $timezone
            : 'America/Chicago';
    }

    private function resolveScheduledTime(): string
    {
        $time = (string) AppSetting::getValue('price_sync.time', '18:00');

        return preg_match('/^\d{2}:\d{2}$/', $time)
            ? $time
            : '18:00';
    }

    private function acquireSlotLock(string $slotKey): bool
    {
        return Cache::add('price_sync.schedule_slot_lock:' . md5($slotKey), true, now()->addMinutes(2));
    }

    private function isRunCurrentlyInProgress(): bool
    {
        $existing = AppSetting::getValue('price_sync.run_state', []);
        if (!is_array($existing) || ($existing['status'] ?? '') !== 'running') {
            return false;
        }

        $startedAt = $existing['started_at'] ?? null;
        $stuckMins = $startedAt ? now()->diffInMinutes(Carbon::parse($startedAt)) : 999;

        return $stuckMins < 30;
    }

    private function spawnRefreshCommand(string $scope, string $skusRaw): void
    {
        $phpBin = PHP_BINARY;
        $artisan = base_path('artisan');
        $scopeArg = '--scope=' . $scope;
        $skusArg = '--skus=' . $skusRaw;

        if (PHP_OS_FAMILY === 'Windows') {
            $cmd = sprintf(
                'cmd /C start "" /B %s %s tdsynnex:refresh-live-prices %s %s > NUL 2>&1',
                $this->shellArg($phpBin),
                $this->shellArg($artisan),
                $this->shellArg($scopeArg),
                $this->shellArg($skusArg)
            );
            pclose(popen($cmd, 'r'));

            return;
        }

        $cmd = sprintf(
            '%s %s tdsynnex:refresh-live-prices %s %s > /dev/null 2>&1 &',
            escapeshellarg($phpBin),
            escapeshellarg($artisan),
            escapeshellarg($scopeArg),
            escapeshellarg($skusArg)
        );
        exec($cmd);
    }

    private function shellArg(string $value): string
    {
        return '"' . str_replace('"', '\"', $value) . '"';
    }
}
