<?php

namespace App\Jobs;

use App\Mail\PriceSyncStatusMail;
use App\Services\TDSynnexService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NightlyPriceSyncJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 3600;
    public int $tries   = 1;

    public function middleware(): array
    {
        return [(new WithoutOverlapping('nightly-price-sync'))->expireAfter(3700)];
    }

    public function handle(TDSynnexService $service): void
    {
        if (!$service->usesPriceAvailabilityAsProductSource()) {
            return;
        }

        $adminEmail = config('mail.admin_email', env('ADMIN_EMAIL', env('SUPPORT_EMAIL')));

        try {
            Mail::to($adminEmail)->cc('malvine.owuor@armely.com')->send(new PriceSyncStatusMail(
                'Nightly Price Sync',
                'started',
                ['scheduled_at' => now()->format('Y-m-d H:i:s T')],
            ));

            $start  = microtime(true);
            $result = $service->applyNightlyPriceSync();
            $elapsed = round(microtime(true) - $start, 1);

            Log::info('NightlyPriceSyncJob complete', [
                'compared' => $result['compared'],
                'changed'  => $result['changed'],
            ]);

            Mail::to($adminEmail)->cc('malvine.owuor@armely.com')->send(new PriceSyncStatusMail(
                'Nightly Price Sync',
                'completed',
                [
                    'Products Compared' => $result['compared'],
                    'Products Updated'  => $result['changed'],
                    'Duration (s)'      => $elapsed,
                    'log'               => $result['log'],
                ],
            ));
        } catch (\Throwable $e) {
            Log::error('NightlyPriceSyncJob failed', ['error' => $e->getMessage()]);

            Mail::to($adminEmail)->cc('malvine.owuor@armely.com')->send(new PriceSyncStatusMail(
                'Nightly Price Sync',
                'failed',
                ['error' => $e->getMessage()],
            ));

            throw $e;
        }
    }
}
