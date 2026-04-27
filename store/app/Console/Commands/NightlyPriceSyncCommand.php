<?php

namespace App\Console\Commands;

use App\Services\TDSynnexService;
use Illuminate\Console\Command;

class NightlyPriceSyncCommand extends Command
{
    protected $signature = 'tdsynnex:nightly-price-sync {--dry-run : Show what would change without writing to the database}';

    protected $description = 'Midnight job: compare live_* shadow columns against displayed prices and apply any changes to base_price, retail_price, quantity, is_available.';

    public function handle(TDSynnexService $service): int
    {
        if (!$service->usesPriceAvailabilityAsProductSource()) {
            $this->warn('TDSYNNEX_PRODUCTS_SOURCE is not priceavailability. Skipped.');
            return self::SUCCESS;
        }

        $dryRun = (bool) $this->option('dry-run');

        if ($dryRun) {
            $this->warn('DRY RUN — no changes will be written.');
        }

        $this->info('Running nightly price sync...');
        $start = microtime(true);

        if ($dryRun) {
            // Show what would change without writing
            $result = $this->dryRunReport($service);
        } else {
            $result = $service->applyNightlyPriceSync();
        }

        $elapsed = round(microtime(true) - $start, 1);

        $this->info('Nightly price sync complete.');
        $this->line("Products compared: {$result['compared']}");
        $this->line("Products updated:  {$result['changed']}");
        $this->line("Time elapsed:      {$elapsed}s");

        if (!empty($result['log'])) {
            $this->newLine();
            $this->line('Changes applied:');
            foreach (array_slice($result['log'], 0, 30) as $entry) {
                $this->line("  {$entry}");
            }
            if (count($result['log']) > 30) {
                $remaining = count($result['log']) - 30;
                $this->line("  ... and {$remaining} more (see laravel.log)");
            }
        }

        return self::SUCCESS;
    }

    private function dryRunReport(TDSynnexService $service): array
    {
        $cutoff  = now()->subHours(25);
        $compared = 0;
        $wouldChange = 0;
        $log = [];

        \App\Models\Product::query()
            ->whereNotNull('live_checked_at')
            ->where('live_checked_at', '>=', $cutoff)
            ->orderBy('id')
            ->chunk(200, function ($products) use (&$compared, &$wouldChange, &$log) {
                foreach ($products as $product) {
                    $compared++;
                    $changes = [];

                    if ($product->live_price !== null && abs((float) $product->live_price - (float) $product->base_price) >= 0.01) {
                        $changes[] = "price {$product->base_price} → {$product->live_price}";
                    }
                    if ($product->live_retail_price !== null && abs((float) $product->live_retail_price - (float) $product->retail_price) >= 0.01) {
                        $changes[] = "retail {$product->retail_price} → {$product->live_retail_price}";
                    }
                    if ($product->live_quantity !== null && (int) $product->live_quantity !== (int) $product->quantity) {
                        $changes[] = "qty {$product->quantity} → {$product->live_quantity}";
                    }
                    if ($product->live_is_available !== null && (bool) $product->live_is_available !== (bool) $product->is_available) {
                        $changes[] = "available {$product->is_available} → {$product->live_is_available}";
                    }

                    if (!empty($changes)) {
                        $wouldChange++;
                        $log[] = "SKU {$product->tdsynnex_sku_no} [{$product->product_name}]: " . implode(', ', $changes);
                    }
                }
            });

        return ['compared' => $compared, 'changed' => $wouldChange, 'log' => $log];
    }
}
