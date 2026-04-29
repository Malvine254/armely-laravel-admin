<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Services\TDSynnexService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Fill missing images for storefront-visible TD SYNNEX products.
 *
 * Single-worker mode (sequential):
 *   php artisan products:fill-missing-images
 *
 * Five parallel workers (recommended for bulk runs):
 *   php artisan products:fill-missing-images --workers=5
 *
 * Individual worker (if you prefer to open 5 terminals manually):
 *   php artisan products:fill-missing-images --worker=0 --total-workers=5
 *   php artisan products:fill-missing-images --worker=1 --total-workers=5  ... etc.
 *
 * Dry-run to see how many products need images:
 *   php artisan products:fill-missing-images --dry-run
 */
class FillMissingProductImagesCommand extends Command
{
    protected $signature = 'products:fill-missing-images
                            {--workers=1      : Number of parallel workers to spawn (orchestrator mode)}
                            {--worker=        : Worker index (0-based, set internally by orchestrator)}
                            {--total-workers= : Total worker count (set internally by orchestrator)}
                            {--limit=0        : Max products to process across all workers (0 = all)}
                            {--chunk=50       : DB fetch chunk size per worker loop}
                            {--force          : Re-enrich products that already have images}
                            {--dry-run        : Count and list products needing images without processing}
                            {--quiet-output   : Suppress per-product lines (useful for log capture)}';

    protected $description = 'Fill missing product images using Icecat → GTIN → SerpAPI → scraping → Bing waterfall, with optional parallel workers';

    public function handle(TDSynnexService $service): int
    {
        $workerOption    = $this->option('worker');
        $totalWorkers    = max(1, (int) ($this->option('total-workers') ?? 1));
        $workerId        = $workerOption !== null ? max(0, (int) $workerOption) : null;
        $workers         = max(1, (int) $this->option('workers'));
        $limit           = max(0, (int) $this->option('limit'));
        $chunk           = max(1, (int) $this->option('chunk'));
        $force           = (bool) $this->option('force');
        $dryRun          = (bool) $this->option('dry-run');

        // ── Worker mode (spawned by orchestrator) ──────────────────────────────
        if ($workerId !== null) {
            return $this->runWorker($workerId, $totalWorkers, $chunk, $limit, $force, $service);
        }

        // ── Dry-run mode ───────────────────────────────────────────────────────
        $productIds = $this->fetchProductIds($limit, $force);
        $total      = count($productIds);

        if ($dryRun) {
            $this->info("Products needing images: {$total}");
            if ($total > 0 && $total <= 20) {
                foreach ($productIds as $id) {
                    $p = Product::find($id, ['id', 'product_name', 'tdsynnex_sku_no', 'mfg_part_no']);
                    $this->line("  #{$id}  SKU:" . ($p?->tdsynnex_sku_no ?? '—') . '  ' . mb_substr((string) ($p?->product_name ?? ''), 0, 60));
                }
            }
            return self::SUCCESS;
        }

        if ($total === 0) {
            $this->info('All storefront products already have images — nothing to process.');
            return self::SUCCESS;
        }

        $this->info("Found {$total} products with missing images.");

        // ── Single-worker (no parallelism) ─────────────────────────────────────
        if ($workers <= 1) {
            $this->info('Running in single-worker mode. Use --workers=5 for parallel processing.');
            return $this->runWorker(0, 1, $chunk, $limit, $force, $service);
        }

        // ── Orchestrator: spawn N parallel sub-processes ───────────────────────
        $this->info("Spawning {$workers} parallel workers for {$total} products...");
        $this->newLine();

        $phpBin  = PHP_BINARY;
        $artisan = base_path('artisan');
        $limitOpt = $limit > 0 ? " --limit={$limit}" : '';
        $forceOpt = $force ? ' --force' : '';

        $processes = [];
        $pipes     = [];

        for ($i = 0; $i < $workers; $i++) {
            $cmd = sprintf(
                '%s %s products:fill-missing-images --worker=%d --total-workers=%d --chunk=%d%s%s --no-interaction 2>&1',
                escapeshellarg($phpBin),
                escapeshellarg($artisan),
                $i,
                $workers,
                $chunk,
                $limitOpt,
                $forceOpt
            );

            $descriptors = [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ];

            $proc = proc_open($cmd, $descriptors, $pipe);
            if ($proc === false) {
                $this->error("Failed to spawn worker {$i}. Falling back to single-worker mode.");
                return $this->runWorker(0, 1, $chunk, $limit, $force, $service);
            }

            fclose($pipe[0]);
            stream_set_blocking($pipe[1], false);
            stream_set_blocking($pipe[2], false);
            $processes[$i] = $proc;
            $pipes[$i]     = ['stdout' => $pipe[1], 'stderr' => $pipe[2]];
        }

        // Read output from all workers in a non-blocking loop
        $finished = array_fill(0, $workers, false);
        while (in_array(false, $finished, true)) {
            foreach ($processes as $i => $proc) {
                if ($finished[$i]) {
                    continue;
                }

                $line = fgets($pipes[$i]['stdout']);
                if ($line !== false && $line !== '') {
                    $this->line("[W{$i}] " . rtrim($line));
                }

                $status = proc_get_status($proc);
                if (!$status['running']) {
                    // Drain any remaining output
                    while (($line = fgets($pipes[$i]['stdout'])) !== false) {
                        if ($line !== '') {
                            $this->line("[W{$i}] " . rtrim($line));
                        }
                    }
                    fclose($pipes[$i]['stdout']);
                    fclose($pipes[$i]['stderr']);
                    proc_close($proc);
                    $finished[$i] = true;
                }
            }

            if (in_array(false, $finished, true)) {
                usleep(50000); // 50ms poll interval
            }
        }

        $this->newLine();
        $this->info('All workers finished.');
        return self::SUCCESS;
    }

    // ─── Worker implementation ─────────────────────────────────────────────────

    private function runWorker(int $workerId, int $totalWorkers, int $chunk, int $limit, bool $force, TDSynnexService $service): int
    {
        $prefix = $totalWorkers > 1 ? "[Worker {$workerId}/{$totalWorkers}] " : '';

        $allIds = $this->fetchProductIds($limit, $force);

        // Each worker takes every Nth product (stable, no coordination needed)
        $myIds = [];
        foreach ($allIds as $offset => $id) {
            if ($offset % $totalWorkers === $workerId) {
                $myIds[] = $id;
            }
        }

        $myTotal = count($myIds);
        if ($myTotal === 0) {
            $this->line("{$prefix}No products assigned to this worker.");
            return self::SUCCESS;
        }

        $this->line("{$prefix}Processing {$myTotal} products...");

        $processed  = 0;
        $updated    = 0;
        $skipped    = 0;
        $failed     = 0;
        $startTime  = microtime(true);

        foreach (array_chunk($myIds, $chunk) as $chunkIds) {
            $products = Product::query()
                ->whereIn('id', $chunkIds)
                ->get(['id', 'product_name', 'tdsynnex_sku_no', 'tdsynnex_product_id', 'mfg_part_no', 'images', 'specifications', 'vendor_id']);

            foreach ($products as $product) {
                $processed++;
                $existingImages = is_array($product->images) ? $product->images : [];

                if (!$force && !empty($existingImages)) {
                    $skipped++;
                    continue;
                }

                $sku  = trim((string) ($product->tdsynnex_sku_no ?? $product->tdsynnex_product_id ?? $product->id));
                $name = mb_substr(trim((string) ($product->product_name ?? '')), 0, 50);
                $elapsed = round(microtime(true) - $startTime, 1);

                try {
                    $result    = $service->syncPriceAvailabilityImageForProductId($product->id);
                    $didUpdate = (int) ($result['updated'] ?? 0) > 0;
                    $reason    = (string) ($result['reason'] ?? 'unknown');
                    $source    = (string) ($result['image_source'] ?? '');

                    if ($didUpdate) {
                        $updated++;
                        $this->line(sprintf(
                            '%s[%d/%d] SAVED  SKU:%-12s  src:%-15s  %s  (%.1fs)',
                            $prefix, $processed, $myTotal, $sku, $source, $name, $elapsed
                        ));
                    } else {
                        $failed++;
                        $this->line(sprintf(
                            '%s[%d/%d] miss   SKU:%-12s  reason:%-18s  %s  (%.1fs)',
                            $prefix, $processed, $myTotal, $sku, $reason, $name, $elapsed
                        ));
                    }
                } catch (\Throwable $e) {
                    $failed++;
                    Log::warning('fill-missing-images: exception', [
                        'product_id' => $product->id,
                        'sku'        => $sku,
                        'error'      => $e->getMessage(),
                    ]);
                    $this->line(sprintf(
                        '%s[%d/%d] ERROR  SKU:%-12s  %s  (%.1fs)',
                        $prefix, $processed, $myTotal, $sku, mb_substr($e->getMessage(), 0, 60), $elapsed
                    ));
                }
            }
        }

        $elapsed = round(microtime(true) - $startTime, 1);
        $this->line("{$prefix}Done — processed:{$processed}  saved:{$updated}  no_match:{$failed}  skipped:{$skipped}  time:{$elapsed}s");

        return self::SUCCESS;
    }

    // ─── Query ────────────────────────────────────────────────────────────────

    /**
     * Return the IDs of storefront-visible products that need images.
     * Applies the same filters as the product page: TD SYNNEX, hardware, available,
     * not discontinued, non-zero price, no shipping/support-only junk.
     */
    private function fetchProductIds(int $limit, bool $force): array
    {
        $query = Product::query()
            ->where('vendor_id', 'TD SYNNEX')
            ->where('is_available', 1)
            ->where('is_hardware', 1)
            ->where(function ($q) {
                $q->where('is_discontinued', 0)->orWhereNull('is_discontinued');
            })
            ->whereRaw("COALESCE(base_price, retail_price, 0) > 0")
            // Exclude $0.05 warranty/support lines (same as products page catalog_clean)
            ->whereRaw("NOT (COALESCE(base_price, retail_price, 0) <= 0.05 AND (
                LOWER(COALESCE(product_name,'')) LIKE '%support%' OR
                LOWER(COALESCE(product_name,'')) LIKE '%warranty%' OR
                LOWER(COALESCE(product_name,'')) LIKE '%consulting%' OR
                LOWER(COALESCE(product_name,'')) LIKE '%annual fee%'
            ))")
            ->whereRaw("LOWER(COALESCE(mfg_part_no,'')) <> 'shipping'")
            ->whereRaw("LOWER(COALESCE(product_name,'')) NOT LIKE '%shipping%'")
            ->orderBy('id');

        if (!$force) {
            $query->where(function ($q) {
                $q->whereNull('images')->orWhere('images', '[]');
            });
        }

        $q = $query->select('id');

        if ($limit > 0) {
            $q->limit($limit);
        }

        return $q->pluck('id')->all();
    }
}
