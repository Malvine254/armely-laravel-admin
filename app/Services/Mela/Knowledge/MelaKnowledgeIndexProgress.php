<?php

namespace App\Services\Mela\Knowledge;

use Illuminate\Support\Facades\Cache;

class MelaKnowledgeIndexProgress
{
    private const CACHE_KEY = 'mela:knowledge:index-progress';

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

    public function update(array $values): void
    {
        Cache::put(self::CACHE_KEY, array_merge($this->get(), $values, [
            'updated_at' => now()->toIso8601String(),
        ]), now()->addDay());
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