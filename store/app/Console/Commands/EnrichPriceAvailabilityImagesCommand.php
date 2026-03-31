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
    protected $signature = 'tdsynnex:enrich-priceavailability-images {--chunk=25 : Number of products per batch} {--limit=0 : Max products to process (0 = all)} {--sync : Run inline and block until complete} {--descriptions : Backfill Icecat descriptions for products with missing or name-only descriptions}';

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

            if (!$this->option('sync')) {
                if ($descriptionMode) {
                    $this->warn('Description backfill currently supports sync mode only. Use --sync --descriptions.');
                    return self::FAILURE;
                }

                $query = Product::query()
                    ->where('vendor_id', 'TD SYNNEX')
                    ->where(function ($q) {
                        $q->whereNull('images')
                            ->orWhere('images', '[]');
                    })
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
                $this->info('Backfilling Icecat descriptions for products missing descriptions...');
                $result = $service->syncPriceAvailabilityDescriptionsFromDatabase($chunk, $limit);

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

            $this->info('Enriching images from cached database products...');
            $result = $service->syncPriceAvailabilityImagesFromDatabase($chunk, $limit);

            if ((int) ($result['processed'] ?? 0) === 0) {
                $this->warn('No database products found that need image enrichment.');
                return self::SUCCESS;
            }

            $withImages = Product::query()
                ->where('vendor_id', 'TD SYNNEX')
                ->whereNotNull('images')
                ->where('images', '!=', '[]')
                ->count();

            $this->info('Image enrichment complete.');
            $this->line('Products processed: ' . (int) ($result['processed'] ?? 0));
            $this->line('Products updated: ' . (int) ($result['updated'] ?? 0));
            $this->line('Products with saved images: ' . $withImages);

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Image enrichment failed: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
