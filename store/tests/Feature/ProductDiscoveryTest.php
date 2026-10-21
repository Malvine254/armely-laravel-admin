<?php

namespace Tests\Feature;

use App\Exceptions\TDSynnexApiException;
use App\Models\User;
use App\Services\TDSynnexService;
use Illuminate\Support\Facades\Http;
use Mockery;
use ReflectionMethod;
use Tests\TestCase;

class ProductDiscoveryTest extends TestCase
{
    public function test_model_images_do_not_require_supplier_operating_system_shorthand(): void
    {
        $method = new ReflectionMethod(TDSynnexService::class, 'discoveryMatchesSupplier');
        $service = app(TDSynnexService::class);
        $this->assertTrue($method->invoke($service, 'HP ZBook 8 G1AS workstation', 'HP ZBOOK 8 G1AS W11 PRO', ''));
        $this->assertFalse($method->invoke($service, 'HP ZBook 9 G1AS workstation', 'HP ZBOOK 8 G1AS W11 PRO', ''));
        $this->assertFalse($method->invoke($service, 'HP ZBook 8 G2 workstation', 'HP ZBOOK 8 G1AS W11 PRO', ''));
    }

    public function test_cached_search_candidates_reuse_metadata_without_rescanning_the_catalog(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'discovery-metadata-');
        $parts = array_fill(0, 37, '');
        $parts[1] = 'DTL';
        $parts[2] = 'PART-10001';
        $parts[4] = '10001';
        $parts[6] = 'HP ZBOOK 8 G1AS';
        $parts[7] = 'HP';
        file_put_contents($file, implode('~', $parts));
        config(['tdsynnex.price_availability.flat_file_path' => $file]);
        try {
            $this->assertSame(['10001'], (new TDSynnexService)->discoveryCandidateSkus('HP ZBOOK'));
            file_put_contents($file, '');
            $service = new TDSynnexService;
            $this->assertSame(['10001'], $service->discoveryCandidateSkus('HP ZBOOK'));
            $metadata = (new ReflectionMethod(TDSynnexService::class, 'readApMetadata'))->invoke($service, ['10001']);
            $this->assertSame('PART-10001', $metadata['10001']['mpn']);
        } finally {
            unlink($file);
        }
    }

    protected function setUp(): void
    {
        parent::setUp();
        \Illuminate\Support\Facades\Schema::create('app_settings', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->json('value')->nullable();
            $table->timestamps();
        });
        \Illuminate\Support\Facades\Schema::create('categories', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable();
            $table->string('name');
            $table->timestamps();
        });
        \Illuminate\Support\Facades\Schema::create('products', function (\Illuminate\Database\Schema\Blueprint $table) {
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
            $table->timestamp('search_imported_at')->nullable();
            $table->string('search_import_query', 500)->nullable();
            $table->string('search_import_review_status')->nullable();
            $table->string('manufacturer')->nullable();
            $table->json('specifications')->nullable();
            $table->json('images')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamp('image_enrichment_attempted_at')->nullable();
            $table->timestamps();
        });
    }

    public function test_supplier_search_matches_all_terms_across_ap_fields_without_requiring_phrase_order(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'discovery-ap-');
        $parts = array_fill(0, 37, '');
        $parts[1] = 'DTL';
        $parts[2] = 'ABC123';
        $parts[4] = '12345';
        $parts[6] = 'ZBOOK 8 G1AS 32GB W11 PRO';
        $parts[7] = 'HP';
        file_put_contents($file, implode('~', $parts));
        config(['tdsynnex.price_availability.flat_file_path' => $file]);
        $method = new ReflectionMethod(TDSynnexService::class, 'findMatchingSkusInApFiles');

        try {
            $this->assertSame(['12345'], $method->invoke(app(TDSynnexService::class), 'hp zbook 8 g1as w11 pro', 10));
            $this->assertSame([], $method->invoke(app(TDSynnexService::class), 'hp zbook missing-model', 10));
        } finally {
            unlink($file);
        }
    }

    private function product(): array
    {
        return [
            'productId' => '12345',
            'productName' => 'HP ZBook Workstation',
            'manufacturer' => 'HP',
            'mfgPartNo' => 'ABC123',
            'description' => 'Supplier summary',
            'upc' => '',
            'productPrice' => [['rsPrice' => 100, 'msrp' => 150]],
            'hasApiResellerPrice' => true,
            'hasApiRetailPrice' => true,
            'availableQuantity' => 4,
        ];
    }

    public function test_guest_gets_catalog_sell_price_and_stock_without_internal_supplier_fields(): void
    {
        $supplier = Mockery::mock(TDSynnexService::class)->makePartial();
        $supplier->shouldReceive('searchPriceAvailabilityCatalog')->once()
            ->with('HP ZBook', 10, false, true, true)->andReturn([$this->product()]);
        $this->app->instance(TDSynnexService::class, $supplier);

        $this->getJson('/api/v1/product-discovery?search=HP%20ZBook')
            ->assertOk()
            ->assertJsonPath('data.0.part_number', 'ABC123')
            ->assertJsonPath('data.0.productPrice.0.rsPrice', 100)
            ->assertJsonPath('data.0.productPrice.0.msrp', 150)
            ->assertJsonPath('data.0.pricing_available', true)
            ->assertJsonPath('data.0.availableQuantity', 4)
            ->assertJsonMissingPath('data.0.price')
            ->assertJsonMissingPath('data.0.hasApiResellerPrice');
    }

    public function test_discovery_applies_customer_discount_per_request_without_mutating_supplier_prices(): void
    {
        $supplier = Mockery::mock(TDSynnexService::class)->makePartial();
        $supplier->shouldReceive('searchPriceAvailabilityCatalog')->twice()->andReturn([$this->product()]);
        $this->app->instance(TDSynnexService::class, $supplier);
        $user = new User;
        $user->special_pricing_percent = 10;
        $this->actingAs($user, 'sanctum');

        $response = $this->getJson('/api/v1/product-discovery?search=ZBook')
            ->assertOk()
            ->assertJsonPath('data.0.productPrice.0.rsPrice', 90)
            ->assertJsonPath('data.0.productPrice.0.msrp', 150)
            ->assertJsonMissingPath('data.0.specialPricing');
        $this->assertStringContainsString('private', $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));

        $user->special_pricing_percent = 20;
        $this->getJson('/api/v1/product-discovery?search=ZBook')
            ->assertOk()->assertJsonPath('data.0.productPrice.0.rsPrice', 80);
    }

    public function test_missing_live_price_does_not_fall_back_to_ap_or_msrp_even_for_discounted_customers(): void
    {
        $supplier = Mockery::mock(TDSynnexService::class)->makePartial();
        $supplier->shouldReceive('searchPriceAvailabilityCatalog')->once()->andReturn([
            [...$this->product(), 'hasApiResellerPrice' => false],
        ]);
        $this->app->instance(TDSynnexService::class, $supplier);
        $user = new User;
        $user->special_pricing_percent = 10;
        $this->actingAs($user, 'sanctum');

        $this->getJson('/api/v1/product-discovery?search=ZBook')
            ->assertOk()
            ->assertJsonPath('data', []);
    }

    public function test_empty_supplier_search_has_an_explicit_message(): void
    {
        $supplier = Mockery::mock(TDSynnexService::class)->makePartial();
        $supplier->shouldReceive('searchPriceAvailabilityCatalog')->once()->with('unknown', 10, false, true, true)->andReturn([]);
        $this->app->instance(TDSynnexService::class, $supplier);

        $this->getJson('/api/v1/product-discovery?search=unknown')
            ->assertOk()->assertJsonPath('data', [])
            ->assertJsonPath('message', 'No in-stock supplier match found. Try the manufacturer and exact model or part number.');
    }

    public function test_supplier_pagination_reuses_checked_stock_and_details_use_the_selected_page(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'discovery-pages-');
        $rows = [];
        for ($index = 1; $index <= 23; $index++) {
            $parts = array_fill(0, 37, '');
            $parts[1] = 'DTL';
            $parts[2] = 'PART-'.$index;
            $parts[4] = (string) (10000 + $index);
            $parts[6] = 'HP ZBOOK LAPTOP';
            $parts[7] = 'HP';
            $rows[] = implode('~', $parts);
        }
        file_put_contents($file, implode("\n", $rows));
        config([
            'tdsynnex.price_availability.flat_file_path' => $file,
            'tdsynnex.price_availability.flat_files_dir' => 'tests/fixtures/no-ap-files',
            'tdsynnex.price_availability.customer_no' => 'test-customer',
            'tdsynnex.price_availability.username' => 'test-user',
            'tdsynnex.price_availability.password' => 'test-password',
        ]);
        $supplier = Mockery::mock(TDSynnexService::class)->makePartial();
        $supplier->shouldReceive('postPriceAvailabilityXml')->once()->andReturnUsing(function (string $xml) {
            preg_match_all('/<synnexSKU>(\d+)<\/synnexSKU>/', $xml, $matches);
            $this->assertLessThanOrEqual(50, count($matches[1]));

            return ['PriceAvailabilityList' => array_map(static fn (string $sku) => [
                'synnexSKU' => $sku, 'description' => 'HP ZBOOK LAPTOP',
                'price' => '100', 'msrp' => '150', 'totalQuantity' => '4',
            ], $matches[1])];
        });
        $supplier->shouldReceive('enrichDiscoveredProduct')->once()->andReturn(['description' => 'Online details']);
        $this->app->instance(TDSynnexService::class, $supplier);

        try {
            foreach ([1 => 10, 2 => 10, 3 => 3] as $page => $count) {
                $this->getJson('/api/v1/product-discovery?search=HP%20ZBOOK&page='.$page.'&per_page=10')
                    ->assertOk()->assertJsonCount($count, 'data')
                    ->assertJsonPath('data.0.product_id', (string) (10001 + ($page - 1) * 10))
                    ->assertJsonPath('data.0.productPrice.0.rsPrice', 100)
                    ->assertJsonPath('pagination.total', 23)
                    ->assertJsonPath('pagination.page', $page)
                    ->assertJsonPath('pagination.has_more', $page < 3);
            }
            $this->getJson('/api/v1/product-discovery/details?search=HP%20ZBOOK&product_id=10011&page=2&per_page=10')
                ->assertOk()->assertJsonPath('data.product_id', '10011')
                ->assertJsonPath('data.productPrice.0.rsPrice', 100);
            $this->getJson('/api/v1/product-discovery/details?search=HP%20ZBOOK&product_id=10011&page=1&per_page=10')
                ->assertNotFound();
        } finally {
            unlink($file);
        }
    }

    public function test_supplier_candidates_reuse_the_storefront_cap(): void
    {
        \App\Models\AppSetting::setValue('catalog.storefront_product_limit', 1000);
        $file = tempnam(sys_get_temp_dir(), 'discovery-cap-');
        $rows = [];
        for ($index = 0; $index < 1005; $index++) {
            $parts = array_fill(0, 37, '');
            $parts[1] = 'DTL';
            $parts[4] = (string) (20000 + $index);
            $parts[6] = 'HP ZBOOK LAPTOP';
            $rows[] = implode('~', $parts);
        }
        file_put_contents($file, implode("\n", $rows));
        config([
            'tdsynnex.price_availability.flat_file_path' => $file,
            'tdsynnex.price_availability.flat_files_dir' => 'tests/fixtures/no-ap-files',
        ]);

        try {
            $this->assertCount(1000, app(TDSynnexService::class)->discoveryCandidateSkus('HP ZBOOK'));
        } finally {
            unlink($file);
        }
    }

    public function test_price_floor_offers_stock_and_maximum_are_applied_before_pagination(): void
    {
        $supplier = Mockery::mock(TDSynnexService::class)->makePartial();
        $rows = [];
        foreach ([99.99, 100, 200, 250, 150, 151, 101, 300, 120] as $index => $price) {
            $rows[] = [...$this->product(), 'productId' => (string) (10001 + $index),
                'productPrice' => [['rsPrice' => $price, 'msrp' => 350]]];
        }
        $rows[4]['availableQuantity'] = 0;
        $rows[5]['discontinueProduct'] = true;
        $rows[6]['hasApiResellerPrice'] = false;
        $underFloor = $supplier->saveDiscoveredProduct($rows[2], 'HP ZBook');
        $underFloor->update(['sale_price' => 99.99, 'is_on_sale' => true, 'offer_source' => 'manual']);
        $offer = $supplier->saveDiscoveredProduct($rows[3], 'HP ZBook');
        $offer->update(['sale_price' => 120, 'is_on_sale' => true, 'offer_source' => 'manual']);
        $supplier->shouldReceive('discoveryCandidateSkus')->twice()->andReturn(array_column($rows, 'productId'));
        $supplier->shouldReceive('searchPriceAvailabilityCatalog')->once()->andReturn($rows);

        $first = $supplier->discoverInStockPage('HP ZBook', 2, 1, 0, 200);
        $second = $supplier->discoverInStockPage('HP ZBook', 2, 2, 0, 200);
        $this->assertSame(['10002', '10004'], array_column($first['products'], 'productId'));
        $this->assertSame(['10009'], array_column($second['products'], 'productId'));
        $this->assertSame(3, $first['total']);
        $this->assertFalse($second['has_more']);
        $this->assertSame('manual', $offer->fresh()->offer_source);
    }

    public function test_supplier_results_are_persisted_and_use_the_normal_detail_and_cart_pricing(): void
    {
        $supplier = Mockery::mock(TDSynnexService::class)->makePartial();
        $supplier->shouldReceive('searchPriceAvailabilityCatalog')->once()->andReturn([$this->product()]);
        $this->app->instance(TDSynnexService::class, $supplier);
        $user = new User;
        $user->special_pricing_percent = 10;
        $this->actingAs($user, 'sanctum');

        $payload = $this->getJson('/api/v1/product-discovery?search=ZBook')->assertOk()
            ->assertJsonPath('data.0.catalog_product.productPrice.0.rsPrice', 90)
            ->assertJsonPath('data.0.catalog_product.onlineContentStatus', 'pending')
            ->json('data.0.catalog_product');
        $saved = \App\Models\Product::query()->where('tdsynnex_product_id', 12345)->sole();
        $this->assertEquals(100, $saved->base_price);
        $this->assertEquals($payload, app(\App\Http\Controllers\ProductController::class)->cartProductPayload($saved, $user));
        $this->getJson('/api/v1/products/12345')->assertOk()
            ->assertJsonPath('data.productId', '12345')
            ->assertJsonPath('data.productPrice.0.rsPrice', 90);
    }

    public function test_failed_verification_keeps_supplier_text_and_does_not_attach_online_content(): void
    {
        $supplier = Mockery::mock(TDSynnexService::class)->makePartial();
        $saved = $supplier->saveDiscoveredProduct($this->product(), 'ZBook');
        $supplier->shouldReceive('discoverOnlineProductDetails')->once()->andReturn([
            'description' => 'Unverified description', 'specifications' => [['name' => 'Memory', 'value' => '32 GB']],
            'image_url' => 'https://images.example.com/wrong.jpg',
            'image_candidates' => [['image_url' => 'https://images.example.com/wrong.jpg']],
            'sources' => [], 'warnings' => [], 'online_details_found' => true,
        ]);
        $verifier = Mockery::mock(\App\Services\AzureOpenAiChatService::class);
        $verifier->shouldReceive('verifyProductEvidence')->once()->andReturnNull();
        $this->app->instance(\App\Services\AzureOpenAiChatService::class, $verifier);
        $this->app->instance(TDSynnexService::class, $supplier);

        $this->getJson('/api/v1/product-discovery/products/12345/content')->assertOk()
            ->assertJsonPath('data.description', 'Supplier summary')
            ->assertJsonPath('data.onlineSpecifications', [])
            ->assertJsonPath('data.onlineContentStatus', 'unavailable')
            ->assertJsonPath('data.productImages', [])
            ->assertJsonCount(1, 'warnings');
        $this->assertSame([], $saved->fresh()->images);
    }

    public function test_background_import_preserves_verified_content_and_updates_supplier_stock(): void
    {
        \Illuminate\Support\Facades\Queue::fake();
        $supplier = Mockery::mock(TDSynnexService::class)->makePartial();
        $saved = $supplier->saveDiscoveredProduct($this->product(), 'ZBook');
        $saved->update([
            'description' => 'Verified online description',
            'images' => [['imageUrl' => 'https://images.example.com/verified.jpg', 'source' => 'openai-verified-online']],
            'specifications' => [...$saved->specifications, 'online_content_status' => 'verified',
                'online_specifications' => [['name' => 'Memory', 'value' => '32 GB']]],
        ]);
        $supplier->shouldReceive('searchPriceAvailabilityCatalog')->andReturn([
            [...$this->product(), 'availableQuantity' => 2],
        ]);
        $supplier->importMissingPriceAvailabilitySearch('ZBook', 1);
        $fresh = $saved->fresh();
        $this->assertSame('Verified online description', $fresh->description);
        $this->assertSame('openai-verified-online', $fresh->images[0]['source']);
        $this->assertSame('32 GB', $fresh->specifications['online_specifications'][0]['value']);
        $this->assertSame(2, $fresh->quantity);
        $this->assertSame(2, $fresh->specifications['availableQuantity']);
    }

    public function test_background_image_enrichment_reuses_ai_verification_for_discovered_products(): void
    {
        $supplier = Mockery::mock(TDSynnexService::class)->makePartial();
        $saved = $supplier->saveDiscoveredProduct($this->product(), 'ZBook');
        $supplier->shouldReceive('enrichDiscoveredProduct')->once()
            ->withArgs(fn ($product) => $product->id === $saved->id)->andReturn([]);
        $supplier->shouldNotReceive('syncPriceAvailabilityImageForProductId');
        (new \App\Jobs\EnrichPriceAvailabilityProductImageJob($saved->id))->handle($supplier);
        $this->assertNotNull($saved->fresh()->image_enrichment_attempted_at);
    }

    public function test_background_content_queues_once_and_returns_saved_content_without_online_requests(): void
    {
        \Illuminate\Support\Facades\Queue::fake();
        Http::preventStrayRequests();
        $supplier = Mockery::mock(TDSynnexService::class)->makePartial();
        $saved = $supplier->saveDiscoveredProduct($this->product(), 'ZBook');
        $supplier->shouldNotReceive('enrichDiscoveredProduct');
        $this->app->instance(TDSynnexService::class, $supplier);
        for ($i = 0; $i < 2; $i++) {
            $this->getJson('/api/v1/product-discovery/products/12345/content?background=1')
                ->assertStatus(202)->assertJsonPath('content_pending', true)
                ->assertJsonPath('data.productId', '12345');
        }
        \Illuminate\Support\Facades\Queue::assertPushed(\App\Jobs\EnrichPriceAvailabilityProductImageJob::class, 1);
        \Illuminate\Support\Facades\Queue::assertPushed(\App\Jobs\EnrichPriceAvailabilityProductImageJob::class,
            fn ($job) => $job->productId === $saved->id && $job->queue === 'products-sync' && $job->connection === 'database');

        $saved->update(['images' => [['imageUrl' => 'https://images.example.com/verified.jpg']],
            'specifications' => [...$saved->specifications, 'online_content_status' => 'verified']]);
        $this->getJson('/api/v1/product-discovery/products/12345/content?background=1')
            ->assertOk()->assertJsonPath('content_pending', false);
    }

    public function test_verified_specs_without_a_photo_still_queue_image_lookup(): void
    {
        \Illuminate\Support\Facades\Queue::fake();
        $supplier = app(TDSynnexService::class);
        $saved = $supplier->saveDiscoveredProduct($this->product(), 'ZBook');
        $saved->update(['specifications' => [...$saved->specifications, 'online_content_status' => 'verified']]);
        $this->getJson('/api/v1/product-discovery/products/12345/content?background=1')
            ->assertStatus(202)->assertJsonPath('content_pending', true);
        \Illuminate\Support\Facades\Queue::assertPushed(\App\Jobs\EnrichPriceAvailabilityProductImageJob::class, 1);
    }

    public function test_failed_background_lookup_returns_warning_and_does_not_requeue_immediately(): void
    {
        \Illuminate\Support\Facades\Queue::fake();
        $saved = app(TDSynnexService::class)->saveDiscoveredProduct($this->product(), 'ZBook');
        (new \App\Jobs\EnrichPriceAvailabilityProductImageJob($saved->id))->failed(new \RuntimeException('Provider failed'));
        $this->getJson('/api/v1/product-discovery/products/12345/content?background=1')
            ->assertOk()->assertJsonPath('content_pending', false)
            ->assertJsonPath('warnings.0', 'Product image lookup failed. Please try again later.');
        \Illuminate\Support\Facades\Queue::assertNothingPushed();
    }

    public function test_verified_images_and_specs_persist_and_survive_search_and_failed_reverification(): void
    {
        $supplier = Mockery::mock(TDSynnexService::class)->makePartial();
        $saved = $supplier->saveDiscoveredProduct($this->product(), 'ZBook');
        $supplier->shouldReceive('discoverOnlineProductDetails')->twice()->andReturn([
            'description' => 'Verified workstation description',
            'specifications' => [['name' => 'Memory', 'value' => '32 GB']],
            'image_url' => 'https://images.example.com/verified.jpg',
            'image_candidates' => [['image_url' => 'https://images.example.com/verified.jpg']],
            'sources' => ['https://manufacturer.example.com/model'],
            'warnings' => [], 'online_details_found' => true,
        ]);
        $verifier = Mockery::mock(\App\Services\AzureOpenAiChatService::class);
        $verifier->shouldReceive('verifyProductEvidence')->once()->ordered()->andReturn([
            'description_matches' => true, 'specification_indices' => [0], 'image_index' => 0,
        ]);
        $verifier->shouldReceive('verifyProductEvidence')->once()->ordered()->andReturnNull();
        $this->app->instance(\App\Services\AzureOpenAiChatService::class, $verifier);
        (new \App\Jobs\EnrichPriceAvailabilityProductImageJob($saved->id))->handle($supplier);
        $this->assertSame('openai-verified-online', $saved->fresh()->images[0]['source']);
        $this->assertSame('https://images.example.com/verified.jpg', $saved->fresh()->images[0]['imageUrl']);
        $supplier->saveDiscoveredProduct([...$this->product(), 'availableQuantity' => 2], 'ZBook again');
        $this->travel(11)->minutes();
        $supplier->enrichDiscoveredProduct($saved->fresh());
        $fresh = $saved->fresh();
        $this->assertSame('Verified workstation description', $fresh->description);
        $this->assertSame('32 GB', $fresh->specifications['online_specifications'][0]['value']);
        $this->assertSame('verified', $fresh->specifications['online_content_status']);
        $this->assertSame('https://images.example.com/verified.jpg', $fresh->images[0]['imageUrl']);
        $this->assertSame(2, $fresh->quantity);
        $this->assertSame(1, \App\Models\Product::count());
    }

    public function test_stock_filtering_happens_before_pagination_and_refills_from_later_supplier_batches(): void
    {
        config(['tdsynnex.price_availability.batch_size' => 50]);
        $supplier = Mockery::mock(TDSynnexService::class)->makePartial();
        $supplier->shouldReceive('discoveryCandidateSkus')->andReturn(array_map('strval', range(1, 90)));
        $rows = array_map(fn (int $sku) => [
            ...$this->product(), 'productId' => (string) $sku,
            'availableQuantity' => $sku >= 46 && $sku <= 70 ? 4 : 0,
            'discontinueProduct' => $sku === 55,
        ], range(1, 90));
        $supplier->shouldReceive('searchPriceAvailabilityCatalog')->once()
            ->with('HP ZBook', 50, false, true, true, 1)->andReturn(array_slice($rows, 0, 50));
        $supplier->shouldReceive('searchPriceAvailabilityCatalog')->once()
            ->with('HP ZBook', 50, false, true, true, 2)->andReturn(array_slice($rows, 50));

        $all = [];
        foreach ([1 => 10, 2 => 10, 3 => 4] as $page => $count) {
            $result = $supplier->discoverInStockPage('HP ZBook', 10, $page);
            $this->assertCount($count, $result['products']);
            $this->assertSame(24, $result['total']);
            $this->assertFalse($result['total_is_estimate']);
            $this->assertSame($page < 3, $result['has_more']);
            $all = [...$all, ...$result['products']];
        }
        $this->assertCount(24, array_unique(array_column($all, 'productId')));
        foreach ($all as $product) {
            $this->assertGreaterThan(0, $product['availableQuantity']);
            $this->assertFalse($product['discontinueProduct']);
        }
    }

    public function test_stock_pagination_marks_unscreened_totals_as_estimates_without_fetching_every_candidate(): void
    {
        config(['tdsynnex.price_availability.batch_size' => 50]);
        $supplier = Mockery::mock(TDSynnexService::class)->makePartial();
        $supplier->shouldReceive('discoveryCandidateSkus')->once()->andReturn(array_map('strval', range(1, 100)));
        $supplier->shouldReceive('searchPriceAvailabilityCatalog')->once()
            ->with('HP ZBook', 50, false, true, true, 1)
            ->andReturn(array_map(fn (int $sku) => [...$this->product(), 'productId' => (string) $sku], range(1, 50)));

        $result = $supplier->discoverInStockPage('HP ZBook', 10, 1);
        $this->assertCount(10, $result['products']);
        $this->assertSame(50, $result['total']);
        $this->assertTrue($result['total_is_estimate']);
        $this->assertTrue($result['has_more']);
    }

    public function test_details_only_accept_a_supplier_identity_from_the_search(): void
    {
        $supplier = Mockery::mock(TDSynnexService::class)->makePartial();
        $supplier->shouldReceive('searchPriceAvailabilityCatalog')->once()->with('ZBook', 10, false, true, true)->andReturn([$this->product()]);
        $supplier->shouldNotReceive('discoverOnlineProductDetails');
        $this->app->instance(TDSynnexService::class, $supplier);

        $this->getJson('/api/v1/product-discovery/details?search=ZBook&product_id=other')->assertNotFound();
    }

    public function test_supplier_failures_do_not_expose_internal_error_details(): void
    {
        $supplier = Mockery::mock(TDSynnexService::class);
        $supplier->shouldReceive('searchPriceAvailabilityCatalog')->once()
            ->andThrow(new TDSynnexApiException('Private supplier endpoint and credentials'));
        $this->app->instance(TDSynnexService::class, $supplier);

        $this->getJson('/api/v1/product-discovery?search=ZBook')
            ->assertStatus(503)
            ->assertJsonPath('success', false)
            ->assertDontSee('Private supplier');
    }

    public function test_invalid_search_is_rejected_before_supplier_lookup(): void
    {
        $this->getJson('/api/v1/product-discovery?search=')->assertUnprocessable();
        $this->getJson('/api/v1/product-discovery?search='.str_repeat('a', 201))->assertUnprocessable();
        $this->getJson('/api/v1/product-discovery?search=ZBook&page=0')->assertUnprocessable();
        $this->getJson('/api/v1/product-discovery?search=ZBook&per_page=101')->assertUnprocessable();
    }

    private function configureOnlineProviders(): void
    {
        $verifier = Mockery::mock(\App\Services\AzureOpenAiChatService::class);
        $verifier->shouldReceive('verifyProductEvidence')->andReturnUsing(static fn (array $product, array $details) => [
            'description_matches' => true,
            'specification_indices' => array_keys($details['specifications']),
            'image_index' => empty($details['image_candidates']) ? null : 0,
        ]);
        $this->app->instance(\App\Services\AzureOpenAiChatService::class, $verifier);
        config([
            'tdsynnex.icecat.enabled' => true,
            'tdsynnex.icecat.username' => 'test-user',
            'tdsynnex.icecat.password' => 'test-password',
            'tdsynnex.icecat.endpoint' => 'https://live.icecat.biz/api',
            'tdsynnex.serpapi.enabled' => false,
        ]);
        Http::preventStrayRequests();
    }

    public function test_details_return_online_description_specifications_and_original_image_url_and_are_cached(): void
    {
        $this->configureOnlineProviders();
        Http::fake([
            'live.icecat.biz/*' => Http::response(['data' => [
                'Image' => ['HighPic' => 'https://images.icecat.biz/product.jpg'],
                'GeneralInfo' => ['Description' => ['LongDesc' => '<p>Online product description</p>']],
                'FeaturesGroups' => [[
                    'Features' => [[
                        'Feature' => ['Name' => ['Value' => 'Memory']],
                        'PresentationValue' => '32 GB',
                    ]],
                ]],
            ]]),
        ]);
        $supplier = Mockery::mock(TDSynnexService::class)->makePartial();
        $supplier->shouldReceive('searchPriceAvailabilityCatalog')->twice()
            ->with('ZBook', 10, false, true, true)->andReturn([$this->product()]);
        $this->app->instance(TDSynnexService::class, $supplier);

        for ($attempt = 0; $attempt < 2; $attempt++) {
            $saved = $supplier->saveDiscoveredProduct($this->product(), 'ZBook');
            $supplier->enrichDiscoveredProduct($saved);
            $this->assertSame('Online product description', $saved->fresh()->description);
            $this->getJson('/api/v1/product-discovery/details?search=ZBook&product_id=12345')
                ->assertOk()
                ->assertJsonPath('data.description', 'Online product description')
                ->assertJsonPath('data.specifications.0.value', '32 GB')
                ->assertJsonPath('data.image_url', 'https://images.icecat.biz/product.jpg')
                ->assertJsonPath('data.online_details_found', true)
                ->assertJsonPath('data.productPrice.0.rsPrice', 100)
                ->assertJsonPath('data.availableQuantity', 4)
                ->assertJsonPath('data.internet_search_query', 'HP ZBook Workstation')
                ->assertJsonPath('data.warnings.0', 'Internet title search is not configured. Enable SerpAPI and configure its API key to search the supplier product title for specifications and images.');
        }
        Http::assertSentCount(1);
    }

    public function test_description_and_specs_are_not_discarded_when_online_image_is_missing(): void
    {
        $this->configureOnlineProviders();
        Http::fake(['live.icecat.biz/*' => Http::response(['data' => [
            'GeneralInfo' => ['Description' => ['LongDesc' => 'Description without an image']],
        ]])]);

        $details = app(TDSynnexService::class)->discoverOnlineProductDetails($this->product());
        $this->assertSame('Description without an image', $details['description']);
        $this->assertSame('', $details['image_url']);
        $this->assertNotEmpty($details['warnings']);
    }

    public function test_missing_online_content_is_explicit_and_does_not_use_supplier_summary_as_online_description(): void
    {
        $this->configureOnlineProviders();
        Http::fake(['live.icecat.biz/*' => Http::response(['msg' => 'no_product'])]);

        $details = app(TDSynnexService::class)->discoverOnlineProductDetails($this->product());
        $this->assertFalse($details['online_details_found']);
        $this->assertSame('', $details['description']);
        $this->assertSame([], $details['specifications']);
        $this->assertNotEmpty($details['warnings']);
    }

    public function test_web_fallback_uses_exact_part_results_and_rejects_unsafe_image_urls(): void
    {
        $this->configureOnlineProviders();
        config(['tdsynnex.serpapi.enabled' => true, 'tdsynnex.serpapi.api_key' => 'test-key']);
        Http::fake([
            'live.icecat.biz/*' => Http::response(['msg' => 'no_product']),
            'serpapi.com/*' => Http::response([
                'organic_results' => [
                    ['title' => 'Different ABC1234 model', 'link' => 'https://example.com/ABC1234', 'snippet' => 'Wrong model'],
                    ['title' => 'HP ABC123', 'link' => 'https://example.com/ABC123', 'snippet' => 'Exact model description'],
                ],
                'images_results' => [
                    ['title' => 'HP ABC123', 'link' => 'https://example.com/ABC123', 'original' => 'http://127.0.0.1/private.jpg'],
                    ['title' => 'HP ABC123', 'link' => 'https://example.com/ABC123', 'original' => 'https://images.example.com/ABC123.jpg'],
                ],
            ]),
        ]);

        $details = app(TDSynnexService::class)->discoverOnlineProductDetails($this->product());
        $this->assertSame('Exact model description', $details['description']);
        $this->assertSame('https://images.example.com/ABC123.jpg', $details['image_url']);
        $this->assertSame('https://example.com/ABC123', $details['sources'][0]['url']);
        Http::assertSentCount(3);
    }

    public function test_regional_part_numbers_accept_model_pages_without_the_region_suffix_but_not_longer_part_numbers(): void
    {
        $this->configureOnlineProviders();
        config(['tdsynnex.icecat.enabled' => false, 'tdsynnex.serpapi.enabled' => true, 'tdsynnex.serpapi.api_key' => 'test-key']);
        Http::fake(['serpapi.com/*' => Http::response([
            'organic_results' => [
                ['title' => 'Different ABC1234 model', 'link' => 'https://example.com/ABC1234', 'snippet' => 'Wrong model'],
                ['title' => 'HP ABC123', 'link' => 'https://example.com/ABC123', 'snippet' => 'Exact physical model evidence'],
            ],
            'images_results' => [[
                'title' => 'HP ABC123ABA', 'link' => 'https://example.com/ABC123ABA',
                'original' => 'https://images.example.com/ABC123ABA.jpg',
            ]],
        ])]);
        $details = app(TDSynnexService::class)->discoverOnlineProductDetails([...$this->product(), 'mfgPartNo' => 'ABC123#ABA']);
        $this->assertSame('Exact physical model evidence', $details['description']);
        $this->assertSame('https://images.example.com/ABC123ABA.jpg', $details['image_candidates'][0]['image_url']);
    }

    public function test_supplier_title_drives_both_internet_queries_and_returns_specifications_without_a_part_number(): void
    {
        $this->configureOnlineProviders();
        config(['tdsynnex.icecat.enabled' => false, 'tdsynnex.serpapi.enabled' => true, 'tdsynnex.serpapi.api_key' => 'test-key']);
        $product = [...$this->product(), 'productName' => 'HP ZBOOK 8 G1AS W11 PRO', 'mfgPartNo' => ''];
        Http::fake(['serpapi.com/*' => Http::response([
            'organic_results' => [
                ['title' => 'HP ZBook 9 G1AS W11 PRO', 'link' => 'https://example.com/wrong', 'snippet' => 'Memory: 8 GB'],
                [
                    'title' => 'HP ZBook 8 G1AS W11 PRO',
                    'link' => 'https://example.com/zbook',
                    'snippet' => 'Processor: Ryzen 7; Memory: 32 GB; Storage: 1 TB SSD',
                    'rich_snippet' => ['top' => ['detected_extensions' => ['screen_size' => '14 inches', 'rating' => 5, 'price' => 1200]]],
                ],
            ],
            'images_results' => [[
                'title' => 'HP ZBook 8 G1AS W11 PRO',
                'link' => 'https://example.com/zbook',
                'original' => 'https://images.example.com/zbook.jpg',
            ]],
        ])]);

        $details = app(TDSynnexService::class)->discoverOnlineProductDetails($product);
        $this->assertSame('HP ZBOOK 8 G1AS W11 PRO', $details['internet_search_query']);
        $this->assertSame('https://images.example.com/zbook.jpg', $details['image_url']);
        $specs = collect($details['specifications'])->pluck('value', 'name')->all();
        $this->assertSame(['Screen Size' => '14 inches', 'Processor' => 'Ryzen 7', 'Memory' => '32 GB', 'Storage' => '1 TB SSD'], $specs);
        Http::assertSent(fn ($request) => $request['engine'] === 'google' && $request['q'] === 'HP ZBOOK 8 G1AS W11 PRO specifications');
        Http::assertSent(fn ($request) => $request['engine'] === 'google_images' && $request['q'] === 'HP ZBOOK 8 G1AS W11 PRO');
        Http::assertSentCount(2);
    }

    public function test_internet_title_search_supplements_complete_icecat_data_without_overwriting_its_specs(): void
    {
        $this->configureOnlineProviders();
        config(['tdsynnex.serpapi.enabled' => true, 'tdsynnex.serpapi.api_key' => 'test-key']);
        Http::fake([
            'live.icecat.biz/*' => Http::response(['data' => [
                'Image' => ['HighPic' => 'https://images.icecat.biz/product.jpg'],
                'GeneralInfo' => ['Description' => ['LongDesc' => 'Icecat description']],
                'FeaturesGroups' => [['Features' => [[
                    'Feature' => ['Name' => ['Value' => 'Memory']], 'PresentationValue' => '32 GB',
                ]]]],
            ]]),
            'serpapi.com/*' => Http::response([
                'organic_results' => [[
                    'title' => 'HP ABC123',
                    'link' => 'https://example.com/ABC123',
                    'snippet' => 'Memory: 16 GB; Storage: 1 TB SSD',
                ]],
                'images_results' => [[
                    'title' => 'HP ABC123',
                    'link' => 'https://example.com/ABC123',
                    'original' => 'https://images.example.com/ABC123.jpg',
                ]],
            ]),
        ]);

        $details = app(TDSynnexService::class)->discoverOnlineProductDetails($this->product());
        $this->assertSame('Icecat description', $details['description']);
        $this->assertSame(['Memory' => '32 GB', 'Storage' => '1 TB SSD'], collect($details['specifications'])->pluck('value', 'name')->all());
        $this->assertSame([], $details['warnings']);
        Http::assertSentCount(3);
    }

    public function test_title_search_cache_changes_when_the_supplier_title_changes(): void
    {
        $this->configureOnlineProviders();
        config(['tdsynnex.icecat.enabled' => false, 'tdsynnex.serpapi.enabled' => true, 'tdsynnex.serpapi.api_key' => 'test-key']);
        Http::fake(['serpapi.com/*' => Http::response(['organic_results' => [], 'images_results' => []])]);
        $supplier = app(TDSynnexService::class);
        $supplier->discoverOnlineProductDetails($this->product());
        $supplier->discoverOnlineProductDetails([...$this->product(), 'productName' => 'HP ZBook Updated Title']);
        Http::assertSentCount(4);
    }

    public function test_exact_sku_missing_from_ap_files_is_queried_but_zero_stock_is_not_displayed(): void
    {
        config([
            'tdsynnex.price_availability.flat_file_path' => '',
            'tdsynnex.price_availability.flat_files_dir' => 'tests/fixtures/no-ap-files',
            'tdsynnex.price_availability.customer_no' => 'test-customer',
            'tdsynnex.price_availability.username' => 'test-user',
            'tdsynnex.price_availability.password' => 'test-password',
        ]);
        $supplier = Mockery::mock(TDSynnexService::class)->makePartial();
        $supplier->shouldReceive('postPriceAvailabilityXml')->once()
            ->withArgs(fn (string $xml, string $region, bool $test) => str_contains($xml, '<synnexSKU>15428226</synnexSKU>'))
            ->andReturn(['PriceAvailabilityList' => [
                'synnexSKU' => '15428226',
                'mfgPN' => 'BX8M7UTR#ABA',
                'status' => 'Active',
                'description' => 'MANUFACTURER RENEWED HP ZBOOK 8 14 INCH',
                'totalQuantity' => '0',
            ]]);
        $this->app->instance(TDSynnexService::class, $supplier);

        $this->getJson('/api/v1/product-discovery?search=15428226')
            ->assertOk()
            ->assertJsonPath('data', [])
            ->assertJsonPath('pagination.total', 0);
    }

    public function test_unknown_direct_sku_does_not_become_a_product_from_a_supplier_error_row(): void
    {
        config([
            'tdsynnex.price_availability.customer_no' => 'test-customer',
            'tdsynnex.price_availability.username' => 'test-user',
            'tdsynnex.price_availability.password' => 'test-password',
        ]);
        $supplier = Mockery::mock(TDSynnexService::class)->makePartial();
        $supplier->shouldReceive('postPriceAvailabilityXml')->once()->andReturn([
            'PriceAvailabilityList' => ['synnexSKU' => '99999999', 'status' => 'Not Found'],
        ]);
        $this->app->instance(TDSynnexService::class, $supplier);

        $this->getJson('/api/v1/product-discovery?search=99999999')
            ->assertOk()->assertJsonPath('data', []);
    }

    public function test_relaxed_titles_keep_product_family_but_rank_exact_configurations_and_laptops_before_accessories(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'discovery-ranked-');
        $rows = [];
        foreach ([
            ['10001', 'HP ZBOOK 8 G1AS W11 PRO AC ADAPTER'],
            ['10002', 'MANUFACTURER RENEWED HP ZBOOK 8 14 INCH'],
            ['10003', 'HP ZBOOK 8 G1AS W11 PRO'],
            ['10004', 'HP ELITEBOOK 8 G1AS W11 PRO'],
            ['10005', 'DELL ZBOOK 8 G1AS W11 PRO'],
        ] as [$sku, $title]) {
            $parts = array_fill(0, 37, '');
            $parts[1] = 'DTL';
            $parts[2] = 'PART-'.$sku;
            $parts[4] = $sku;
            $parts[6] = $title;
            $parts[7] = $sku === '10005' ? 'DELL' : 'HP';
            $rows[] = implode('~', $parts);
        }
        file_put_contents($file, implode("\n", $rows));
        config(['tdsynnex.price_availability.flat_file_path' => $file]);
        $method = new ReflectionMethod(TDSynnexService::class, 'findMatchingSkusInApFiles');

        try {
            $matches = $method->invoke(app(TDSynnexService::class), 'hp zbook 8 g1as w11 pro', 3, true);
            $this->assertSame(['10003', '10002', '10001'], $matches);
            $this->assertSame(['10001'], $method->invoke(app(TDSynnexService::class), 'PART-10001', 3, true));
            $this->assertSame([], $method->invoke(app(TDSynnexService::class), 'hp zbook unknown-model', 3, true));
            \Illuminate\Support\Facades\Cache::put('tdsynnex:discovery:verified-identities', [
                '15428226' => ['sku' => '15428226', 'title' => 'HP ZBOOK 8', 'part_number' => 'BX8M7UTR#ABA'],
            ]);
            $this->assertSame(['10003'], $method->invoke(app(TDSynnexService::class), 'hp zbook 8 g1as w11 pro', 1, true));
        } finally {
            unlink($file);
        }
    }

    public function test_relaxed_title_can_find_recently_verified_sku_missing_from_ap_index(): void
    {
        config([
            'tdsynnex.price_availability.flat_file_path' => '',
            'tdsynnex.price_availability.flat_files_dir' => 'tests/fixtures/no-ap-files',
            'tdsynnex.price_availability.customer_no' => 'test-customer',
            'tdsynnex.price_availability.username' => 'test-user',
            'tdsynnex.price_availability.password' => 'test-password',
        ]);
        $supplier = Mockery::mock(TDSynnexService::class)->makePartial();
        $supplier->shouldReceive('postPriceAvailabilityXml')->twice()->andReturn([
            'PriceAvailabilityList' => [
                'synnexSKU' => '15428226',
                'mfgPN' => 'BX8M7UTR#ABA',
                'status' => 'Active',
                'description' => 'MANUFACTURER RENEWED HP ZBOOK 8 14 INCH',
            ],
        ]);
        $supplier->searchPriceAvailabilityCatalog('15428226', 10, false, false, true);
        $products = $supplier->searchPriceAvailabilityCatalog('HP ZBOOK 8 G1AS W11 PRO', 10, false, false, true);
        $this->assertSame('15428226', $products[0]['productId']);
    }

    public function test_live_supplier_results_are_ranked_even_if_api_returns_accessories_first(): void
    {
        \Illuminate\Support\Facades\Cache::put('tdsynnex:discovery:verified-identities', [
            '15428226' => ['sku' => '15428226', 'title' => 'HP ZBOOK 8', 'part_number' => 'BX8M7UTR#ABA'],
        ]);
        config([
            'tdsynnex.price_availability.flat_file_path' => '',
            'tdsynnex.price_availability.flat_files_dir' => 'tests/fixtures/no-ap-files',
            'tdsynnex.price_availability.customer_no' => 'test-customer',
            'tdsynnex.price_availability.username' => 'test-user',
            'tdsynnex.price_availability.password' => 'test-password',
        ]);
        $supplier = Mockery::mock(TDSynnexService::class)->makePartial();
        $supplier->shouldReceive('postPriceAvailabilityXml')->andReturn([
            'PriceAvailabilityList' => [
                ['synnexSKU' => '10001', 'description' => 'HP ZBOOK 8 G1AS W11 PRO AC ADAPTER'],
                ['synnexSKU' => '15428226', 'description' => 'MANUFACTURER RENEWED HP ZBOOK 8 14 INCH'],
                ['synnexSKU' => '10003', 'description' => 'HP ZBOOK 8 G1AS W11 PRO'],
            ],
        ]);

        $products = $supplier->searchPriceAvailabilityCatalog('HP ZBOOK 8 G1AS W11 PRO', 10, false, false, true);
        $this->assertSame(['10003', '15428226', '10001'], array_column($products, 'productId'));
    }
}
