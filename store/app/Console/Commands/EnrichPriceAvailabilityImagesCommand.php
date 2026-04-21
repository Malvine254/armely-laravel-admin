<?php

namespace App\Console\Commands;

use App\Jobs\EnrichPriceAvailabilityProductImageJob;
use App\Models\Product;
use App\Services\TDSynnexService;
use Illuminate\Console\Command;

class EnrichPriceAvailabilityImagesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tdsynnex:enrich-priceavailability-images {--chunk=1 : Number of products per batch} {--limit=0 : Max products to process (0 = all)} {--sync : Run inline and block until complete} {--descriptions : Backfill Icecat descriptions for products with missing or name-only descriptions} {--force-descriptions : Overwrite existing descriptions with fresh pulled descriptions} {--vendor=TD SYNNEX : Product vendor_id filter} {--search= : Product search filter (title, SKU, MPN, description, manufacturer)} {--manufacturer= : Manufacturer filter (from specifications.manufacturer)} {--manufacturer-id= : Manufacturer identifier filter (alias of manufacturer)} {--sku= : SKU/MPN filter, accepts CSV for multiple values} {--force-web-refresh : Include rows that already have images and prioritize web/title lookup}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Icecat images (and optionally descriptions) for PriceAvailability products from Icecat and persist to DB';

    public function handle(TDSynnexService $service): int
    {
        try {
            if (!$service->usesPriceAvailabilityAsProductSource()) {
                $this->warn('TDSYNNEX_PRODUCTS_SOURCE is not set to priceavailability. Command skipped.');
                return self::SUCCESS;
            }

            $chunk = max(1, (int) $this->option('chunk'));
            $limit = max(0, (int) $this->option('limit'));
            $descriptionMode = (bool) $this->option('descriptions');
            $vendor = trim((string) $this->option('vendor'));
            $search = trim((string) $this->option('search'));
            $manufacturer = trim((string) $this->option('manufacturer'));
            $manufacturerId = trim((string) $this->option('manufacturer-id'));
            $sku = trim((string) $this->option('sku'));
            $forceWebRefresh = (bool) $this->option('force-web-refresh');
            $forceDescriptions = (bool) $this->option('force-descriptions');

            $filters = [
                'vendor' => $vendor,
                'search' => $search,
                'manufacturer' => $manufacturer,
                'manufacturer_id' => $manufacturerId,
                'sku' => $sku,
                'include_with_images' => $forceWebRefresh,
                'prefer_web' => $forceWebRefresh,
            ];

            if (!$this->option('sync')) {
                if ($descriptionMode) {
                    $this->warn('Description backfill currently supports sync mode only. Use --sync --descriptions.');
                    return self::FAILURE;
                }

                $query = $service->priceAvailabilityImageSyncQuery($filters)
                    ->orderBy('id')
                    ->select('id');

                if ($limit > 0) {
                    $query->limit($limit);
                }

                $productIds = $query->pluck('id');
                foreach ($productIds as $productId) {
                    EnrichPriceAvailabilityProductImageJob::dispatch((int) $productId);
                }

                $this->info('PriceAvailability image enrichment queued. Processing will continue in the background.');
                $this->line('Products queued: ' . $productIds->count());
                $this->line('Queue connection: database');
                $this->line('Queue name: products-sync');
                $this->line('Run multiple workers for parallelism, for example: php artisan queue:work database --queue=products-sync --sleep=1 --timeout=120');

                return self::SUCCESS;
            }

            if ($descriptionMode) {
                if ($forceDescriptions) {
                    $this->info('Force-refreshing descriptions for all TD SYNNEX products...');
                } else {
                    $this->info('Backfilling Icecat descriptions for products missing descriptions...');
                }
                $result = $service->syncPriceAvailabilityDescriptionsFromDatabase(
                    $chunk,
                    $limit,
                    function (int $processed, int $updated, Product $product, bool $didUpdate) {
                        $sku = trim((string) ($product->tdsynnex_sku_no ?? $product->tdsynnex_product_id ?? ''));
                        $name = mb_substr(trim((string) ($product->product_name ?? $product->name ?? '—')), 0, 70);
                        $status = $didUpdate ? '<fg=green>UPDATED</>' : '<fg=yellow>SKIPPED</>';
                        $this->line(sprintf('[%d] %s SKU:%s %s', $processed, $status, $sku, $name));
                    },
                    $forceDescriptions
                );

                if ((int) ($result['processed'] ?? 0) === 0) {
                    $this->warn('No database products found that need description enrichment.');
                    return self::SUCCESS;
                }

                $withDesc = Product::query()
                    ->where('vendor_id', 'TD SYNNEX')
                    ->whereNotNull('description')
                    ->where('description', '!=', '')
                    ->count();

                $this->info('Description enrichment complete.');
                $this->line('Products processed: ' . (int) ($result['processed'] ?? 0));
                $this->line('Products updated:   ' . (int) ($result['updated'] ?? 0));
                $this->line('Products with descriptions: ' . $withDesc);

                return self::SUCCESS;
            }

            // Count total products to process for progress display
            $totalQuery = $service->priceAvailabilityImageSyncQuery($filters);
            if ($limit > 0) {
                $totalToProcess = min($limit, $totalQuery->count());
            } else {
                $totalToProcess = $totalQuery->count();
            }

            if ($totalToProcess === 0) {
                $this->warn('No database products found that need image enrichment.');
                return self::SUCCESS;
            }

            $this->info("Enriching images for {$totalToProcess} products...");
            if ($search !== '' || $manufacturer !== '' || $manufacturerId !== '' || $sku !== '' || $vendor !== '') {
                $this->line(
                    'Filters: vendor=' . ($vendor !== '' ? $vendor : 'TD SYNNEX')
                    . ', search=' . ($search !== '' ? $search : 'none')
                    . ', manufacturer=' . ($manufacturer !== '' ? $manufacturer : 'none')
                    . ', manufacturer-id=' . ($manufacturerId !== '' ? $manufacturerId : 'none')
                    . ', sku=' . ($sku !== '' ? $sku : 'none')
                    . ', force-web-refresh=' . ($forceWebRefresh ? 'yes' : 'no')
                );
            }
            $this->newLine();

            $startTime = microtime(true);
            $command = $this;

            $result = $service->syncPriceAvailabilityImagesFromDatabase($chunk, $limit, function (
                int $processed,
                int $updated,
                $product,
                bool $didUpdate,
                array $images
            ) use ($command, $totalToProcess, $startTime) {
                $elapsed = microtime(true) - $startTime;
                $rate = $processed > 0 ? round($elapsed / $processed, 1) : 0;
                $pct = $totalToProcess > 0 ? round(($processed / $totalToProcess) * 100, 1) : 0;

                $name = mb_substr(trim((string) ($product->name ?? $product->description ?? '—')), 0, 50);
                $sku = trim((string) ($product->tdsynnex_sku_no ?? $product->tdsynnex_product_id ?? ''));
                $status = $didUpdate ? '<fg=green>✓ image saved</>' : '<fg=yellow>✗ no image</>';
                $imageCount = count($images);

                $command->line(
                    sprintf(
                        '  [%s%%] %d/%d  %s  SKU:%s  %s (%d img)  %.1fs/product',
                        str_pad((string) $pct, 5, ' ', STR_PAD_LEFT),
                        $processed,
                        $totalToProcess,
                        $status,
                        $sku,
                        $name,
                        $imageCount,
                        $rate
                    )
                );
            }, $filters);

            $elapsed = round(microtime(true) - $startTime, 1);

            $withImages = Product::query()
                ->where('vendor_id', 'TD SYNNEX')
                ->whereNotNull('images')
                ->where('images', '!=', '[]')
                ->count();

            $this->newLine();
            $this->info('Image enrichment complete.');
            $this->line("Products processed:          " . (int) ($result['processed'] ?? 0));
            $this->line("Products updated:            " . (int) ($result['updated'] ?? 0));
            $this->line("Total products with images:  " . $withImages);
            $this->line("Time elapsed:                {$elapsed}s");

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Image enrichment failed: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
