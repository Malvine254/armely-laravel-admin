<?php

namespace App\Services\Mela\Knowledge;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

use function Illuminate\Support\defer;

class MelaKnowledgeIndexRefresh
{
    public const RUNNING_KEY = 'mela:knowledge:reindex-running';
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
}