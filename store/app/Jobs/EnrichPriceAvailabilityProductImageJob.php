<?php

namespace App\Jobs;

use App\Services\TDSynnexService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EnrichPriceAvailabilityProductImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 120;

    public int $tries = 3;

    public int $backoff = 3;

    public function __construct(public int $productId)
    {
        $this->onConnection('database');
        $this->onQueue('products-sync');
    }

    public function handle(TDSynnexService $service): void
    {
        $result = $service->syncPriceAvailabilityImageForProductId($this->productId);

        Log::info('PriceAvailability image enrichment result.', [
            'product_id' => $this->productId,
            'sku' => (string) ($result['sku'] ?? ''),
            'updated' => (int) ($result['updated'] ?? 0),
            'reason' => (string) ($result['reason'] ?? ''),
            'image_url' => (string) ($result['image_url'] ?? ''),
            'image_source' => (string) ($result['image_source'] ?? ''),
        ]);

        if ((int) ($result['updated'] ?? 0) > 0) {
            Log::info('PriceAvailability image persisted.', [
                'product_id' => $this->productId,
                'sku' => (string) ($result['sku'] ?? ''),
                'image_url' => (string) ($result['image_url'] ?? ''),
                'image_source' => (string) ($result['image_source'] ?? ''),
            ]);
        }
    }
}