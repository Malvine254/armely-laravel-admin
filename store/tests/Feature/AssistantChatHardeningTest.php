<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AssistantChatHardeningTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

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
}
