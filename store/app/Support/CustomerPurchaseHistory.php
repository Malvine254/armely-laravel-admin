<?php

namespace App\Support;

use App\Models\Order;
use App\Models\Product;
use App\Models\Quote;
use Illuminate\Support\Carbon;

/**
 * Lifecycle emails (viewed, saved, cart, price drop) are only for items a
 * customer looked at but has not bought or asked to quote. This answers that
 * question consistently for every campaign.
 */
class CustomerPurchaseHistory
{
    public static function hasQuotedOrOrderedSince(int $userId, array $productIds, Carbon $since): bool
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

        $quotes = Quote::query()
            ->where('user_id', $userId)
            ->where('created_at', '>=', $since)
            ->whereNotIn('status', ['cancelled', 'rejected'])
            ->get(['items']);
        $orders = Order::query()
            ->where('user_id', $userId)
            ->where('created_at', '>=', $since)
            ->whereNotIn('status', ['cancelled', 'refunded'])
            ->get(['items']);

        return $quotes->concat($orders)->contains(function ($record) use ($references): bool {
            foreach ((array) ($record->items ?? []) as $item) {
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
}
