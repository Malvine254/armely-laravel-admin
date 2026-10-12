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
            $table->unsignedInteger('revision')->default(0);
            $table->timestamps();
        });

        Schema::create('user_product_views', function (Blueprint $table) {
            $table->id();
            $table->string('identity_key', 96);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('product_id');
            $table->timestamp('viewed_at');
            $table->timestamps();
        });

        Schema::create('user_cart_events', function (Blueprint $table) {
            $table->id();
            $table->string('identity_key', 96);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('event_type', 24);
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedInteger('quantity')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('event_at');
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

    public function test_supplier_identifiers_are_canonicalized_for_browse_and_favorite_events(): void
    {
        $user = User::create(['name' => 'Buyer', 'email' => 'buyer@example.com', 'password' => 'password']);
        $this->actingAs($user, 'sanctum')->postJson('/api/v1/behavior/product-view', ['product_id' => 9001])->assertOk();
        $this->postJson('/api/v1/behavior/favorite-event', ['product_id' => 9001, 'event_type' => 'add'])->assertOk();
        $this->assertDatabaseHas('user_product_views', ['product_id' => 101, 'user_id' => $user->id]);
        $this->assertDatabaseHas('user_favorite_events', ['product_id' => 101, 'user_id' => $user->id]);
        $this->assertDatabaseHas('reminder_subscriptions', ['product_id' => 101, 'trigger_type' => 'viewed_product']);
        $this->postJson('/api/v1/behavior/favorite-event', [
            'product_id' => 9001, 'event_type' => 'toggle', 'metadata' => ['state' => 'off'],
        ])->assertOk();
        $this->assertDatabaseHas('reminder_subscriptions', ['product_id' => 101, 'trigger_type' => 'favorite_product', 'is_active' => false]);
    }

    public function test_account_cart_restores_merges_max_quantity_rejects_stale_writes_and_ignores_forged_prices(): void
    {
        $this->withoutMiddleware(\App\Http\Middleware\EnsureUserIsActive::class);
        $user = User::create(['name' => 'Buyer', 'email' => 'cart-buyer@example.com', 'password' => 'password']);
        $this->actingAs($user, 'sanctum')->getJson('/api/v1/cart')->assertOk()->assertJsonPath('data.revision', 0);
        $this->putJson('/api/v1/cart', [
            'revision' => 0, 'items' => [['productId' => '9001', 'quantity' => 4, 'productPrice' => [['rsPrice' => 0.01]]]],
        ])->assertOk()->assertJsonPath('data.revision', 1)->assertJsonPath('data.items.0.productPrice.0.rsPrice', 1100);
        $this->postJson('/api/v1/cart/merge', ['items' => [['productId' => '9001', 'quantity' => 2]]])
            ->assertOk()->assertJsonPath('data.items.0.quantity', 4)->assertJsonPath('data.revision', 1);
        $this->postJson('/api/v1/cart/merge', ['items' => [['productId' => '9001', 'quantity' => 6]]])
            ->assertOk()->assertJsonPath('data.items.0.quantity', 6)->assertJsonPath('data.revision', 2);
        $this->putJson('/api/v1/cart', ['revision' => 1, 'items' => []])->assertStatus(409);
        $this->postJson('/api/v1/behavior/cart-snapshot', ['items' => []])->assertUnprocessable();
        $this->postJson('/api/v1/behavior/cart-snapshot', ['revision' => 1, 'items' => []])->assertStatus(409);
        $this->getJson('/api/v1/cart')->assertOk()->assertJsonPath('data.items.0.quantity', 6);
        $other = User::create(['name' => 'Other', 'email' => 'other@example.com', 'password' => 'password']);
        $this->actingAs($other, 'sanctum')->getJson('/api/v1/cart')->assertOk()->assertJsonPath('data.items', []);
        $this->actingAs($user, 'sanctum')->putJson('/api/v1/cart', ['revision' => 2, 'items' => []])
            ->assertOk()->assertJsonPath('data.items', [])->assertJsonPath('data.revision', 3);
        $this->assertDatabaseHas('reminder_subscriptions', ['user_id' => $user->id, 'trigger_type' => 'abandoned_cart', 'is_active' => false]);
    }

    public function test_missing_cart_products_are_retained_and_guest_behavior_is_claimed_during_merge(): void
    {
        $this->withoutMiddleware(\App\Http\Middleware\EnsureUserIsActive::class);
        $token = 'guest-cart-test-token-123456';
        $this->postJson('/api/v1/behavior/product-view', ['product_id' => 9001, 'visitor_token' => $token])->assertOk();
        $user = User::create(['name' => 'Buyer', 'email' => 'merge@example.com', 'password' => 'password']);
        $this->actingAs($user, 'sanctum')->postJson('/api/v1/cart/merge', [
            'items' => [['productId' => 'missing-product', 'quantity' => 2]], 'visitor_token' => $token,
        ])->assertOk()->assertJsonPath('data.items.0.productId', 'missing-product')
            ->assertJsonPath('data.items.0.isAvailable', false)->assertJsonPath('data.items.0.quantity', 2);
        $this->assertDatabaseHas('user_product_views', ['identity_key' => 'user:'.$user->id, 'product_id' => 101]);
    }

    public function test_pending_users_can_save_carts_but_suspension_takes_priority(): void
    {
        $user = new User(['name' => 'Pending', 'status' => 'pending', 'email_verified_at' => null]);
        $user->setRelation('company', (object) ['status' => 'pending']);
        $request = \Illuminate\Http\Request::create('/api/v1/cart', 'PUT');
        $request->setUserResolver(fn () => $user);
        $middleware = new \App\Http\Middleware\EnsureUserIsActive;
        $next = fn () => response()->json(['allowed' => true]);
        $this->assertSame(200, $middleware->handle($request, $next)->getStatusCode());
        $user->status = 'suspended';
        $this->assertSame(403, $middleware->handle($request, $next)->getStatusCode());
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

        $payload = ['revision' => 0, 'items' => [['productId' => '9001', 'quantity' => 2]]];
        $this->actingAs($user, 'sanctum')->postJson('/api/v1/behavior/cart-snapshot', $payload)->assertOk();

        $subscription = ReminderSubscription::query()->where('trigger_type', 'abandoned_cart')->firstOrFail();
        $originalSyncedAt = \DB::table('user_cart_snapshots')->value('last_synced_at');
        $subscription->update(['metadata' => ['sequence_stage' => 1]]);

        $this->travel(30)->minutes();
        $payload['revision'] = 1;
        $this->actingAs($user, 'sanctum')->postJson('/api/v1/behavior/cart-snapshot', $payload)->assertOk();

        $this->assertSame($originalSyncedAt, \DB::table('user_cart_snapshots')->value('last_synced_at'));
        $this->assertSame(1, (int) $subscription->fresh()->metadata['sequence_stage']);

        $this->actingAs($user, 'sanctum')->postJson('/api/v1/behavior/cart-snapshot', ['items' => [], 'revision' => 1])->assertOk();
        $this->assertFalse((bool) $subscription->fresh()->is_active);
    }

    public function test_guest_product_interest_is_claimed_after_login(): void
    {
        $user = User::query()->create([
            'name' => 'Known Shopper',
            'email' => 'shopper@example.com',
            'password' => 'secret123',
            'status' => 'active',
            'role' => 'customer',
        ]);

        $visitorToken = (string) \Illuminate\Support\Str::uuid();
        $viewResponse = $this->withUnencryptedCookie('armely_behavior_visitor', $visitorToken)
            ->postJson('/api/v1/behavior/product-view', ['product_id' => 101, 'visitor_token' => $visitorToken]);
        $viewResponse->assertOk();

        $issuedCookies = $viewResponse->headers->getCookies();
        if ($issuedCookies !== []) {
            $visitorToken = $issuedCookies[0]->getValue();
        }

        $this->assertDatabaseHas('reminder_subscriptions', [
            'trigger_type' => 'viewed_product',
            'product_id' => 101,
            'user_id' => null,
        ]);

        $this->actingAs($user, 'sanctum')
            ->withUnencryptedCookie('armely_behavior_visitor', $visitorToken)
            ->postJson('/api/v1/behavior/cart-snapshot', ['items' => [], 'visitor_token' => $visitorToken, 'revision' => 0])
            ->assertOk();

        $this->assertDatabaseHas('reminder_subscriptions', [
            'trigger_type' => 'viewed_product',
            'product_id' => 101,
            'user_id' => $user->id,
            'identity_key' => 'user:' . $user->id,
        ]);
    }
}
