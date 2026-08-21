<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ReminderSubscription;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BehaviorFavoriteReminderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('status')->default('active');
            $table->string('role')->default('customer');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tdsynnex_product_id')->nullable();
            $table->string('tdsynnex_sku_no')->nullable();
            $table->string('product_name')->nullable();
            $table->string('mfg_part_no')->nullable();
            $table->decimal('base_price', 12, 2)->default(0);
            $table->decimal('sale_price', 12, 2)->default(0);
            $table->boolean('is_on_sale')->default(false);
            $table->string('offer_source')->nullable();
            $table->timestamps();
        });

        Schema::create('price_alert_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('identity_key', 96);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('product_id');
            $table->decimal('baseline_price', 15, 2)->nullable();
            $table->decimal('min_drop_amount', 15, 2)->default(0);
            $table->decimal('min_drop_percent', 7, 2)->default(0);
            $table->unsignedInteger('cooldown_minutes')->default(1440);
            $table->timestamp('last_notified_at')->nullable();
            $table->string('source', 24)->default('manual');
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('reminder_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('identity_key', 96);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('trigger_type', 32);
            $table->string('channel', 16)->default('email');
            $table->unsignedInteger('delay_minutes')->default(120);
            $table->unsignedInteger('cooldown_minutes')->default(1440);
            $table->timestamp('last_notified_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('user_favorite_events', function (Blueprint $table) {
            $table->id();
            $table->string('identity_key', 96);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('product_id');
            $table->string('event_type', 16);
            $table->json('metadata')->nullable();
            $table->timestamp('event_at');
            $table->timestamps();
        });

        Schema::create('user_cart_snapshots', function (Blueprint $table) {
            $table->id();
            $table->string('identity_key', 96)->unique();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->json('items');
            $table->unsignedInteger('item_count')->default(0);
            $table->unsignedInteger('total_quantity')->default(0);
            $table->timestamp('last_synced_at');
            $table->timestamps();
        });

        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        \DB::table('products')->insert([
            'id' => 101,
            'tdsynnex_product_id' => 9001,
            'tdsynnex_sku_no' => '9001',
            'product_name' => 'Rugged Notebook',
            'mfg_part_no' => 'RN-101',
            'base_price' => 1200,
            'sale_price' => 1100,
            'is_on_sale' => true,
            'offer_source' => 'manual',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_favorite_add_creates_active_favorite_reminder_and_remove_disables_it(): void
    {
        $user = User::query()->create([
            'name' => 'Favorite User',
            'email' => 'favorite@example.com',
            'password' => 'secret123',
            'status' => 'active',
            'role' => 'customer',
        ]);

        $addResponse = $this->actingAs($user, 'sanctum')->postJson('/api/v1/behavior/favorite-event', [
            'product_id' => 101,
            'event_type' => 'add',
        ]);

        $addResponse->assertOk()->assertJsonPath('success', true);

        $subscription = ReminderSubscription::query()
            ->where('trigger_type', 'favorite_product')
            ->where('product_id', 101)
            ->first();

        $this->assertNotNull($subscription);
        $this->assertTrue((bool) $subscription->is_active);

        $removeResponse = $this->actingAs($user, 'sanctum')->postJson('/api/v1/behavior/favorite-event', [
            'product_id' => 101,
            'event_type' => 'remove',
        ]);

        $removeResponse->assertOk()->assertJsonPath('success', true);

        $subscription->refresh();
        $this->assertFalse((bool) $subscription->is_active);
    }

    public function test_identical_cart_heartbeat_does_not_restart_reminder_and_empty_cart_deactivates_it(): void
    {
        $user = User::query()->create([
            'name' => 'Cart User',
            'email' => 'cart@example.com',
            'password' => 'secret123',
            'status' => 'active',
            'role' => 'customer',
        ]);

        $payload = ['items' => [['productId' => 9001, 'quantity' => 2]]];
        $this->actingAs($user, 'sanctum')->postJson('/api/v1/behavior/cart-snapshot', $payload)->assertOk();

        $subscription = ReminderSubscription::query()->where('trigger_type', 'abandoned_cart')->firstOrFail();
        $originalSyncedAt = \DB::table('user_cart_snapshots')->value('last_synced_at');
        $subscription->update(['metadata' => ['sequence_stage' => 1]]);

        $this->travel(30)->minutes();
        $this->actingAs($user, 'sanctum')->postJson('/api/v1/behavior/cart-snapshot', $payload)->assertOk();

        $this->assertSame($originalSyncedAt, \DB::table('user_cart_snapshots')->value('last_synced_at'));
        $this->assertSame(1, (int) $subscription->fresh()->metadata['sequence_stage']);

        $this->actingAs($user, 'sanctum')->postJson('/api/v1/behavior/cart-snapshot', ['items' => []])->assertOk();
        $this->assertFalse((bool) $subscription->fresh()->is_active);
    }
}
