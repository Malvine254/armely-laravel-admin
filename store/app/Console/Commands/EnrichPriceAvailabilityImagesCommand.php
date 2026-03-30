<?php

namespace App\Console\Commands;

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
    protected $signature = 'tdsynnex:enrich-priceavailability-images {--chunk=25 : Number of products per batch} {--limit=0 : Max products to process (0 = all)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Icecat images for PriceAvailability products and persist them to products.images by SKU';

    public function handle(TDSynnexService $service): int
    {
        try {
            if (!$service->usesPriceAvailabilityAsProductSource()) {
                $this->warn('TDSYNNEX_PRODUCTS_SOURCE is not set to priceavailability. Command skipped.');
                return self::SUCCESS;
            }

            $chunk = max(1, (int) $this->option('chunk'));
            $limit = max(0, (int) $this->option('limit'));

            $this->info('Loading PriceAvailability catalog...');
            $catalog = $service->getPriceAvailabilityCatalog();

            if ($limit > 0) {
                $catalog = array_slice($catalog, 0, $limit);
            }

            $total = count($catalog);
            if ($total === 0) {
                $this->warn('No products found in catalog.');
                return self::SUCCESS;
            }

            $this->info("Enriching images for {$total} products in chunks of {$chunk}...");

            $processed = 0;
            foreach (array_chunk($catalog, $chunk) as $batch) {
                // Override lookup cap so this batch is fully processed.
                $service->enrichProductsWithIcecatImages($batch, count($batch));
                $processed += count($batch);
                $this->line("Processed {$processed}/{$total}");
            }

            $withImages = Product::query()
                ->where('vendor_id', 'TD SYNNEX')
                ->whereNotNull('images')
                ->where('images', '!=', '[]')
                ->count();

            $this->info('Image enrichment complete.');
            $this->line('Products with saved images: ' . $withImages);

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Image enrichment failed: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
