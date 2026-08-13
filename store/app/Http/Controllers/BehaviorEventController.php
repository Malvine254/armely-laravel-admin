<?php

namespace App\Http\Controllers;

use App\Models\PriceAlertSubscription;
use App\Models\Product;
use App\Models\ReminderSubscription;
use App\Models\UserCartEvent;
use App\Models\UserCartSnapshot;
use App\Models\UserFavoriteEvent;
use App\Models\UserProductView;
use App\Support\OfferPricing;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BehaviorEventController extends Controller
{
    private const COOKIE_NAME = 'armely_behavior_visitor';

    public function trackProductView(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'min:1'],
            'viewed_at' => ['nullable', 'date'],
        ]);

        [$identityKey, $userId, $newToken] = $this->identity($request);

        UserProductView::create([
            'identity_key' => $identityKey,
            'user_id' => $userId,
            'product_id' => (int) $validated['product_id'],
            'viewed_at' => isset($validated['viewed_at']) ? now()->parse($validated['viewed_at']) : now(),
        ]);

        $this->upsertReminderSubscription(
            $identityKey,
            $userId,
            'viewed_product',
            (int) $validated['product_id'],
            1440,
            4320,
            ['source' => 'product_view']
        );

        return $this->responseOk($newToken);
    }

    public function syncCartSnapshot(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'max:300'],
            'items.*.productId' => ['required'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:9999'],
        ]);

        [$identityKey, $userId, $newToken] = $this->identity($request);

        $normalizedItems = collect($validated['items'])
            ->map(function (array $item): array {
                return [
                    'productId' => (string) $item['productId'],
                    'quantity' => (int) $item['quantity'],
                ];
            })
            ->filter(fn (array $item): bool => trim($item['productId']) !== '')
            ->values()
            ->all();

        $totalQuantity = array_reduce($normalizedItems, fn (int $sum, array $item): int => $sum + (int) $item['quantity'], 0);

        UserCartSnapshot::updateOrCreate(
            ['identity_key' => $identityKey],
            [
                'user_id' => $userId,
                'items' => $normalizedItems,
                'item_count' => count($normalizedItems),
                'total_quantity' => $totalQuantity,
                'last_synced_at' => now(),
            ]
        );

        $this->upsertReminderSubscription(
            $identityKey,
            $userId,
            'abandoned_cart',
            null,
            120,
            1440,
            ['item_count' => count($normalizedItems), 'total_quantity' => $totalQuantity]
        );

        return $this->responseOk($newToken);
    }

    public function trackCartEvent(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'event_type' => ['required', 'string', 'in:add,remove,update,clear,replace,merge_guest'],
            'product_id' => ['nullable', 'integer', 'min:1'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:9999'],
            'metadata' => ['nullable', 'array'],
            'event_at' => ['nullable', 'date'],
        ]);

        [$identityKey, $userId, $newToken] = $this->identity($request);

        UserCartEvent::create([
            'identity_key' => $identityKey,
            'user_id' => $userId,
            'event_type' => (string) $validated['event_type'],
            'product_id' => isset($validated['product_id']) ? (int) $validated['product_id'] : null,
            'quantity' => isset($validated['quantity']) ? (int) $validated['quantity'] : null,
            'metadata' => (array) ($validated['metadata'] ?? []),
            'event_at' => isset($validated['event_at']) ? now()->parse($validated['event_at']) : now(),
        ]);

        if (in_array((string) $validated['event_type'], ['add', 'update'], true) && isset($validated['product_id'])) {
            $this->upsertPriceAlertSubscription($identityKey, $userId, (int) $validated['product_id'], 'cart');
        }

        return $this->responseOk($newToken);
    }

    public function trackFavoriteEvent(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'min:1'],
            'event_type' => ['required', 'string', 'in:add,remove,toggle'],
            'metadata' => ['nullable', 'array'],
            'event_at' => ['nullable', 'date'],
        ]);

        [$identityKey, $userId, $newToken] = $this->identity($request);

        UserFavoriteEvent::create([
            'identity_key' => $identityKey,
            'user_id' => $userId,
            'product_id' => (int) $validated['product_id'],
            'event_type' => (string) $validated['event_type'],
            'metadata' => (array) ($validated['metadata'] ?? []),
            'event_at' => isset($validated['event_at']) ? now()->parse($validated['event_at']) : now(),
        ]);

        if (in_array((string) $validated['event_type'], ['add', 'toggle'], true)) {
            $this->upsertPriceAlertSubscription($identityKey, $userId, (int) $validated['product_id'], 'favorite');
            $this->upsertReminderSubscription(
                $identityKey,
                $userId,
                'favorite_product',
                (int) $validated['product_id'],
                720,
                2880,
                ['source' => 'favorite_event']
            );
        }

        if ((string) $validated['event_type'] === 'remove') {
            $this->deactivateReminderSubscription($identityKey, $userId, 'favorite_product', (int) $validated['product_id']);
        }

        return $this->responseOk($newToken);
    }

    private function upsertPriceAlertSubscription(string $identityKey, ?int $userId, int $productReference, string $source): void
    {
        $product = $this->resolveProduct($productReference);
        if (!$product) {
            return;
        }

        $currentPrice = OfferPricing::sellPrice($product);
        if ($currentPrice <= 0) {
            return;
        }

        $subscription = PriceAlertSubscription::query()
            ->where('product_id', (int) $product->id)
            ->where(function ($query) use ($identityKey, $userId) {
                $query->where('identity_key', $identityKey);
                if ($userId) {
                    $query->orWhere('user_id', $userId);
                }
            })
            ->first();

        if (!$subscription) {
            PriceAlertSubscription::create([
                'identity_key' => $identityKey,
                'user_id' => $userId,
                'product_id' => (int) $product->id,
                'baseline_price' => $currentPrice,
                'min_drop_amount' => 0,
                'min_drop_percent' => 5,
                'cooldown_minutes' => 1440,
                'source' => $source,
                'is_active' => true,
            ]);

            return;
        }

        $subscription->fill([
            'identity_key' => $identityKey,
            'user_id' => $userId,
            'is_active' => true,
            'source' => $source,
        ]);

        if ((float) ($subscription->baseline_price ?? 0) <= 0) {
            $subscription->baseline_price = $currentPrice;
        }

        $subscription->save();
    }

    private function deactivateReminderSubscription(
        string $identityKey,
        ?int $userId,
        string $triggerType,
        int $productReference
    ): void {
        $product = $this->resolveProduct($productReference);
        if (!$product) {
            return;
        }

        ReminderSubscription::query()
            ->where('trigger_type', $triggerType)
            ->where('product_id', (int) $product->id)
            ->where(function ($query) use ($identityKey, $userId) {
                $query->where('identity_key', $identityKey);
                if ($userId) {
                    $query->orWhere('user_id', $userId);
                }
            })
            ->update(['is_active' => false]);
    }

    private function upsertReminderSubscription(
        string $identityKey,
        ?int $userId,
        string $triggerType,
        ?int $productReference,
        int $delayMinutes,
        int $cooldownMinutes,
        array $metadata = []
    ): void {
        $resolvedProductId = null;

        if ($productReference !== null) {
            $product = $this->resolveProduct($productReference);
            if (!$product) {
                return;
            }
            $resolvedProductId = (int) $product->id;
        }

        $subscription = ReminderSubscription::query()
            ->where('trigger_type', $triggerType)
            ->where(function ($query) use ($identityKey, $userId) {
                $query->where('identity_key', $identityKey);
                if ($userId) {
                    $query->orWhere('user_id', $userId);
                }
            })
            ->where(function ($query) use ($resolvedProductId) {
                if ($resolvedProductId === null) {
                    $query->whereNull('product_id');
                    return;
                }

                $query->where('product_id', $resolvedProductId);
            })
            ->first();

        if (!$subscription) {
            ReminderSubscription::create([
                'identity_key' => $identityKey,
                'user_id' => $userId,
                'product_id' => $resolvedProductId,
                'trigger_type' => $triggerType,
                'channel' => 'email',
                'delay_minutes' => max(30, $delayMinutes),
                'cooldown_minutes' => max(30, $cooldownMinutes),
                'is_active' => true,
                'metadata' => $metadata,
            ]);

            return;
        }

        $subscription->fill([
            'identity_key' => $identityKey,
            'user_id' => $userId,
            'product_id' => $resolvedProductId,
            'channel' => 'email',
            'delay_minutes' => max(30, $delayMinutes),
            'cooldown_minutes' => max(30, $cooldownMinutes),
            'is_active' => true,
            'metadata' => $metadata,
        ]);
        $subscription->save();
    }

    private function resolveProduct(int $productReference): ?Product
    {
        return Product::query()
            ->where(function ($query) use ($productReference) {
                $query->where('id', $productReference)
                    ->orWhere('tdsynnex_product_id', $productReference)
                    ->orWhere('tdsynnex_sku_no', (string) $productReference);
            })
            ->orderByDesc('updated_at')
            ->first();
    }

    private function identity(Request $request): array
    {
        $user = $request->user('sanctum');
        if ($user) {
            return ['user:' . $user->getAuthIdentifier(), $user->getAuthIdentifier(), null];
        }

        $token = (string) $request->cookie(self::COOKIE_NAME);
        $newToken = null;

        if (!preg_match('/^[a-f0-9-]{36}$/i', $token)) {
            $token = (string) Str::uuid();
            $newToken = $token;
        }

        return ['guest:' . hash('sha256', $token), null, $newToken];
    }

    private function responseOk(?string $newToken): JsonResponse
    {
        $response = response()->json(['success' => true]);

        if ($newToken) {
            $response->cookie(self::COOKIE_NAME, $newToken, 60 * 24 * 365, '/', null, request()->isSecure(), true, false, 'Lax');
        }

        return $response;
    }
}
