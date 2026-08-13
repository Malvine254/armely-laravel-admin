<?php

namespace App\Jobs;

use App\Models\PriceAlertSubscription;
use App\Services\AzureGraphMailService;
use App\Services\UserEmailPreferenceService;
use App\Support\OfferPricing;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class ProcessPriceDropAlertsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 900;
    public int $tries = 1;

    public function middleware(): array
    {
        return [(new WithoutOverlapping('process-price-drop-alerts'))->expireAfter(1200)];
    }

    public function handle(AzureGraphMailService $mailer, UserEmailPreferenceService $preferences): void
    {
        $now = now();
        $scanned = 0;
        $sent = 0;

        PriceAlertSubscription::query()
            ->where('is_active', true)
            ->with([
                'product:id,product_name,mfg_part_no,base_price,sale_price,is_on_sale,offer_source',
                'user:id,name,email,status',
            ])
            ->orderBy('id')
            ->chunkById(200, function ($subscriptions) use (&$scanned, &$sent, $mailer, $preferences, $now) {
                foreach ($subscriptions as $subscription) {
                    $scanned++;

                    $product = $subscription->product;
                    $user = $subscription->user;

                    if (!$product || !$user || strtolower((string) ($user->status ?? 'active')) !== 'active') {
                        continue;
                    }

                    if (!$preferences->shouldSendPriceAlert($user, $now)) {
                        continue;
                    }

                    $baseline = (float) ($subscription->baseline_price ?? 0);
                    $current = OfferPricing::sellPrice($product);

                    if ($baseline <= 0 || $current <= 0 || $current >= ($baseline - 0.01)) {
                        continue;
                    }

                    if (!$this->cooldownElapsed($subscription->last_notified_at, (int) ($subscription->cooldown_minutes ?? 0), $now)) {
                        continue;
                    }

                    $dropAmount = max(0, round($baseline - $current, 2));
                    $dropPercent = $baseline > 0
                        ? round(($dropAmount / $baseline) * 100, 2)
                        : 0.0;

                    if (!$this->passesThresholds(
                        $dropAmount,
                        $dropPercent,
                        (float) ($subscription->min_drop_amount ?? 0),
                        (float) ($subscription->min_drop_percent ?? 0)
                    )) {
                        continue;
                    }

                    $emailSent = $mailer->sendPriceDropAlertEmail(
                        $user,
                        $product,
                        $baseline,
                        $current,
                        $dropAmount,
                        $dropPercent
                    );

                    if (!$emailSent) {
                        continue;
                    }

                    $subscription->update([
                        'last_notified_at' => $now,
                        'baseline_price' => $current,
                    ]);

                    $sent++;
                }
            });

        Log::info('ProcessPriceDropAlertsJob complete', [
            'scanned' => $scanned,
            'sent' => $sent,
        ]);
    }

    private function passesThresholds(float $dropAmount, float $dropPercent, float $minAmount, float $minPercent): bool
    {
        $requiresAmount = $minAmount > 0;
        $requiresPercent = $minPercent > 0;

        if (!$requiresAmount && !$requiresPercent) {
            return $dropAmount >= 0.01;
        }

        return ($requiresAmount && $dropAmount >= $minAmount)
            || ($requiresPercent && $dropPercent >= $minPercent);
    }

    private function cooldownElapsed(?Carbon $lastNotifiedAt, int $cooldownMinutes, Carbon $now): bool
    {
        if (!$lastNotifiedAt) {
            return true;
        }

        $minutes = max(30, $cooldownMinutes > 0 ? $cooldownMinutes : 1440);

        return $lastNotifiedAt->copy()->addMinutes($minutes)->lessThanOrEqualTo($now);
    }
}
