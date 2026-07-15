<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\ProductSourcingRequest;
use App\Models\User;
use App\Http\Middleware\EnsureUserIsActive;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProductSourcingRequestTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role')->default('customer');
            $table->string('status')->default('active');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
        Schema::create('product_sourcing_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('search_query', 500);
            $table->string('manufacturer', 100)->nullable();
            $table->string('model_or_part_number', 150)->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->text('notes')->nullable();
            $table->string('status', 30)->default('pending');
            $table->timestamps();
        });
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('type');
            $table->string('title');
            $table->text('message');
            $table->string('reference_id')->nullable();
            $table->string('reference_type')->nullable();
            $table->string('status')->default('unread');
            $table->string('priority')->default('normal');
            $table->json('metadata')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function test_authenticated_customer_can_submit_a_product_sourcing_request(): void
    {
        $this->withoutMiddleware(EnsureUserIsActive::class);
        $customer = User::factory()->create(['role' => 'customer', 'status' => 'active']);
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);

        $response = $this->actingAs($customer, 'sanctum')->postJson('/api/v1/product-sourcing-requests', [
            'search_query' => 'Dell Pro Rugged 14 (RB14250)',
            'manufacturer' => 'Dell',
            'model_or_part_number' => 'RB14250',
            'quantity' => 2,
            'notes' => 'Core Ultra 7 configuration preferred.',
        ]);

        $response->assertCreated()->assertJsonPath('success', true);
        $this->assertDatabaseHas(ProductSourcingRequest::class, [
            'user_id' => $customer->id,
            'model_or_part_number' => 'RB14250',
            'quantity' => 2,
            'status' => 'pending',
        ]);
        $this->assertDatabaseHas(Notification::class, [
            'user_id' => $admin->id,
            'type' => 'product_sourcing_requested',
            'reference_type' => 'product_sourcing_request',
        ]);
    }

    public function test_guest_cannot_submit_a_product_sourcing_request(): void
    {
        $this->postJson('/api/v1/product-sourcing-requests', [
            'search_query' => 'Dell Pro Rugged 14 (RB14250)',
            'quantity' => 1,
        ])->assertUnauthorized();
    }
}
