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

class RefreshLivePricesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 3600;
    public int $tries   = 1;

    public function middleware(): array
    {
        return [(new WithoutOverlapping('refresh-live-prices'))->expireAfter(3700)];
    }

    public function handle(TDSynnexService $service, AzureGraphMailService $mailer): void
    {
        if (!$service->usesPriceAvailabilityAsProductSource()) {
            return;
        }

        try {
            $mailer->sendSyncStatusEmail('Live Price Refresh', 'started', [
                'Scheduled At' => now()->format('Y-m-d H:i:s T'),
            ]);

            $start   = microtime(true);
            $result  = $service->refreshLivePricesInDatabase();
            $elapsed = round(microtime(true) - $start, 1);

            Log::info('RefreshLivePricesJob complete', $result);

            $mailer->sendSyncStatusEmail('Live Price Refresh', 'completed', [
                'Products Checked' => $result['checked'],
                'Duration (s)'     => $elapsed,
                'Next Step'        => 'Nightly sync at midnight will apply changes to displayed prices',
            ]);
        } catch (\Throwable $e) {
            Log::error('RefreshLivePricesJob failed', ['error' => $e->getMessage()]);

            $mailer->sendSyncStatusEmail('Live Price Refresh', 'failed', [
                'Error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
