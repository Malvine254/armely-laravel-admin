<?php

namespace Tests\Feature;

use App\Models\EmailPreference;
use App\Models\AppSetting;
use App\Models\SuppressionEvent;
use App\Models\User;
use App\Http\Middleware\EnsureUserIsActive;
use App\Services\UserEmailPreferenceService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class EmailPreferenceLifecycleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->string('role')->default('customer');
            $table->string('status')->default('active');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('email_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique();
            $table->boolean('transactional_enabled')->default(true);
            $table->boolean('notification_email_enabled')->default(true);
            $table->boolean('quotes_notifications_enabled')->default(true);
            $table->boolean('orders_notifications_enabled')->default(true);
            $table->boolean('invoices_notifications_enabled')->default(true);
            $table->boolean('marketing_enabled')->default(true);
            $table->boolean('ai_marketing_enabled')->default(false);
            $table->timestamp('ai_marketing_consent_at')->nullable();
            $table->boolean('price_alerts_enabled')->default(true);
            $table->boolean('cart_reminders_enabled')->default(true);
            $table->boolean('browse_reminders_enabled')->default(true);
            $table->string('timezone', 64)->nullable();
            $table->unsignedSmallInteger('quiet_hours_start')->nullable();
            $table->unsignedSmallInteger('quiet_hours_end')->nullable();
            $table->timestamps();
        });

        Schema::create('suppression_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable();
            $table->string('email', 320)->nullable();
            $table->string('event_type', 32);
            $table->string('channel', 16)->default('email');
            $table->string('reason', 64)->nullable();
            $table->string('source', 64)->default('system');
            $table->json('metadata')->nullable();
            $table->timestamp('occurred_at');
            $table->timestamps();
        });

        Schema::create('unsubscribe_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('email', 320)->nullable();
            $table->char('token_hash', 64)->unique();
            $table->string('scope', 32)->default('marketing');
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('used_at')->nullable();
            $table->string('last_used_ip', 45)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function test_authenticated_user_can_read_and_update_email_preferences(): void
    {
        $this->withoutMiddleware(EnsureUserIsActive::class);

        $user = User::query()->create([
            'name' => 'Lifecycle User',
            'email' => 'lifecycle@example.com',
            'password' => 'password1234',
            'status' => 'active',
            'role' => 'customer',
        ]);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/behavior/email-preferences')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.marketing_enabled', true)
            ->assertJsonPath('data.ai_marketing_enabled', false)
            ->assertJsonPath('data.price_alerts_enabled', true)
            ->assertJsonPath('data.cart_reminders_enabled', true)
            ->assertJsonPath('data.browse_reminders_enabled', true);

        $this->actingAs($user, 'sanctum')
            ->putJson('/api/v1/behavior/email-preferences', [
                'marketing_enabled' => false,
                'ai_marketing_enabled' => true,
                'price_alerts_enabled' => false,
                'browse_reminders_enabled' => false,
                'timezone' => 'UTC',
                'quiet_hours_start' => 1320,
                'quiet_hours_end' => 420,
            ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.marketing_enabled', false)
            ->assertJsonPath('data.ai_marketing_enabled', false)
            ->assertJsonPath('data.price_alerts_enabled', false)
            ->assertJsonPath('data.browse_reminders_enabled', false)
            ->assertJsonPath('data.timezone', 'UTC')
            ->assertJsonPath('data.quiet_hours_start', 1320)
            ->assertJsonPath('data.quiet_hours_end', 420);

        $this->actingAs($user, 'sanctum')
            ->putJson('/api/v1/behavior/email-preferences', [
                'marketing_enabled' => true,
                'ai_marketing_enabled' => true,
            ])
            ->assertOk()
            ->assertJsonPath('data.ai_marketing_enabled', true);

        $this->assertNotNull(EmailPreference::query()->where('user_id', $user->id)->value('ai_marketing_consent_at'));
        $this->assertDatabaseHas(EmailPreference::class, [
            'user_id' => $user->id,
            'marketing_enabled' => 1,
            'ai_marketing_enabled' => 1,
            'price_alerts_enabled' => 0,
            'browse_reminders_enabled' => 0,
            'timezone' => 'UTC',
            'quiet_hours_start' => 1320,
            'quiet_hours_end' => 420,
        ]);

        $this->actingAs($user, 'sanctum')
            ->putJson('/api/v1/behavior/email-preferences', ['ai_marketing_enabled' => false])
            ->assertOk()
            ->assertJsonPath('data.ai_marketing_enabled', false);
        $this->assertDatabaseHas('suppression_events', [
            'user_id' => $user->id,
            'event_type' => 'unsubscribe',
            'reason' => 'ai_marketing_opt_out',
        ]);
    }

    public function test_graph_refreshes_rejected_tokens_and_never_retries_accepted_mail(): void
    {
        \Illuminate\Support\Facades\Cache::flush();
        config(['services.azure.tenant_id' => 'test-tenant', 'services.azure.client_id' => 'test-client',
            'services.azure.client_secret' => 'test-only-secret', 'services.azure.from_email' => 'sender@example.com']);
        \Illuminate\Support\Facades\Http::preventStrayRequests();
        \Illuminate\Support\Facades\Http::fake([
            'login.microsoftonline.com/*' => \Illuminate\Support\Facades\Http::sequence()
                ->push(['access_token' => 'expired-token', 'expires_in' => 3600])
                ->push(['access_token' => 'new-token', 'expires_in' => 3600]),
            'graph.microsoft.com/*' => \Illuminate\Support\Facades\Http::sequence()
                ->push(['error' => ['code' => 'InvalidAuthenticationToken']], 401)->push([], 202),
        ]);
        $this->assertTrue(app(\App\Services\AzureGraphMailService::class)->sendTestEmail('recipient@example.test'));
        \Illuminate\Support\Facades\Http::assertSentCount(4);
        $this->assertTrue(AppSetting::getValue('lifecycle.mail.last_delivery')['accepted']);
    }

    public function test_graph_provider_failures_are_visible_and_missing_config_does_not_send(): void
    {
        \Illuminate\Support\Facades\Cache::flush();
        config(['services.azure.tenant_id' => 'test-tenant', 'services.azure.client_id' => 'test-client',
            'services.azure.client_secret' => 'test-only-secret', 'services.azure.from_email' => 'sender@example.com']);
        \Illuminate\Support\Facades\Http::preventStrayRequests();
        \Illuminate\Support\Facades\Http::fake([
            'login.microsoftonline.com/*' => \Illuminate\Support\Facades\Http::response(['access_token' => 'token', 'expires_in' => 3600]),
            'graph.microsoft.com/*' => \Illuminate\Support\Facades\Http::response(['error' => ['code' => 'ErrorAccessDenied']], 403),
        ]);
        $mailer = app(\App\Services\AzureGraphMailService::class);
        $this->assertFalse($mailer->sendTestEmail('recipient@example.com'));
        \Illuminate\Support\Facades\Http::assertSentCount(2);
        $this->assertSame('ErrorAccessDenied', AppSetting::getValue('lifecycle.mail.last_delivery')['provider_code']);
        config(['services.azure.client_secret' => '']);
        $this->assertSame(['client_secret'], $mailer->configurationHealth()['missing_fields']);
        $this->assertFalse($mailer->sendTestEmail('recipient@example.com'));
        \Illuminate\Support\Facades\Http::assertSentCount(2);
    }

    public function test_search_campaign_matches_real_interest_skips_purchases_and_is_acceptance_idempotent(): void
    {
        $this->createSearchCampaignTables();
        $this->travelTo(\Illuminate\Support\Carbon::parse('2026-10-12 12:00:00'));
        $user = User::create(['name' => 'Buyer', 'email' => 'search@example.com', 'password' => 'password', 'status' => 'active']);
        \DB::table('products')->insert([
            ['id' => 1, 'tdsynnex_sku_no' => '9001', 'product_name' => 'Business laptop', 'mfg_part_no' => 'LP-1', 'base_price' => 100, 'is_available' => true, 'is_discontinued' => false, 'quantity' => 10],
            ['id' => 2, 'tdsynnex_sku_no' => '9002', 'product_name' => 'Purchased laptop', 'mfg_part_no' => 'LP-2', 'base_price' => 100, 'is_available' => true, 'is_discontinued' => false, 'quantity' => 10],
            ['id' => 3, 'tdsynnex_sku_no' => '9003', 'product_name' => 'Unrelated printer', 'mfg_part_no' => 'PR-1', 'base_price' => 100, 'is_available' => true, 'is_discontinued' => false, 'quantity' => 10],
        ]);
        \App\Models\SearchProfile::create(['identity_key' => 'user:'.$user->id, 'user_id' => $user->id,
            'terms' => [['term' => 'laptop', 'termKey' => 'laptop', 'lastSearched' => now()->subDays(2)->toISOString()]]]);
        \DB::table('orders')->insert(['user_id' => $user->id, 'status' => 'confirmed',
            'items' => json_encode([['productId' => '9002']]), 'created_at' => now()->subDay()]);
        $mailer = \Mockery::mock(\App\Services\AzureGraphMailService::class);
        $mailer->shouldReceive('sendSearchReminderEmail')->once()->withArgs(function ($recipient, $term, $products) use ($user) {
            return $recipient->id === $user->id && $term === 'laptop' && count($products) === 1 && $products[0]->id === 1;
        })->andReturn(false);
        $job = new \App\Jobs\ProcessReminderSubscriptionsJob;
        $preferences = app(UserEmailPreferenceService::class);
        $job->handle($mailer, $preferences);
        $this->assertSame(1, AppSetting::getValue('lifecycle.reminders.last_metrics')['delivery_failed']);
        $this->assertSame(0, SuppressionEvent::count());
        $accepted = \Mockery::mock(\App\Services\AzureGraphMailService::class);
        $accepted->shouldReceive('sendSearchReminderEmail')->once()->andReturn(true);
        $job->handle($accepted, $preferences);
        $job->handle($accepted, $preferences);
        $this->assertSame(2, SuppressionEvent::count());
        $this->assertSame(0, AppSetting::getValue('lifecycle.reminders.last_metrics')['search_sent']);
        $this->travel(1)->days();
        EmailPreference::where('user_id', $user->id)->update(['browse_reminders_enabled' => false]);
        $job->handle($accepted, $preferences);
        $this->assertSame(2, SuppressionEvent::count());
    }

    private function createSearchCampaignTables(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('tdsynnex_sku_no');
            $table->unsignedBigInteger('tdsynnex_product_id')->nullable();
            $table->string('product_name');
            $table->string('mfg_part_no');
            $table->decimal('base_price');
            $table->boolean('is_available');
            $table->boolean('is_discontinued');
            $table->integer('quantity');
        });
        Schema::create('search_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('identity_key')->unique();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->json('terms');
            $table->timestamps();
        });
        Schema::create('reminder_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_active')->default(true);
            $table->string('trigger_type');
            $table->string('channel')->default('email');
            $table->string('identity_key')->nullable();
            $table->unsignedInteger('delay_minutes')->default(1440);
            $table->unsignedInteger('cooldown_minutes')->default(1440);
            $table->timestamp('last_notified_at')->nullable();
            $table->json('metadata')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->timestamps();
        });
        Schema::create('user_cart_snapshots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->json('items');
            $table->string('identity_key')->nullable();
            $table->timestamp('last_synced_at')->nullable();
        });
        Schema::create('user_product_views', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('product_id');
            $table->string('identity_key');
            $table->timestamp('viewed_at');
            $table->timestamps();
        });
        foreach (['quotes', 'orders'] as $name) {
            Schema::create($name, function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('status');
                $table->json('items');
                $table->timestamps();
            });
        }
    }

    public function test_graph_token_cache_obeys_provider_expiry_and_search_email_has_working_links(): void
    {
        \Illuminate\Support\Facades\Cache::flush();
        config(['services.azure.tenant_id' => 'test-tenant', 'services.azure.client_id' => 'test-client',
            'services.azure.client_secret' => 'test-only-secret', 'services.azure.from_email' => 'sender@example.com']);
        \Illuminate\Support\Facades\Http::preventStrayRequests();
        \Illuminate\Support\Facades\Http::fake([
            'login.microsoftonline.com/*' => \Illuminate\Support\Facades\Http::response(['access_token' => 'short-token', 'expires_in' => 120]),
            'graph.microsoft.com/*' => \Illuminate\Support\Facades\Http::response([], 202),
        ]);
        $user = User::create(['name' => 'Buyer', 'email' => 'links@example.com', 'password' => 'password']);
        $product = new \App\Models\Product(['product_name' => 'Laptop', 'tdsynnex_sku_no' => '9001',
            'base_price' => 100, 'images' => ['/store/images/products/laptop.jpg']]);
        $product->id = 101;
        $mailer = app(\App\Services\AzureGraphMailService::class);
        $this->assertTrue($mailer->sendSearchReminderEmail($user, 'laptop', [$product]));
        \Illuminate\Support\Facades\Http::assertSent(function ($request) {
            if (!str_contains($request->url(), 'graph.microsoft.com')) {
                return false;
            }
            $html = $request['message']['body']['content'];
            return str_contains($html, '/products/9001') && !str_contains($html, '/products/101')
                && str_contains($html, '/images/products/laptop.jpg')
                && !str_contains($html, '/store/store/') && str_contains($html, 'unsubscribe');
        });
        $this->assertTrue($mailer->sendTestEmail('recipient@example.com'));
        \Illuminate\Support\Facades\Http::assertSentCount(3);
        $this->travel(61)->seconds();
        $this->assertTrue($mailer->sendTestEmail('recipient@example.com'));
        \Illuminate\Support\Facades\Http::assertSentCount(5);
    }

    public function test_historical_supplier_view_events_can_trigger_canonical_reminders(): void
    {
        $this->createSearchCampaignTables();
        $this->travelTo(\Illuminate\Support\Carbon::parse('2026-10-12 12:00:00'));
        $user = User::create(['name' => 'Buyer', 'email' => 'legacy@example.com', 'password' => 'password', 'status' => 'active']);
        \DB::table('products')->insert(['id' => 101, 'tdsynnex_sku_no' => '9001', 'product_name' => 'Laptop',
            'mfg_part_no' => 'LP-1', 'base_price' => 100, 'is_available' => true, 'is_discontinued' => false, 'quantity' => 10]);
        \App\Models\UserProductView::create(['user_id' => $user->id, 'identity_key' => 'user:'.$user->id,
            'product_id' => 9001, 'viewed_at' => now()->subDays(2)]);
        \App\Models\ReminderSubscription::create(['user_id' => $user->id, 'identity_key' => 'user:'.$user->id,
            'product_id' => 101, 'trigger_type' => 'viewed_product', 'channel' => 'email', 'is_active' => true,
            'delay_minutes' => 1440, 'cooldown_minutes' => 1440]);
        $mailer = \Mockery::mock(\App\Services\AzureGraphMailService::class);
        $mailer->shouldReceive('sendViewedProductReminderEmail')->once()->andReturn(true);
        (new \App\Jobs\ProcessReminderSubscriptionsJob)->handle($mailer, app(UserEmailPreferenceService::class));
        $this->assertSame(1, AppSetting::getValue('lifecycle.reminders.last_metrics')['viewed_sent']);
    }

    public function test_sign_in_claims_guest_searches_for_account_recommendations(): void
    {
        $this->createSearchCampaignTables();
        $token = 'aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee';
        \App\Models\SearchProfile::create(['identity_key' => 'guest:'.hash('sha256', $token),
            'terms' => [['term' => 'laptop', 'termKey' => 'laptop', 'count' => 2, 'lastSearched' => now()->toISOString()]]]);
        $user = User::create(['name' => 'Buyer', 'email' => 'profile@example.com', 'password' => 'password']);
        $this->actingAs($user, 'sanctum')->withCredentials()->withUnencryptedCookie('armely_search_visitor', $token)
            ->getJson('/api/v1/search-profile')->assertOk()->assertJsonPath('data.terms.0.term', 'laptop');
        $this->assertDatabaseHas('search_profiles', ['identity_key' => 'user:'.$user->id, 'user_id' => $user->id]);
        $this->assertDatabaseMissing('search_profiles', ['identity_key' => 'guest:'.hash('sha256', $token)]);
    }

    public function test_unsubscribe_link_disables_scoped_marketing_channel_and_logs_event(): void
    {
        $user = User::query()->create([
            'name' => 'Alert User',
            'email' => 'alerts@example.com',
            'password' => 'password1234',
            'status' => 'active',
            'role' => 'customer',
        ]);

        $service = app(UserEmailPreferenceService::class);
        $url = $service->unsubscribeUrl($user, 'price_alerts');
        $token = basename($url);

        $this->getJson('/api/v1/behavior/unsubscribe/' . $token)
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.scope', 'price_alerts')
            ->assertJsonPath('data.preferences.marketing_enabled', false)
            ->assertJsonPath('data.preferences.ai_marketing_enabled', false)
            ->assertJsonPath('data.preferences.price_alerts_enabled', false);

        $this->assertDatabaseHas(EmailPreference::class, [
            'user_id' => $user->id,
            'marketing_enabled' => 0,
            'ai_marketing_enabled' => 0,
            'price_alerts_enabled' => 0,
        ]);

        $this->assertDatabaseHas(SuppressionEvent::class, [
            'user_id' => $user->id,
            'event_type' => 'unsubscribe',
            'reason' => 'one_click_unsubscribe',
        ]);
    }
}
