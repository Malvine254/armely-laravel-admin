<?php

namespace Tests\Feature;

use App\Models\EmailPreference;
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
            ->assertJsonPath('data.price_alerts_enabled', true)
            ->assertJsonPath('data.cart_reminders_enabled', true)
            ->assertJsonPath('data.browse_reminders_enabled', true);

        $this->actingAs($user, 'sanctum')
            ->putJson('/api/v1/behavior/email-preferences', [
                'marketing_enabled' => false,
                'price_alerts_enabled' => false,
                'browse_reminders_enabled' => false,
                'timezone' => 'UTC',
                'quiet_hours_start' => 1320,
                'quiet_hours_end' => 420,
            ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.marketing_enabled', false)
            ->assertJsonPath('data.price_alerts_enabled', false)
            ->assertJsonPath('data.browse_reminders_enabled', false)
            ->assertJsonPath('data.timezone', 'UTC')
            ->assertJsonPath('data.quiet_hours_start', 1320)
            ->assertJsonPath('data.quiet_hours_end', 420);

        $this->assertDatabaseHas(EmailPreference::class, [
            'user_id' => $user->id,
            'marketing_enabled' => 0,
            'price_alerts_enabled' => 0,
            'browse_reminders_enabled' => 0,
            'timezone' => 'UTC',
            'quiet_hours_start' => 1320,
            'quiet_hours_end' => 420,
        ]);
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
            ->assertJsonPath('data.preferences.price_alerts_enabled', false);

        $this->assertDatabaseHas(EmailPreference::class, [
            'user_id' => $user->id,
            'marketing_enabled' => 0,
            'price_alerts_enabled' => 0,
        ]);

        $this->assertDatabaseHas(SuppressionEvent::class, [
            'user_id' => $user->id,
            'event_type' => 'unsubscribe',
            'reason' => 'one_click_unsubscribe',
        ]);
    }
}
