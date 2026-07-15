<?php

namespace App\Support;

use App\Models\Product;

class OfferPricing
{
    public static function regularPrice(Product $product): float
    {
        $salePrice = (float) ($product->sale_price ?? 0);

        foreach ([$product->supplier_regular_price, $product->base_price] as $candidate) {
            $price = (float) ($candidate ?? 0);
            if ($price > $salePrice) {
                return $price;
            }
        }

        return 0.0;
    }

    public static function msrp(Product $product): float
    {
        return (float) ($product->retail_price ?? $product->base_price ?? 0);
    }

    /** Customer sell-price basis; MSRP is never used as a fallback. */
    public static function sellPrice(Product $product): float
    {
        $salePrice = (float) ($product->sale_price ?? 0);
        $activeOffer = (bool) $product->is_on_sale
            && in_array((string) ($product->offer_source ?? ''), ['manual', 'verified_tdsynnex_special', 'tdsynnex_price_drop'], true)
            && $salePrice > 0;

        return $activeOffer ? $salePrice : (float) ($product->base_price ?? 0);
    }
}
