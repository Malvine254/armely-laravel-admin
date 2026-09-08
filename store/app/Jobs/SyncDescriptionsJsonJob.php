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

class SyncDescriptionsJsonJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600;

    public $tries = 1;

    public function __construct()
    {
        $this->onConnection('database');
        $this->onQueue('products-metadata');
    }

    public function middleware(): array
    {
        return [
            (new WithoutOverlapping('descriptions:sync-json'))->expireAfter(3700),
        ];
    }

    public function handle(CatalogOperationStateService $stateService): void
    {
        $stateService->running('Product descriptions (JSON) sync started...');

        $exitCode = Artisan::call('descriptions:sync-json', [
            '--report-progress' => true,
        ]);
        $output = $stateService->normalizeOutput(Artisan::output());

        if ($exitCode !== 0) {
            throw new RuntimeException($output !== '' ? $output : 'Descriptions JSON sync command failed.');
        }

        Cache::forget('catalog_ops_counts_v2');
        Cache::flush();

        $summary = $output !== ''
            ? $output . "\nProduct browse caches cleared."
            : 'Product descriptions synced from descriptions.json. Product browse caches cleared.';

        Log::info('Descriptions JSON sync completed.', [
            'output' => $output,
        ]);

        $stateService->complete('Product descriptions synced from descriptions.json.', $summary);
    }

    public function failed(\Throwable $e): void
    {
        Log::error('Descriptions JSON sync failed.', [
            'message' => $e->getMessage(),
        ]);

        app(CatalogOperationStateService::class)
            ->fail('Descriptions JSON sync failed: ' . $e->getMessage());
    }
}
