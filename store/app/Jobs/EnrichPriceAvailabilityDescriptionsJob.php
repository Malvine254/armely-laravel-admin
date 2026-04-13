<?php

namespace App\Jobs;

use App\Services\TDSynnexService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EnrichPriceAvailabilityDescriptionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600;

    public $tries = 1;

    public function __construct(public int $chunk = 25, public int $limit = 0)
    {
        $this->onConnection('database');
        $this->onQueue('products-sync');
    }

    public function handle(TDSynnexService $service): void
    {
        if (!$service->usesPriceAvailabilityAsProductSource()) {
            Log::info('PriceAvailability description enrichment skipped: products source is not priceavailability.');
            return;
        }

        $chunk = max(1, $this->chunk);
        $limit = max(0, $this->limit);

        Log::info('PriceAvailability description enrichment started.', [
            'chunk' => $chunk,
            'limit' => $limit,
        ]);

        $result = $service->syncPriceAvailabilityDescriptionsFromDatabase($chunk, $limit);

        Log::info('PriceAvailability description enrichment completed.', [
            'processed' => (int) ($result['processed'] ?? 0),
            'updated' => (int) ($result['updated'] ?? 0),
        ]);
    }
}
