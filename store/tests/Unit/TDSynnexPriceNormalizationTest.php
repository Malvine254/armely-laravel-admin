<?php

namespace Tests\Unit;

use App\Services\TDSynnexService;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class TDSynnexPriceNormalizationTest extends TestCase
{
    private function normalize(array $row, array $metadata = []): array
    {
        $method = new ReflectionMethod(TDSynnexService::class, 'normalizePriceAvailabilityProduct');

        return $method->invoke(new TDSynnexService(), $row, $metadata);
    }

    public function test_it_keeps_supplier_price_and_msrp_in_separate_fields(): void
    {
        $product = $this->normalize([
            'synnexSKU' => '15359167',
            'price' => '1175.00',
            'msrp' => '1650.00',
            'totalQuantity' => '689',
        ]);

        $this->assertSame(1175.0, $product['productPrice'][0]['rsPrice']);
        $this->assertSame(1650.0, $product['productPrice'][0]['msrp']);
        $this->assertTrue($product['hasApiResellerPrice']);
        $this->assertTrue($product['hasApiRetailPrice']);
    }

    public function test_flat_file_fallback_is_not_marked_as_a_fresh_api_price(): void
    {
        $product = $this->normalize([
            'synnexSKU' => '15359167',
            'price' => '0',
            'totalQuantity' => '689',
        ], [
            '15359167' => [
                'base_price' => 1250.0,
                'retail_price' => 1220.0,
            ],
        ]);

        $this->assertSame(1250.0, $product['productPrice'][0]['rsPrice']);
        $this->assertFalse($product['hasApiResellerPrice']);
        $this->assertFalse($product['hasApiRetailPrice']);
    }
}
