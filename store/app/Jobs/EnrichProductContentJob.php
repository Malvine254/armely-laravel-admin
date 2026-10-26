<?php

namespace App\Jobs;

use App\Models\Product;
use App\Services\ProductContentEnrichmentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EnrichProductContentJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 180;

    public int $tries = 3;

    public int $uniqueFor = 86400;

    public array $backoff = [60, 300];

    public function __construct(public int $productId)
    {
        $this->onConnection('database');
        $this->onQueue('products-sync');
    }

    public function uniqueId(): string
    {
        return (string) $this->productId;
    }

    public function handle(ProductContentEnrichmentService $service): void
    {
        $product = Product::find($this->productId);
        if ($product) {
            $service->enrich($product);
        }
    }

    public function failed(?\Throwable $error): void
    {
        $product = Product::find($this->productId);
        if ($product) {
            $spec = (array) $product->specifications;
            $spec['content_enrichment_status'] = 'failed';
            $spec['content_enrichment_warnings'] = ['Product enrichment failed. Check the queue logs and retry.'];
            $product->specifications = $spec;
            $product->content_enrichment_attempted_at = now();
            $product->save();
        }
        Log::error('Product content enrichment failed.', [
            'product_id' => $this->productId,
            'exception' => $error ? $error::class : null,
        ]);
    }
}
