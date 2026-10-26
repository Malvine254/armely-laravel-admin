<?php

namespace Tests\Feature;

use App\Console\Commands\SyncExcelCatalogFilesCommand;
use App\Http\Middleware\EnsureUserIsActive;
use App\Jobs\EnrichProductContentJob;
use App\Jobs\QueueMissingProductContentJob;
use App\Models\Product;
use App\Models\User;
use App\Services\AzureGraphMailService;
use App\Services\AzureOpenAiChatService;
use App\Services\ProductContentEnrichmentService;
use App\Services\TDSynnexService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schema;
use Mockery;
use Tests\TestCase;

class ProductContentEnrichmentTest extends TestCase
{
    private int $nextSku = 81000000;

    protected function setUp(): void
    {
        parent::setUp();
        // Any attempt to send a sync email during content lookup fails the test.
        $this->app->instance(AzureGraphMailService::class, Mockery::mock(AzureGraphMailService::class));
        $this->withoutMiddleware(EnsureUserIsActive::class);
        Http::preventStrayRequests();
        Schema::create('app_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
        Schema::create('products', function (Blueprint $table): void {
            $table->id();
            $table->integer('tdsynnex_product_id')->unique();
            $table->integer('tdsynnex_sku_no')->nullable();
            $table->string('vendor_id')->default('TD SYNNEX');
            $table->string('product_name');
            $table->string('manufacturer')->nullable();
            $table->string('mfg_part_no')->nullable();
            $table->text('description')->nullable();
            $table->json('images')->nullable();
            $table->json('specifications')->nullable();
            $table->decimal('base_price', 12, 2)->default(100);
            $table->decimal('retail_price', 12, 2)->nullable();
            $table->integer('quantity')->default(5);
            $table->boolean('is_available')->default(true);
            $table->timestamp('content_enrichment_attempted_at')->nullable();
            $table->timestamp('description_enrichment_attempted_at')->nullable();
            $table->timestamp('images_enrichment_attempted_at')->nullable();
            $table->timestamps();
        });
    }

    private function product(array $attributes = []): Product
    {
        return Product::create(array_merge([
            'tdsynnex_product_id' => ++$this->nextSku,
            'product_name' => 'HP ZBook 8 workstation',
            'manufacturer' => 'HP',
            'mfg_part_no' => 'ABC123',
            'images' => [],
            'specifications' => [],
        ], $attributes));
    }

    private function providers(array $details, ?string $generated = null): ProductContentEnrichmentService
    {
        $supplier = Mockery::mock(TDSynnexService::class);
        $supplier->shouldReceive('verifiedOnlineProductDetails')->once()->andReturn(array_merge([
            'description' => '', 'image_url' => '',
            'specifications' => $generated === null ? [] : [['name' => 'Memory', 'value' => '32 GB']],
            'sources' => [], 'warnings' => [],
        ], $details));
        $ai = Mockery::mock(AzureOpenAiChatService::class);
        if ($generated !== null) {
            $ai->shouldReceive('generateProductDescription')->once()->andReturn($generated);
        } else {
            $ai->shouldReceive('generateProductDescription')->zeroOrMoreTimes()->andReturnNull();
        }

        return new ProductContentEnrichmentService($supplier, $ai);
    }

    private function admin(): void
    {
        $user = new User(['name' => 'Admin', 'role' => 'admin', 'status' => 'active']);
        $user->id = 1;
        $this->actingAs($user, 'sanctum');
    }

    private function configureAi(bool $configured = true): void
    {
        config(['services.azure_openai' => [
            'endpoint' => 'https://enrichment.example.com',
            'api_key' => 'test-key',
            'deployment' => $configured ? 'test-model' : '',
        ]]);
    }

    public function test_verified_content_is_persisted_without_changing_prices_or_stock(): void
    {
        $product = $this->product();
        $this->providers([
            'description' => 'Verified HP ZBook 8 workstation description.',
            'image_url' => 'https://images.example.com/zbook.jpg',
            'sources' => [['name' => 'Manufacturer', 'url' => 'https://hp.com/zbook']],
        ])->enrich($product);
        $product->refresh();
        $this->assertSame('Verified HP ZBook 8 workstation description.', $product->description);
        $this->assertSame('openai-verified-online', $product->images[0]['source']);
        $this->assertSame('complete', $product->specifications['content_enrichment_status']);
        $this->assertSame('openai-verified-online', $product->specifications['description_source']);
        $this->assertNotNull($product->content_enrichment_attempted_at);
        $this->assertSame('100.00', $product->base_price);
        $this->assertSame(5, $product->quantity);
        $this->assertNotNull(Cache::get('catalog:price_version'));
    }

    public function test_title_only_description_is_generated_but_uncertain_images_stay_missing(): void
    {
        $product = $this->product(['description' => '  hp zbook 8 WORKSTATION  ']);
        $this->providers([], 'HP ZBook 8 workstation from HP, manufacturer part ABC123.')->enrich($product);
        $product->refresh();
        $this->assertFalse($product->needsDescription());
        $this->assertSame('ai-generated-from-verified-web-evidence', $product->specifications['description_source']);
        $this->assertSame([], $product->images);
        $this->assertSame('needs_review', $product->specifications['content_enrichment_status']);
        $this->assertNotEmpty($product->specifications['content_enrichment_warnings']);
    }

    public function test_existing_description_is_preserved_when_filling_an_image(): void
    {
        $product = $this->product(['description' => 'Administrator supplied description.']);
        $this->providers(['description' => 'Different online text.', 'image_url' => 'https://images.example.com/zbook.jpg'])
            ->enrich($product);
        $this->assertSame('Administrator supplied description.', $product->fresh()->description);
        $this->assertSame('https://images.example.com/zbook.jpg', $product->fresh()->images[0]['imageUrl']);
    }

    public function test_missing_website_evidence_uses_a_labeled_title_fallback_and_preserves_it_on_import(): void
    {
        $product = $this->product();
        $description = 'The HP ZBook workstation is a computer for running desktop applications.';
        $supplier = Mockery::mock(TDSynnexService::class);
        $supplier->shouldReceive('verifiedOnlineProductDetails')->once()->andReturn([
            'warnings' => ['No website match found.'],
        ]);
        $ai = Mockery::mock(AzureOpenAiChatService::class);
        $ai->shouldReceive('generateProductDescription')->once()->with(Mockery::on(
            fn (array $facts): bool => $facts['allow_title_fallback'] === true
                && $facts['product_name'] === $product->product_name
                && $facts['verified_website_description'] === ''
                && $facts['verified_specifications'] === []
        ))->andReturn($description);
        $result = (new ProductContentEnrichmentService($supplier, $ai))->processNext('description', 0);
        $product->refresh();
        $this->assertSame('complete', $result['result']);
        $this->assertSame($description, $product->description);
        $this->assertSame('ai-generated-from-title', $product->specifications['description_source']);
        $this->assertFalse($product->needsDescription());
        $this->assertSame(0, Product::missingDescription()->count());
        $this->assertStringContainsString('not verified against a website', implode(' ', $result['warnings']));
        $this->assertSame([], $product->images);
        $rows = Product::preserveEnrichedContent([[
            'tdsynnex_product_id' => $product->tdsynnex_product_id,
            'description' => '', 'specifications' => '{}',
        ]]);
        $this->assertSame($description, $rows[0]['description']);
        $this->assertSame('ai-generated-from-title', json_decode($rows[0]['specifications'], true)['description_source']);
    }

    public function test_existing_image_is_preserved_when_generating_a_description(): void
    {
        $images = [['imageUrl' => 'https://images.example.com/manual.jpg', 'source' => 'manual']];
        $product = $this->product(['images' => $images]);
        $this->providers(['image_url' => 'https://images.example.com/replacement.jpg'],
            'HP ZBook 8 workstation from HP, manufacturer part ABC123.')->enrich($product);
        $this->assertSame($images, $product->fresh()->images);
        $this->assertSame('complete', $product->fresh()->specifications['content_enrichment_status']);
    }

    public function test_content_added_by_admin_during_lookup_is_not_overwritten(): void
    {
        $product = $this->product();
        $supplier = Mockery::mock(TDSynnexService::class);
        $supplier->shouldReceive('verifiedOnlineProductDetails')->once()->andReturnUsing(function () use ($product): array {
            $product->fresh()->update(['description' => 'Manual description entered during lookup.',
                'images' => [['imageUrl' => 'https://images.example.com/manual.jpg']]]);

            return ['description' => 'Online description to ignore.', 'image_url' => 'https://images.example.com/online.jpg'];
        });
        $ai = Mockery::mock(AzureOpenAiChatService::class);
        $ai->shouldReceive('generateProductDescription')->once()->andReturnNull();
        (new ProductContentEnrichmentService($supplier, $ai))->enrich($product);
        $this->assertSame('Manual description entered during lookup.', $product->fresh()->description);
        $this->assertSame('https://images.example.com/manual.jpg', $product->fresh()->images[0]['imageUrl']);
    }

    public function test_generation_failure_is_explicit_and_leaves_description_missing(): void
    {
        $product = $this->product();
        $supplier = Mockery::mock(TDSynnexService::class);
        $supplier->shouldReceive('verifiedOnlineProductDetails')->once()->andReturn([]);
        $ai = Mockery::mock(AzureOpenAiChatService::class);
        $ai->shouldReceive('generateProductDescription')->once()->andReturnNull();
        (new ProductContentEnrichmentService($supplier, $ai))->enrich($product);
        $this->assertNull($product->fresh()->description);
        $this->assertSame('needs_review', $product->fresh()->specifications['content_enrichment_status']);
        $this->assertStringContainsString('could not approve a description', $product->fresh()->specifications['content_enrichment_warnings'][0]);
    }

    public function test_complete_products_do_not_call_any_provider(): void
    {
        $product = $this->product(['description' => 'Useful existing description.',
            'images' => [['imageUrl' => 'https://images.example.com/manual.jpg']]]);
        $service = new ProductContentEnrichmentService(Mockery::mock(TDSynnexService::class), Mockery::mock(AzureOpenAiChatService::class));
        $service->enrich($product);
        $this->assertNull($product->fresh()->content_enrichment_attempted_at);
    }

    public function test_catalog_queue_covers_all_vendors_honors_cooldown_and_prioritizes_unattempted_products(): void
    {
        Queue::fake();
        $old = $this->product(['content_enrichment_attempted_at' => now()->subDays(2)]);
        $this->product(['content_enrichment_attempted_at' => now()->subHours(23)]);
        $this->product(['description' => 'Useful existing description.',
            'images' => [['imageUrl' => 'https://images.example.com/manual.jpg']]]);
        $new = $this->product(['vendor_id' => 'Other supplier']);
        $service = app(ProductContentEnrichmentService::class);
        $this->assertSame(1, $service->queueMissing(1));
        Queue::assertPushed(EnrichProductContentJob::class, fn ($job) => $job->productId === $new->id);
        Queue::assertPushed(EnrichProductContentJob::class, 1);
        Queue::fake();
        $this->assertSame(2, $service->queueMissing());
        Queue::assertPushed(EnrichProductContentJob::class, fn ($job) => $job->productId === $old->id);
        Queue::assertPushed(EnrichProductContentJob::class, 2);
    }

    public function test_admin_can_queue_catalog_and_individual_retries_but_customers_and_guests_cannot(): void
    {
        Queue::fake();
        $this->configureAi();
        $product = $this->product();
        $this->postJson('/api/v1/admin/products/enrich-content')->assertUnauthorized();
        $this->actingAs(new User(['role' => 'customer']), 'sanctum');
        $this->postJson('/api/v1/admin/products/enrich-content')->assertForbidden();
        $this->postJson("/api/v1/admin/products/{$product->id}/enrich-content")->assertForbidden();
        $this->admin();
        $this->postJson('/api/v1/admin/products/enrich-content')->assertStatus(202);
        $this->postJson("/api/v1/admin/products/{$product->id}/enrich-content")->assertStatus(202);
        Queue::assertPushed(QueueMissingProductContentJob::class);
        Queue::assertPushed(EnrichProductContentJob::class, fn ($job) => $job->productId === $product->id);
    }

    public function test_missing_ai_configuration_is_reported_without_queuing(): void
    {
        Queue::fake();
        $this->configureAi(false);
        $this->admin();
        $this->postJson('/api/v1/admin/products/enrich-content')->assertStatus(422);
        $this->artisan('products:enrich-content')->assertFailed();
        Queue::assertNothingPushed();
    }

    public function test_admin_description_filter_and_coverage_counts_include_title_only_descriptions(): void
    {
        $this->product(['description' => '  ']);
        $this->product(['description' => ' hp ZBOOK 8 workstation ']);
        $this->product(['description' => 'A useful product description.']);
        $this->admin();
        $this->getJson('/api/v1/admin/products?missing_description=1')
            ->assertOk()->assertJsonPath('data.total', 2)
            ->assertJsonPath('stats.without_descriptions', 2)
            ->assertJsonPath('data.data.0.has_description', false);
    }

    public function test_terminal_job_failure_is_visible_and_does_not_fabricate_content(): void
    {
        $product = $this->product();
        (new EnrichProductContentJob($product->id))->failed(new \RuntimeException('Provider unavailable'));
        $this->assertSame('failed', $product->fresh()->specifications['content_enrichment_status']);
        $this->assertNotNull($product->fresh()->content_enrichment_attempted_at);
        $this->assertNull($product->fresh()->description);
    }

    public function test_enriched_content_survives_sync_payloads_while_supplier_fields_can_refresh(): void
    {
        $product = $this->product(['description' => 'AI description to preserve.',
            'images' => [['imageUrl' => 'https://images.example.com/verified.jpg', 'source' => 'openai-verified-online']],
            'specifications' => ['source' => 'old-feed', 'description_source' => 'ai-generated-from-catalog-facts',
                'content_enrichment_status' => 'complete', 'online_sources' => [['url' => 'https://hp.com']]]]);
        $rows = Product::preserveEnrichedContent([[
            'tdsynnex_product_id' => $product->tdsynnex_product_id,
            'description' => 'Supplier title only', 'images' => '[]',
            'base_price' => 150, 'specifications' => json_encode(['source' => 'new-feed']),
        ]]);
        $this->assertSame('AI description to preserve.', $rows[0]['description']);
        $this->assertSame($product->images, json_decode($rows[0]['images'], true));
        $spec = json_decode($rows[0]['specifications'], true);
        $this->assertSame('new-feed', $spec['source']);
        $this->assertSame('complete', $spec['content_enrichment_status']);
        $this->assertSame('ai-generated-from-catalog-facts', $spec['description_source']);
        $this->assertSame(150, $rows[0]['base_price']);
    }

    public function test_command_queues_a_bounded_batch_and_rejects_invalid_limits(): void
    {
        Queue::fake();
        $this->configureAi();
        $this->product();
        $this->product();
        $this->artisan('products:enrich-content', ['--limit' => 1])->assertSuccessful();
        Queue::assertPushed(EnrichProductContentJob::class, 1);
        $this->artisan('products:enrich-content', ['--limit' => -1])->assertExitCode(2);
    }

    public function test_real_ai_verification_pipeline_only_assigns_images_at_the_confidence_threshold(): void
    {
        $this->configureAi();
        $responses = Http::fakeSequence('enrichment.example.com/*');
        foreach ([0.949, 0.95] as $confidence) {
            $responses->push([
                'choices' => [['message' => ['content' => json_encode([
                    'description_matches' => true, 'specification_indices' => [],
                    'image_index' => 0, 'image_confidence' => $confidence,
                ])]]],
            ]);
            $responses->push(['choices' => [['message' => ['content' => '{}']]]]);
        }
        foreach ([0.949, 0.95] as $confidence) {
            $product = $this->product();
            $supplier = Mockery::mock(TDSynnexService::class)->makePartial();
            $supplier->shouldReceive('discoverOnlineProductDetails')->once()->andReturn([
                'description' => 'Verified HP ZBook 8 workstation description.',
                'specifications' => [], 'image_url' => 'https://images.example.com/zbook.jpg',
                'image_candidates' => [['image_url' => 'https://images.example.com/zbook.jpg',
                    'title' => 'HP ZBook 8 ABC123', 'source_url' => 'https://hp.com/zbook']],
                'sources' => [['name' => 'Manufacturer', 'url' => 'https://hp.com/zbook']],
                'warnings' => [], 'online_details_found' => true,
            ]);
            (new ProductContentEnrichmentService($supplier, app(AzureOpenAiChatService::class)))->enrich($product);
            $this->assertSame('Verified HP ZBook 8 workstation description.', $product->fresh()->description);
            if ($confidence < 0.95) {
                $this->assertSame([], $product->fresh()->images);
                $this->assertSame('needs_review', $product->fresh()->specifications['content_enrichment_status']);
            } else {
                $this->assertSame('https://images.example.com/zbook.jpg', $product->fresh()->images[0]['imageUrl']);
                $this->assertSame('complete', $product->fresh()->specifications['content_enrichment_status']);
            }
        }
    }

    public function test_excel_sync_persists_enriched_content_without_double_encoding_json(): void
    {
        $product = $this->product(['description' => 'AI description to preserve.',
            'images' => [['imageUrl' => 'https://images.example.com/verified.jpg', 'source' => 'openai-verified-online']],
            'specifications' => ['description_source' => 'ai-generated-from-catalog-facts']]);
        $save = new \ReflectionMethod(SyncExcelCatalogFilesCommand::class, 'saveOne');
        $save->invoke(new SyncExcelCatalogFilesCommand, [
            'tdsynnex_product_id' => $product->tdsynnex_product_id,
            'description' => 'Supplier title', 'images' => '[]', 'base_price' => 150,
            'specifications' => json_encode(['source' => 'new-feed']),
        ]);
        $product->refresh();
        $this->assertSame('AI description to preserve.', $product->description);
        $this->assertSame('150.00', $product->base_price);
        $this->assertSame('new-feed', $product->specifications['source']);
        $this->assertSame('ai-generated-from-catalog-facts', $product->specifications['description_source']);
        $this->assertSame('openai-verified-online', $product->images[0]['source']);
    }

    public function test_identity_changes_during_lookup_cannot_attach_old_model_content(): void
    {
        $product = $this->product();
        $supplier = Mockery::mock(TDSynnexService::class);
        $supplier->shouldReceive('verifiedOnlineProductDetails')->once()->andReturnUsing(function () use ($product): array {
            $product->fresh()->update(['mfg_part_no' => 'DIFFERENT-MODEL']);

            return ['description' => 'Old model description.', 'image_url' => 'https://images.example.com/old.jpg'];
        });
        $ai = Mockery::mock(AzureOpenAiChatService::class);
        $ai->shouldReceive('generateProductDescription')->once()->andReturnNull();
        try {
            (new ProductContentEnrichmentService($supplier, $ai))->enrich($product);
            $this->fail('Changed product identity should trigger a retry.');
        } catch (\RuntimeException $error) {
            $this->assertStringContainsString('identity changed', $error->getMessage());
        }
        $this->assertSame([], $product->fresh()->images);
        $this->assertNull($product->fresh()->description);
        $this->assertNull($product->fresh()->content_enrichment_attempted_at);
    }

    public function test_content_enrichment_migration_is_reversible(): void
    {
        $migration = require database_path('migrations/2026_10_26_120000_add_content_enrichment_attempted_at_to_products.php');
        $migration->down();
        $this->assertFalse(Schema::hasColumn('products', 'content_enrichment_attempted_at'));
        $migration->up();
        $this->assertTrue(Schema::hasColumn('products', 'content_enrichment_attempted_at'));
        $this->assertNull($this->product()->content_enrichment_attempted_at);
    }

    public function test_description_only_does_not_assign_images_and_image_only_does_not_generate_or_replace_descriptions(): void
    {
        $product = $this->product();
        $this->providers([
            'description' => 'Verified workstation description.',
            'image_url' => 'https://images.example.com/zbook.jpg',
        ])->enrich($product, 'description');
        $product->refresh();
        $this->assertSame('Verified workstation description.', $product->description);
        $this->assertSame([], $product->images);
        $this->assertNotNull($product->description_enrichment_attempted_at);
        $this->assertNull($product->images_enrichment_attempted_at);
        $this->assertSame('complete', $product->specifications['content_enrichment_result']);

        $imageOnly = $this->product();
        $this->providers([
            'description' => 'Must not be attached.',
            'image_url' => 'https://images.example.com/zbook.jpg',
        ])->enrich($imageOnly, 'images');
        $imageOnly->refresh();
        $this->assertNull($imageOnly->description);
        $this->assertSame('https://images.example.com/zbook.jpg', $imageOnly->images[0]['imageUrl']);
        $this->assertNotNull($imageOnly->images_enrichment_attempted_at);
        $this->assertNull($imageOnly->description_enrichment_attempted_at);
    }

    public function test_image_queue_cooldown_is_independent_of_description_attempts(): void
    {
        Queue::fake();
        $product = $this->product(['description_enrichment_attempted_at' => now(),
            'content_enrichment_attempted_at' => now()]);
        $service = app(ProductContentEnrichmentService::class);
        $this->assertSame(0, $service->queueMissing(0, 'description'));
        $this->assertSame(1, $service->queueMissing(0, 'images'));
        Queue::assertPushed(EnrichProductContentJob::class, fn ($job) => $job->productId === $product->id && $job->mode === 'images');
        $this->assertNotSame((new EnrichProductContentJob($product->id, 'images'))->uniqueId(),
            (new EnrichProductContentJob($product->id, 'description'))->uniqueId());
        $this->assertNotSame((new QueueMissingProductContentJob(0, 'images'))->uniqueId(),
            (new QueueMissingProductContentJob(0, 'description'))->uniqueId());
    }

    public function test_admin_enrichment_validates_and_propagates_selected_mode(): void
    {
        Queue::fake();
        $this->configureAi();
        $this->admin();
        $product = $this->product();
        $this->postJson('/api/v1/admin/products/enrich-content', ['mode' => 'images'])
            ->assertStatus(202)->assertJsonPath('mode', 'images');
        $this->postJson("/api/v1/admin/products/{$product->id}/enrich-content", ['mode' => 'description'])
            ->assertStatus(202)->assertJsonPath('mode', 'description');
        Queue::assertPushed(QueueMissingProductContentJob::class, fn ($job) => $job->mode === 'images');
        Queue::assertPushed(EnrichProductContentJob::class, fn ($job) => $job->mode === 'description');
        $this->postJson("/api/v1/admin/products/{$product->id}/enrich-content", ['mode' => 'invalid'])
            ->assertUnprocessable();
    }

    public function test_separate_attempt_migration_is_reversible(): void
    {
        $migration = require database_path('migrations/2026_10_26_140000_add_separate_content_attempts_to_products.php');
        $migration->down();
        $this->assertFalse(Schema::hasColumn('products', 'images_enrichment_attempted_at'));
        $migration->up();
        $this->assertTrue(Schema::hasColumn('products', 'images_enrichment_attempted_at'));
        $this->assertTrue(Schema::hasColumn('products', 'description_enrichment_attempted_at'));
    }

    public function test_catalog_only_fallback_is_not_ready_and_can_be_researched_again_immediately(): void
    {
        $product = $this->product([
            'description' => 'The HP ZBook 8 is a product manufactured by HP. No specifications were supplied.',
            'specifications' => ['description_source' => 'ai-generated-from-catalog-facts',
                'content_enrichment_status' => 'complete'],
            'description_enrichment_attempted_at' => now(),
            'content_enrichment_attempted_at' => now(),
        ]);
        $this->assertTrue($product->needsDescription());
        $this->assertSame(1, Product::missingDescription()->count());
        Queue::fake();
        $this->assertSame(1, app(ProductContentEnrichmentService::class)->queueMissing(0, 'description'));
        Queue::assertPushed(EnrichProductContentJob::class, fn ($job) => $job->productId === $product->id);
        $this->providers([
            'description' => 'Verified HP ZBook 8 workstation with 32 GB memory.',
        ])->enrich($product, 'description');
        $product->refresh();
        $this->assertFalse($product->needsDescription());
        $this->assertSame('openai-verified-online', $product->specifications['description_source']);
    }

    public function test_direct_processing_saves_one_product_per_request_without_queueing(): void
    {
        Queue::fake();
        $this->configureAi();
        $this->admin();
        $first = $this->product(['description_enrichment_attempted_at' => now()]);
        $second = $this->product();
        $supplier = Mockery::mock(TDSynnexService::class);
        $supplier->shouldReceive('verifiedOnlineProductDetails')->twice()->andReturn([
            'description' => 'Verified HP workstation with 32 GB memory.', 'warnings' => [],
        ]);
        $ai = Mockery::mock(AzureOpenAiChatService::class);
        $ai->shouldReceive('isConfigured')->andReturnTrue();
        $ai->shouldReceive('generateProductDescription')->twice()->andReturnNull();
        $this->app->instance(TDSynnexService::class, $supplier);
        $this->app->instance(AzureOpenAiChatService::class, $ai);
        foreach ([$first, $second] as $index => $product) {
            $this->postJson('/api/v1/admin/products/process-content', [
                'mode' => 'description', 'after_id' => $index === 0 ? 0 : $first->id,
            ])->assertOk()->assertJsonPath('data.processed', true)
                ->assertJsonPath('data.product_id', $product->id)->assertJsonPath('data.result', 'complete');
            $this->assertSame('Verified HP workstation with 32 GB memory.', $product->fresh()->description);
        }
        $this->postJson('/api/v1/admin/products/process-content', [
            'mode' => 'description', 'after_id' => $second->id,
        ])->assertOk()->assertJsonPath('data.done', true);
        Queue::assertNothingPushed();
    }

    public function test_direct_processing_repairs_only_tagged_bad_ai_text_and_keeps_a_backup_when_research_fails(): void
    {
        $bad = 'The HP ZBook is a product by HP. No other details were supplied.';
        $product = $this->product(['description' => $bad,
            'specifications' => ['description_source' => 'ai-generated-from-catalog-facts']]);
        $manual = $this->product(['description' => 'Manual product description.']);
        $service = $this->providers([]);
        $result = $service->processNext('description', 0);
        $this->assertSame('needs_review', $result['result']);
        $this->assertNull($product->fresh()->description);
        $this->assertSame($bad, $product->fresh()->specifications['description_repair_backup']);
        $rows = Product::preserveEnrichedContent([[
            'tdsynnex_product_id' => $product->tdsynnex_product_id,
            'description' => '', 'specifications' => json_encode(['source' => 'new-feed']),
        ]]);
        $this->assertSame($bad, json_decode($rows[0]['specifications'], true)['description_repair_backup']);
        $this->assertSame('Manual product description.', $manual->fresh()->description);
        $this->assertTrue($service->processNext('description', $product->id)['done']);
    }

    public function test_direct_image_processing_skips_usable_images_and_keeps_descriptions(): void
    {
        $existing = $this->product(['images' => [['imageUrl' => 'https://example.com/existing.jpg']]]);
        $missing = $this->product(['description' => 'Manual description.']);
        $service = $this->providers(['image_url' => 'https://example.com/verified.jpg']);
        $result = $service->processNext('images', 0);
        $this->assertSame($missing->id, $result['product_id']);
        $this->assertSame('complete', $result['result']);
        $this->assertSame('Manual description.', $missing->fresh()->description);
        $this->assertSame('https://example.com/verified.jpg', $missing->fresh()->images[0]['imageUrl']);
        $this->assertNull($existing->fresh()->images_enrichment_attempted_at);
        $this->assertTrue($service->processNext('images', $missing->id)['done']);
    }

    public function test_direct_processing_does_not_research_a_product_locked_by_another_request(): void
    {
        $product = $this->product();
        $service = new ProductContentEnrichmentService(
            Mockery::mock(TDSynnexService::class), Mockery::mock(AzureOpenAiChatService::class)
        );
        $lock = Cache::lock('store:content-product:'.$product->id, 180);
        $this->assertTrue($lock->get());
        try {
            $result = $service->processNext('description', 0);
            $this->assertFalse($result['processed']);
            $this->assertSame($product->id, $result['after_id']);
            $this->assertStringContainsString('another request', $result['message']);
            $this->assertNull($product->fresh()->content_enrichment_attempted_at);
        } finally {
            $lock->release();
        }
    }

    public function test_direct_processing_is_admin_only_and_reports_missing_migrations(): void
    {
        $this->configureAi();
        $this->postJson('/api/v1/admin/products/process-content', ['mode' => 'description', 'after_id' => 0])
            ->assertUnauthorized();
        $this->actingAs(new User(['role' => 'customer']), 'sanctum');
        $this->postJson('/api/v1/admin/products/process-content', ['mode' => 'description', 'after_id' => 0])
            ->assertForbidden();
        $this->admin();
        $this->postJson('/api/v1/admin/products/process-content', ['mode' => 'wrong', 'after_id' => -1])
            ->assertUnprocessable();
        Schema::table('products', fn (Blueprint $table) => $table->dropColumn('description_enrichment_attempted_at'));
        $this->postJson('/api/v1/admin/products/process-content', ['mode' => 'description', 'after_id' => 0])
            ->assertStatus(503)->assertJsonPath('success', false);
    }

    public function test_direct_processing_reports_product_failures_instead_of_success(): void
    {
        $this->configureAi();
        $this->admin();
        $this->product();
        $supplier = Mockery::mock(TDSynnexService::class);
        $supplier->shouldReceive('verifiedOnlineProductDetails')->once()->andThrow(new \RuntimeException('Provider failed.'));
        $this->app->instance(TDSynnexService::class, $supplier);
        $this->postJson('/api/v1/admin/products/process-content', ['mode' => 'description', 'after_id' => 0])
            ->assertStatus(503)->assertJsonPath('success', false);
    }

    public function test_repair_endpoint_rewrites_populated_ai_descriptions_and_preserves_manual_and_supplier_text(): void
    {
        Queue::fake();
        $this->admin();
        $old = 'HP workstation runs desktop applications. Choose a configuration that suits you.';
        $manual = $this->product(['description' => 'Administrator description.']);
        $supplierProduct = $this->product(['description' => 'Supplier excerpt.',
            'specifications' => ['description_source' => 'openai-verified-online']]);
        $sources = ['ai-generated-from-title', 'ai-generated-from-verified-web-evidence', 'ai-generated-from-catalog-facts'];
        $products = [];
        foreach ($sources as $source) {
            $products[] = $this->product(['description' => $old, 'images' => [['imageUrl' => 'https://example.com/original.jpg']],
                'specifications' => ['description_source' => $source],
                'description_enrichment_attempted_at' => now()]);
        }
        $description = "HP ZBook workstation.\n\n- RAM: 32 GB DDR4\n- Storage: 512 GB SSD\n- CPU: Intel Core i5";
        $supplier = Mockery::mock(TDSynnexService::class);
        $supplier->shouldReceive('verifiedOnlineProductDetails')->times(3)->andReturn([
            'description' => 'HP ZBook workstation with 32 GB DDR4 RAM, 512 GB SSD, Intel Core i5.',
        ]);
        $ai = Mockery::mock(AzureOpenAiChatService::class);
        $ai->shouldReceive('isConfigured')->andReturnTrue();
        $ai->shouldReceive('generateProductDescription')->times(3)->with(Mockery::on(
            fn (array $facts): bool => ! array_key_exists('description', $facts)
                && ! in_array($old, $facts, true)
                && $facts['verified_website_description'] !== ''
        ))->andReturn($description);
        $this->app->instance(TDSynnexService::class, $supplier);
        $this->app->instance(AzureOpenAiChatService::class, $ai);
        $afterId = 0;
        foreach ($products as $index => $product) {
            $this->postJson('/api/v1/admin/products/process-content', [
                'mode' => 'repair_descriptions', 'after_id' => $afterId,
            ])->assertOk()->assertJsonPath('data.product_id', $product->id)
                ->assertJsonPath('data.result', 'complete');
            $afterId = $product->id;
            $product->refresh();
            $this->assertSame($description, $product->description);
            $this->assertSame($old, $product->specifications['description_repair_backup']);
            $this->assertSame($sources[$index], $product->specifications['description_repair_backup_source']);
            $this->assertSame('updated', $product->specifications['description_repair_status']);
            $this->assertSame('ai-generated-from-verified-web-evidence', $product->specifications['description_source']);
            $this->assertSame('https://example.com/original.jpg', $product->images[0]['imageUrl']);
            $this->assertSame('100.00', $product->base_price);
            $this->assertSame(5, $product->quantity);
        }
        $this->postJson('/api/v1/admin/products/process-content', [
            'mode' => 'repair_descriptions', 'after_id' => $afterId,
        ])->assertOk()->assertJsonPath('data.done', true);
        $this->assertSame('Administrator description.', $manual->fresh()->description);
        $this->assertSame('Supplier excerpt.', $supplierProduct->fresh()->description);
        Queue::assertNothingPushed();
    }

    public function test_failed_repair_keeps_previous_ai_text_and_marks_it_for_review_even_with_existing_content(): void
    {
        $old = 'An existing AI description requiring review.';
        $product = $this->product(['description' => $old,
            'images' => [['imageUrl' => 'https://example.com/existing.jpg']],
            'specifications' => ['description_source' => 'ai-generated-from-title']]);
        // A raw excerpt cannot silently bypass the structured repair's failed AI check.
        $service = $this->providers(['description' => 'Unstructured verified excerpt.']);
        $result = $service->processNext('repair_descriptions', 0);
        $this->assertSame('needs_review', $result['result']);
        $this->assertSame($old, $product->fresh()->description);
        $this->assertSame('needs_review', $product->fresh()->specifications['description_repair_status']);
        $this->assertSame('needs_review', $product->fresh()->specifications['content_enrichment_status']);
        $this->assertStringContainsString('Previous description kept', $result['message']);
        $this->assertArrayNotHasKey('description_repair_backup', $product->fresh()->specifications);
    }

    public function test_repeated_repairs_keep_original_backup_and_latest_previous_text(): void
    {
        $product = $this->product(['description' => 'Earlier AI text.',
            'specifications' => ['description_source' => 'ai-generated-from-title',
                'description_repair_backup' => 'Original AI text.',
                'description_repair_backup_source' => 'ai-generated-from-catalog-facts']]);
        $description = "HP ZBook workstation.\n\n- RAM: 32 GB";
        $this->providers([], $description)->processNext('repair_descriptions', 0);
        $product->refresh();
        $this->assertSame('Original AI text.', $product->specifications['description_repair_backup']);
        $this->assertSame('Earlier AI text.', $product->specifications['description_repair_previous']);
        $rows = Product::preserveEnrichedContent([[
            'tdsynnex_product_id' => $product->tdsynnex_product_id,
            'description' => '', 'specifications' => '{}',
        ]]);
        $this->assertSame($description, $rows[0]['description']);
        $spec = json_decode($rows[0]['specifications'], true);
        $this->assertSame('Original AI text.', $spec['description_repair_backup']);
        $this->assertSame('Earlier AI text.', $spec['description_repair_previous']);
        $this->assertSame('updated', $spec['description_repair_status']);
    }

    public function test_repair_cannot_replace_a_manual_edit_made_during_research(): void
    {
        $product = $this->product(['description' => 'Old AI text.',
            'specifications' => ['description_source' => 'ai-generated-from-title']]);
        $supplier = Mockery::mock(TDSynnexService::class);
        $supplier->shouldReceive('verifiedOnlineProductDetails')->once()->andReturnUsing(function () use ($product): array {
            $product->fresh()->update(['description' => 'Manual edit during research.']);

            return [];
        });
        $ai = Mockery::mock(AzureOpenAiChatService::class);
        $ai->shouldReceive('generateProductDescription')->once()->andReturn("HP workstation.\n\n- RAM: 32 GB");
        try {
            (new ProductContentEnrichmentService($supplier, $ai))->processNext('repair_descriptions', 0);
            $this->fail('A concurrent edit must stop repair.');
        } catch (\RuntimeException $error) {
            $this->assertStringContainsString('Description changed during repair', $error->getMessage());
        }
        $this->assertSame('Manual edit during research.', $product->fresh()->description);
        $this->assertArrayNotHasKey('description_repair_backup', $product->fresh()->specifications);
    }

    public function test_background_discovery_cannot_overwrite_a_repaired_ai_description(): void
    {
        $description = "Memory upgrade module.\n\n- RAM: 16 GB DDR4\n- Memory speed: 3200 MHz";
        $product = $this->product(['description' => $description, 'specifications' => [
            'description_source' => 'ai-generated-from-title', 'description_repair_status' => 'updated',
            'description_repair_backup' => 'Old unclear description.',
        ]]);
        $supplier = Mockery::mock(TDSynnexService::class)->makePartial();
        $supplier->shouldReceive('verifiedOnlineProductDetails')->once()->andReturn([
            'description' => 'Long unstructured supplier paragraph.', 'image_url' => '', 'specifications' => [],
            'sources' => [], 'warnings' => [], 'online_details_found' => true,
        ]);
        $supplier->enrichDiscoveredProduct($product);
        $this->assertSame($description, $product->fresh()->description);
        $this->assertSame('Old unclear description.', $product->fresh()->specifications['description_repair_backup']);
    }
}
