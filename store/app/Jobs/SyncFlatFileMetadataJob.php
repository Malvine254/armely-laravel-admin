<?php

namespace App\Jobs;

use App\Services\CatalogOperationStateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class SyncFlatFileMetadataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600;

    public $tries = 1;

    public function __construct()
    {
        $this->onConnection('database');
        $this->onQueue('products-sync');
    }

    public function middleware(): array
    {
        return [
            (new WithoutOverlapping('tdsynnex:sync-flatfile-metadata'))->expireAfter(3700),
        ];
    }

    public function handle(CatalogOperationStateService $stateService): void
    {
        $stateService->running('Flat-file description and metadata sync started...');

        $exitCode = Artisan::call('tdsynnex:sync-flatfile-metadata');
        $output = $stateService->normalizeOutput(Artisan::output());

        if ($exitCode !== 0) {
            throw new RuntimeException($output !== '' ? $output : 'Flat-file metadata command failed.');
        }

        Cache::forget('catalog_ops_counts_v2');
        Cache::flush();

        $summary = $output !== ''
            ? $output . "\nProduct browse caches cleared."
            : 'Flat-file descriptions and metadata synced. Product browse caches cleared.';

        Log::info('Flat-file description and metadata sync completed.', [
            'output' => $output,
        ]);

        $stateService->complete('Flat-file descriptions and metadata synced.', $summary);
    }

    public function failed(\Throwable $e): void
    {
        Log::error('Flat-file description and metadata sync failed.', [
            'message' => $e->getMessage(),
        ]);

        app(CatalogOperationStateService::class)
            ->fail('Flat-file sync failed: ' . $e->getMessage());
    }
}
