<?php

namespace App\Jobs;

use App\Http\Controllers\ProductController;
use App\Models\Product;
use App\Services\CatalogOperationStateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReindexProductsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 1800;
    public $tries   = 1;

    public function __construct(public bool $fromAdmin = false)
    {
        $this->onConnection('database');
        $this->onQueue('products-sync');
    }

    public function handle(CatalogOperationStateService $stateService): void
    {
        if ($this->fromAdmin) {
            $stateService->running('Reindex started: backfilling category_segment and is_hardware…');
        }

        Log::info('ReindexProductsJob: started');

        $total     = 0;
        $updated   = 0;
        $chunkSize = 500;

        // ── Pass 1: backfill category_segment from specifications JSON ────────
        Product::query()
            ->where('vendor_id', 'TD SYNNEX')
            ->where(function ($q) {
                $q->whereNull('category_segment')
                  ->orWhere('category_segment', '');
            })
            ->select(['id', 'specifications'])
            ->orderBy('id')
            ->chunk($chunkSize, function ($products) use (&$total, &$updated) {
                foreach ($products as $product) {
                    $total++;
                    $spec = is_array($product->specifications)
                        ? $product->specifications
                        : (json_decode($product->specifications ?? '{}', true) ?: []);

                    $categoryCode = trim((string) ($spec['categoryCode'] ?? ''));
                    $segment      = substr($categoryCode, 0, 2) ?: null;

                    if ($segment !== null) {
                        Product::where('id', $product->id)
                            ->update(['category_segment' => $segment]);
                        $updated++;
                    }
                }
            });

        Log::info("ReindexProductsJob: category_segment pass complete — {$updated}/{$total} rows updated");

        // ── Pass 2: backfill is_hardware where still 0 but product name is hardware ─
        $hwUpdated = 0;
        Product::query()
            ->where('vendor_id', 'TD SYNNEX')
            ->where('is_hardware', 0)
            ->select(['id', 'product_name', 'description'])
            ->orderBy('id')
            ->chunk($chunkSize, function ($products) use (&$hwUpdated) {
                foreach ($products as $product) {
                    if (ProductController::isHardwareProduct(
                        (string) ($product->product_name ?? ''),
                        (string) ($product->description ?? '')
                    )) {
                        Product::where('id', $product->id)->update(['is_hardware' => 1]);
                        $hwUpdated++;
                    }
                }
            });

        Log::info("ReindexProductsJob: is_hardware pass complete — {$hwUpdated} rows corrected");

        // ── Pass 3: flush all browse caches so the UI sees fresh data immediately ─
        $flushed = Cache::flush();
        Log::info('ReindexProductsJob: cache flushed', ['result' => $flushed]);

        $summary = "Reindex complete.\n"
            . "category_segment: updated {$updated} / {$total} rows.\n"
            . "is_hardware: corrected {$hwUpdated} rows.\n"
            . 'Browse caches cleared.';

        Log::info('ReindexProductsJob: ' . $summary);

        if ($this->fromAdmin) {
            $stateService->complete('Reindex complete.', $summary);
        }
    }
}
