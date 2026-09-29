<?php

namespace App\Services\Mela\Knowledge;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

use function Illuminate\Support\defer;

class MelaKnowledgeIndexRefresh
{
    public const RUNNING_KEY = 'mela:knowledge:reindex-running';
    public const SCHEDULED_KEY = 'mela:knowledge:scheduled-at';
    private const PENDING_KEY = 'mela:knowledge:reindex-pending';

    public function dispatchAfterResponse(string $trigger): bool
    {
        if (!Cache::add(self::RUNNING_KEY, true, now()->addMinutes(30))) {
            Cache::put(self::PENDING_KEY, true, now()->addMinutes(30));

            return false;
        }

        defer(function () use ($trigger) {
            try {
                do {
                    Cache::forget(self::PENDING_KEY);
                    @set_time_limit(1800);
                    $exitCode = Artisan::call('mela:index');

                    if ($exitCode !== 0) {
                        Log::error('Automatic Mela knowledge reindex exited unsuccessfully.', [
                            'trigger' => $trigger,
                            'exit_code' => $exitCode,
                            'output' => Artisan::output(),
                        ]);
                        break;
                    }
                } while (Cache::pull(self::PENDING_KEY));
            } catch (\Throwable $e) {
                Log::error('Automatic Mela knowledge reindex failed.', [
                    'trigger' => $trigger,
                    'error' => $e->getMessage(),
                ]);
            } finally {
                Cache::forget(self::PENDING_KEY);
                Cache::forget(self::RUNNING_KEY);
            }
        });

        return true;
    }

    public function scheduleAt(Carbon $scheduledAt): void
    {
        Cache::put(self::SCHEDULED_KEY, $scheduledAt->toIso8601String(), $scheduledAt->copy()->addDay());
    }

    public function scheduledAt(): ?string
    {
        $scheduledAt = Cache::get(self::SCHEDULED_KEY);

        return is_string($scheduledAt) ? $scheduledAt : null;
    }

    public function cancelScheduled(): bool
    {
        return Cache::forget(self::SCHEDULED_KEY);
    }

    public function runScheduledIfDue(): ?int
    {
        $scheduledAt = $this->scheduledAt();
        if ($scheduledAt === null || Carbon::parse($scheduledAt)->isFuture()) {
            return null;
        }

        if (!Cache::add(self::RUNNING_KEY, true, now()->addMinutes(30))) {
            return null;
        }

        Cache::forget(self::SCHEDULED_KEY);

        try {
            @set_time_limit(1800);

            return Artisan::call('mela:index');
        } finally {
            Cache::forget(self::RUNNING_KEY);
        }
    }
}