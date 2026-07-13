<?php

namespace App\Services;

use App\Models\User;

class CustomerPricingService
{
    public function resolveDiscountPercent(?User $user): float
    {
        if (!$user) {
            return 0.0;
        }

        $percent = (float) ($user->special_pricing_percent ?? 0);

        return max(0.0, min(100.0, round($percent, 2)));
    }

    public function applyDiscount(float $amount, ?User $user): float
    {
        return $this->applyDiscountPercent($amount, $this->resolveDiscountPercent($user));
    }

    public function applyDiscountPercent(float $amount, float $discountPercent): float
    {
        $baseAmount = max(0.0, (float) $amount);
        $clampedPercent = max(0.0, min(100.0, (float) $discountPercent));

        if ($baseAmount <= 0.0 || $clampedPercent <= 0.0) {
            return round($baseAmount, 2);
        }

        $discountFactor = 1 - ($clampedPercent / 100);

        return round(max(0.0, $baseAmount * $discountFactor), 2);
    }

    public function applyToProductPayload(array $product, ?User $user): array
    {
        $discountPercent = $this->resolveDiscountPercent($user);
        if ($discountPercent <= 0.0) {
            return $product;
        }

        if (!isset($product['productPrice']) || !is_array($product['productPrice']) || empty($product['productPrice'])) {
            $product['productPrice'] = [['minQty' => 1]];
        }

        if (!is_array($product['productPrice'][0] ?? null)) {
            $product['productPrice'][0] = ['minQty' => 1];
        }

        $basePrice = (float) ($product['productPrice'][0]['rsPrice'] ?? 0);
        if ($basePrice <= 0.0) {
            $basePrice = (float) ($product['productPrice'][0]['msrp'] ?? 0);
        }

        if ($basePrice <= 0.0) {
            return $product;
        }

        $adjustedPrice = $this->applyDiscountPercent($basePrice, $discountPercent);
        $product['productPrice'][0]['rsPrice'] = $adjustedPrice;
        if (!empty($product['isOnSale'])) {
            $product['salePrice'] = $adjustedPrice;
            if (is_array($product['offer'] ?? null)) {
                $product['offer']['salePrice'] = $adjustedPrice;
            }
        }
        $product['specialPricing'] = [
            'discountPercent' => $discountPercent,
            'basePrice' => round($basePrice, 2),
            'adjustedPrice' => $adjustedPrice,
        ];

        return $product;
    }
}
