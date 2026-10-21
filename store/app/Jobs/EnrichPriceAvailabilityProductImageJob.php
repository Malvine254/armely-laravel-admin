<?php

namespace App\Jobs;

use App\Models\Product;
use App\Services\TDSynnexService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EnrichPriceAvailabilityProductImageJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 120;

    public int $tries = 3;

    public int $backoff = 3;

    public function uniqueId(): string
    {
        return (string) $this->productId;
    }

    public function __construct(public int $productId)
    {
        $this->onConnection('database');
        $this->onQueue('products-sync');
    }

    public function handle(TDSynnexService $service): void
    {
        $product = Product::query()->find($this->productId);
        if ($product && data_get($product->specifications, 'online_content_status') !== null) {
            $service->enrichDiscoveredProduct($product);
            Product::query()->whereKey($this->productId)->update(['image_enrichment_attempted_at' => now()]);
            return;
        }
        $result = $service->syncPriceAvailabilityImageForProductId($this->productId);
        Product::query()->whereKey($this->productId)->update(['image_enrichment_attempted_at' => now()]);

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

    public function failed(?\Throwable $error): void
    {
        $product = Product::query()->find($this->productId);
        if ($product) {
            $spec = (array) $product->specifications;
            $spec['online_content_warnings'] = ['Product image lookup failed. Please try again later.'];
            $product->specifications = $spec;
            $product->image_enrichment_attempted_at = now();
            $product->save();
        }
        Log::error('Product image enrichment job failed.', [
            'product_id' => $this->productId,
            'exception' => $error ? $error::class : null,
        ]);
    }
}
