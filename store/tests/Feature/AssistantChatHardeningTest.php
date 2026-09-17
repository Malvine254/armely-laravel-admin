<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\AzureOpenAiChatService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AssistantChatHardeningTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge();
        DB::reconnect();

        config()->set('services.azure_openai.endpoint', '');
        config()->set('services.azure_openai.api_key', '');
        config()->set('services.azure_openai.deployment', '');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('status')->default('active');
            $table->string('role')->default('customer');
            $table->timestamp('email_verified_at')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('status')->default('approved');
            $table->timestamps();
        });

        Schema::create('chat_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('title')->default('New chat');
            $table->timestamp('last_message_at')->nullable();
            $table->boolean('escalated_to_human')->default(false);
            $table->timestamp('escalated_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('chat_session_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('role', 20);
            $table->text('content');
            $table->json('actions')->nullable();
            $table->json('attachments')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->foreign('chat_session_id')->references('id')->on('chat_sessions')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('chat_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('chat_message_id')->nullable();
            $table->string('disk', 32)->default('local');
            $table->string('path');
            $table->string('original_name');
            $table->string('mime_type', 128);
            $table->unsignedInteger('size_bytes');
            $table->text('extracted_text')->nullable();
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
            $table->string('tdsynnex_product_id')->nullable();
            $table->string('tdsynnex_sku_no')->nullable();
            $table->string('vendor_id')->nullable();
            $table->string('product_name')->nullable();
            $table->string('mfg_part_no')->nullable();
            $table->text('description')->nullable();
            $table->decimal('base_price', 12, 2)->default(0);
            $table->decimal('retail_price', 12, 2)->default(0);
            $table->decimal('sale_price', 12, 2)->default(0);
            $table->boolean('is_on_sale')->default(false);
            $table->string('offer_source')->nullable();
            $table->json('images')->nullable();
            $table->boolean('is_available')->nullable();
            $table->integer('quantity')->nullable();
            $table->boolean('is_discontinued')->nullable();
            $table->string('manufacturer')->nullable();
            $table->string('category_segment')->nullable();
            $table->json('specifications')->nullable();
            $table->timestamps();
        });
    }

    public function test_complex_laptop_request_excludes_accessories(): void
    {
        $user = $this->createCustomer('Laptop Search User', 'laptop-search@example.com');
        $this->insertCatalogProduct('LAPTOP-32', 'Contoso Business Notebook 14', 'Business notebook with 32 GB RAM, USB-C power delivery, and three-year warranty.', 1399, 'Laptops');
        $this->insertCatalogProduct('LAPTOP-16', 'Contoso Business Notebook 13', 'Business notebook with 16 GB RAM and USB-C power delivery.', 1099, 'Laptops');
        $this->insertCatalogProduct('USB-HUB', 'Add USB Type-C and three USB Type-A ports to your laptop', 'Portable USB hub for laptop peripherals.', 33.55, 'Computer Accessories');

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/messages/assistant/chat', [
            'message' => 'Compare three business laptops under $1,500 with 32 GB RAM, USB-C charging, and a three-year warranty.',
        ]);

        $response->assertOk();
        $productIds = collect($response->json('data.product_suggestions'))->pluck('product_id')->all();
        $this->assertContains('LAPTOP-32', $productIds, json_encode($productIds));
        $this->assertNotContains('LAPTOP-16', $productIds, json_encode($productIds));
        $this->assertNotContains('USB-HUB', $productIds, json_encode($productIds));
    }

    public function test_office_wifi_request_searches_infrastructure_categories(): void
    {
        $user = $this->createCustomer('WiFi Search User', 'wifi-search@example.com');
        $this->insertCatalogProduct('AP-1', 'Ceiling Wireless Access Point', 'WiFi 6 wireless AP for office deployments.', 449, 'Wireless Access Points');
        $this->insertCatalogProduct('ROUTER-1', 'Business Wireless Router', 'Secure wireless gateway for small offices.', 699, 'Routers');
        $this->insertCatalogProduct('SWITCH-1', '48-Port Managed Network Switch', 'Managed Ethernet switch with PoE for access points.', 899, 'Network Switches');

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/messages/assistant/chat', [
            'message' => 'I need Wi-Fi coverage for a two-floor office with 80 employees. What equipment should I consider?',
        ]);

        $response->assertOk();
        $productIds = collect($response->json('data.product_suggestions'))->pluck('product_id')->all();
        $this->assertContains('AP-1', $productIds, json_encode($productIds));
        $this->assertContains('ROUTER-1', $productIds, json_encode($productIds));
        $this->assertContains('SWITCH-1', $productIds, json_encode($productIds));
    }

    public function test_multi_category_request_returns_each_requested_product_type(): void
    {
        $user = $this->createCustomer('Bundle Search User', 'bundle-search@example.com');
        $this->insertCatalogProduct('MONITOR-1', '27-inch Business Monitor', 'USB-C office display.', 329, 'Monitors');
        $this->insertCatalogProduct('DOCK-1', 'Universal USB-C Docking Station', 'Laptop dock with dual display support.', 249, 'Docking Stations');
        $this->insertCatalogProduct('HEADSET-1', 'Teams Wireless Headset', 'Business headset with noise-cancelling microphone.', 199, 'Headsets');

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/messages/assistant/chat', [
            'message' => 'Find compatible monitors, docks, and headsets for the laptops you just showed me.',
        ]);

        $response->assertOk();
        $productIds = collect($response->json('data.product_suggestions'))->pluck('product_id')->all();
        $this->assertContains('MONITOR-1', $productIds);
        $this->assertContains('DOCK-1', $productIds);
        $this->assertContains('HEADSET-1', $productIds);
    }

    public function test_assistant_chat_marks_fallback_responses_as_degraded(): void
    {
        $companyId = \DB::table('companies')->insertGetId([
            'name' => 'Hardening Co',
            'status' => 'approved',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $user = User::query()->create([
            'name' => 'Chat User',
            'email' => 'chat-user@example.com',
            'password' => bcrypt('secret123'),
            'status' => 'active',
            'role' => 'customer',
            'company_id' => $companyId,
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/messages/assistant/chat', [
            'message' => 'How are you?',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.degraded', true)
            ->assertJsonPath('data.status', 'degraded')
            ->assertJsonPath('data.source', 'local_fallback')
            ->assertJsonPath('data.product_suggestions', []);

        $sessionId = $response->json('data.chat_session.id');
        $history = $this->actingAs($user, 'sanctum')->getJson("/api/v1/messages/chats/{$sessionId}");
        $history->assertOk();
        $this->assertTrue((bool) collect($history->json('data.messages'))->last()['degraded']);
    }

    public function test_small_talk_does_not_trigger_product_suggestions(): void
    {
        $companyId = \DB::table('companies')->insertGetId([
            'name' => 'Small Talk Co',
            'status' => 'approved',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $user = User::query()->create([
            'name' => 'Small Talk User',
            'email' => 'small-talk@example.com',
            'password' => bcrypt('secret123'),
            'status' => 'active',
            'role' => 'customer',
            'company_id' => $companyId,
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/messages/assistant/chat', [
            'message' => 'What is your name?',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.product_suggestions', []);

        $this->assertStringNotContainsString(
            'matching product',
            strtolower((string) $response->json('data.reply'))
        );
    }

    public function test_general_product_question_does_not_display_catalog_products(): void
    {
        $companyId = \DB::table('companies')->insertGetId([
            'name' => 'General Question Co',
            'status' => 'approved',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $user = User::query()->create([
            'name' => 'General Question User',
            'email' => 'general-question@example.com',
            'password' => bcrypt('secret123'),
            'status' => 'active',
            'role' => 'customer',
            'company_id' => $companyId,
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/messages/assistant/chat', [
            'message' => 'Can you explain how long a business laptop should last?',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.product_suggestions', []);

        $this->assertFalse(collect($response->json('data.actions', []))->contains(
            static fn (array $action) => str_contains(strtolower((string) ($action['label'] ?? '')), 'product')
        ));
    }

    public function test_tool_agent_handles_novel_product_wording_but_catalog_owns_facts(): void
    {
        config()->set('services.azure_openai.endpoint', 'https://example.openai.azure.com');
        config()->set('services.azure_openai.api_key', 'test-key');
        config()->set('services.azure_openai.deployment', 'test-deployment');

        $toolCallIssued = false;

        Http::fake(function ($request) use (&$toolCallIssued) {
            if (!$toolCallIssued) {
                $toolCallIssued = true;

                return Http::response([
                    'choices' => [[
                        'message' => [
                            'content' => null,
                            'tool_calls' => [[
                                'id' => 'call_1',
                                'type' => 'function',
                                'function' => [
                                    'name' => 'search_catalog',
                                    'arguments' => json_encode([
                                        'query' => 'wireless collaboration bar',
                                        'category' => 'video conferencing',
                                    ]),
                                ],
                            ]],
                        ],
                    ]],
                ]);
            }

            // The catalogue result must be present before the model is allowed to answer.
            $toolResult = collect((array) data_get($request->data(), 'messages', []))
                ->firstWhere('role', 'tool');
            $this->assertNotNull($toolResult);
            $this->assertStringContainsString('CONF-BAR', (string) $toolResult['content']);

            return Http::response([
                'choices' => [[
                    'message' => ['content' => 'The Contoso Wireless Collaboration Bar fits a meeting room like that.'],
                ]],
            ]);
        });

        $user = $this->createCustomer('Novel Wording User', 'novel-wording@example.com');
        $this->insertCatalogProduct('CONF-BAR', 'Contoso Wireless Collaboration Bar', 'Wireless video conferencing device for meeting rooms.', 899, 'Video Conferencing');

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/messages/assistant/chat', [
            'message' => 'I need something for wireless meetings where remote participants can see and hear the room.',
        ]);

        $response->assertOk();
        $response->assertJsonPath('data.source', 'tool_agent');
        $response->assertJsonPath('data.product_suggestions.0.product_id', 'CONF-BAR');
        $response->assertJsonPath('data.product_suggestions.0.name', 'Contoso Wireless Collaboration Bar');
        $this->assertSame(899.0, (float) $response->json('data.product_suggestions.0.price'));
    }

    public function test_ai_laptop_conversation_keeps_topic_budget_and_recommendation_context(): void
    {
        $this->assertTrue(\App\Support\ChatIntentSignals::isProductLookupIntent('hi check for me lptops best for my ai project below 1k dollars budget'));
        $user = $this->createCustomer('AI Laptop User', 'ai-laptop@example.com');
        $this->insertCatalogProduct('LAPTOP-900', 'Contoso AI Laptop 15', 'Laptop with NVIDIA graphics for entry-level AI development.', 899, 'Laptops');
        $this->insertCatalogProduct('LAPTOP-1500', 'Contoso Deep Learning Laptop 16', 'Laptop with NVIDIA RTX graphics and 32 GB RAM for deep learning.', 1499, 'Laptops');
        $this->insertCatalogProduct('PROJECTOR-1', 'Portable Presentation Projector', 'Portable projector for presentations.', 1001, 'Projectors');
        $this->insertCatalogProduct('LAPTOP-CORD', '6ft Laptop Power Cord', 'Power cord for most laptop power bricks.', 6, 'Computer Accessories', 'StarTech');
        $this->insertCatalogProduct('LAPTOP-BAG', 'Classic Laptop Topload Bag', 'Padded laptop compartment and carrying handle.', 27, 'Computer Accessories', 'Targus');
        $this->insertCatalogProduct('LAPTOP-HUB', 'USB Hub for Laptop', 'Adds USB ports to a laptop.', 34, 'Computer Accessories', 'StarTech');
        $this->insertCatalogProduct('LAPTOP-KVM', 'Portable KVM Console for Laptop', 'Use a laptop to access servers and devices.', 603, 'Computer Accessories', 'StarTech');

        $first = $this->actingAs($user, 'sanctum')->postJson('/api/v1/messages/assistant/chat', [
            'message' => 'hi check for me lptops best for my ai project below 1k dollars budget',
        ])->assertOk();
        $sessionId = (int) $first->json('data.chat_session.id');
        $firstIds = collect($first->json('data.product_suggestions'))->pluck('product_id');
        $this->assertContains('LAPTOP-900', $firstIds, json_encode($first->json('data')));
        $this->assertNotContains('PROJECTOR-1', $firstIds, json_encode($firstIds->values()->all()));
        foreach (['LAPTOP-CORD', 'LAPTOP-BAG', 'LAPTOP-HUB', 'LAPTOP-KVM'] as $accessoryId) {
            $this->assertNotContains($accessoryId, $firstIds, json_encode($first->json('data')));
        }

        $this->actingAs($user, 'sanctum')->postJson('/api/v1/messages/assistant/chat', [
            'message' => 'deep learning',
            'chat_session_id' => $sessionId,
        ])->assertOk();

        $budget = $this->actingAs($user, 'sanctum')->postJson('/api/v1/messages/assistant/chat', [
            'message' => 'check for 1500',
            'chat_session_id' => $sessionId,
        ])->assertOk();
        $budgetIds = collect($budget->json('data.product_suggestions'))->pluck('product_id');
        $this->assertContains('LAPTOP-1500', $budgetIds, json_encode($budget->json('data')));
        $this->assertNotContains('PROJECTOR-1', $budgetIds, json_encode($budgetIds->values()->all()));

        $cart = $this->actingAs($user, 'sanctum')->postJson('/api/v1/messages/assistant/chat', [
            'message' => 'can you add it to cart',
            'chat_session_id' => $sessionId,
        ])->assertOk();
        $this->assertSame('local_product_cart', $cart->json('data.source'));
        $this->assertSame('add_to_cart', $cart->json('data.cart_operation.type'));
        $this->assertNotEmpty($cart->json('data.product_suggestions'));

        $recommendation = $this->actingAs($user, 'sanctum')->postJson('/api/v1/messages/assistant/chat', [
            'message' => 'i need your recommendation i will go with what you recommend',
            'chat_session_id' => $sessionId,
        ])->assertOk();
        $this->assertSame('local_product_recommendation_follow_up', $recommendation->json('data.source'));
        $this->assertCount(1, $recommendation->json('data.product_suggestions'));
        $this->assertStringContainsString('LAPTOP', (string) $recommendation->json('data.product_suggestions.0.product_id'));
    }

    public function test_real_laptop_correction_replaces_stale_monitor_context(): void
    {
        $user = $this->createCustomer('Context Replacement User', 'context-replacement@example.com');
        $this->insertCatalogProduct('MONITOR-CONTEXT', 'Contoso Business Monitor', '24-inch office monitor.', 250, 'Monitors');
        $this->insertCatalogProduct('LAPTOP-CORD-CONTEXT', 'Laptop Power Cord', 'Power cord for laptop adapters.', 8, 'Computer Accessories', 'StarTech');

        $first = $this->actingAs($user, 'sanctum')->postJson('/api/v1/messages/assistant/chat', [
            'message' => 'Show me monitors under $1500.',
        ])->assertOk();
        $sessionId = (int) $first->json('data.chat_session.id');

        $recommendation = $this->actingAs($user, 'sanctum')->postJson('/api/v1/messages/assistant/chat', [
            'message' => 'Which one do you recommend?',
            'chat_session_id' => $sessionId,
        ])->assertOk();
        $this->assertSame('MONITOR-CONTEXT', $recommendation->json('data.product_suggestions.0.product_id'));

        $quote = $this->actingAs($user, 'sanctum')->postJson('/api/v1/messages/assistant/chat', [
            'message' => 'Generate the quote for the item.',
            'chat_session_id' => $sessionId,
        ])->assertOk();
        $this->assertSame('local_product_cart', $quote->json('data.source'));
        $this->assertSame('prepare_quote', $quote->json('data.cart_operation.type'));
        $this->assertSame('MONITOR-CONTEXT', $quote->json('data.cart_operation.items.0.productId'));
        $this->assertSame('MONITOR-CONTEXT', $quote->json('data.product_suggestions.0.product_id'));
        $this->assertFalse(str_contains(strtolower((string) $quote->json('data.reply')), 'quote(s) on record'));

        $laptop = $this->actingAs($user, 'sanctum')->postJson('/api/v1/messages/assistant/chat', [
            'message' => 'I need a real laptop, not accessories.',
            'chat_session_id' => $sessionId,
        ])->assertOk();
        $this->assertSame([], $laptop->json('data.product_suggestions'));
        $this->assertStringNotContainsString('MONITOR-CONTEXT', json_encode($laptop->json('data')));
        $this->assertStringNotContainsString('LAPTOP-CORD-CONTEXT', json_encode($laptop->json('data')));
    }

    public function test_non_product_planner_result_cannot_activate_catalog_search(): void
    {
        config()->set('services.azure_openai.endpoint', 'https://example.openai.azure.com');
        config()->set('services.azure_openai.api_key', 'test-key');
        config()->set('services.azure_openai.deployment', 'test-deployment');
        Http::fake([
            '*' => Http::response([
                'choices' => [[
                    'message' => ['content' => json_encode([
                        'is_product_request' => false,
                        'query' => '',
                        'product_type' => null,
                        'constraints' => [],
                        'operation' => 'none',
                        'selection' => 'none',
                        'is_follow_up' => false,
                    ])],
                ]],
            ]),
        ]);

        $user = $this->createCustomer('Planner Boundary User', 'planner-boundary@example.com');
        $this->insertCatalogProduct('BOUNDARY-1', 'Boundary Product', 'A product that must not be returned.', 100, 'Miscellaneous');

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/messages/assistant/chat', [
            'message' => 'I am planning a better way to organize our meeting room.',
        ]);

        $response->assertOk()->assertJsonPath('data.product_suggestions', []);
    }

    public function test_current_question_is_not_duplicated_in_azure_history(): void
    {
        config()->set('services.azure_openai.endpoint', 'https://example.openai.azure.com');
        config()->set('services.azure_openai.api_key', 'test-key');
        config()->set('services.azure_openai.deployment', 'test-deployment');

        $capturedBody = null;
        Http::fake(function ($request) use (&$capturedBody) {
            $capturedBody = $request->data();

            return Http::response([
                'choices' => [[
                    'message' => ['content' => 'A concise answer from Mela.'],
                ]],
            ]);
        });

        $companyId = DB::table('companies')->insertGetId([
            'name' => 'History Co',
            'status' => 'approved',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $user = User::query()->create([
            'name' => 'History User',
            'email' => 'history-user@example.com',
            'password' => bcrypt('secret123'),
            'status' => 'active',
            'role' => 'customer',
            'company_id' => $companyId,
            'email_verified_at' => now(),
        ]);

        $question = 'Explain endpoint detection and response for my team.';
        $this->actingAs($user, 'sanctum')->postJson('/api/v1/messages/assistant/chat', [
            'message' => $question,
        ])->assertOk();

        $this->assertSame(1, substr_count((string) json_encode($capturedBody), $question));
    }

    public function test_general_support_prompt_does_not_send_account_records_to_azure(): void
    {
        config()->set('services.azure_openai.endpoint', 'https://example.openai.azure.com');
        config()->set('services.azure_openai.api_key', 'test-key');
        config()->set('services.azure_openai.deployment', 'test-deployment');

        $capturedBody = null;
        Http::fake(function ($request) use (&$capturedBody) {
            $capturedBody = $request->data();

            return Http::response([
                'choices' => [[
                    'message' => ['content' => 'A business laptop commonly lasts three to five years.'],
                ]],
            ]);
        });

        $context = [
            'customer' => ['name' => 'Privacy User'],
            'summary' => [
                'open_invoice_count' => 1,
                'open_invoice_total' => 9876.54,
            ],
            'recent_orders' => [[
                'order_number' => 'PRIVATE-ORDER-123',
                'status' => 'processing',
                'total_amount' => 9876.54,
            ]],
            'completed_paid_quotes' => [[
                'quote_id' => 'PRIVATE-QUOTE-456',
                'total_amount' => 9876.54,
            ]],
            'recent_invoices' => [[
                'invoice_number' => 'PRIVATE-INVOICE-789',
                'remaining_amount' => 9876.54,
            ]],
            'product_suggestions' => [],
            'product_intent' => false,
            'smart_intent' => 'general_support',
        ];

        $service = new AzureOpenAiChatService();
        $result = $service->orchestrate('How long should a business laptop last?', $context);

        $payload = json_encode($capturedBody);
        $this->assertSame('general_support', $result['intent']);
        $this->assertStringNotContainsString('PRIVATE-ORDER-123', $payload);
        $this->assertStringNotContainsString('PRIVATE-QUOTE-456', $payload);
        $this->assertStringNotContainsString('PRIVATE-INVOICE-789', $payload);
        $this->assertStringNotContainsString('9876.54', $payload);

        $service->orchestrate('Explain the difference between an invoice and a quote.', $context);
        $informationalPayload = json_encode($capturedBody);
        $this->assertStringNotContainsString('PRIVATE-ORDER-123', $informationalPayload);
        $this->assertStringNotContainsString('PRIVATE-QUOTE-456', $informationalPayload);
        $this->assertStringNotContainsString('PRIVATE-INVOICE-789', $informationalPayload);
        $this->assertStringNotContainsString('9876.54', $informationalPayload);
    }

    public function test_azure_failure_is_reported_as_a_degraded_response(): void
    {
        config()->set('services.azure_openai.endpoint', 'https://example.openai.azure.com');
        config()->set('services.azure_openai.api_key', 'test-key');
        config()->set('services.azure_openai.deployment', 'test-deployment');
        Http::fake(['*' => Http::response(['error' => 'temporary failure'], 503)]);

        $result = (new AzureOpenAiChatService())->orchestrate(
            'How should I secure an office network?',
            [
                'customer' => ['name' => 'Network User'],
                'summary' => [],
                'recent_orders' => [],
                'completed_paid_quotes' => [],
                'recent_invoices' => [],
                'product_suggestions' => [],
                'product_intent' => false,
                'smart_intent' => 'general_support',
            ]
        );

        $this->assertTrue($result['degraded']);
        $this->assertSame('general_support', $result['intent']);
        $this->assertNotEmpty($result['reply']);
    }

    public function test_assistant_chat_endpoint_declares_throttle_middleware(): void
    {
        $route = collect(Route::getRoutes())->first(static function ($candidate) {
            return in_array('POST', $candidate->methods(), true)
                && $candidate->uri() === 'api/v1/messages/assistant/chat';
        });

        $this->assertNotNull($route);
        $this->assertContains('throttle:30,1', $route->gatherMiddleware());
    }

    public function test_due_questions_return_natural_reply_without_action_buttons(): void
    {
        $companyId = \DB::table('companies')->insertGetId([
            'name' => 'Due Co',
            'status' => 'approved',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $user = User::query()->create([
            'name' => 'Due User',
            'email' => 'due-user@example.com',
            'password' => bcrypt('secret123'),
            'status' => 'active',
            'role' => 'customer',
            'company_id' => $companyId,
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/messages/assistant/chat', [
            'message' => 'how much is due for the order/quotes',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.actions', []);
    }

    public function test_mixed_account_summary_question_avoids_forced_quote_buttons(): void
    {
        $companyId = \DB::table('companies')->insertGetId([
            'name' => 'Mixed Co',
            'status' => 'approved',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $user = User::query()->create([
            'name' => 'Mixed User',
            'email' => 'mixed-user@example.com',
            'password' => bcrypt('secret123'),
            'status' => 'active',
            'role' => 'customer',
            'company_id' => $companyId,
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/messages/assistant/chat', [
            'message' => 'check my quotes and orders',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.actions', []);
    }

    public function test_greeting_after_quote_turn_does_not_get_forced_back_to_quote_agent(): void
    {
        $companyId = \DB::table('companies')->insertGetId([
            'name' => 'Greeting Co',
            'status' => 'approved',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $user = User::query()->create([
            'name' => 'Greeting User',
            'email' => 'greeting-user@example.com',
            'password' => bcrypt('secret123'),
            'status' => 'active',
            'role' => 'customer',
            'company_id' => $companyId,
            'email_verified_at' => now(),
        ]);

        $first = $this->actingAs($user, 'sanctum')->postJson('/api/v1/messages/assistant/chat', [
            'message' => 'check my quotes',
        ]);

        $first->assertOk()->assertJsonPath('success', true);
        $chatSessionId = (int) ($first->json('data.chat_session.id') ?? 0);
        $this->assertGreaterThan(0, $chatSessionId);

        $second = $this->actingAs($user, 'sanctum')->postJson('/api/v1/messages/assistant/chat', [
            'message' => 'hi',
            'chat_session_id' => $chatSessionId,
        ]);

        $second->assertOk()->assertJsonPath('success', true);
        $reply = strtolower((string) $second->json('data.reply'));
        $this->assertStringNotContainsString('you have **', $reply);
        $this->assertStringNotContainsString('quote(s) on record', $reply);
    }

    public function test_product_conversation_preserves_cards_and_allows_category_changes(): void
    {
        $user = $this->createCustomer('Product Continuity User', 'product-continuity@example.com');
        $this->insertCatalogProduct('AP-6', 'Office Wi-Fi 6 Access Point', 'Wi-Fi 6 wireless access point for offices.', 449, 'Wireless Access Points');
        $this->insertCatalogProduct('AP-6E', 'Office Wi-Fi 6E Access Point', 'Wi-Fi 6E wireless access point for dense offices.', 749, 'Wireless Access Points');
        $this->insertCatalogProduct('SWITCH-POE', '24-Port Managed PoE Switch', 'Managed PoE network switch for wireless access points.', 699, 'Network Switches');

        $first = $this->actingAs($user, 'sanctum')->postJson('/api/v1/messages/assistant/chat', [
            'message' => 'Show wireless access points and managed PoE switches for our office.',
        ])->assertOk();

        $sessionId = (int) $first->json('data.chat_session.id');
        $firstIds = collect($first->json('data.product_suggestions'))->pluck('product_id');
        $this->assertContains('AP-6', $firstIds, json_encode($firstIds->values()->all()));
        $this->assertContains('SWITCH-POE', $firstIds, json_encode($firstIds->values()->all()));

        foreach ([
            'Include both Wi-Fi 6 and Wi-Fi 6E options.',
            'Show me their images please.',
            'Provide a description and price for each product.',
            'Give me the links to those products.',
            'Exclude accessories and discontinued products.',
        ] as $followUp) {
            $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/messages/assistant/chat', [
                'message' => $followUp,
                'chat_session_id' => $sessionId,
            ]);

            $response->assertOk();
            $this->assertSame('local_product_context_follow_up', $response->json('data.source'), $followUp);
            $this->assertNotEmpty($response->json('data.product_suggestions'), $followUp);
            $this->assertFalse(collect($response->json('data.actions', []))->contains(
                static fn (array $action) => in_array($action['label'] ?? '', ['Open quotes', 'Open orders', 'See all invoices'], true)
            ));
        }

        $switchResponse = $this->actingAs($user, 'sanctum')->postJson('/api/v1/messages/assistant/chat', [
            'message' => 'Find a compatible managed PoE switch for that access point.',
            'chat_session_id' => $sessionId,
        ])->assertOk();
        $switchIds = collect($switchResponse->json('data.product_suggestions'))->pluck('product_id');
        $this->assertSame(['SWITCH-POE'], $switchIds->values()->all());

        $quoteResponse = $this->actingAs($user, 'sanctum')->postJson('/api/v1/messages/assistant/chat', [
            'message' => 'Add the best access point and switch to a quote.',
            'chat_session_id' => $sessionId,
        ]);

        $quoteResponse->assertOk()->assertJsonPath('data.source', 'local_product_context_follow_up');
        $quoteIds = collect($quoteResponse->json('data.product_suggestions'))->pluck('product_id');
        $this->assertTrue($quoteIds->contains('SWITCH-POE'));
        $this->assertTrue($quoteIds->contains(fn (string $id) => str_starts_with($id, 'AP-')));
        $this->assertFalse(collect($quoteResponse->json('data.actions', []))->contains(
            static fn (array $action) => ($action['label'] ?? '') === 'Open quotes'
        ));
    }

    public function test_printer_flow_refines_recommends_shows_specs_and_prepares_quote(): void
    {
        $user = $this->createCustomer('Printer Flow User', 'printer-flow@example.com');
        $this->insertCatalogProduct('CANON-COLOR', 'Canon Color Laser Printer', 'Duplex color laser printer with economical toner.', 799, 'Printers', 'Canon');
        $this->insertCatalogProduct('HP-COLOR', 'HP Enterprise Color Laser Printer', 'High-volume duplex color laser printer.', 1199, 'Printers', 'HP');
        $this->insertCatalogProduct('BROTHER-MONO', 'Brother Mono Laser Printer', 'Compact monochrome laser printer.', 399, 'Printers', 'Brother');
        $this->insertCatalogProduct('BROTHER-LABEL', 'Brother Wireless Label Printer', 'Desktop laminated label printer.', 476, 'Printers', 'Brother');
        $this->insertCatalogProduct('XEROX-TONER', 'Xerox Waste Toner Bottle', 'Waste toner bottle for a color printer.', 17, 'Printer Supplies', 'Xerox');

        $first = $this->actingAs($user, 'sanctum')->postJson('/api/v1/messages/assistant/chat', [
            'message' => 'Show me color laser printers for a small accounting office.',
        ])->assertOk();
        $sessionId = (int) $first->json('data.chat_session.id');
        $firstIds = collect($first->json('data.product_suggestions'))->pluck('product_id');
        $this->assertContains('CANON-COLOR', $firstIds);
        $this->assertContains('HP-COLOR', $firstIds);
        $this->assertNotContains('BROTHER-MONO', $firstIds);
        $this->assertNotContains('BROTHER-LABEL', $firstIds);
        $this->assertNotContains('XEROX-TONER', $firstIds);

        $refined = $this->actingAs($user, 'sanctum')->postJson('/api/v1/messages/assistant/chat', [
            'message' => 'Show Canon printers under $900.',
            'chat_session_id' => $sessionId,
        ])->assertOk();
        $this->assertSame(
            ['CANON-COLOR'],
            collect($refined->json('data.product_suggestions'))->pluck('product_id')->values()->all()
        );

        $recommended = $this->actingAs($user, 'sanctum')->postJson('/api/v1/messages/assistant/chat', [
            'message' => 'Which one would you recommend for low running costs?',
            'chat_session_id' => $sessionId,
        ])->assertOk();
        $this->assertSame('CANON-COLOR', $recommended->json('data.product_suggestions.0.product_id'));
        $this->assertCount(1, $recommended->json('data.product_suggestions'));
        $this->assertSame('local_product_recommendation_follow_up', $recommended->json('data.source'));

        $specifications = $this->actingAs($user, 'sanctum')->postJson('/api/v1/messages/assistant/chat', [
            'message' => 'Show its specifications.',
            'chat_session_id' => $sessionId,
        ]);
        $specifications->assertOk();
        $this->assertSame('local_product_context_follow_up', $specifications->json('data.source'));
        $this->assertSame('CANON-COLOR', $specifications->json('data.product_suggestions.0.product_id'));
        $this->assertCount(1, $specifications->json('data.product_suggestions'));

        $quote = $this->actingAs($user, 'sanctum')->postJson('/api/v1/messages/assistant/chat', [
            'message' => 'Add that printer to a quote.',
            'chat_session_id' => $sessionId,
        ]);
        $quote->assertOk()->assertJsonPath('data.source', 'local_product_context_follow_up');
        $this->assertSame('CANON-COLOR', $quote->json('data.product_suggestions.0.product_id'));
        $this->assertCount(1, $quote->json('data.product_suggestions'));
        $this->assertFalse(collect($quote->json('data.actions', []))->contains(
            static fn (array $action) => ($action['label'] ?? '') === 'Open quotes'
        ));
    }

    public function test_agent_negotiates_parameters_a_deployment_rejects(): void
    {
        config()->set('services.azure_openai.endpoint', 'https://example.openai.azure.com');
        config()->set('services.azure_openai.api_key', 'test-key');
        config()->set('services.azure_openai.deployment', 'test-deployment');

        $payloads = [];

        Http::fake(function ($request) use (&$payloads) {
            $payload = $request->data();
            $payloads[] = $payload;

            if (array_key_exists('max_tokens', $payload)) {
                return Http::response([
                    'error' => [
                        'message' => "Unsupported parameter: 'max_tokens' is not supported with this model. Use 'max_completion_tokens' instead.",
                        'code' => 'unsupported_parameter',
                        'param' => 'max_tokens',
                    ],
                ], 400);
            }

            if (array_key_exists('temperature', $payload)) {
                return Http::response([
                    'error' => [
                        'message' => "Unsupported value: 'temperature' does not support 0.5 with this model.",
                        'code' => 'unsupported_value',
                        'param' => 'temperature',
                    ],
                ], 400);
            }

            return Http::response([
                'choices' => [['message' => ['content' => 'All set — what would you like to look at?']]],
            ]);
        });

        $user = $this->createCustomer('Parameter Profile User', 'parameter-profile@example.com');

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/messages/assistant/chat', [
            'message' => 'Hi',
        ]);

        $response->assertOk();
        $response->assertJsonPath('data.source', 'tool_agent');
        $response->assertJsonPath('data.degraded', false);
        $response->assertJsonPath('data.reply', 'All set — what would you like to look at?');

        $accepted = end($payloads);
        $this->assertArrayNotHasKey('max_tokens', $accepted);
        $this->assertArrayNotHasKey('temperature', $accepted);
        $this->assertSame(700, $accepted['max_completion_tokens']);

        // The learned shape is reused, so later turns cost no extra round trips.
        $payloads = [];
        $this->actingAs($user, 'sanctum')->postJson('/api/v1/messages/assistant/chat', [
            'message' => 'Thanks',
            'chat_session_id' => (int) $response->json('data.chat_session.id'),
        ])->assertOk();
        $this->assertCount(1, $payloads);
    }

    public function test_quantity_follow_up_resolves_the_product_staged_earlier(): void
    {
        config()->set('services.azure_openai.endpoint', 'https://example.openai.azure.com');
        config()->set('services.azure_openai.api_key', 'test-key');
        config()->set('services.azure_openai.deployment', 'test-deployment');

        $systemPrompts = [];
        $round = 0;

        Http::fake(function ($request) use (&$round, &$systemPrompts) {
            $systemPrompts[] = (string) data_get($request->data(), 'messages.0.content', '');
            $round++;

            if ($round === 1) {
                return $this->fakeToolCall('call_add', 'add_to_cart', ['product_id' => 'DOCK-1', 'quantity' => 1]);
            }

            if ($round === 3) {
                // The follow-up carries no identifier, exactly as a customer would phrase it.
                return $this->fakeToolCall('call_qty', 'update_cart_quantity', ['quantity' => 5]);
            }

            return Http::response([
                'choices' => [['message' => ['content' => 'Done.']]],
            ]);
        });

        $user = $this->createCustomer('Quantity Follow Up User', 'quantity-followup@example.com');
        $this->insertCatalogProduct('DOCK-1', 'Contoso Thunderbolt Dock', 'Thunderbolt docking station.', 249, 'Docking Stations');

        $first = $this->actingAs($user, 'sanctum')->postJson('/api/v1/messages/assistant/chat', [
            'message' => 'Add that dock to my cart.',
        ])->assertOk();

        $second = $this->actingAs($user, 'sanctum')->postJson('/api/v1/messages/assistant/chat', [
            'message' => 'update the quantity to 5',
            'chat_session_id' => (int) $first->json('data.chat_session.id'),
        ])->assertOk();

        $second->assertJsonPath('data.cart_operations.0.type', 'set_cart_quantity');
        $second->assertJsonPath('data.cart_operations.0.items.0.productId', 'DOCK-1');
        $second->assertJsonPath('data.cart_operations.0.items.0.quantity', 5);

        // The staged item is carried into the prompt so the model can reference it directly.
        $this->assertStringContainsString('DOCK-1', end($systemPrompts));
    }

    public function test_cart_totals_come_from_the_catalogue_not_the_client_payload(): void
    {
        config()->set('services.azure_openai.endpoint', 'https://example.openai.azure.com');
        config()->set('services.azure_openai.api_key', 'test-key');
        config()->set('services.azure_openai.deployment', 'test-deployment');

        $round = 0;
        $cartToolResult = null;

        Http::fake(function ($request) use (&$round, &$cartToolResult) {
            foreach ((array) data_get($request->data(), 'messages', []) as $message) {
                if (($message['role'] ?? '') === 'tool') {
                    $cartToolResult = json_decode((string) $message['content'], true);
                }
            }

            if (++$round === 1) {
                return $this->fakeToolCall('call_cart', 'view_cart', []);
            }

            return Http::response([
                'choices' => [['message' => ['content' => 'Your cart subtotal is $1,269.10.']]],
            ]);
        });

        $user = $this->createCustomer('Cart Total User', 'cart-total@example.com');
        $this->insertCatalogProduct('MON-27', 'ViewSonic VG2755 27 inch Monitor', '27 inch business monitor.', 253.82, 'Monitors');
        $this->insertCatalogProduct('LAP-1', 'Lenovo ThinkPad E14 Gen 7', 'Business laptop.', 1175, 'Laptops');

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/messages/assistant/chat', [
            'message' => 'What is my cart subtotal?',
            'cart' => [
                ['productId' => 'MON-27', 'quantity' => 5],
                // A tampered price must be ignored; only id and quantity are trusted.
                ['productId' => 'LAP-1', 'quantity' => 2, 'price' => 1],
            ],
        ]);

        $response->assertOk();
        $response->assertJsonPath('data.source', 'tool_agent');

        $this->assertSame(2, $cartToolResult['line_count']);
        $this->assertSame(7, $cartToolResult['total_units']);
        $this->assertSame(3619.10, $cartToolResult['subtotal_usd']);
        $this->assertSame(1269.10, $cartToolResult['lines'][0]['line_total_usd']);
        $this->assertEqualsWithDelta(1175.0, $cartToolResult['lines'][1]['unit_price_usd'], 0.001);
    }

    public function test_agent_can_remove_a_cart_line_but_only_one_that_exists(): void
    {
        config()->set('services.azure_openai.endpoint', 'https://example.openai.azure.com');
        config()->set('services.azure_openai.api_key', 'test-key');
        config()->set('services.azure_openai.deployment', 'test-deployment');

        $round = 0;
        $toolResults = [];

        Http::fake(function ($request) use (&$round, &$toolResults) {
            foreach ((array) data_get($request->data(), 'messages', []) as $message) {
                if (($message['role'] ?? '') === 'tool') {
                    $toolResults[] = (string) $message['content'];
                }
            }

            $round++;

            if ($round === 1) {
                return $this->fakeToolCall('call_absent', 'remove_from_cart', ['product_id' => 'NOT-IN-CART']);
            }

            if ($round === 2) {
                return $this->fakeToolCall('call_remove', 'remove_from_cart', ['product_id' => 'LAP-1']);
            }

            return Http::response([
                'choices' => [['message' => ['content' => 'Removed the ThinkPad from your cart.']]],
            ]);
        });

        $user = $this->createCustomer('Cart Remove User', 'cart-remove@example.com');
        $this->insertCatalogProduct('LAP-1', 'Lenovo ThinkPad E14 Gen 7', 'Business laptop.', 1175, 'Laptops');

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/messages/assistant/chat', [
            'message' => 'remove it from the cart',
            'cart' => [['productId' => 'LAP-1', 'quantity' => 5]],
        ]);

        $response->assertOk();
        $response->assertJsonPath('data.cart_operations.0.type', 'remove_from_cart');
        $response->assertJsonPath('data.cart_operations.0.items.0.productId', 'LAP-1');
        $this->assertStringContainsString('not in the cart', $toolResults[0] ?? '');
    }

    public function test_clearing_several_cart_lines_returns_every_removal(): void
    {
        config()->set('services.azure_openai.endpoint', 'https://example.openai.azure.com');
        config()->set('services.azure_openai.api_key', 'test-key');
        config()->set('services.azure_openai.deployment', 'test-deployment');

        $round = 0;
        $toolResults = [];

        Http::fake(function ($request) use (&$round, &$toolResults) {
            // Each request replays every prior tool result, so keep only the latest snapshot.
            $toolResults = collect((array) data_get($request->data(), 'messages', []))
                ->where('role', 'tool')
                ->map(static fn (array $message) => json_decode((string) $message['content'], true))
                ->values()
                ->all();

            $round++;

            if ($round === 1) {
                return $this->fakeToolCall('remove_a', 'remove_from_cart', ['product_id' => 'LAP-1']);
            }

            if ($round === 2) {
                return $this->fakeToolCall('remove_b', 'remove_from_cart', ['product_id' => 'MON-27']);
            }

            if ($round === 3) {
                // A third removal must fail: the working cart is already empty.
                return $this->fakeToolCall('remove_c', 'remove_from_cart', ['product_id' => 'LAP-1']);
            }

            return Http::response([
                'choices' => [['message' => ['content' => 'Your cart is now empty.']]],
            ]);
        });

        $user = $this->createCustomer('Cart Clear User', 'cart-clear@example.com');
        $this->insertCatalogProduct('LAP-1', 'Lenovo ThinkPad E14 Gen 7', 'Business laptop.', 1175, 'Laptops');
        $this->insertCatalogProduct('MON-27', 'ViewSonic VG2755 27 inch Monitor', '27 inch business monitor.', 253.82, 'Monitors');

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/messages/assistant/chat', [
            'message' => 'clear the cart',
            'cart' => [
                ['productId' => 'LAP-1', 'quantity' => 5],
                ['productId' => 'MON-27', 'quantity' => 5],
            ],
        ]);

        $response->assertOk();

        // Both removals must reach the browser; the old single-slot payload dropped the first.
        $this->assertCount(2, $response->json('data.cart_operations'));
        $this->assertSame(
            ['LAP-1', 'MON-27'],
            collect($response->json('data.cart_operations'))->pluck('items.0.productId')->all()
        );
        $this->assertTrue($toolResults[0]['ok']);
        $this->assertTrue($toolResults[1]['ok']);
        $this->assertFalse($toolResults[2]['ok']);
    }

    public function test_uploaded_image_is_sent_to_the_model_as_vision_input(): void
    {
        config()->set('services.azure_openai.endpoint', 'https://example.openai.azure.com');
        config()->set('services.azure_openai.api_key', 'test-key');
        config()->set('services.azure_openai.deployment', 'test-deployment');
        \Illuminate\Support\Facades\Storage::fake('local');

        $userContent = null;
        Http::fake(function ($request) use (&$userContent) {
            $userContent = collect((array) data_get($request->data(), 'messages', []))
                ->last(static fn (array $message) => ($message['role'] ?? '') === 'user')['content'] ?? null;

            return Http::response([
                'choices' => [['message' => ['content' => 'That looks like a Lenovo ThinkPad label.']]],
            ]);
        });

        $user = $this->createCustomer('Attachment User', 'attachment@example.com');

        $upload = $this->actingAs($user, 'sanctum')->postJson('/api/v1/messages/assistant/attachments', [], []);
        $upload->assertStatus(422);

        $image = \Illuminate\Http\UploadedFile::fake()->image('label.png', 40, 40);
        $uploaded = $this->actingAs($user, 'sanctum')->post('/api/v1/messages/assistant/attachments', ['file' => $image]);
        $uploaded->assertOk();
        $uploaded->assertJsonPath('data.is_image', true);
        $attachmentId = (int) $uploaded->json('data.id');

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/messages/assistant/chat', [
            'message' => 'What is in this photo?',
            'attachment_ids' => [$attachmentId],
        ]);

        $response->assertOk();
        $response->assertJsonPath('data.source', 'tool_agent');

        // The bytes must reach the model as a vision part, not just the filename.
        $this->assertIsArray($userContent);
        $types = collect($userContent)->pluck('type')->all();
        $this->assertContains('image_url', $types);
        $this->assertStringStartsWith('data:image/', collect($userContent)->firstWhere('type', 'image_url')['image_url']['url']);
    }

    public function test_attachments_are_private_to_their_owner(): void
    {
        \Illuminate\Support\Facades\Storage::fake('local');

        $owner = $this->createCustomer('Attachment Owner', 'attachment-owner@example.com');
        $stranger = $this->createCustomer('Attachment Stranger', 'attachment-stranger@example.com');

        $uploaded = $this->actingAs($owner, 'sanctum')->post('/api/v1/messages/assistant/attachments', [
            'file' => \Illuminate\Http\UploadedFile::fake()->image('private.png'),
        ]);
        $uploaded->assertOk();
        $attachmentId = (int) $uploaded->json('data.id');

        $this->actingAs($stranger, 'sanctum')->get("/api/v1/messages/assistant/attachments/{$attachmentId}")->assertNotFound();
        $this->actingAs($owner, 'sanctum')->get("/api/v1/messages/assistant/attachments/{$attachmentId}")->assertOk();

        // A stranger also cannot attach someone else's upload to their own message.
        config()->set('services.azure_openai.endpoint', '');
        $this->actingAs($stranger, 'sanctum')->postJson('/api/v1/messages/assistant/chat', [
            'message' => 'Read this',
            'attachment_ids' => [$attachmentId],
        ])->assertOk();

        $this->assertSame(
            $owner->id,
            (int) \Illuminate\Support\Facades\DB::table('chat_attachments')->where('id', $attachmentId)->value('user_id')
        );
    }

    public function test_executable_uploads_are_rejected(): void
    {
        \Illuminate\Support\Facades\Storage::fake('local');

        $user = $this->createCustomer('Bad Upload User', 'bad-upload@example.com');

        $this->actingAs($user, 'sanctum')->post('/api/v1/messages/assistant/attachments', [
            'file' => \Illuminate\Http\UploadedFile::fake()->createWithContent('shell.php', '<?php echo 1;'),
        ], ['Accept' => 'application/json'])->assertStatus(422);
    }

    public function test_every_tool_schema_is_valid_json_for_the_api(): void
    {
        $user = $this->createCustomer('Schema User', 'schema@example.com');
        $toolkit = new \App\Services\Assistant\AssistantToolkit($user, static fn () => []);

        $definitions = $toolkit->definitions();
        $this->assertNotEmpty($definitions);

        foreach ($definitions as $tool) {
            $name = $tool['function']['name'];
            $parameters = $tool['function']['parameters'];

            $this->assertSame('object', $parameters['type'], $name);
            // A parameterless tool must still encode properties as {}; [] is rejected outright
            // and makes the API refuse the entire request.
            $this->assertStringStartsWith(
                '{',
                json_encode($parameters['properties']),
                "{$name} must encode properties as a JSON object"
            );
        }
    }

    public function test_agent_sends_a_schema_the_api_accepts_for_parameterless_tools(): void
    {
        config()->set('services.azure_openai.endpoint', 'https://example.openai.azure.com');
        config()->set('services.azure_openai.api_key', 'test-key');
        config()->set('services.azure_openai.deployment', 'test-deployment');

        $sentTools = null;

        Http::fake(function ($request) use (&$sentTools) {
            $sentTools = data_get($request->data(), 'tools');

            return Http::response([
                'choices' => [['message' => ['content' => 'Hello.']]],
            ]);
        });

        $user = $this->createCustomer('Schema Wire User', 'schema-wire@example.com');

        $this->actingAs($user, 'sanctum')->postJson('/api/v1/messages/assistant/chat', [
            'message' => 'Hi',
        ])->assertOk()->assertJsonPath('data.source', 'tool_agent');

        $viewCart = collect($sentTools)->firstWhere('function.name', 'view_cart');
        $this->assertNotNull($viewCart);
        $this->assertStringContainsString('"properties":{}', json_encode($viewCart['function']['parameters']));
    }

    public function test_tool_agent_cart_action_requires_a_real_catalog_product(): void
    {
        config()->set('services.azure_openai.endpoint', 'https://example.openai.azure.com');
        config()->set('services.azure_openai.api_key', 'test-key');
        config()->set('services.azure_openai.deployment', 'test-deployment');

        $round = 0;
        $toolResults = [];

        Http::fake(function ($request) use (&$round, &$toolResults) {
            foreach ((array) data_get($request->data(), 'messages', []) as $message) {
                if (($message['role'] ?? '') === 'tool') {
                    $toolResults[] = (string) $message['content'];
                }
            }

            $round++;

            if ($round === 1) {
                return $this->fakeToolCall('call_missing', 'add_to_cart', ['sku' => 'DOES-NOT-EXIST', 'quantity' => 2]);
            }

            if ($round === 2) {
                return $this->fakeToolCall('call_real', 'add_to_cart', ['product_id' => 'DOCK-1', 'quantity' => 2, 'mode' => 'cart']);
            }

            return Http::response([
                'choices' => [[
                    'message' => ['content' => 'Two Contoso Thunderbolt Docks are in your cart.'],
                ]],
            ]);
        });

        $user = $this->createCustomer('Cart Tool User', 'cart-tool@example.com');
        $this->insertCatalogProduct('DOCK-1', 'Contoso Thunderbolt Dock', 'Thunderbolt docking station.', 249, 'Docking Stations');

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/messages/assistant/chat', [
            'message' => 'Add two of those docks to my cart.',
        ]);

        $response->assertOk();
        $response->assertJsonPath('data.source', 'tool_agent');
        $response->assertJsonPath('data.cart_operations.0.type', 'add_to_cart');
        $response->assertJsonPath('data.cart_operations.0.items.0.productId', 'DOCK-1');
        $response->assertJsonPath('data.cart_operations.0.items.0.quantity', 2);

        // The unknown SKU must be reported back as a failure so the model cannot claim success.
        $this->assertStringContainsString('"ok":false', $toolResults[0] ?? '');
        $this->assertTrue(collect($response->json('data.actions', []))->contains(
            static fn (array $action) => ($action['link'] ?? '') === '/cart'
        ));
    }

    private function fakeToolCall(string $id, string $name, array $arguments): \GuzzleHttp\Promise\PromiseInterface
    {
        return Http::response([
            'choices' => [[
                'message' => [
                    'content' => null,
                    'tool_calls' => [[
                        'id' => $id,
                        'type' => 'function',
                        'function' => ['name' => $name, 'arguments' => json_encode($arguments)],
                    ]],
                ],
            ]],
        ]);
    }

    private function createCustomer(string $name, string $email): User
    {
        $companyId = DB::table('companies')->insertGetId([
            'name' => $name . ' Company',
            'status' => 'approved',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return User::query()->create([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt('secret123'),
            'status' => 'active',
            'role' => 'customer',
            'company_id' => $companyId,
            'email_verified_at' => now(),
        ]);
    }

    private function insertCatalogProduct(
        string $productId,
        string $name,
        string $description,
        float $price,
        string $category,
        string $manufacturer = 'Contoso'
    ): void {
        DB::table('products')->insert([
            'tdsynnex_product_id' => $productId,
            'tdsynnex_sku_no' => $productId,
            'product_name' => $name,
            'description' => $description,
            'base_price' => $price,
            'retail_price' => $price,
            'sale_price' => 0,
            'is_on_sale' => false,
            'is_available' => true,
            'quantity' => 10,
            'is_discontinued' => false,
            'manufacturer' => $manufacturer,
            'category_segment' => $category,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
