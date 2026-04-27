<?php

namespace App\Jobs;

use App\Models\Product;
use App\Services\TDSynnexService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncProductPricesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected TDSynnexService $tdsynnexService;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->tdsynnexService = new TDSynnexService();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('Starting product price sync job');

            // Get all vendors with products in the database
            $vendors = Product::distinct('vendor_id')
                ->pluck('vendor_id')
                ->toArray();

            $pricesUpdated = 0;
            $pricesSynced = 0;

            foreach ($vendors as $vendor) {
                try {
                    // Fetch products from API for this vendor (first page only to get pricing)
                    $response = $this->tdsynnexService->getProducts($vendor, 1, 100);
                    
                    if (!isset($response['data']['records'])) {
                        continue;
                    }

                    $apiProducts = $response['data']['records'];

                    // Update prices in database
                    foreach ($apiProducts as $apiProduct) {
                        $pricesSynced++;
                        
                        $rsPrice  = (float) ($apiProduct['productPrice'][0]['rsPrice'] ?? 0);
                        $msrp     = (float) ($apiProduct['productPrice'][0]['msrp']    ?? $rsPrice);

                        $updated = Product::where('vendor_id', $vendor)
                            ->where('tdsynnex_product_id', $apiProduct['productId'] ?? null)
                            ->update([
                                'base_price'      => $rsPrice,
                                'retail_price'    => $msrp,
                                'is_available'    => !($apiProduct['discontinueProduct'] ?? false),
                                'is_discontinued' => $apiProduct['discontinueProduct'] ?? false,
                            ]);

                        if ($updated) {
                            $pricesUpdated++;
                        }
                    }

                    Log::info("Synced prices for vendor {$vendor}", [
                        'total_synced' => count($apiProducts),
                        'prices_updated' => $pricesUpdated
                    ]);

                } catch (\Exception $e) {
                    Log::warning("Failed to sync prices for vendor {$vendor}: " . $e->getMessage());
                    continue;
                }
            }

            Log::info('Product price sync completed', [
                'vendors_processed' => count($vendors),
                'prices_synced' => $pricesSynced,
                'prices_updated' => $pricesUpdated
            ]);

        } catch (\Exception $e) {
            Log::error('Product price sync failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
