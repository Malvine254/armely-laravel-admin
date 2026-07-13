<?php

namespace App\Jobs;

use App\Models\AppSetting;
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
            $syncScope = (string) AppSetting::getValue('price_sync.scope', 'all');
            $specificSkus = $syncScope === 'specific'
                ? $this->configuredSkuList()
                : null;
            $scopeLabel = $specificSkus === null ? 'All products' : 'Specific products';

            $mailer->sendSyncStatusEmail('Live Price Refresh', 'started', [
                'Started At' => now()->format('Y-m-d H:i:s T'),
                'Process' => 'Checking TD SYNNEX price availability updates',
                'Sync Scope' => $scopeLabel,
                'Requested Products' => $specificSkus === null ? 'All configured TD SYNNEX products' : count($specificSkus),
                'Queue' => 'products-sync',
            ]);

            $start   = microtime(true);
            $result  = $service->refreshLivePricesInDatabase($specificSkus, function (): bool {
                $state = \App\Models\AppSetting::getValue('price_sync.run_state', []);
                return !is_array($state) || ($state['status'] ?? '') !== 'cancelled';
            });
            $elapsed = round(microtime(true) - $start, 1);

            Log::info('RefreshLivePricesJob complete', $result);

            $mailer->sendSyncStatusEmail('Live Price Refresh', 'completed', [
                'Finished At' => now()->format('Y-m-d H:i:s T'),
                'Sync Scope' => $scopeLabel,
                'Requested Products' => $result['requested'] ?? ($specificSkus === null ? 'All' : count($specificSkus)),
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

    private function configuredSkuList(): array
    {
        $raw = (string) AppSetting::getValue('price_sync.skus', '');
        $tokens = preg_split('/[\s,;]+/', $raw) ?: [];

        return array_values(array_unique(array_filter(array_map('trim', $tokens), fn ($value) => $value !== '')));
    }
}
