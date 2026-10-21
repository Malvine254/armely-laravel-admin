<?php

namespace Tests\Feature;

use App\Models\EmailPreference;
use App\Models\MarketingCampaign;
use App\Models\MarketingCampaignDecision;
use App\Models\Product;
use App\Models\SuppressionEvent;
use App\Models\User;
use App\Services\CustomerIntelligenceService;
use App\Services\MarketingDecisionEngine;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AiMarketingFoundationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('password');
            $table->string('status')->default('active');
            $table->string('role')->default('customer');
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps();
        });

        $migration = require database_path('migrations/2026_10_21_120000_create_ai_marketing_tables.php');
        $migration->up();

        Schema::create('email_preferences', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained();
            $table->boolean('marketing_enabled')->default(false);
            $table->boolean('browse_reminders_enabled')->default(false);
            $table->timestamps();
        });
        $consentMigration = require database_path('migrations/2026_10_21_120100_add_ai_marketing_consent_to_email_preferences.php');
        $consentMigration->up();

        Schema::create('suppression_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable();
            $table->string('email')->nullable();
            $table->string('event_type');
            $table->timestamp('occurred_at');
            $table->timestamps();
        });

        Schema::create('user_product_views', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable();
            $table->unsignedBigInteger('product_id');
            $table->timestamp('viewed_at');
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('product_name');
            $table->boolean('is_available')->default(true);
            $table->boolean('is_discontinued')->default(false);
            $table->integer('quantity')->default(0);
            $table->decimal('base_price', 15, 2)->nullable();
            $table->decimal('supplier_regular_price', 15, 2)->nullable();
            $table->decimal('sale_price', 15, 2)->nullable();
            $table->boolean('is_on_sale')->default(false);
            $table->string('offer_source')->nullable();
            $table->boolean('is_storefront_curated')->default(false);
            $table->timestamps();
        });
    }

    public function test_personalized_recommendation_campaign_is_disabled_by_default(): void
    {
        config([
            'ai_marketing.enabled' => false,
            'ai_marketing.customer_intelligence_enabled' => false,
            'ai_marketing.auto_send_enabled' => false,
        ]);

        $campaign = MarketingCampaign::query()
            ->where('type', 'personalized_recommendation')
            ->firstOrFail();

        $this->assertSame('disabled', $campaign->status);
        $this->assertFalse(config('ai_marketing.enabled'));
        $this->assertFalse(config('ai_marketing.customer_intelligence_enabled'));
        $this->assertFalse(config('ai_marketing.auto_send_enabled'));
    }

    public function test_eligible_recommendations_are_recorded_without_sending_mail(): void
    {
        config([
            'ai_marketing.enabled' => true,
            'ai_marketing.customer_intelligence_enabled' => true,
            'ai_marketing.auto_send_enabled' => true,
        ]);

        $user = $this->createEligibleUser();
        $campaign = MarketingCampaign::query()
            ->where('type', 'personalized_recommendation')
            ->firstOrFail();
        $campaign->update(['status' => 'active']);

        $viewedProduct = Product::query()->create([
            'category_id' => 3,
            'product_name' => 'Viewed Business Laptop',
            'is_available' => true,
            'is_discontinued' => false,
            'quantity' => 5,
            'base_price' => 900,
        ]);
        $recommendedProduct = Product::query()->create([
            'category_id' => 3,
            'product_name' => 'Recommended Business Laptop',
            'is_available' => true,
            'is_discontinued' => false,
            'quantity' => 5,
            'base_price' => 950,
        ]);
        for ($index = 0; $index < 3; $index++) {
            \Illuminate\Support\Facades\DB::table('user_product_views')->insert([
                'user_id' => $user->id,
                'product_id' => $viewedProduct->id,
                'viewed_at' => now()->subHours($index),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $intelligence = \Mockery::mock(CustomerIntelligenceService::class);
        $intelligence->shouldReceive('refresh')->once()->withArgs(fn (User $argument): bool => $argument->is($user));
        $this->app->instance(CustomerIntelligenceService::class, $intelligence);

        $decision = app(MarketingDecisionEngine::class)->evaluate($user);

        $this->assertInstanceOf(MarketingCampaignDecision::class, $decision);
        $this->assertSame('dry_run', $decision->status);
        $this->assertStringContainsString('sending is blocked', $decision->reason);
        $this->assertSame([(int) $recommendedProduct->id], $decision->product_ids);
        $this->assertDatabaseCount('marketing_email_history', 0);
    }

    public function test_unsubscribed_customer_is_suppressed_before_profile_refresh(): void
    {
        config([
            'ai_marketing.enabled' => true,
            'ai_marketing.customer_intelligence_enabled' => true,
        ]);

        $user = $this->createEligibleUser();
        MarketingCampaign::query()
            ->where('type', 'personalized_recommendation')
            ->update(['status' => 'active']);
        SuppressionEvent::query()->create([
            'user_id' => $user->id,
            'email' => $user->email,
            'event_type' => 'unsubscribe',
            'occurred_at' => now(),
        ]);

        $intelligence = \Mockery::mock(CustomerIntelligenceService::class);
        $intelligence->shouldNotReceive('refresh');
        $this->app->instance(CustomerIntelligenceService::class, $intelligence);

        $decision = app(MarketingDecisionEngine::class)->evaluate($user);

        $this->assertSame('suppressed', $decision->status);
        $this->assertSame([], $decision->product_ids);
    }

    private function createEligibleUser(): User
    {
        $user = User::query()->create([
            'name' => 'Marketing Test',
            'email' => 'marketing@example.test',
            'password' => 'password',
            'email_verified_at' => now(),
            'status' => 'active',
            'role' => 'customer',
        ]);
        EmailPreference::query()->create([
            'user_id' => $user->id,
            'marketing_enabled' => true,
            'ai_marketing_enabled' => true,
            'ai_marketing_consent_at' => now(),
            'browse_reminders_enabled' => true,
        ]);

        return $user;
    }
}
