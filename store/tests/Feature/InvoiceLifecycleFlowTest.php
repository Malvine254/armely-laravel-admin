<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\User;
use App\Services\AzureGraphMailService;
use App\Services\NotificationService;
use App\Services\UserEmailPreferenceService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Mockery;
use Tests\TestCase;

class InvoiceLifecycleFlowTest extends TestCase
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
            $table->timestamps();
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('invoice_number')->unique();
            $table->string('order_number')->nullable();
            $table->string('status')->default('issued');
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->json('items')->nullable();
            $table->json('raw_data')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('issued_email_attempted_at')->nullable();
            $table->timestamp('issued_email_sent_at')->nullable();
            $table->timestamp('due_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->string('pdf_url')->nullable();
            $table->string('stripe_checkout_session_id')->nullable();
            $table->string('stripe_payment_intent_id')->nullable();
            $table->timestamps();
        });
    }

    public function test_purchase_conversions_require_full_payment_and_invoice_ownership(): void
    {
        // This fixture has no company tables; keep authentication but skip company eligibility.
        $this->withoutMiddleware(\App\Http\Middleware\EnsureUserIsActive::class);
        $user = User::create(['name' => 'Buyer', 'email' => 'buyer@example.com', 'password' => 'secret']);
        foreach (['paid', 'issued', 'partial', 'cancelled', 'merged', 'other-user', 'no-date'] as $index => $state) {
            Invoice::create([
                'user_id' => $state === 'other-user' ? $user->id + 1 : $user->id,
                'invoice_number' => 'INV-'.$state,
                'status' => in_array($state, ['other-user', 'no-date', 'partial']) ? 'paid' : $state,
                'total_amount' => 115,
                'tax_amount' => 10,
                'paid_amount' => $state === 'partial' ? 50 : 115,
                'paid_at' => $state === 'no-date' ? null : now(),
                'raw_data' => ['invoice_charge_breakdown' => ['shipping_amount' => 5]],
            ]);
        }
        $response = $this->actingAs($user)->postJson('/api/v1/purchase-conversions', [
            'invoice_numbers' => ['INV-paid', 'INV-issued', 'INV-partial', 'INV-cancelled', 'INV-merged', 'INV-other-user', 'INV-no-date'],
        ]);
        $response->assertOk()->assertJsonCount(1, 'purchases')
            ->assertJsonPath('purchases.0.currency', 'USD')
            ->assertJsonPath('purchases.0.value', 100)
            ->assertJsonPath('purchases.0.total', 115);
    }

    public function test_issued_invoice_email_is_sent_only_once_even_without_cache(): void
    {
        $user = User::query()->create([
            'name' => 'Invoice Customer',
            'email' => 'invoice@example.com',
            'password' => 'secret',
        ]);
        $invoice = Invoice::query()->create([
            'user_id' => $user->id,
            'invoice_number' => 'INV-FLOW-001',
            'order_number' => 'ORD-FLOW-001',
            'total_amount' => 125,
        ]);

        $preference = (object) [
            'notification_email_enabled' => true,
            'invoices_notifications_enabled' => true,
            'transactional_enabled' => true,
        ];
        $preferences = Mockery::mock(UserEmailPreferenceService::class);
        $preferences->shouldReceive('ensurePreference')->twice()->andReturn($preference);
        $mailer = Mockery::mock(AzureGraphMailService::class);
        $mailer->shouldReceive('sendInvoiceEmail')->once()->andReturnTrue();

        $service = new NotificationService($mailer, $preferences);

        $this->assertTrue($service->sendInvoiceNotification($invoice));
        $this->assertFalse($service->sendInvoiceNotification($invoice->fresh()));
        $this->assertNotNull($invoice->fresh()->issued_email_sent_at);
    }
}
