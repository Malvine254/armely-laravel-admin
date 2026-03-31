<?php

namespace App\Jobs;

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

    public function __construct(public bool $forceRefresh = false)
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

    public function handle(TDSynnexService $service): void
    {
        if (!$service->usesPriceAvailabilityAsProductSource()) {
            Log::info('PriceAvailability catalog sync skipped: products source is not priceavailability.');
            return;
        }

        Log::info('PriceAvailability catalog sync started.', [
            'force' => $this->forceRefresh,
        ]);

        $result = $service->syncPriceAvailabilityCatalogToDatabase($this->forceRefresh);

        Log::info('PriceAvailability catalog sync completed.', [
            'source_count' => (int) ($result['source_count'] ?? 0),
            'synced' => (int) ($result['synced'] ?? 0),
            'updated' => (int) ($result['updated'] ?? 0),
        ]);
    }
}
