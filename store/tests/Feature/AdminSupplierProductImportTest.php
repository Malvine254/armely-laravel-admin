<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureUserIsActive;
use App\Models\Product;
use App\Models\User;
use App\Services\TDSynnexService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Mockery;
use Tests\TestCase;

class AdminSupplierProductImportTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(EnsureUserIsActive::class);
        DB::connection()->getPdo()->sqliteCreateFunction('JSON_UNQUOTE', static fn ($value) => $value, 1);

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role')->default('customer');
            $table->string('status')->default('active');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable();
            $table->string('name');
            $table->timestamps();
        });
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable();
            $table->integer('tdsynnex_product_id')->unique();
            $table->integer('tdsynnex_sku_no')->nullable();
            $table->string('vendor_id');
            $table->string('product_name', 500);
            $table->string('mfg_part_no')->nullable();
            $table->text('description')->nullable();
            $table->decimal('base_price', 15, 2)->nullable();
            $table->decimal('supplier_regular_price', 15, 2)->nullable();
            $table->decimal('retail_price', 15, 2)->nullable();
            $table->decimal('sale_price', 15, 2)->nullable();
            $table->boolean('is_on_sale')->default(false);
            $table->string('offer_source')->nullable();
            $table->timestamp('sale_started_at')->nullable();
            $table->timestamp('sale_ended_at')->nullable();
            $table->integer('quantity')->default(0);
            $table->string('billing_model')->nullable();
            $table->string('billing_frequency')->nullable();
            $table->boolean('is_available')->default(true);
            $table->boolean('is_discontinued')->default(false);
            $table->boolean('is_hardware')->default(true);
            $table->string('category_segment')->nullable();
            $table->boolean('is_storefront_pinned')->default(false);
            $table->timestamp('storefront_pinned_at')->nullable();
            $table->boolean('is_storefront_curated')->default(false);
            $table->decimal('storefront_score', 12, 2)->nullable();
            $table->unsignedInteger('storefront_rank')->nullable();
            $table->timestamp('storefront_curated_at')->nullable();
            $table->timestamp('search_imported_at')->nullable();
            $table->string('search_import_query', 500)->nullable();
            $table->string('search_import_review_status')->nullable();
            $table->timestamp('search_import_reviewed_at')->nullable();
            $table->foreignId('search_import_reviewed_by')->nullable();
            $table->string('manufacturer')->nullable();
            $table->json('specifications')->nullable();
            $table->json('images')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
        });
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
        Schema::create('order_line_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id');
            $table->foreignId('product_id')->nullable();
            $table->unsignedInteger('quantity')->default(1);
        });
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
        Schema::create('quote_line_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id');
            $table->foreignId('product_id')->nullable();
            $table->unsignedInteger('quantity')->default(1);
        });
    }

    public function test_admin_search_bypasses_database_catalog(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $supplierProduct = $this->supplierProduct();
        $service = Mockery::mock(TDSynnexService::class);
        $service->shouldReceive('searchPriceAvailabilityCatalog')
            ->once()->with('Lenovo IdeaPad', 25, false)->andReturn([$supplierProduct]);
        $service->shouldReceive('mapPriceAvailabilityCatalogProductToDatabaseRow')
            ->once()->with($supplierProduct)->andReturn(['tdsynnex_product_id' => 1900150025]);
        $this->app->instance(TDSynnexService::class, $service);

        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/admin/imported-products/supplier-search?q=Lenovo%20IdeaPad')
            ->assertOk()
            ->assertJsonPath('data.0.identifier', '1900150025')
            ->assertJsonPath('data.0.orderable', true)
            ->assertJsonPath('data.0.storefront_pinned', false);
    }

    public function test_selected_supplier_product_is_revalidated_imported_and_pinned(): void
    {
        Queue::fake();
        $service = Mockery::mock(TDSynnexService::class)->makePartial();
        $service->shouldReceive('searchPriceAvailabilityCatalog')
            ->once()->with('1900150025', 50, false, false)->andReturn([$this->supplierProduct()]);

        $product = $service->importSelectedPriceAvailabilityProduct('1900150025', 'Lenovo IdeaPad', 7);

        $this->assertTrue($product->is_storefront_pinned);
        $this->assertSame(18, $product->quantity);
        $this->assertSame('Lenovo IdeaPad', $product->search_import_query);
        $this->assertSame('approved', $product->search_import_review_status);
        $this->assertSame(7, $product->search_import_reviewed_by);
        $this->assertSame('Supplier text from TD', $product->description);
    }

    public function test_active_zero_stock_supplier_product_can_be_imported_as_orderable(): void
    {
        Queue::fake();
        $supplierProduct = $this->supplierProduct();
        $supplierProduct['status'] = 'Active';
        $supplierProduct['availableQuantity'] = 0;
        $supplierProduct['totalQuantity'] = 0;
        $service = Mockery::mock(TDSynnexService::class)->makePartial();
        $service->shouldReceive('searchPriceAvailabilityCatalog')
            ->once()->with('1900150025', 50, false, false)->andReturn([$supplierProduct]);

        $product = $service->importSelectedPriceAvailabilityProduct('1900150025', 'Lenovo IdeaPad', 7);

        $this->assertTrue($product->is_storefront_pinned);
        $this->assertTrue((bool) $product->is_available);
        $this->assertSame(0, $product->quantity);
    }

    public function test_non_admin_cannot_search_supplier_catalog(): void
    {
        $customer = User::factory()->create(['role' => 'customer', 'status' => 'active']);
        $this->actingAs($customer, 'sanctum')
            ->getJson('/api/v1/admin/imported-products/supplier-search?q=Lenovo')
            ->assertForbidden();
    }

    public function test_import_database_failure_does_not_expose_sql(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $service = Mockery::mock(TDSynnexService::class);
        $service->shouldReceive('importSelectedPriceAvailabilityProduct')
            ->once()
            ->andThrow(new QueryException(
                'mysql',
                'insert into products (secret_column) values (?)',
                ['secret-value'],
                new \PDOException("Unknown column 'secret_column'")
            ));
        $this->app->instance(TDSynnexService::class, $service);

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/v1/admin/imported-products/import', [
                'identifier' => '7624063',
                'search_query' => '7624063',
            ]);

        $response->assertServerError()
            ->assertJsonPath('message', 'The product could not be saved because the catalog database is not ready.');
        $this->assertStringNotContainsString('secret_column', $response->getContent());
        $this->assertStringNotContainsString('secret-value', $response->getContent());
    }

    public function test_admin_can_remove_an_imported_product_pin(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $product = Product::create([
            'tdsynnex_product_id' => 1900150025,
            'vendor_id' => 'TD SYNNEX',
            'product_name' => 'Lenovo IdeaPad 5',
            'is_storefront_pinned' => true,
            'storefront_pinned_at' => now(),
            'search_imported_at' => now(),
        ]);

        $this->actingAs($admin, 'sanctum')
            ->putJson("/api/v1/admin/imported-products/{$product->id}/storefront-pin", ['pinned' => false])
            ->assertOk();

        $this->assertFalse($product->fresh()->is_storefront_pinned);
        $this->assertNull($product->fresh()->storefront_pinned_at);
    }

    public function test_assortment_rebuild_keeps_pin_first_without_exceeding_limit(): void
    {
        config()->set('storefront.assortment_size', 2);
        config()->set('storefront.category_quotas', ['01' => 2]);
        foreach ([1, 2, 3] as $index) {
            Product::create([
                'tdsynnex_product_id' => 1900150025 + $index,
                'vendor_id' => 'TD SYNNEX',
                'product_name' => "Business Laptop {$index}",
                'base_price' => 500,
                'retail_price' => 600,
                'quantity' => 10,
                'is_available' => true,
                'is_discontinued' => false,
                'is_hardware' => true,
                'category_segment' => '01',
                'is_storefront_pinned' => $index === 3,
                'storefront_pinned_at' => $index === 3 ? now() : null,
            ]);
        }

        $this->artisan('storefront:rebuild-assortment')->assertSuccessful();

        $this->assertSame(2, Product::where('is_storefront_curated', true)->count());
        $this->assertSame(1, Product::where('is_storefront_pinned', true)->value('storefront_rank'));
        $this->assertSame(3, Product::count());
    }

    private function supplierProduct(): array
    {
        return [
            'productId' => '1900150025',
            'sku' => '1900150025',
            'synnexSKU' => '1900150025',
            'vendorId' => 'TD SYNNEX',
            'vendorName' => 'TD SYNNEX',
            'productName' => 'Lenovo IdeaPad 5 15.6 i7-1255U 16GB 512GB NVIDIA GeForce MX550',
            'description' => 'Supplier text from TD',
            'mfgPartNo' => '82SF0002US',
            'manufacturer' => 'Lenovo',
            'availableQuantity' => 18,
            'totalQuantity' => 18,
            'status' => 'Active',
            'discontinueProduct' => false,
            'flatCategoryName' => 'Laptops',
            'productPrice' => [['rsPrice' => 966.47, 'msrp' => 1099.99]],
            'productImages' => [],
        ];
    }
}