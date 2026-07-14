<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Support\OfferPricing;
use PHPUnit\Framework\TestCase;

class OfferPricingTest extends TestCase
{
    public function test_supplier_regular_price_is_used_for_offer_comparison_instead_of_msrp(): void
    {
        $product = new Product([
            'base_price' => 335.89,
            'supplier_regular_price' => 375.89,
            'retail_price' => 478.00,
            'sale_price' => 335.89,
        ]);

        $this->assertSame(375.89, OfferPricing::regularPrice($product));
        $this->assertSame(478.0, OfferPricing::msrp($product));
    }

    public function test_msrp_is_the_fallback_when_supplier_regular_price_is_missing(): void
    {
        $product = new Product([
            'base_price' => 335.89,
            'retail_price' => 478.00,
            'sale_price' => 335.89,
        ]);

        $this->assertSame(478.0, OfferPricing::regularPrice($product));
    }

    public function test_invalid_supplier_regular_price_falls_back_to_msrp(): void
    {
        $product = new Product([
            'base_price' => 335.89,
            'supplier_regular_price' => 300.00,
            'retail_price' => 478.00,
            'sale_price' => 335.89,
        ]);

        $this->assertSame(478.0, OfferPricing::regularPrice($product));
    }
}
