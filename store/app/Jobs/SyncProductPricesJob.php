<?php

namespace App\Jobs;

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

            $result = $this->tdsynnexService->refreshLivePricesInDatabase();

            Log::info('Product price sync completed', [
                'result' => $result,
            ]);

        } catch (\Exception $e) {
            Log::error('Product price sync failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
