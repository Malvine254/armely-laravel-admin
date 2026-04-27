<?php

namespace App\Console\Commands;

use App\Jobs\SyncPriceAvailabilityCatalogJob;
use App\Services\TDSynnexService;
use Illuminate\Console\Command;

class SyncPriceAvailabilityProductsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tdsynnex:sync-priceavailability-products {--force : Ignore cached catalog and refetch from source} {--sync : Run inline and block until complete}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync TD SYNNEX PriceAvailability products into local products table';

    public function handle(TDSynnexService $service): int
    {
        try {
            if (!$service->usesPriceAvailabilityAsProductSource()) {
                $this->warn('TDSYNNEX_PRODUCTS_SOURCE is not set to priceavailability. Sync skipped.');
                return self::SUCCESS;
            }

            $force = (bool) $this->option('force');

            if (!$this->option('sync')) {
                SyncPriceAvailabilityCatalogJob::dispatch($force);

                $this->info('PriceAvailability catalog sync queued. Processing will continue in the background.');
                $this->line('Queue connection: database');
                $this->line('Queue name: products-sync');
                $this->line('Run a worker if needed: php artisan queue:work --queue=products-sync');

                return self::SUCCESS;
            }

            $this->info('Syncing PriceAvailability catalog into products table...');
            $result = $service->syncPriceAvailabilityCatalogToDatabase($force);

            $this->info('Sync complete.');
            $this->line('Source products: ' . (int) ($result['source_count'] ?? 0));
            $this->line('Rows synced: ' . (int) ($result['synced'] ?? 0));

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Sync failed: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
