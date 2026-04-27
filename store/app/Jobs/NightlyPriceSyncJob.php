<?php

namespace App\Jobs;

use App\Services\AzureGraphMailService;
use App\Services\TDSynnexService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NightlyPriceSyncJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 3600;
    public int $tries   = 1;

    public function middleware(): array
    {
        return [(new WithoutOverlapping('nightly-price-sync'))->expireAfter(3700)];
    }

    public function handle(TDSynnexService $service, AzureGraphMailService $mailer): void
    {
        if (!$service->usesPriceAvailabilityAsProductSource()) {
            return;
        }

        try {
            $mailer->sendSyncStatusEmail('Nightly Price Sync', 'started', [
                'Scheduled At' => now()->format('Y-m-d H:i:s T'),
            ]);

            $start   = microtime(true);
            $result  = $service->applyNightlyPriceSync();
            $elapsed = round(microtime(true) - $start, 1);

            Log::info('NightlyPriceSyncJob complete', [
                'compared' => $result['compared'],
                'changed'  => $result['changed'],
            ]);

            $mailer->sendSyncStatusEmail('Nightly Price Sync', 'completed', [
                'Products Compared' => $result['compared'],
                'Products Updated'  => $result['changed'],
                'Duration (s)'      => $elapsed,
            ]);
        } catch (\Throwable $e) {
            Log::error('NightlyPriceSyncJob failed', ['error' => $e->getMessage()]);

            $mailer->sendSyncStatusEmail('Nightly Price Sync', 'failed', [
                'Error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
