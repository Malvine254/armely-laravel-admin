<?php

namespace App\Services\Mela\Knowledge;

use Illuminate\Support\Facades\Cache;

class MelaKnowledgeIndexProgress
{
    private const CACHE_KEY = 'mela:knowledge:index-progress';
    private const STALE_AFTER_MINUTES = 30;

    public function start(): void
    {
        Cache::put(self::CACHE_KEY, [
            'status' => 'running',
            'phase' => 'discovering',
            'discovered' => 0,
            'fetched' => 0,
            'processed' => 0,
            'total' => 0,
            'indexed' => 0,
            'unchanged' => 0,
            'skipped' => 0,
            'failed' => 0,
            'chunks' => 0,
            'current_url' => null,
            'started_at' => now()->toIso8601String(),
            'updated_at' => now()->toIso8601String(),
            'finished_at' => null,
            'error' => null,
        ], now()->addDay());
    }

    public function queue(): void
    {
        $this->update([
            'status' => 'queued',
            'phase' => 'queued',
            'discovered' => 0,
            'fetched' => 0,
            'processed' => 0,
            'total' => 0,
            'indexed' => 0,
            'unchanged' => 0,
            'skipped' => 0,
            'failed' => 0,
            'chunks' => 0,
            'current_url' => null,
            'started_at' => now()->toIso8601String(),
            'finished_at' => null,
            'error' => null,
        ]);
    }

    public function requestStop(): void
    {
        $this->update(['status' => 'stopping', 'phase' => 'stopping']);
    }

    public function stop(): void
    {
        $this->update([
            'status' => 'stopped',
            'phase' => 'stopped',
            'current_url' => null,
            'finished_at' => now()->toIso8601String(),
            'error' => null,
        ]);
    }

    public function update(array $values): void
    {
        $state = array_merge($this->get(), $values, [
            'updated_at' => now()->toIso8601String(),
        ]);
        Cache::put(self::CACHE_KEY, $state, now()->addDay());

        if (in_array($state['status'], ['running', 'stopping'], true) && Cache::has(MelaKnowledgeIndexRefresh::RUNNING_KEY)) {
            Cache::put(MelaKnowledgeIndexRefresh::RUNNING_KEY, true, now()->addMinutes(self::STALE_AFTER_MINUTES));
        }
    }

    public function failIfStale(): bool
    {
        $state = $this->get();
        if (!in_array($state['status'], ['running', 'stopping', 'queued'], true) || empty($state['updated_at'])) {
            return false;
        }

        try {
            $updatedAt = \Illuminate\Support\Carbon::parse($state['updated_at']);
        } catch (\Throwable) {
            $updatedAt = now()->subMinutes(self::STALE_AFTER_MINUTES + 1);
        }

        if ($updatedAt->greaterThan(now()->subMinutes(self::STALE_AFTER_MINUTES))) {
            return false;
        }

        $this->update([
            'status' => 'failed',
            'phase' => 'failed',
            'current_url' => null,
            'error' => 'Indexing stopped reporting progress and has been queued to resume; already indexed pages will be skipped.',
            'finished_at' => now()->toIso8601String(),
        ]);

        return true;
    }

    public function finish(array $stats): void
    {
        $this->update(array_merge($stats, [
            'status' => 'completed',
            'phase' => 'complete',
            'processed' => $this->get()['total'],
            'finished_at' => now()->toIso8601String(),
        ]));
    }

    public function fail(): void
    {
        $this->update([
            'status' => 'failed',
            'phase' => 'failed',
            'error' => 'Indexing failed. Check the application log for details.',
            'finished_at' => now()->toIso8601String(),
        ]);
    }

    public function get(): array
    {
        return array_merge([
            'status' => 'idle',
            'phase' => 'idle',
            'discovered' => 0,
            'fetched' => 0,
            'processed' => 0,
            'total' => 0,
            'indexed' => 0,
            'unchanged' => 0,
            'skipped' => 0,
            'failed' => 0,
            'chunks' => 0,
            'current_url' => null,
            'started_at' => null,
            'updated_at' => null,
            'finished_at' => null,
            'error' => null,
        ], (array) Cache::get(self::CACHE_KEY, []));
    }
}