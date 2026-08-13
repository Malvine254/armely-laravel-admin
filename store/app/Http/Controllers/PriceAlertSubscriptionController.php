<?php

namespace App\Http\Controllers;

use App\Models\PriceAlertSubscription;
use App\Models\Product;
use App\Support\OfferPricing;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PriceAlertSubscriptionController extends Controller
{
    private const COOKIE_NAME = 'armely_behavior_visitor';

    public function index(Request $request): JsonResponse
    {
        [$identityKey, $userId, $newToken] = $this->identity($request);

        $subscriptions = PriceAlertSubscription::query()
            ->where('is_active', true)
            ->when($userId, function ($query) use ($userId) {
                $query->where(function ($inner) use ($userId) {
                    $inner->where('user_id', $userId)
                        ->orWhere(function ($guest) use ($userId) {
                            $guest->whereNull('user_id')->where('identity_key', 'user:' . $userId);
                        });
                });
            }, fn ($query) => $query->where('identity_key', $identityKey))
            ->with(['product:id,product_name,mfg_part_no,base_price,sale_price,is_on_sale,offer_source'])
            ->orderByDesc('updated_at')
            ->get()
            ->map(function (PriceAlertSubscription $subscription): array {
                $product = $subscription->product;
                $currentPrice = $product ? OfferPricing::sellPrice($product) : null;

                return [
                    'id' => $subscription->id,
                    'product_id' => $subscription->product_id,
                    'product_name' => (string) ($product->product_name ?? 'Unknown Product'),
                    'mfg_part_no' => (string) ($product->mfg_part_no ?? ''),
                    'baseline_price' => (float) ($subscription->baseline_price ?? 0),
                    'current_price' => $currentPrice,
                    'min_drop_amount' => (float) ($subscription->min_drop_amount ?? 0),
                    'min_drop_percent' => (float) ($subscription->min_drop_percent ?? 0),
                    'cooldown_minutes' => (int) ($subscription->cooldown_minutes ?? 0),
                    'source' => (string) ($subscription->source ?? 'manual'),
                    'last_notified_at' => optional($subscription->last_notified_at)->toISOString(),
                    'updated_at' => optional($subscription->updated_at)->toISOString(),
                ];
            })
            ->values();

        return $this->responseData(['records' => $subscriptions], $newToken);
    }

    public function upsert(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'min:1'],
            'min_drop_amount' => ['nullable', 'numeric', 'min:0', 'max:9999999.99'],
            'min_drop_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'cooldown_minutes' => ['nullable', 'integer', 'min:30', 'max:525600'],
            'source' => ['nullable', 'string', 'max:24'],
        ]);

        [$identityKey, $userId, $newToken] = $this->identity($request);

        $product = $this->resolveProduct((int) $validated['product_id']);
        if (!$product) {
            return $this->responseMessage('Product not found for price alerts.', 404, $newToken);
        }

        $baseline = OfferPricing::sellPrice($product);
        if ($baseline <= 0) {
            return $this->responseMessage('This product currently has no alertable price.', 422, $newToken);
        }

        $subscription = $this->findExistingSubscription($identityKey, $userId, (int) $product->id)
            ?? new PriceAlertSubscription([
                'identity_key' => $identityKey,
                'user_id' => $userId,
                'product_id' => (int) $product->id,
            ]);

        $subscription->fill([
            'identity_key' => $identityKey,
            'user_id' => $userId,
            'product_id' => (int) $product->id,
            'baseline_price' => $subscription->baseline_price ?: $baseline,
            'min_drop_amount' => round((float) ($validated['min_drop_amount'] ?? 0), 2),
            'min_drop_percent' => round((float) ($validated['min_drop_percent'] ?? 5), 2),
            'cooldown_minutes' => (int) ($validated['cooldown_minutes'] ?? 1440),
            'source' => substr((string) ($validated['source'] ?? 'manual'), 0, 24),
            'is_active' => true,
        ]);
        $subscription->save();

        return $this->responseData([
            'subscription' => [
                'id' => $subscription->id,
                'product_id' => (int) $product->id,
                'product_name' => (string) ($product->product_name ?? ''),
                'baseline_price' => (float) ($subscription->baseline_price ?? 0),
                'current_price' => $baseline,
                'min_drop_amount' => (float) ($subscription->min_drop_amount ?? 0),
                'min_drop_percent' => (float) ($subscription->min_drop_percent ?? 0),
                'cooldown_minutes' => (int) ($subscription->cooldown_minutes ?? 0),
                'source' => (string) ($subscription->source ?? 'manual'),
            ],
        ], $newToken, 201);
    }

    public function deactivate(Request $request, int $productId): JsonResponse
    {
        [$identityKey, $userId, $newToken] = $this->identity($request);

        $product = $this->resolveProduct($productId);
        if (!$product) {
            return $this->responseMessage('Product not found for price alerts.', 404, $newToken);
        }

        $subscription = $this->findExistingSubscription($identityKey, $userId, (int) $product->id);

        if (!$subscription) {
            return $this->responseData(['removed' => false], $newToken);
        }

        $subscription->update(['is_active' => false]);

        return $this->responseData(['removed' => true], $newToken);
    }

    private function findExistingSubscription(string $identityKey, ?int $userId, int $productId): ?PriceAlertSubscription
    {
        return PriceAlertSubscription::query()
            ->where('product_id', $productId)
            ->where(function ($query) use ($identityKey, $userId) {
                $query->where('identity_key', $identityKey);
                if ($userId) {
                    $query->orWhere('user_id', $userId);
                }
            })
            ->first();
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

    private function responseData(array $data, ?string $newToken, int $status = 200): JsonResponse
    {
        $response = response()->json([
            'success' => true,
            'data' => $data,
        ], $status);

        if ($newToken) {
            $response->cookie(self::COOKIE_NAME, $newToken, 60 * 24 * 365, '/', null, request()->isSecure(), true, false, 'Lax');
        }

        return $response;
    }

    private function responseMessage(string $message, int $status, ?string $newToken): JsonResponse
    {
        $response = response()->json([
            'success' => false,
            'message' => $message,
        ], $status);

        if ($newToken) {
            $response->cookie(self::COOKIE_NAME, $newToken, 60 * 24 * 365, '/', null, request()->isSecure(), true, false, 'Lax');
        }

        return $response;
    }
}
