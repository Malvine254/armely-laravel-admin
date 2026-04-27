<?php

namespace App\Console\Commands;

use App\Services\TDSynnexService;
use Illuminate\Console\Command;

class RefreshLivePricesCommand extends Command
{
    protected $signature = 'tdsynnex:refresh-live-prices';

    protected $description = 'Poll TD Synnex every 2 hours and write price/qty/availability into live_* shadow columns. Does NOT change displayed prices.';

    public function handle(TDSynnexService $service): int
    {
        if (!$service->usesPriceAvailabilityAsProductSource()) {
            $this->warn('TDSYNNEX_PRODUCTS_SOURCE is not priceavailability. Skipped.');
            return self::SUCCESS;
        }

        $this->info('Refreshing live prices from TD Synnex...');
        $start  = microtime(true);
        $result = $service->refreshLivePricesInDatabase();
        $elapsed = round(microtime(true) - $start, 1);

        $this->info('Live price refresh complete.');
        $this->line("Products checked: {$result['checked']}");
        $this->line("Time elapsed:     {$elapsed}s");
        $this->line('Note: main prices/availability unchanged — apply changes at midnight with tdsynnex:nightly-price-sync');

        return self::SUCCESS;
    }
}
