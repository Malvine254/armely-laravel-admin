<?php

namespace App\Jobs;

use App\Models\AppSetting;
use App\Models\Product;
use App\Models\Quote;
use App\Models\ReminderSubscription;
use App\Models\UserCartSnapshot;
use App\Models\UserFavoriteEvent;
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
            'favorite_scanned' => 0,
            'favorite_sent' => 0,
        ];

        $this->processAbandonedCartReminders($mailer, $preferences, $now, $metrics);
        $this->processViewedProductReminders($mailer, $preferences, $now, $metrics);
        $this->processFavoriteProductReminders($mailer, $preferences, $now, $metrics);

        AppSetting::setValue('lifecycle.reminders.last_run_at', $now->toISOString());
        AppSetting::setValue('lifecycle.reminders.last_metrics', array_merge($metrics, [
            'processed_at' => $now->toISOString(),
        ]));

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

                    $metadata = is_array($subscription->metadata) ? $subscription->metadata : [];
                    $sequenceStage = max(0, (int) ($metadata['sequence_stage'] ?? 0));
                    $delays = $this->sequenceDelaysForTrigger('abandoned_cart', (int) ($subscription->delay_minutes ?? 120));
                    if ($sequenceStage >= count($delays)) {
                        $sequenceStage = count($delays) - 1;
                    }

                    $delayMinutes = $delays[$sequenceStage];
                    if ($lastSyncedAt->copy()->addMinutes($delayMinutes)->greaterThan($now)) {
                        continue;
                    }

                    $items = $this->buildCartReminderItems((array) ($snapshot->items ?? []));
                    if (count($items) === 0) {
                        continue;
                    }

                    if ($this->hasRequestedQuoteSince(
                        (int) $user->id,
                        array_column($items, 'product_id'),
                        $lastSyncedAt
                    )) {
                        $subscription->update(['is_active' => false]);
                        continue;
                    }

                    $idempotencyKey = 'abandoned_cart:' . (int) $subscription->id . ':' . $sequenceStage . ':' . $lastSyncedAt->format('YmdHi');
                    if ($preferences->wasIdempotencyKeySent($idempotencyKey)) {
                        continue;
                    }

                    if (!$preferences->underDailySendCap($user, 'abandoned_cart_reminder', 2, $now)) {
                        continue;
                    }

                    $sent = $mailer->sendAbandonedCartReminderEmail($user, $items, $lastSyncedAt);
                    if (!$sent) {
                        continue;
                    }

                    $nextStage = min($sequenceStage + 1, count($delays) - 1);
                    $subscription->update([
                        'last_notified_at' => $now,
                        'metadata' => array_merge($metadata, [
                            'sequence_stage' => $nextStage,
                            'sequence_anchor_at' => $lastSyncedAt->toISOString(),
                            'last_sequence_sent_at' => $now->toISOString(),
                        ]),
                    ]);
                    $preferences->markIdempotencyKeySent($user, $idempotencyKey, [
                        'subscription_id' => (int) $subscription->id,
                        'trigger_type' => 'abandoned_cart',
                        'sequence_stage' => $sequenceStage,
                    ]);
                    $preferences->markMarketingSent($user, 'abandoned_cart_reminder', [
                        'subscription_id' => (int) $subscription->id,
                        'trigger_type' => 'abandoned_cart',
                        'sequence_stage' => $sequenceStage,
                    ]);
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

                    $metadata = is_array($subscription->metadata) ? $subscription->metadata : [];
                    $sequenceStage = max(0, (int) ($metadata['sequence_stage'] ?? 0));
                    $delays = $this->sequenceDelaysForTrigger('viewed_product', (int) ($subscription->delay_minutes ?? 1440));
                    if ($sequenceStage >= count($delays)) {
                        $sequenceStage = count($delays) - 1;
                    }

                    $delayMinutes = $delays[$sequenceStage];
                    if ($viewedAt->copy()->addMinutes($delayMinutes)->greaterThan($now)) {
                        continue;
                    }

                    $product = Product::query()->find((int) $subscription->product_id);
                    if (!$product) {
                        continue;
                    }

                    if ($this->hasRequestedQuoteSince((int) $user->id, [(int) $product->id], $viewedAt)) {
                        $subscription->update(['is_active' => false]);
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

                    if ($cartSnapshot && $this->cartContainsProduct((array) ($cartSnapshot->items ?? []), $product)) {
                        continue;
                    }

                    $price = OfferPricing::sellPrice($product);

                    $idempotencyKey = 'viewed_product:' . (int) $subscription->id . ':' . $sequenceStage . ':' . $viewedAt->format('YmdHi');
                    if ($preferences->wasIdempotencyKeySent($idempotencyKey)) {
                        continue;
                    }

                    if (!$preferences->underDailySendCap($user, 'viewed_product_reminder', 2, $now)) {
                        continue;
                    }

                    $sent = $mailer->sendViewedProductReminderEmail($user, $product, $viewedAt, $price);
                    if (!$sent) {
                        continue;
                    }

                    $nextStage = min($sequenceStage + 1, count($delays) - 1);
                    $subscription->update([
                        'last_notified_at' => $now,
                        'metadata' => array_merge($metadata, [
                            'sequence_stage' => $nextStage,
                            'sequence_anchor_at' => $viewedAt->toISOString(),
                            'last_sequence_sent_at' => $now->toISOString(),
                        ]),
                    ]);
                    $preferences->markIdempotencyKeySent($user, $idempotencyKey, [
                        'subscription_id' => (int) $subscription->id,
                        'trigger_type' => 'viewed_product',
                        'product_id' => (int) $product->id,
                        'sequence_stage' => $sequenceStage,
                    ]);
                    $preferences->markMarketingSent($user, 'viewed_product_reminder', [
                        'subscription_id' => (int) $subscription->id,
                        'trigger_type' => 'viewed_product',
                        'product_id' => (int) $product->id,
                        'sequence_stage' => $sequenceStage,
                    ]);
                    $metrics['viewed_sent']++;
                }
            });
    }

    private function processFavoriteProductReminders(
        AzureGraphMailService $mailer,
        UserEmailPreferenceService $preferences,
        Carbon $now,
        array &$metrics
    ): void {
        ReminderSubscription::query()
            ->where('is_active', true)
            ->where('channel', 'email')
            ->where('trigger_type', 'favorite_product')
            ->whereNotNull('user_id')
            ->whereNotNull('product_id')
            ->with(['user:id,name,email,status'])
            ->orderBy('id')
            ->chunkById(200, function ($subscriptions) use ($mailer, $preferences, $now, &$metrics) {
                foreach ($subscriptions as $subscription) {
                    $metrics['favorite_scanned']++;

                    $user = $subscription->user;
                    if (!$user || strtolower((string) ($user->status ?? 'active')) !== 'active' || trim((string) ($user->email ?? '')) === '') {
                        continue;
                    }

                    if (!$preferences->shouldSendReminder($user, 'favorite_product', $now)) {
                        continue;
                    }

                    if (!$this->cooldownElapsed($subscription->last_notified_at, (int) ($subscription->cooldown_minutes ?? 0), $now)) {
                        continue;
                    }

                    $latestFavoriteEvent = UserFavoriteEvent::query()
                        ->where('product_id', (int) $subscription->product_id)
                        ->where(function ($query) use ($subscription) {
                            $query->where('identity_key', (string) $subscription->identity_key);
                            if ($subscription->user_id) {
                                $query->orWhere('user_id', (int) $subscription->user_id);
                            }
                        })
                        ->orderByDesc('event_at')
                        ->first();

                    if (!$latestFavoriteEvent) {
                        continue;
                    }

                    $eventType = (string) ($latestFavoriteEvent->event_type ?? '');
                    if (!in_array($eventType, ['add', 'toggle'], true)) {
                        continue;
                    }

                    $favoritedAt = $latestFavoriteEvent->event_at instanceof Carbon
                        ? $latestFavoriteEvent->event_at
                        : Carbon::parse((string) ($latestFavoriteEvent->event_at ?? $now->toISOString()));

                    $metadata = is_array($subscription->metadata) ? $subscription->metadata : [];
                    $sequenceStage = max(0, (int) ($metadata['sequence_stage'] ?? 0));
                    $delays = $this->sequenceDelaysForTrigger('favorite_product', (int) ($subscription->delay_minutes ?? 720));
                    if ($sequenceStage >= count($delays)) {
                        $sequenceStage = count($delays) - 1;
                    }

                    $delayMinutes = $delays[$sequenceStage];
                    if ($favoritedAt->copy()->addMinutes($delayMinutes)->greaterThan($now)) {
                        continue;
                    }

                    $product = Product::query()->find((int) $subscription->product_id);
                    if (!$product) {
                        continue;
                    }

                    $cartSnapshot = UserCartSnapshot::query()
                        ->where(function ($query) use ($subscription) {
                            $query->where('identity_key', (string) $subscription->identity_key);
                            if ($subscription->user_id) {
                                $query->orWhere('user_id', (int) $subscription->user_id);
                            }
                        })
                        ->orderByDesc('last_synced_at')
                        ->first();

                    if ($cartSnapshot && $this->cartContainsProduct((array) ($cartSnapshot->items ?? []), $product)) {
                        continue;
                    }

                    $price = OfferPricing::sellPrice($product);

                    $idempotencyKey = 'favorite_product:' . (int) $subscription->id . ':' . $sequenceStage . ':' . $favoritedAt->format('YmdHi');
                    if ($preferences->wasIdempotencyKeySent($idempotencyKey)) {
                        continue;
                    }

                    if (!$preferences->underDailySendCap($user, 'favorite_product_reminder', 2, $now)) {
                        continue;
                    }

                    $sent = $mailer->sendFavoriteProductReminderEmail($user, $product, $favoritedAt, $price);
                    if (!$sent) {
                        continue;
                    }

                    $nextStage = min($sequenceStage + 1, count($delays) - 1);
                    $subscription->update([
                        'last_notified_at' => $now,
                        'metadata' => array_merge($metadata, [
                            'sequence_stage' => $nextStage,
                            'sequence_anchor_at' => $favoritedAt->toISOString(),
                            'last_sequence_sent_at' => $now->toISOString(),
                        ]),
                    ]);
                    $preferences->markIdempotencyKeySent($user, $idempotencyKey, [
                        'subscription_id' => (int) $subscription->id,
                        'trigger_type' => 'favorite_product',
                        'product_id' => (int) $product->id,
                        'sequence_stage' => $sequenceStage,
                    ]);
                    $preferences->markMarketingSent($user, 'favorite_product_reminder', [
                        'subscription_id' => (int) $subscription->id,
                        'trigger_type' => 'favorite_product',
                        'product_id' => (int) $product->id,
                        'sequence_stage' => $sequenceStage,
                    ]);
                    $metrics['favorite_sent']++;
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

        $references = array_values(array_unique(array_map(fn ($item) => $item['product_id'], $normalized)));
        $products = Product::query()
            ->where(function ($query) use ($references) {
                $query->whereIn('id', $references)
                    ->orWhereIn('tdsynnex_product_id', $references)
                    ->orWhereIn('tdsynnex_sku_no', array_map('strval', $references));
            })
            ->get();

        $productsByReference = [];
        foreach ($products as $product) {
            foreach ([$product->id, $product->tdsynnex_product_id, $product->tdsynnex_sku_no] as $reference) {
                if ($reference !== null && trim((string) $reference) !== '') {
                    $productsByReference[(string) $reference] = $product;
                }
            }
        }

        $result = [];
        foreach ($normalized as $line) {
            $product = $productsByReference[(string) $line['product_id']] ?? null;
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
                'image_url' => $this->productImageUrl($product),
                'product_url' => $this->publicStoreUrl() . '/products/' . rawurlencode((string) $product->id),
            ];
        }

        return $result;
    }

    private function productImageUrl(Product $product): ?string
    {
        $images = is_array($product->images) ? $product->images : [];
        $candidate = $images[0]['imageUrl'] ?? $images[0]['url'] ?? $images[0] ?? null;
        if (!is_string($candidate) || trim($candidate) === '') {
            return null;
        }

        $candidate = trim($candidate);
        if (preg_match('#^https?://#i', $candidate)) {
            return $candidate;
        }

        return $this->publicStoreUrl() . '/' . ltrim($candidate, '/');
    }

    private function publicStoreUrl(): string
    {
        $url = rtrim((string) config('app.frontend_url', ''), '/');
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        if (in_array($host, ['127.0.0.1', 'localhost', ''], true)) {
            return rtrim((string) env('PUBLIC_STOREFRONT_URL', 'https://armely.com/store'), '/');
        }

        return $url;
    }

    private function hasRequestedQuoteSince(int $userId, array $productIds, Carbon $since): bool
    {
        $references = Product::query()
            ->whereIn('id', array_map('intval', $productIds))
            ->get(['id', 'tdsynnex_product_id', 'tdsynnex_sku_no', 'mfg_part_no'])
            ->flatMap(fn (Product $product) => [
                (string) $product->id,
                (string) ($product->tdsynnex_product_id ?? ''),
                (string) ($product->tdsynnex_sku_no ?? ''),
                (string) ($product->mfg_part_no ?? ''),
            ])
            ->filter()
            ->unique()
            ->all();

        if ($references === []) {
            return false;
        }

        return Quote::query()
            ->where('user_id', $userId)
            ->where('created_at', '>=', $since)
            ->whereNotIn('status', ['cancelled', 'rejected'])
            ->get(['items'])
            ->contains(function (Quote $quote) use ($references): bool {
                foreach ((array) ($quote->items ?? []) as $item) {
                    $line = is_array($item) ? $item : [];
                    foreach (['product_id', 'productId', 'id', 'sku', 'partNumber', 'mfg_part_no', 'mfgPartNo'] as $key) {
                        if (isset($line[$key]) && in_array((string) $line[$key], $references, true)) {
                            return true;
                        }
                    }
                }

                return false;
            });
    }

    private function cartContainsProduct(array $items, Product $product): bool
    {
        $references = array_map('strval', array_filter([
            $product->id,
            $product->tdsynnex_product_id,
            $product->tdsynnex_sku_no,
        ], fn ($value) => $value !== null && trim((string) $value) !== ''));

        foreach ($items as $item) {
            $line = is_array($item) ? $item : [];
            if (in_array((string) ($line['productId'] ?? ''), $references, true)) {
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

    private function sequenceDelaysForTrigger(string $triggerType, int $baseDelayMinutes): array
    {
        $base = max(30, $baseDelayMinutes);

        return match ($triggerType) {
            'abandoned_cart' => [120, 1440, 4320],
            // A light two-touch browse sequence: next day, then four days
            // after the original view. Idempotency and daily caps still apply.
            'viewed_product' => [max(1440, $base), 5760],
            'favorite_product' => [max(720, $base), 2880],
            default => [$base],
        };
    }
}
