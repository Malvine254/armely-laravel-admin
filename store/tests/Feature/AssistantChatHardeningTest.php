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
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->foreign('chat_session_id')->references('id')->on('chat_sessions')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
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

        $first = $this->actingAs($user, 'sanctum')->postJson('/api/v1/messages/assistant/chat', [
            'message' => 'Show me color laser printers for a small accounting office.',
        ])->assertOk();
        $sessionId = (int) $first->json('data.chat_session.id');
        $this->assertNotEmpty($first->json('data.product_suggestions'));

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

        $specifications = $this->actingAs($user, 'sanctum')->postJson('/api/v1/messages/assistant/chat', [
            'message' => 'Show its specifications.',
            'chat_session_id' => $sessionId,
        ]);
        $specifications->assertOk();
        $this->assertSame('local_product_context_follow_up', $specifications->json('data.source'));
        $this->assertSame('CANON-COLOR', $specifications->json('data.product_suggestions.0.product_id'));

        $quote = $this->actingAs($user, 'sanctum')->postJson('/api/v1/messages/assistant/chat', [
            'message' => 'Add that printer to a quote.',
            'chat_session_id' => $sessionId,
        ]);
        $quote->assertOk()->assertJsonPath('data.source', 'local_product_context_follow_up');
        $this->assertSame('CANON-COLOR', $quote->json('data.product_suggestions.0.product_id'));
        $this->assertFalse(collect($quote->json('data.actions', []))->contains(
            static fn (array $action) => ($action['label'] ?? '') === 'Open quotes'
        ));
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
