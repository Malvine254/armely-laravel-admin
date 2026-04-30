<?php

namespace App\Jobs;

use App\Services\AzureGraphMailService;
use App\Services\CatalogOperationStateService;
use App\Services\TDSynnexService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncPriceAvailabilityCatalogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600;

    public $tries = 1;

    public function __construct(public bool $forceRefresh = false, public bool $fromAdmin = false)
    {
    $this->onConnection('database');
    $this->onQueue('products-sync');
    }

    public function middleware(): array
    {
        return [
            (new WithoutOverlapping('tdsynnex:sync-priceavailability-catalog'))->expireAfter(3700),
        ];
    }

    public function handle(TDSynnexService $service, CatalogOperationStateService $stateService, AzureGraphMailService $mailer): void
    {
        if (!$service->usesPriceAvailabilityAsProductSource()) {
            Log::info('PriceAvailability catalog sync skipped: products source is not priceavailability.');
            if ($this->fromAdmin) {
                $mailer->sendSyncStatusEmail('PriceAvailability Catalog Sync', 'failed', [
                    'Result' => 'Skipped',
                    'Reason' => 'Products source is not set to priceavailability',
                    'Triggered From' => 'Admin settings',
                ]);
                $stateService->fail('Catalog sync skipped: products source is not set to priceavailability.');
            }
            return;
        }

        if ($this->fromAdmin) {
            $stateService->running('Catalog sync job started...');
        }

        // Admin-triggered runs should not use the short web timeout.
        config([
            'tdsynnex.price_availability.request_timeout' => max(
                30,
                (int) config('tdsynnex.price_availability.request_timeout', 8)
            ),
            'tdsynnex.price_availability.max_runtime_seconds' => max(
                300,
                (int) config('tdsynnex.price_availability.max_runtime_seconds', 45)
            ),
        ]);

        Log::info('PriceAvailability catalog sync started.', [
            'force' => $this->forceRefresh,
        ]);

        if ($this->fromAdmin) {
            $mailer->sendSyncStatusEmail('PriceAvailability Catalog Sync', 'started', [
                'Started At' => now()->format('Y-m-d H:i:s T'),
                'Force Refresh' => $this->forceRefresh ? 'Yes' : 'No',
                'Triggered From' => 'Admin settings',
                'Queue' => 'products-sync',
            ]);
        }

        $start = microtime(true);
        $result = $service->syncPriceAvailabilityCatalogToDatabase($this->forceRefresh);
        $elapsed = round(microtime(true) - $start, 1);

        Log::info('PriceAvailability catalog sync completed.', [
            'source_count' => (int) ($result['source_count'] ?? 0),
            'synced' => (int) ($result['synced'] ?? 0),
            'updated' => (int) ($result['updated'] ?? 0),
        ]);

        if ($this->fromAdmin) {
            $mailer->sendSyncStatusEmail('PriceAvailability Catalog Sync', 'completed', [
                'Finished At' => now()->format('Y-m-d H:i:s T'),
                'Source Products' => (int) ($result['source_count'] ?? 0),
                'Rows Synced' => (int) ($result['synced'] ?? 0),
                'Rows Updated' => (int) ($result['updated'] ?? 0),
                'Duration (s)' => $elapsed,
                'Triggered From' => 'Admin settings',
            ]);

            $stateService->complete(
                'Catalog sync completed.',
                "Catalog sync completed.\nSource products: "
                . (int) ($result['source_count'] ?? 0)
                . "\nRows synced: "
                . (int) ($result['synced'] ?? 0)
                . "\nRows updated: "
                . (int) ($result['updated'] ?? 0)
            );
        }
    }

    public function failed(\Throwable $e): void
    {
        if ($this->fromAdmin) {
            app(AzureGraphMailService::class)->sendSyncStatusEmail('PriceAvailability Catalog Sync', 'failed', [
                'Failed At' => now()->format('Y-m-d H:i:s T'),
                'Error' => $e->getMessage(),
                'Triggered From' => 'Admin settings',
            ]);

            app(CatalogOperationStateService::class)
                ->fail('Catalog sync failed: ' . $e->getMessage());
        }
    }
}
