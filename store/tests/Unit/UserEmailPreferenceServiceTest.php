<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\UserEmailPreferenceService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UserEmailPreferenceServiceTest extends TestCase
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

        Schema::create('email_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique();
            $table->boolean('transactional_enabled')->default(true);
            $table->boolean('notification_email_enabled')->default(true);
            $table->boolean('quotes_notifications_enabled')->default(true);
            $table->boolean('orders_notifications_enabled')->default(true);
            $table->boolean('invoices_notifications_enabled')->default(true);
            $table->boolean('marketing_enabled')->default(true);
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
    }

    public function test_quiet_hours_prevent_reminder_send_inside_window(): void
    {
        $service = app(UserEmailPreferenceService::class);
        $user = User::query()->create([
            'name' => 'Quiet Hours User',
            'email' => 'quiet@example.com',
            'password' => 'secret123',
            'status' => 'active',
            'role' => 'customer',
        ]);

        $pref = $service->ensurePreference($user);
        $pref->timezone = 'UTC';
        $pref->quiet_hours_start = 1320; // 22:00
        $pref->quiet_hours_end = 360; // 06:00
        $pref->save();

        $inside = Carbon::parse('2026-08-13 23:15:00', 'UTC');
        $outside = Carbon::parse('2026-08-13 14:00:00', 'UTC');

        $this->assertFalse($service->shouldSendReminder($user, 'abandoned_cart', $inside));
        $this->assertTrue($service->shouldSendReminder($user, 'abandoned_cart', $outside));
    }

    public function test_daily_send_cap_and_idempotency_markers_are_enforced(): void
    {
        $service = app(UserEmailPreferenceService::class);
        $user = User::query()->create([
            'name' => 'Cap User',
            'email' => 'cap@example.com',
            'password' => 'secret123',
            'status' => 'active',
            'role' => 'customer',
        ]);

        $this->assertTrue($service->underDailySendCap($user, 'viewed_product_reminder', 2, now()));

        $service->markMarketingSent($user, 'viewed_product_reminder', ['stage' => 0]);
        $service->markMarketingSent($user, 'viewed_product_reminder', ['stage' => 1]);

        $this->assertFalse($service->underDailySendCap($user, 'viewed_product_reminder', 2, now()));

        $idempotencyKey = 'viewed:42:1:202608131400';
        $this->assertFalse($service->wasIdempotencyKeySent($idempotencyKey));

        $service->markIdempotencyKeySent($user, $idempotencyKey, ['subscription_id' => 42]);
        $this->assertTrue($service->wasIdempotencyKeySent($idempotencyKey));
    }
}
