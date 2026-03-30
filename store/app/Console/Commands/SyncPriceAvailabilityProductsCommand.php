<?php

namespace App\Console\Commands;

use App\Services\TDSynnexService;
use Illuminate\Console\Command;

class SyncPriceAvailabilityProductsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tdsynnex:sync-priceavailability-products {--force : Ignore cached catalog and refetch from source}';

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

            $this->info('Syncing PriceAvailability catalog into products table...');
            $result = $service->syncPriceAvailabilityCatalogToDatabase((bool) $this->option('force'));

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
