<?php

namespace App\Services\Mela\Knowledge;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

use function Illuminate\Support\defer;

class MelaKnowledgeIndexRefresh
{
    public const RUNNING_KEY = 'mela:knowledge:reindex-running';
    public const SCHEDULED_KEY = 'mela:knowledge:scheduled-at';
    private const PENDING_KEY = 'mela:knowledge:reindex-pending';

    public function settings(): object
    {
        DB::table('mela_index_settings')->insertOrIgnore(['id' => 1, 'interval_minutes' => 1440, 'next_run_at' => now()->addDay()]);

        return DB::table('mela_index_settings')->where('id', 1)->first();
    }

    public function dispatchAfterResponse(string $trigger): bool
    {
        Cache::forever(self::PENDING_KEY, true);
        if (!Cache::add(self::RUNNING_KEY, true, now()->addMinutes(30))) {
            return false;
        }

        defer(function () use ($trigger) {
            try {
                $fingerprint = $this->siteFingerprint();
                if ($this->runIndex() === 0) {
                    $this->settings();
                    DB::table('mela_index_settings')->where('id', 1)->update(['site_fingerprint' => $fingerprint]);
                }
            } catch (\Throwable $e) {
                Log::error('Automatic Mela knowledge reindex failed.', ['trigger' => $trigger, 'error' => $e->getMessage()]);
            } finally {
                Cache::forget(self::RUNNING_KEY);
            }
        });

        return true;
    }

    private function runIndex(): int
    {
        // Remove only the work being handled. Changes during this run stay pending.
        Cache::forget(self::PENDING_KEY);
        try {
            @set_time_limit(1800);
            $code = Artisan::call('mela:index');
            if ($code !== 0) {
                Cache::forever(self::PENDING_KEY, true);
                Log::error('Mela index failed.', ['exit_code' => $code]);
            }
            return $code;
        } catch (\Throwable $e) {
            Cache::forever(self::PENDING_KEY, true);
            throw $e;
        }
    }

    public function scheduleEvery(int $minutes): void
    {
        $this->settings();
        DB::table('mela_index_settings')->where('id', 1)->update([
            'interval_minutes' => $minutes,
            'next_run_at' => now()->addMinutes($minutes),
        ]);
        Cache::forget(self::SCHEDULED_KEY);
    }

    public function scheduledAt(): ?string
    {
        $next = $this->settings()->next_run_at;
        return $next ? Carbon::parse($next)->toIso8601String() : null;
    }

    public function cancelScheduled(): bool
    {
        $this->settings();
        DB::table('mela_index_settings')->where('id', 1)->update(['interval_minutes' => null, 'next_run_at' => null]);
        Cache::forget(self::SCHEDULED_KEY);
        return true;
    }

    public function siteFingerprint(): string
    {
        $hashes = [];
        // Detect published template/controller/route changes after deployment too.
        foreach ([resource_path('views'), app_path('Http/Controllers'), base_path('routes')] as $directory) {
            foreach (File::allFiles($directory) as $file) {
                $relative = str_replace('\\', '/', $file->getRelativePathname());
                if (str_starts_with(strtolower($relative), 'admin/')) continue;
                $hashes[$directory . '/' . $relative] = hash_file('sha256', $file->getPathname());
            }
        }
        ksort($hashes);
        return hash('sha256', json_encode($hashes));
    }

    public function runScheduledIfDue(): ?int
    {
        $settings = $this->settings();
        $fingerprint = $this->siteFingerprint();
        $changed = $settings->site_fingerprint !== $fingerprint;
        $due = $settings->next_run_at && Carbon::parse($settings->next_run_at)->isPast();
        if (!$changed && !$due && !Cache::has(self::PENDING_KEY)) return null;
        if (!Cache::add(self::RUNNING_KEY, true, now()->addMinutes(30))) return null;

        try {
            $code = $this->runIndex();
            if ($code === 0) {
                DB::table('mela_index_settings')->where('id', 1)->update(['site_fingerprint' => $fingerprint]);
                // A concurrently edited or disabled schedule must remain untouched.
                if ($due) {
                    DB::table('mela_index_settings')->where('id', 1)
                        ->where('next_run_at', $settings->next_run_at)
                        ->where('interval_minutes', $settings->interval_minutes)
                        ->update(['next_run_at' => $settings->interval_minutes ? now()->addMinutes($settings->interval_minutes) : null]);
                }
            }
            return $code;
        } finally {
            Cache::forget(self::RUNNING_KEY);
        }
    }
}
