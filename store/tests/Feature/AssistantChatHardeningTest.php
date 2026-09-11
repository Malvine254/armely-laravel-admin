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
}
