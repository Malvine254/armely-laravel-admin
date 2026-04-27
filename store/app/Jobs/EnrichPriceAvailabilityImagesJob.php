<?php

namespace App\Jobs;

use App\Models\Product;
use App\Services\CatalogOperationStateService;
use App\Services\TDSynnexService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EnrichPriceAvailabilityImagesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600;

    public $tries = 1;

    public function __construct(public int $chunk = 1, public int $limit = 0, public bool $fromAdmin = false)
    {
    $this->onConnection('database');
    $this->onQueue('products-sync');
    }

    public function middleware(): array
    {
        return [
            (new WithoutOverlapping('tdsynnex:enrich-priceavailability-images'))->expireAfter(3700),
        ];
    }

    public function handle(TDSynnexService $service, CatalogOperationStateService $stateService): void
    {
        if (!$service->usesPriceAvailabilityAsProductSource()) {
            Log::info('PriceAvailability image enrichment skipped: products source is not priceavailability.');
            if ($this->fromAdmin) {
                $stateService->fail('Image enrichment skipped: products source is not set to priceavailability.');
            }
            return;
        }

        $chunk = max(1, $this->chunk);
        $limit = max(0, $this->limit);

        Log::info('PriceAvailability image enrichment started.', [
            'chunk' => $chunk,
            'limit' => $limit,
        ]);

        if ($this->fromAdmin) {
            $stateService->running(
                'Image enrichment job started... chunk=' . $chunk . ', limit=' . $limit
            );
        }

        $result = $service->syncPriceAvailabilityImagesFromDatabase(
            $chunk,
            $limit,
            $this->fromAdmin
                ? function (int $processed, int $updated, $product, bool $didUpdate, array $images) use ($stateService) {
                    $sku = trim((string) ($product->tdsynnex_sku_no ?? $product->tdsynnex_product_id ?? $product->id));
                    $status = $didUpdate ? 'UPDATED' : 'SKIPPED';
                    $stateService->append(sprintf('[%d] %s SKU:%s (images:%d)', $processed, $status, $sku, count($images)));
                }
                : null
        );

        $withImages = Product::query()
            ->where('vendor_id', 'TD SYNNEX')
            ->whereNotNull('images')
            ->where('images', '!=', '[]')
            ->count();

        Log::info('PriceAvailability image enrichment completed.', [
            'processed' => (int) ($result['processed'] ?? 0),
            'updated' => (int) ($result['updated'] ?? 0),
            'with_images' => (int) $withImages,
        ]);

        if ($this->fromAdmin) {
            $stateService->complete(
                'Image enrichment completed.',
                "Image enrichment completed.\nProcessed: "
                . (int) ($result['processed'] ?? 0)
                . "\nUpdated: "
                . (int) ($result['updated'] ?? 0)
                . "\nProducts with images: "
                . (int) $withImages
            );
        }
    }

    public function failed(\Throwable $e): void
    {
        if ($this->fromAdmin) {
            app(CatalogOperationStateService::class)
                ->fail('Image enrichment failed: ' . $e->getMessage());
        }
    }
}
