<?php

namespace App\Jobs;

use App\Models\AppSetting;
use App\Models\Product;
use App\Models\ReminderSubscription;
use App\Models\SearchProfile;
use App\Models\UserCartSnapshot;
use App\Models\UserFavoriteEvent;
use App\Models\UserProductView;
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

class ProcessReminderSubscriptionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 900;
    public int $tries = 1;

    /** Views older than this are no longer a useful "still interested?" signal. */
    private const VIEWED_REMINDER_MAX_AGE_DAYS = 30;

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
            'viewed_expired' => 0,
            'favorite_scanned' => 0,
            'favorite_sent' => 0,
            'search_scanned' => 0,
            'search_sent' => 0,
            'skipped_no_photo' => 0,
            'skipped_already_bought' => 0,
            'delivery_failed' => 0,
            'errors' => 0,
        ];

        // Each campaign runs independently: an error in one (bad data, DB
        // hiccup) must not stop the others or leave last_run_at unrecorded.
        foreach ([
            'abandoned_cart' => 'processAbandonedCartReminders',
            'viewed_product' => 'processViewedProductReminders',
            'favorite_product' => 'processFavoriteProductReminders',
            'searched_products' => 'processSearchReminders',
        ] as $campaign => $method) {
            try {
                $this->{$method}($mailer, $preferences, $now, $metrics);
            } catch (\Throwable $error) {
                $metrics['errors']++;
                Log::error('Lifecycle reminder campaign failed', ['campaign' => $campaign, 'error' => $error->getMessage()]);
            }
        }

        AppSetting::setValue('lifecycle.reminders.last_run_at', $now->toISOString());
        AppSetting::setValue('lifecycle.reminders.last_metrics', array_merge($metrics, [
            'processed_at' => $now->toISOString(),
        ]));

        Log::info('ProcessReminderSubscriptionsJob complete', $metrics);
    }

    /** Runs one subscription; a failure is logged and the loop moves on. */
    private function safely(string $campaign, int $subscriptionId, array &$metrics, callable $work): void
    {
        try {
            $work();
        } catch (\Throwable $error) {
            $metrics['errors']++;
            Log::warning('Lifecycle reminder skipped after an error', [
                'campaign' => $campaign,
                'subscription_id' => $subscriptionId,
                'error' => $error->getMessage(),
            ]);
        }
    }

    private function processAbandonedCartReminders(
        AzureGraphMailService $mailer,
        UserEmailPreferenceService $preferences,
        Carbon $now,
        array &$metrics
    ): void {
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
                    $this->safely('abandoned_cart', (int) $subscription->id, $metrics, function () use ($subscription, $mailer, $preferences, $now, &$metrics) {
                        $user = $subscription->user;
                        if (!$this->isReachableUser($user)) {
                            return;
                        }

                        if (!$preferences->shouldSendReminder($user, 'abandoned_cart', $now)) {
                            return;
                        }

                        if (!$this->cooldownElapsed($subscription->last_notified_at, (int) ($subscription->cooldown_minutes ?? 0), $now)) {
                            return;
                        }

                        $snapshot = $this->latestCartSnapshot($subscription);
                        if (!$snapshot || (int) ($snapshot->item_count ?? 0) <= 0) {
                            return;
                        }

                        $lastSyncedAt = $this->asCarbon($snapshot->last_synced_at, $now);

                        $metadata = is_array($subscription->metadata) ? $subscription->metadata : [];
                        $delays = $this->sequenceDelaysForTrigger('abandoned_cart', (int) ($subscription->delay_minutes ?? 120));
                        $sequenceStage = min(max(0, (int) ($metadata['sequence_stage'] ?? 0)), count($delays) - 1);

                        if ($lastSyncedAt->copy()->addMinutes($delays[$sequenceStage])->greaterThan($now)) {
                            return;
                        }

                        $items = $this->buildCartReminderItems((array) ($snapshot->items ?? []));
                        if (count($items) === 0) {
                            return;
                        }

                        if (CustomerPurchaseHistory::hasQuotedOrOrderedSince((int) $user->id, array_column($items, 'product_id'), $lastSyncedAt)) {
                            $metrics['skipped_already_bought']++;
                            $subscription->update(['is_active' => false]);
                            return;
                        }

                        $idempotencyKey = 'abandoned_cart:' . (int) $subscription->id . ':' . $sequenceStage . ':' . $lastSyncedAt->format('YmdHi');
                        if ($preferences->wasIdempotencyKeySent($idempotencyKey)) {
                            return;
                        }

                        if (!$preferences->canSendLifecycleMarketing($user, 'abandoned_cart_reminder', 1, 2, 12, $now)) {
                            return;
                        }

                        if (!$mailer->sendAbandonedCartReminderEmail($user, $items, $lastSyncedAt)) {
                            $metrics['delivery_failed']++;
                            return;
                        }

                        $this->recordSequenceSend($subscription, $metadata, $sequenceStage, count($delays), $lastSyncedAt, $now);
                        $context = [
                            'subscription_id' => (int) $subscription->id,
                            'trigger_type' => 'abandoned_cart',
                            'sequence_stage' => $sequenceStage,
                        ];
                        $preferences->markIdempotencyKeySent($user, $idempotencyKey, $context);
                        $preferences->markMarketingSent($user, 'abandoned_cart_reminder', $context);
                        $metrics['abandoned_sent']++;
                    });
                }
            });
    }

    private function processViewedProductReminders(
        AzureGraphMailService $mailer,
        UserEmailPreferenceService $preferences,
        Carbon $now,
        array &$metrics
    ): void {
        // Collect every due viewed item per customer first, then send one
        // digest email per customer instead of one email per product.
        $due = [];

        ReminderSubscription::query()
            ->where('is_active', true)
            ->where('channel', 'email')
            ->where('trigger_type', 'viewed_product')
            ->whereNotNull('user_id')
            ->whereNotNull('product_id')
            ->with(['user:id,name,email,status'])
            ->orderBy('id')
            ->chunkById(200, function ($subscriptions) use ($mailer, $preferences, $now, &$metrics, &$due) {
                foreach ($subscriptions as $subscription) {
                    $metrics['viewed_scanned']++;
                    $this->safely('viewed_product', (int) $subscription->id, $metrics, function () use ($subscription, $mailer, $preferences, $now, &$metrics, &$due) {
                        $user = $subscription->user;
                        if (!$this->isReachableUser($user)) {
                            return;
                        }

                        if (!$preferences->shouldSendReminder($user, 'viewed_product', $now)) {
                            return;
                        }

                        if (!$this->cooldownElapsed($subscription->last_notified_at, (int) ($subscription->cooldown_minutes ?? 0), $now)) {
                            return;
                        }

                        $latestView = UserProductView::query()
                            ->whereIn('product_id', $this->productEventReferences((int) $subscription->product_id))
                            ->where(fn ($query) => $this->whereSubscriber($query, $subscription))
                            ->orderByDesc('viewed_at')
                            ->first();

                        if (!$latestView) {
                            return;
                        }

                        $viewedAt = $this->asCarbon($latestView->viewed_at, $now);

                        // A product glanced at months ago is not a reminder
                        // anyone wants. Retire it; a fresh view re-activates it.
                        if ($viewedAt->lessThan($now->copy()->subDays(self::VIEWED_REMINDER_MAX_AGE_DAYS))) {
                            $metrics['viewed_expired']++;
                            $subscription->update(['is_active' => false]);
                            return;
                        }

                        $metadata = is_array($subscription->metadata) ? $subscription->metadata : [];
                        $delays = $this->sequenceDelaysForTrigger('viewed_product', (int) ($subscription->delay_minutes ?? 1440));
                        $sequenceStage = min(max(0, (int) ($metadata['sequence_stage'] ?? 0)), count($delays) - 1);

                        if ($viewedAt->copy()->addMinutes($delays[$sequenceStage])->greaterThan($now)) {
                            return;
                        }

                        $product = Product::query()->find((int) $subscription->product_id);
                        if (!$product) {
                            return;
                        }

                        if (CustomerPurchaseHistory::hasQuotedOrOrderedSince((int) $user->id, [(int) $product->id], $viewedAt)) {
                            $metrics['skipped_already_bought']++;
                            $subscription->update(['is_active' => false]);
                            return;
                        }

                        // Already in the cart: the cart reminder covers it.
                        $cartSnapshot = $this->latestCartSnapshot($subscription);
                        if ($cartSnapshot && $this->cartContainsProduct((array) ($cartSnapshot->items ?? []), $product)) {
                            return;
                        }

                        // Promotional emails always show a product photo. Skip
                        // quietly (not a delivery failure) and retry next run.
                        if (!$mailer->canFeatureProductInMarketing($product)) {
                            $metrics['skipped_no_photo']++;
                            return;
                        }

                        $idempotencyKey = 'viewed_product:' . (int) $subscription->id . ':' . $sequenceStage . ':' . $viewedAt->format('YmdHi');
                        if ($preferences->wasIdempotencyKeySent($idempotencyKey)) {
                            return;
                        }

                        $due[(int) $user->id][] = compact('user', 'subscription', 'product', 'viewedAt', 'metadata', 'sequenceStage', 'delays', 'idempotencyKey');
                    });
                }
            });

        foreach ($due as $userId => $items) {
            $this->safely('viewed_product_digest', (int) $userId, $metrics, function () use ($items, $mailer, $preferences, $now, &$metrics) {
                $user = $items[0]['user'];
                if (!$preferences->canSendLifecycleMarketing($user, 'viewed_product_reminder', 1, 2, 12, $now)) {
                    return;
                }

                // Most recently viewed first; one email shows up to six items.
                usort($items, fn (array $a, array $b): int => $b['viewedAt'] <=> $a['viewedAt']);
                $items = array_slice($items, 0, 6);

                $sent = count($items) === 1
                    ? $mailer->sendViewedProductReminderEmail($user, $items[0]['product'], $items[0]['viewedAt'], OfferPricing::sellPrice($items[0]['product']))
                    : $mailer->sendViewedProductsDigestEmail($user, array_column($items, 'product'));

                if (!$sent) {
                    $metrics['delivery_failed']++;
                    return;
                }

                foreach ($items as $item) {
                    $this->recordSequenceSend($item['subscription'], $item['metadata'], $item['sequenceStage'], count($item['delays']), $item['viewedAt'], $now);
                    $preferences->markIdempotencyKeySent($user, $item['idempotencyKey'], [
                        'subscription_id' => (int) $item['subscription']->id,
                        'trigger_type' => 'viewed_product',
                        'product_id' => (int) $item['product']->id,
                        'sequence_stage' => $item['sequenceStage'],
                    ]);
                }
                $preferences->markMarketingSent($user, 'viewed_product_reminder', [
                    'trigger_type' => 'viewed_product',
                    'product_ids' => array_map(fn (array $item): int => (int) $item['product']->id, $items),
                ]);
                $metrics['viewed_sent']++;
            });
        }
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
                    $this->safely('favorite_product', (int) $subscription->id, $metrics, function () use ($subscription, $mailer, $preferences, $now, &$metrics) {
                        $user = $subscription->user;
                        if (!$this->isReachableUser($user)) {
                            return;
                        }

                        if (!$preferences->shouldSendReminder($user, 'favorite_product', $now)) {
                            return;
                        }

                        if (!$this->cooldownElapsed($subscription->last_notified_at, (int) ($subscription->cooldown_minutes ?? 0), $now)) {
                            return;
                        }

                        $latestFavoriteEvent = UserFavoriteEvent::query()
                            ->whereIn('product_id', $this->productEventReferences((int) $subscription->product_id))
                            ->where(fn ($query) => $this->whereSubscriber($query, $subscription))
                            ->orderByDesc('event_at')
                            ->first();

                        if (!$latestFavoriteEvent || !in_array((string) ($latestFavoriteEvent->event_type ?? ''), ['add', 'toggle'], true)) {
                            return;
                        }

                        $favoritedAt = $this->asCarbon($latestFavoriteEvent->event_at, $now);

                        $metadata = is_array($subscription->metadata) ? $subscription->metadata : [];
                        $delays = $this->sequenceDelaysForTrigger('favorite_product', (int) ($subscription->delay_minutes ?? 720));
                        $sequenceStage = min(max(0, (int) ($metadata['sequence_stage'] ?? 0)), count($delays) - 1);

                        if ($favoritedAt->copy()->addMinutes($delays[$sequenceStage])->greaterThan($now)) {
                            return;
                        }

                        $product = Product::query()->find((int) $subscription->product_id);
                        if (!$product) {
                            return;
                        }

                        if (CustomerPurchaseHistory::hasQuotedOrOrderedSince((int) $user->id, [(int) $product->id], $favoritedAt)) {
                            $metrics['skipped_already_bought']++;
                            $subscription->update(['is_active' => false]);
                            return;
                        }

                        $cartSnapshot = $this->latestCartSnapshot($subscription);
                        if ($cartSnapshot && $this->cartContainsProduct((array) ($cartSnapshot->items ?? []), $product)) {
                            return;
                        }

                        if (!$mailer->canFeatureProductInMarketing($product)) {
                            $metrics['skipped_no_photo']++;
                            return;
                        }

                        $idempotencyKey = 'favorite_product:' . (int) $subscription->id . ':' . $sequenceStage . ':' . $favoritedAt->format('YmdHi');
                        if ($preferences->wasIdempotencyKeySent($idempotencyKey)) {
                            return;
                        }

                        if (!$preferences->canSendLifecycleMarketing($user, 'favorite_product_reminder', 1, 2, 12, $now)) {
                            return;
                        }

                        if (!$mailer->sendFavoriteProductReminderEmail($user, $product, $favoritedAt, OfferPricing::sellPrice($product))) {
                            $metrics['delivery_failed']++;
                            return;
                        }

                        $this->recordSequenceSend($subscription, $metadata, $sequenceStage, count($delays), $favoritedAt, $now);
                        $context = [
                            'subscription_id' => (int) $subscription->id,
                            'trigger_type' => 'favorite_product',
                            'product_id' => (int) $product->id,
                            'sequence_stage' => $sequenceStage,
                        ];
                        $preferences->markIdempotencyKeySent($user, $idempotencyKey, $context);
                        $preferences->markMarketingSent($user, 'favorite_product_reminder', $context);
                        $metrics['favorite_sent']++;
                    });
                }
            });
    }

    private function processSearchReminders(
        AzureGraphMailService $mailer,
        UserEmailPreferenceService $preferences,
        Carbon $now,
        array &$metrics
    ): void {
        SearchProfile::whereNotNull('user_id')->with('user:id,name,email,status')
            ->chunkById(100, function ($profiles) use ($mailer, $preferences, $now, &$metrics): void {
                foreach ($profiles as $profile) {
                    $metrics['search_scanned']++;
                    $this->safely('searched_products', (int) $profile->id, $metrics, function () use ($profile, $mailer, $preferences, $now, &$metrics) {
                        $user = $profile->user;
                        if (!$this->isReachableUser($user) || !$preferences->shouldSendReminder($user, 'searched_products', $now)) {
                            return;
                        }
                        $entries = collect($profile->terms ?: [])->sortByDesc('lastSearched')->take(3);
                        foreach ($entries as $entry) {
                            $term = trim((string) ($entry['term'] ?? ''));
                            try {
                                $searchedAt = Carbon::parse($entry['lastSearched'] ?? '');
                            } catch (\InvalidArgumentException $error) {
                                Log::warning('Skipping malformed search reminder date', ['profile_id' => $profile->id]);
                                continue;
                            }
                            if (mb_strlen($term) < 2 || $searchedAt->greaterThan($now->copy()->subDay())
                                || $searchedAt->lessThan($now->copy()->subDays(7))) {
                                continue;
                            }
                            $key = 'search:' . $user->id . ':' . hash('sha256', mb_strtolower($term) . '|' . $searchedAt->toISOString());
                            if ($preferences->wasIdempotencyKeySent($key)) {
                                continue;
                            }
                            $query = Product::where('is_available', true)->where('is_discontinued', false)->where('quantity', '>', 0);
                            $words = preg_split('/\s+/u', mb_strtolower($term), -1, PREG_SPLIT_NO_EMPTY);
                            foreach ($words as $word) {
                                $query->where(function ($query) use ($word): void {
                                    $pattern = '%' . str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $word) . '%';
                                    $query->whereRaw("LOWER(COALESCE(product_name, '')) LIKE ? ESCAPE '!'", [$pattern])
                                        ->orWhereRaw("LOWER(COALESCE(mfg_part_no, '')) LIKE ? ESCAPE '!'", [$pattern]);
                                });
                            }
                            $cart = UserCartSnapshot::where('user_id', $user->id)->first();
                            $products = $query->orderByDesc('quantity')->limit(30)->get()->filter(function (Product $product) use ($user, $searchedAt, $cart, $mailer): bool {
                                return OfferPricing::sellPrice($product) > 0
                                    && $mailer->canFeatureProductInMarketing($product)
                                    && (!$cart || !$this->cartContainsProduct((array) $cart->items, $product))
                                    && !CustomerPurchaseHistory::hasQuotedOrOrderedSince((int) $user->id, [(int) $product->id], $searchedAt);
                            })->take(3)->values()->all();
                            if ($products === [] || !$preferences->canSendLifecycleMarketing($user, 'search_reminder', 1, 2, 12, $now)) {
                                continue;
                            }
                            if (!$mailer->sendSearchReminderEmail($user, $term, $products)) {
                                $metrics['delivery_failed']++;
                                break;
                            }
                            $preferences->markIdempotencyKeySent($user, $key, ['trigger_type' => 'searched_products']);
                            $preferences->markMarketingSent($user, 'search_reminder', ['profile_id' => $profile->id]);
                            $metrics['search_sent']++;
                            break;
                        }
                    });
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
                'image_url' => null,
                'product_url' => $this->publicStoreUrl() . '/products/' . rawurlencode((string) ($product->tdsynnex_sku_no ?: $product->tdsynnex_product_id ?: $product->id)),
            ];
        }

        return $result;
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

    private function isReachableUser($user): bool
    {
        return $user
            && strtolower((string) ($user->status ?? 'active')) === 'active'
            && trim((string) ($user->email ?? '')) !== '';
    }

    private function whereSubscriber($query, ReminderSubscription $subscription): void
    {
        $query->where('identity_key', (string) $subscription->identity_key);
        if ($subscription->user_id) {
            $query->orWhere('user_id', (int) $subscription->user_id);
        }
    }

    private function latestCartSnapshot(ReminderSubscription $subscription): ?UserCartSnapshot
    {
        return UserCartSnapshot::query()
            ->where(fn ($query) => $this->whereSubscriber($query, $subscription))
            ->orderByDesc('last_synced_at')
            ->first();
    }

    private function asCarbon(mixed $value, Carbon $fallback): Carbon
    {
        return $value instanceof Carbon ? $value : Carbon::parse((string) ($value ?? $fallback->toISOString()));
    }

    private function recordSequenceSend(
        ReminderSubscription $subscription,
        array $metadata,
        int $sequenceStage,
        int $stageCount,
        Carbon $anchor,
        Carbon $now
    ): void {
        $subscription->update([
            'last_notified_at' => $now,
            'metadata' => array_merge($metadata, [
                'sequence_stage' => min($sequenceStage + 1, $stageCount - 1),
                'sequence_anchor_at' => $anchor->toISOString(),
                'last_sequence_sent_at' => $now->toISOString(),
            ]),
        ]);
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

    private function productEventReferences(int $productId): array
    {
        $product = Product::find($productId);
        return array_values(array_unique(array_filter([
            $productId, $product?->tdsynnex_product_id, $product?->tdsynnex_sku_no,
        ], fn ($id) => is_numeric($id) && (int) $id > 0)));
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
