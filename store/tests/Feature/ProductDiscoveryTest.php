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
            ->with('HP ZBook', 10, false)->andReturn([$this->product()]);
        $this->app->instance(TDSynnexService::class, $supplier);

        $this->getJson('/api/v1/product-discovery?search=HP%20ZBook')
            ->assertOk()
            ->assertJsonPath('data.0.part_number', 'ABC123')
            ->assertJsonMissingPath('data.0.productPrice');
    }

    public function test_empty_supplier_search_has_an_explicit_message(): void
    {
        $supplier = Mockery::mock(TDSynnexService::class);
        $supplier->shouldReceive('searchPriceAvailabilityCatalog')->once()->with('unknown', 10, false)->andReturn([]);
        $this->app->instance(TDSynnexService::class, $supplier);

        $this->getJson('/api/v1/product-discovery?search=unknown')
            ->assertOk()->assertJsonPath('data', [])
            ->assertJsonPath('message', 'No supplier match found. Try the manufacturer and exact model or part number.');
    }

    public function test_details_only_accept_a_supplier_identity_from_the_search(): void
    {
        $supplier = Mockery::mock(TDSynnexService::class);
        $supplier->shouldReceive('searchPriceAvailabilityCatalog')->once()->with('ZBook', 10, false)->andReturn([$this->product()]);
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
            ->with('ZBook', 10, false)->andReturn([$this->product()]);
        $this->app->instance(TDSynnexService::class, $supplier);

        for ($attempt = 0; $attempt < 2; $attempt++) {
            $this->getJson('/api/v1/product-discovery/details?search=ZBook&product_id=12345')
                ->assertOk()
                ->assertJsonPath('data.description', 'Online product description')
                ->assertJsonPath('data.specifications.0.value', '32 GB')
                ->assertJsonPath('data.image_url', 'https://images.icecat.biz/product.jpg')
                ->assertJsonPath('data.online_details_found', true)
                ->assertJsonPath('data.warnings', []);
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
}
