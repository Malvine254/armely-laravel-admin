<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureUserIsActive;
use App\Models\User;
use App\Services\AzureGraphMailService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdminAccountManagementTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(EnsureUserIsActive::class);
        $this->withoutMiddleware(\App\Http\Middleware\KickOffDuePriceSync::class);
        $this->mock(\Illuminate\Contracts\Validation\UncompromisedVerifier::class,
            fn ($mock) => $mock->shouldReceive('verify')->andReturn(true));
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone')->nullable();
            $table->string('role')->default('buyer');
            $table->string('status')->default('active');
            $table->json('permissions')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->boolean('force_password_change')->default(false);
            $table->timestamp('temp_password_expires_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
        Schema::create('companies', function (Blueprint $table) { $table->id(); $table->string('name'); });
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary(); $table->string('token'); $table->timestamp('created_at');
        });
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id(); $table->morphs('tokenable'); $table->string('name'); $table->string('token', 64)->unique();
            $table->text('abilities')->nullable(); $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable(); $table->timestamps();
        });
    }

    public function test_customer_cannot_read_or_change_accounts(): void
    {
        $actor = User::factory()->create(['role' => 'buyer']);
        $target = User::factory()->create(['role' => 'buyer']);
        $this->actingAs($actor, 'sanctum');
        $this->getJson("/api/v1/admin/accounts/{$target->id}")->assertForbidden();
        $this->putJson("/api/v1/admin/accounts/{$target->id}", [])->assertForbidden();
        $this->postJson("/api/v1/admin/accounts/{$target->id}/reset-password")->assertForbidden();
    }

    public function test_permissions_and_admin_account_protection(): void
    {
        $actor = User::factory()->create(['role' => 'admin', 'permissions' => ['manage_orders']]);
        $target = User::factory()->create(['role' => 'buyer']);
        $this->actingAs($actor, 'sanctum')->getJson("/api/v1/admin/accounts/{$target->id}")->assertForbidden();
        $actor->update(['permissions' => ['manage_customers', 'manage_admins']]);
        $admin = User::factory()->create(['role' => 'admin']);
        $this->getJson("/api/v1/admin/accounts/{$admin->id}")->assertOk()->assertJsonPath('can_manage', false);
        $this->putJson("/api/v1/admin/accounts/{$admin->id}", [])->assertForbidden();
        $this->postJson("/api/v1/admin/accounts/{$admin->id}/reset-password")->assertForbidden();
    }

    public function test_profile_update_excludes_secrets_and_invalidates_old_email_access(): void
    {
        $actor = User::factory()->create(['role' => 'super_admin']);
        $target = User::factory()->create(['role' => 'admin']);
        $target->createToken('existing');
        DB::table('password_reset_tokens')->insert(['email' => $target->email, 'token' => 'old', 'created_at' => now()]);
        $this->actingAs($actor, 'sanctum')->putJson("/api/v1/admin/accounts/{$target->id}", [
            'name' => 'Updated Admin', 'email' => 'updated@example.com', 'phone' => '555-0100', 'role' => 'super_admin',
        ])->assertOk()->assertJsonPath('data.name', 'Updated Admin')->assertJsonPath('data.role', 'admin')
            ->assertJsonPath('data.email_verified_at', null)->assertJsonMissingPath('data.password')->assertJsonMissingPath('data.remember_token');
        $this->assertDatabaseCount('password_reset_tokens', 0);
        $this->assertDatabaseCount('personal_access_tokens', 0);
        $this->putJson("/api/v1/admin/accounts/{$target->id}", ['name' => 'Duplicate', 'email' => $actor->email])->assertUnprocessable();
    }

    public function test_reset_email_contains_valid_token_without_changing_password_and_is_throttled(): void
    {
        $actor = User::factory()->create(['role' => 'super_admin']);
        $target = User::factory()->create(['role' => 'admin']);
        $password = $target->password;
        $this->mock(AzureGraphMailService::class, function ($mock) use ($target) {
            $mock->shouldReceive('sendPasswordResetEmail')->once()->withArgs(function ($email, $name, $url) use ($target) {
                parse_str(parse_url($url, PHP_URL_QUERY), $query);
                $record = DB::table('password_reset_tokens')->where('email', $email)->first();
                return $email === $target->email && $query['email'] === $email && Hash::check($query['token'], $record->token);
            })->andReturn(true);
        });
        $this->actingAs($actor, 'sanctum')->postJson("/api/v1/admin/accounts/{$target->id}/reset-password")
            ->assertOk()->assertJsonMissingPath('token');
        $this->assertSame($password, $target->fresh()->password);
        $this->postJson("/api/v1/admin/accounts/{$target->id}/reset-password")->assertStatus(429);
    }

    public function test_mail_failure_is_reported_and_token_removed(): void
    {
        $actor = User::factory()->create(['role' => 'super_admin']);
        $target = User::factory()->create();
        $this->mock(AzureGraphMailService::class, fn ($mock) => $mock->shouldReceive('sendPasswordResetEmail')->once()->andReturn(false));
        $this->actingAs($actor, 'sanctum')->postJson("/api/v1/admin/accounts/{$target->id}/reset-password")->assertStatus(502);
        $this->assertDatabaseCount('password_reset_tokens', 0);
    }

    public function test_expired_reset_is_rejected_and_valid_reset_clears_temporary_password_requirement(): void
    {
        $target = User::factory()->create(['force_password_change' => true, 'temp_password_expires_at' => now()->addHour()]);
        DB::table('password_reset_tokens')->insert(['email' => $target->email, 'token' => Hash::make('test-token'), 'created_at' => now()->subMinutes(61)]);
        $payload = ['email' => $target->email, 'token' => 'test-token', 'password' => 'Secure-Test-934!x', 'password_confirmation' => 'Secure-Test-934!x'];
        $this->postJson('/api/v1/auth/reset-password', $payload)->assertUnprocessable();
        DB::table('password_reset_tokens')->insert(['email' => $target->email, 'token' => Hash::make('test-token'), 'created_at' => now()]);
        $this->postJson('/api/v1/auth/reset-password', $payload)->assertOk();
        $this->assertFalse($target->fresh()->force_password_change);
        $this->assertNull($target->fresh()->temp_password_expires_at);
        $this->postJson('/api/v1/auth/reset-password', $payload)->assertUnprocessable();
    }
}
