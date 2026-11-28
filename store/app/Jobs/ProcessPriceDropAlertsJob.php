<?php

namespace App\Jobs;

use App\Models\AppSetting;
use App\Models\PriceAlertSubscription;
use App\Services\AzureGraphMailService;
use App\Services\UserEmailPreferenceService;
use App\Support\CustomerPurchaseHistory;
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

    /** Watches created only by viewing a product lapse after this long without another view. */
    private const VIEW_WATCH_MAX_AGE_DAYS = 45;

    public function middleware(): array
    {
        return [(new WithoutOverlapping('process-price-drop-alerts'))->expireAfter(1200)];
    }

    public function handle(AzureGraphMailService $mailer, UserEmailPreferenceService $preferences): void
    {
        $now = now();
        $metrics = [
            'scanned' => 0,
            'sent' => 0,
            'delivery_failed' => 0,
            'skipped_no_photo' => 0,
            'skipped_already_bought' => 0,
            'expired' => 0,
            'errors' => 0,
        ];

        PriceAlertSubscription::query()
            ->where('is_active', true)
            ->whereNotNull('user_id')
            ->with([
                'product:id,tdsynnex_product_id,tdsynnex_sku_no,product_name,mfg_part_no,images,base_price,sale_price,is_on_sale,offer_source,is_available,is_discontinued',
                'user:id,name,email,status',
            ])
            ->orderBy('id')
            ->chunkById(200, function ($subscriptions) use (&$metrics, $mailer, $preferences, $now) {
                foreach ($subscriptions as $subscription) {
                    $metrics['scanned']++;

                    try {
                        $this->processSubscription($subscription, $mailer, $preferences, $now, $metrics);
                    } catch (\Throwable $error) {
                        // One bad row must not stop alerts for everyone else.
                        $metrics['errors']++;
                        Log::warning('Price drop alert skipped after an error', [
                            'subscription_id' => (int) $subscription->id,
                            'error' => $error->getMessage(),
                        ]);
                    }
                }
            });

        AppSetting::setValue('lifecycle.price_drop.last_run_at', $now->toISOString());
        AppSetting::setValue('lifecycle.price_drop.last_metrics', array_merge($metrics, [
            'processed_at' => $now->toISOString(),
        ]));

        Log::info('ProcessPriceDropAlertsJob complete', $metrics);
    }

    private function processSubscription(
        PriceAlertSubscription $subscription,
        AzureGraphMailService $mailer,
        UserEmailPreferenceService $preferences,
        Carbon $now,
        array &$metrics
    ): void {
        $product = $subscription->product;
        $user = $subscription->user;

        if (!$product || !$user || strtolower((string) ($user->status ?? 'active')) !== 'active'
            || trim((string) ($user->email ?? '')) === '') {
            return;
        }

        // A watch that came only from viewing the product lapses if the
        // customer has not looked at it again for a while.
        if ((string) $subscription->source === 'view'
            && $subscription->updated_at
            && $subscription->updated_at->lessThan($now->copy()->subDays(self::VIEW_WATCH_MAX_AGE_DAYS))) {
            $metrics['expired']++;
            $subscription->update(['is_active' => false]);
            return;
        }

        if (!$preferences->shouldSendPriceAlert($user, $now)) {
            return;
        }

        if (!(bool) ($product->is_available ?? true) || (bool) ($product->is_discontinued ?? false)) {
            return;
        }

        $baseline = (float) ($subscription->baseline_price ?? 0);
        $current = OfferPricing::sellPrice($product);

        if ($baseline <= 0 || $current <= 0 || $current >= ($baseline - 0.01)) {
            return;
        }

        if (!$this->cooldownElapsed($subscription->last_notified_at, (int) ($subscription->cooldown_minutes ?? 0), $now)) {
            return;
        }

        $dropAmount = max(0, round($baseline - $current, 2));
        $dropPercent = round(($dropAmount / $baseline) * 100, 2);

        if (!$this->passesThresholds(
            $dropAmount,
            $dropPercent,
            (float) ($subscription->min_drop_amount ?? 0),
            (float) ($subscription->min_drop_percent ?? 0)
        )) {
            return;
        }

        // Price alerts are for items the customer has not bought yet.
        $since = $subscription->created_at instanceof Carbon ? $subscription->created_at : $now->copy()->subDays(90);
        if (CustomerPurchaseHistory::hasQuotedOrOrderedSince((int) $user->id, [(int) $product->id], $since)) {
            $metrics['skipped_already_bought']++;
            $subscription->update(['is_active' => false]);
            return;
        }

        if (!$mailer->canFeatureProductInMarketing($product)) {
            $metrics['skipped_no_photo']++;
            return;
        }

        // One alert per subscription per price level, not per hour.
        $idempotencyKey = 'price_drop:' . (int) $subscription->id . ':' . number_format($current, 2, '.', '');
        if ($preferences->wasIdempotencyKeySent($idempotencyKey)) {
            return;
        }

        if (!$preferences->canSendLifecycleMarketing($user, 'price_drop_alert', 1, 2, 12, $now)) {
            return;
        }

        if (!$mailer->sendPriceDropAlertEmail($user, $product, $baseline, $current, $dropAmount, $dropPercent)) {
            $metrics['delivery_failed']++;
            return;
        }

        $subscription->update([
            'last_notified_at' => $now,
            'baseline_price' => $current,
        ]);

        $context = [
            'subscription_id' => (int) $subscription->id,
            'product_id' => (int) $product->id,
        ];
        $preferences->markIdempotencyKeySent($user, $idempotencyKey, $context);
        $preferences->markMarketingSent($user, 'price_drop_alert', $context);
        $metrics['sent']++;
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
