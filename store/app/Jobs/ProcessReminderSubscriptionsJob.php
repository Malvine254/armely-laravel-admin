<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\ReminderSubscription;
use App\Models\UserCartSnapshot;
use App\Models\UserProductView;
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

class ProcessReminderSubscriptionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 900;
    public int $tries = 1;

    public function middleware(): array
    {
        return [(new WithoutOverlapping('process-reminder-subscriptions'))->expireAfter(1200)];
    }

    public function handle(AzureGraphMailService $mailer, UserEmailPreferenceService $preferences): void
    {
        $now = now();

        $metrics = [
            'abandoned_scanned' => 0,
            'abandoned_sent' => 0,
            'viewed_scanned' => 0,
            'viewed_sent' => 0,
        ];

        $this->processAbandonedCartReminders($mailer, $preferences, $now, $metrics);
        $this->processViewedProductReminders($mailer, $preferences, $now, $metrics);

        Log::info('ProcessReminderSubscriptionsJob complete', $metrics);
    }

    private function processAbandonedCartReminders(
        AzureGraphMailService $mailer,
        UserEmailPreferenceService $preferences,
        Carbon $now,
        array &$metrics
    ): void
    {
        ReminderSubscription::query()
            ->where('is_active', true)
            ->where('channel', 'email')
            ->where('trigger_type', 'abandoned_cart')
            ->whereNotNull('user_id')
            ->with(['user:id,name,email,status'])
            ->orderBy('id')
            ->chunkById(200, function ($subscriptions) use ($mailer, $preferences, $now, &$metrics) {
                foreach ($subscriptions as $subscription) {
                    $metrics['abandoned_scanned']++;

                    $user = $subscription->user;
                    if (!$user || strtolower((string) ($user->status ?? 'active')) !== 'active' || trim((string) ($user->email ?? '')) === '') {
                        continue;
                    }

                    if (!$preferences->shouldSendReminder($user, 'abandoned_cart', $now)) {
                        continue;
                    }

                    if (!$this->cooldownElapsed($subscription->last_notified_at, (int) ($subscription->cooldown_minutes ?? 0), $now)) {
                        continue;
                    }

                    $snapshot = UserCartSnapshot::query()
                        ->where(function ($query) use ($subscription) {
                            $query->where('identity_key', (string) $subscription->identity_key);
                            if ($subscription->user_id) {
                                $query->orWhere('user_id', (int) $subscription->user_id);
                            }
                        })
                        ->orderByDesc('last_synced_at')
                        ->first();

                    if (!$snapshot || (int) ($snapshot->item_count ?? 0) <= 0) {
                        continue;
                    }

                    $lastSyncedAt = $snapshot->last_synced_at instanceof Carbon
                        ? $snapshot->last_synced_at
                        : Carbon::parse((string) ($snapshot->last_synced_at ?? $now->toISOString()));

                    $delayMinutes = max(30, (int) ($subscription->delay_minutes ?? 120));
                    if ($lastSyncedAt->copy()->addMinutes($delayMinutes)->greaterThan($now)) {
                        continue;
                    }

                    $items = $this->buildCartReminderItems((array) ($snapshot->items ?? []));
                    if (count($items) === 0) {
                        continue;
                    }

                    $sent = $mailer->sendAbandonedCartReminderEmail($user, $items, $lastSyncedAt);
                    if (!$sent) {
                        continue;
                    }

                    $subscription->update(['last_notified_at' => $now]);
                    $metrics['abandoned_sent']++;
                }
            });
    }

    private function processViewedProductReminders(
        AzureGraphMailService $mailer,
        UserEmailPreferenceService $preferences,
        Carbon $now,
        array &$metrics
    ): void
    {
        ReminderSubscription::query()
            ->where('is_active', true)
            ->where('channel', 'email')
            ->where('trigger_type', 'viewed_product')
            ->whereNotNull('user_id')
            ->whereNotNull('product_id')
            ->with(['user:id,name,email,status'])
            ->orderBy('id')
            ->chunkById(200, function ($subscriptions) use ($mailer, $preferences, $now, &$metrics) {
                foreach ($subscriptions as $subscription) {
                    $metrics['viewed_scanned']++;

                    $user = $subscription->user;
                    if (!$user || strtolower((string) ($user->status ?? 'active')) !== 'active' || trim((string) ($user->email ?? '')) === '') {
                        continue;
                    }

                    if (!$preferences->shouldSendReminder($user, 'viewed_product', $now)) {
                        continue;
                    }

                    if (!$this->cooldownElapsed($subscription->last_notified_at, (int) ($subscription->cooldown_minutes ?? 0), $now)) {
                        continue;
                    }

                    $latestView = UserProductView::query()
                        ->where('product_id', (int) $subscription->product_id)
                        ->where(function ($query) use ($subscription) {
                            $query->where('identity_key', (string) $subscription->identity_key);
                            if ($subscription->user_id) {
                                $query->orWhere('user_id', (int) $subscription->user_id);
                            }
                        })
                        ->orderByDesc('viewed_at')
                        ->first();

                    if (!$latestView) {
                        continue;
                    }

                    $viewedAt = $latestView->viewed_at instanceof Carbon
                        ? $latestView->viewed_at
                        : Carbon::parse((string) ($latestView->viewed_at ?? $now->toISOString()));

                    $delayMinutes = max(30, (int) ($subscription->delay_minutes ?? 1440));
                    if ($viewedAt->copy()->addMinutes($delayMinutes)->greaterThan($now)) {
                        continue;
                    }

                    $product = Product::query()->find((int) $subscription->product_id);
                    if (!$product) {
                        continue;
                    }

                    // Skip viewed reminders when the product is already in the
                    // user's most recent cart snapshot.
                    $cartSnapshot = UserCartSnapshot::query()
                        ->where(function ($query) use ($subscription) {
                            $query->where('identity_key', (string) $subscription->identity_key);
                            if ($subscription->user_id) {
                                $query->orWhere('user_id', (int) $subscription->user_id);
                            }
                        })
                        ->orderByDesc('last_synced_at')
                        ->first();

                    if ($cartSnapshot && $this->cartContainsProduct((array) ($cartSnapshot->items ?? []), (int) $subscription->product_id)) {
                        continue;
                    }

                    $price = OfferPricing::sellPrice($product);
                    $sent = $mailer->sendViewedProductReminderEmail($user, $product, $viewedAt, $price);
                    if (!$sent) {
                        continue;
                    }

                    $subscription->update(['last_notified_at' => $now]);
                    $metrics['viewed_sent']++;
                }
            });
    }

    private function buildCartReminderItems(array $snapshotItems): array
    {
        $normalized = collect($snapshotItems)
            ->map(function ($item): ?array {
                $line = is_array($item) ? $item : [];
                $productId = isset($line['productId']) ? (int) $line['productId'] : 0;
                $qty = isset($line['quantity']) ? max(1, (int) $line['quantity']) : 1;

                if ($productId <= 0) {
                    return null;
                }

                return ['product_id' => $productId, 'quantity' => $qty];
            })
            ->filter()
            ->values()
            ->all();

        if (count($normalized) === 0) {
            return [];
        }

        $products = Product::query()
            ->whereIn('id', array_values(array_unique(array_map(fn ($item) => $item['product_id'], $normalized))))
            ->get()
            ->keyBy('id');

        $result = [];
        foreach ($normalized as $line) {
            $product = $products->get($line['product_id']);
            if (!$product) {
                continue;
            }

            $unitPrice = OfferPricing::sellPrice($product);
            $quantity = (int) $line['quantity'];

            $result[] = [
                'product_id' => (int) $product->id,
                'product_name' => (string) ($product->product_name ?? 'Product'),
                'mfg_part_no' => (string) ($product->mfg_part_no ?? ''),
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'line_total' => $unitPrice > 0 ? round($unitPrice * $quantity, 2) : 0.0,
            ];
        }

        return $result;
    }

    private function cartContainsProduct(array $items, int $productId): bool
    {
        foreach ($items as $item) {
            $line = is_array($item) ? $item : [];
            if ((int) ($line['productId'] ?? 0) === $productId) {
                return true;
            }
        }

        return false;
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
