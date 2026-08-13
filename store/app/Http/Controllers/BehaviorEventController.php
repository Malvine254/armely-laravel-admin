<?php

namespace App\Http\Controllers;

use App\Models\UserCartEvent;
use App\Models\UserCartSnapshot;
use App\Models\UserFavoriteEvent;
use App\Models\UserProductView;
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

        return $this->responseOk($newToken);
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
