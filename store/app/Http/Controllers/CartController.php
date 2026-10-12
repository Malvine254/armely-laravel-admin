<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\UserCartSnapshot;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $snapshot = UserCartSnapshot::where('identity_key', $this->identity($request))->first();

        return $this->respond($request, $snapshot);
    }

    public function update(Request $request): JsonResponse
    {
        return $this->save($request, false);
    }

    public function merge(Request $request): JsonResponse
    {
        return $this->save($request, true);
    }

    private function save(Request $request, bool $merge): JsonResponse
    {
        $user = $request->user();
        abort_if($user->status === 'suspended' || $user->company?->status === 'inactive', 403, 'Suspended accounts cannot modify carts.');
        $validated = $request->validate([
            'items' => ['present', 'array', 'max:300'],
            'items.*.productId' => ['required', 'string', 'max:100', 'distinct'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:9999'],
            'revision' => [$merge ? 'nullable' : 'required', 'integer', 'min:0'],
        ]);

        return DB::transaction(function () use ($request, $validated, $merge) {
            $identity = $this->identity($request);
            UserCartSnapshot::firstOrCreate(['identity_key' => $identity], [
                'user_id' => $request->user()->id,
                'items' => [],
                'item_count' => 0,
                'total_quantity' => 0,
                'last_synced_at' => now(),
                'revision' => 0,
            ]);
            $snapshot = UserCartSnapshot::where('identity_key', $identity)->lockForUpdate()->firstOrFail();
            if (! $merge && (int) $validated['revision'] !== (int) $snapshot->revision) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your saved cart changed on another device. Choose which cart to keep before saving.',
                    'data' => ['revision' => $snapshot->revision],
                ], 409)->header('Cache-Control', 'private, no-store');
            }

            $items = $merge ? collect($snapshot->items)->keyBy('productId') : collect();
            foreach ($validated['items'] as $item) {
                $id = trim($item['productId']);
                abort_if($id === '', 422, 'Product identifier must not be empty.');
                $items->put($id, [
                    'productId' => $id,
                    'quantity' => $merge
                        ? max((int) ($items->get($id)['quantity'] ?? 0), (int) $item['quantity'])
                        : (int) $item['quantity'],
                ]);
            }
            abort_if($items->count() > 300, 422, 'Your merged cart exceeds 300 products.');
            $request->merge(['items' => $items->values()->all()]);
            app(BehaviorEventController::class)->persistCartSnapshot($request);

            return $this->respond($request, $snapshot->fresh());
        });
    }

    private function identity(Request $request): string
    {
        return 'user:'.$request->user()->id;
    }

    private function respond(Request $request, ?UserCartSnapshot $snapshot): JsonResponse
    {
        $items = (array) ($snapshot?->items ?? []);
        $references = array_column($items, 'productId');
        $products = Product::whereIn('tdsynnex_sku_no', $references)
            ->orWhereIn('tdsynnex_product_id', $references)
            ->orWhereIn('id', $references)->get();
        $mapper = app(ProductController::class);
        $hydrated = [];
        foreach ($items as $item) {
            $id = (string) $item['productId'];
            $product = $products->first(fn (Product $product) => (string) $product->tdsynnex_sku_no === $id)
                ?? $products->first(fn (Product $product) => (string) $product->tdsynnex_product_id === $id)
                ?? $products->first(fn (Product $product) => (string) $product->id === $id);
            $payload = $product ? $mapper->cartProductPayload($product, $request->user()) : [
                'productName' => 'Product no longer available ('.$id.')',
                'isAvailable' => false,
                'discontinueProduct' => true,
                'productPrice' => [],
                'productImages' => [],
            ];
            $hydrated[] = array_merge($payload, ['productId' => $id, 'quantity' => (int) $item['quantity']]);
        }

        return response()->json([
            'success' => true,
            'data' => ['items' => $hydrated, 'revision' => (int) ($snapshot?->revision ?? 0)],
        ])->header('Cache-Control', 'private, no-store');
    }
}
