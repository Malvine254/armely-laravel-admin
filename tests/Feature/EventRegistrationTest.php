<?php

namespace Tests\Feature;

use App\Services\AzureMailService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Mockery\MockInterface;
use Tests\TestCase;

class EventRegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.recaptcha.bypass' => true]);
    }

    public function test_hidden_registration_page_is_available_and_noindex(): void
    {
        $this->get(route('events.sovereign-data-cloud.register'))
            ->assertOk()
            ->assertSee('noindex,nofollow,noarchive', false)
            ->assertSee('Sovereign Data Clouds with Snowflake');
    }

    public function test_personal_email_domain_is_rejected(): void
    {
        $this->from(route('events.sovereign-data-cloud.register'))
            ->post(route('events.sovereign-data-cloud.register.store'), $this->payload([
                'work_email' => 'person@gmail.com',
            ]))
            ->assertSessionHasErrors('work_email');

        $this->assertDatabaseCount('event_registrations', 0);
    }

    public function test_company_email_registration_is_stored_and_sends_both_emails(): void
    {
        $this->mock(AzureMailService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('sendEmail')->atLeast()->times(2)->andReturnTrue();
        });

        $this->post(route('events.sovereign-data-cloud.register.store'), $this->payload())
            ->assertRedirect(route('events.sovereign-data-cloud.register'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('event_registrations', [
            'work_email' => 'sarah@city.gov',
            'organization' => 'City Technology Office',
        ]);
    }

    public function test_ajax_registration_returns_json_without_a_page_reload(): void
    {
        $this->mock(AzureMailService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('sendEmail')->atLeast()->times(2)->andReturnTrue();
        });

        $this->postJson(route('events.sovereign-data-cloud.register.store'), $this->payload([
            'work_email' => 'alex@county.gov',
        ]))
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['message']);
    }

    public function test_new_registration_notifies_each_active_admin_once(): void
    {
        config(['mail.event_registration_to' => 'events@armely.com']);
        $recipients = [];

        DB::table('admin')->insert([
            [
                'name' => 'Active Admin',
                'email' => 'active-admin@armely.com',
                'password' => 'not-used',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Inactive Admin',
                'email' => 'inactive-admin@armely.com',
                'password' => 'not-used',
                'status' => 'inactive',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->mock(AzureMailService::class, function (MockInterface $mock) use (&$recipients): void {
            $mock->shouldReceive('sendEmail')
                ->andReturnUsing(function ($from, $to) use (&$recipients): bool {
                    $recipients[] = $to;

                    return true;
                });
        });

        $this->post(route('events.sovereign-data-cloud.register.store'), $this->payload())
            ->assertRedirect(route('events.sovereign-data-cloud.register'));

        $this->assertContains('sarah@city.gov', $recipients);
        $this->assertContains('events@armely.com', $recipients);
        $this->assertContains('ask.me@armely.com', $recipients);
        $this->assertContains('active-admin@armely.com', $recipients);
        $this->assertNotContains('inactive-admin@armely.com', $recipients);
        $this->assertSame(1, count(array_keys($recipients, 'active-admin@armely.com', true)));
    }

    public function test_signed_unsubscribe_link_suppresses_future_event_email(): void
    {
        $id = DB::table('event_registrations')->insertGetId([
            'event_name' => 'Private Event',
            'full_name' => 'Alex Morgan',
            'work_email' => 'alex@county.gov',
            'organization' => 'County',
            'job_title' => 'CIO / CTO',
            'status' => 'verified',
            'unsubscribe_token' => 'secure-unsubscribe-token',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $url = URL::signedRoute('events.emails.unsubscribe', [
            'token' => 'secure-unsubscribe-token',
        ]);

        $this->get($url)->assertOk()->assertSee('You’re unsubscribed');
        $this->assertDatabaseHas('event_email_unsubscribes', [
            'email' => 'alex@county.gov',
        ]);
        $this->assertDatabaseHas('event_registrations', ['id' => $id]);
    }

    public function test_tampered_unsubscribe_link_is_rejected(): void
    {
        $this->get('/event-emails/unsubscribe/fake-token?signature=invalid')
            ->assertForbidden();
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'full_name' => 'Sarah Jenkins',
            'work_email' => 'sarah@city.gov',
            'organization' => 'City Technology Office',
            'job_title' => 'CIO / CTO',
            'compliance_focus' => 'CJIS Compliance & Law Enforcement Data',
            'website' => '',
            'g-recaptcha-response' => 'test-token',
        ], $overrides);
    }
}
