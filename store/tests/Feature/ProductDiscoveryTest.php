<?php

namespace Tests\Feature;

use App\Exceptions\TDSynnexApiException;
use App\Services\TDSynnexService;
use Illuminate\Support\Facades\Http;
use Mockery;
use ReflectionMethod;
use Tests\TestCase;

class ProductDiscoveryTest extends TestCase
{
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
            'productPrice' => [['rsPrice' => 100]],
        ];
    }

    public function test_guest_can_search_supplier_outside_local_catalog_without_exposing_supplier_prices(): void
    {
        $supplier = Mockery::mock(TDSynnexService::class);
        $supplier->shouldReceive('searchPriceAvailabilityCatalog')->once()
            ->with('HP ZBook', 10, false, true, true)->andReturn([$this->product()]);
        $this->app->instance(TDSynnexService::class, $supplier);

        $this->getJson('/api/v1/product-discovery?search=HP%20ZBook')
            ->assertOk()
            ->assertJsonPath('data.0.part_number', 'ABC123')
            ->assertJsonMissingPath('data.0.productPrice');
    }

    public function test_empty_supplier_search_has_an_explicit_message(): void
    {
        $supplier = Mockery::mock(TDSynnexService::class);
        $supplier->shouldReceive('searchPriceAvailabilityCatalog')->once()->with('unknown', 10, false, true, true)->andReturn([]);
        $this->app->instance(TDSynnexService::class, $supplier);

        $this->getJson('/api/v1/product-discovery?search=unknown')
            ->assertOk()->assertJsonPath('data', [])
            ->assertJsonPath('message', 'No supplier match found. Try the manufacturer and exact model or part number.');
    }

    public function test_details_only_accept_a_supplier_identity_from_the_search(): void
    {
        $supplier = Mockery::mock(TDSynnexService::class);
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
    }

    private function configureOnlineProviders(): void
    {
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
            $this->getJson('/api/v1/product-discovery/details?search=ZBook&product_id=12345')
                ->assertOk()
                ->assertJsonPath('data.description', 'Online product description')
                ->assertJsonPath('data.specifications.0.value', '32 GB')
                ->assertJsonPath('data.image_url', 'https://images.icecat.biz/product.jpg')
                ->assertJsonPath('data.online_details_found', true)
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

    public function test_exact_sku_missing_from_ap_files_is_queried_directly_and_includes_renewed_zero_stock_laptops(): void
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
            ->assertJsonPath('data.0.product_id', '15428226')
            ->assertJsonPath('data.0.part_number', 'BX8M7UTR#ABA')
            ->assertJsonPath('data.0.name', 'MANUFACTURER RENEWED HP ZBOOK 8 14 INCH')
            ->assertJsonMissingPath('data.0.productPrice');
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
