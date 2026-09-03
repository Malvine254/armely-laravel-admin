<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Services\TDSynnexService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TDSynnexStaleProductSyncTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('tdsynnex_product_id')->unique();
            $table->string('tdsynnex_sku_no')->nullable();
            $table->string('vendor_id');
            $table->string('product_name');
            $table->string('mfg_part_no')->nullable();
            $table->decimal('base_price', 15, 2)->nullable();
            $table->decimal('supplier_regular_price', 15, 2)->nullable();
            $table->decimal('retail_price', 15, 2)->nullable();
            $table->decimal('sale_price', 15, 2)->nullable();
            $table->boolean('is_on_sale')->default(false);
            $table->string('offer_source')->nullable();
            $table->timestamp('sale_started_at')->nullable();
            $table->timestamp('sale_ended_at')->nullable();
            $table->integer('quantity')->default(0);
            $table->boolean('is_available')->default(true);
            $table->boolean('is_discontinued')->default(false);
            $table->decimal('live_price', 15, 2)->nullable();
            $table->decimal('live_retail_price', 15, 2)->nullable();
            $table->integer('live_quantity')->nullable();
            $table->boolean('live_is_available')->nullable();
            $table->boolean('live_is_discontinued')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamp('live_checked_at')->nullable();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('products');

        parent::tearDown();
    }

    public function test_not_found_product_id_fallback_is_quarantined_from_storefront(): void
    {
        $product = $this->createProduct();

        Http::fake([
            '*' => Http::response($this->priceAvailabilityResponse('Not found'), 200, [
                'Content-Type' => 'application/xml',
            ]),
        ]);

        $result = app(TDSynnexService::class)->refreshLivePricesInDatabase(['1900150025']);

        $product->refresh();
        $this->assertSame(1, $result['checked'], json_encode($result));
        $this->assertFalse((bool) $product->is_available);
        $this->assertTrue((bool) $product->is_discontinued);
        $this->assertSame(0, $product->quantity);
        $this->assertFalse($product->live_is_available);
        $this->assertTrue($product->live_is_discontinued);
        $this->assertNotNull($product->live_checked_at);
    }

    public function test_api_error_response_does_not_quarantine_product(): void
    {
        $product = $this->createProduct();

        Http::fake([
            '*' => Http::response(
                '<?xml version="1.0"?><priceResponse><errorMessage>Client.InvalidRequest</errorMessage></priceResponse>',
                200,
                ['Content-Type' => 'application/xml']
            ),
        ]);

        $result = app(TDSynnexService::class)->refreshLivePricesInDatabase(['1900150025']);

        $product->refresh();
        $this->assertSame(0, $result['checked']);
        $this->assertCount(1, $result['batch_errors']);
        $this->assertTrue((bool) $product->is_available);
        $this->assertFalse((bool) $product->is_discontinued);
        $this->assertSame(10, $product->quantity);
        $this->assertNull($product->live_checked_at);
    }

    private function createProduct(): Product
    {
        return Product::create([
            'tdsynnex_product_id' => 1900150025,
            'tdsynnex_sku_no' => null,
            'vendor_id' => 'TD SYNNEX',
            'product_name' => 'Lenovo ThinkCentre M70s Gen 3',
            'mfg_part_no' => '11T8001VUS',
            'base_price' => 825,
            'retail_price' => 907.50,
            'quantity' => 10,
            'is_available' => true,
            'is_discontinued' => false,
        ]);
    }

    private function priceAvailabilityResponse(string $status): string
    {
        return '<?xml version="1.0"?><priceResponse><PriceAvailabilityList>'
            . '<lineNumber>1</lineNumber><synnexSKU>1900150025</synnexSKU>'
            . '<status>' . $status . '</status><totalQuantity>0</totalQuantity>'
            . '</PriceAvailabilityList></priceResponse>';
    }
}