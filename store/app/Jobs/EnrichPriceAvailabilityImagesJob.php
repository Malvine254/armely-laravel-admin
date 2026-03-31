<?php

namespace App\Jobs;

use App\Models\Product;
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

    public function __construct(public int $chunk = 25, public int $limit = 0)
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

    public function handle(TDSynnexService $service): void
    {
        if (!$service->usesPriceAvailabilityAsProductSource()) {
            Log::info('PriceAvailability image enrichment skipped: products source is not priceavailability.');
            return;
        }

        $chunk = max(1, $this->chunk);
        $limit = max(0, $this->limit);

        Log::info('PriceAvailability image enrichment started.', [
            'chunk' => $chunk,
            'limit' => $limit,
        ]);

        $result = $service->syncPriceAvailabilityImagesFromDatabase($chunk, $limit);

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
    }
}
