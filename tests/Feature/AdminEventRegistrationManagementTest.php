<?php

namespace Tests\Feature;

use App\Http\Middleware\AdminMiddleware;
use App\Services\AzureMailService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Mockery\MockInterface;
use Tests\TestCase;

class AdminEventRegistrationManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(AdminMiddleware::class);
    }

    public function test_admin_can_verify_a_registration(): void
    {
        $id = $this->registration();

        $this->postJson(route('admin.tables.event-registrations.status', $id), [
            'status' => 'verified',
        ])->assertOk()->assertJsonPath('success', true);

        $this->assertDatabaseHas('event_registrations', [
            'id' => $id,
            'status' => 'verified',
        ]);
        $this->assertNotNull(DB::table('event_registrations')->where('id', $id)->value('verified_at'));
    }

    public function test_verifying_an_attendee_automatically_sends_the_event_link(): void
    {
        $eventId = DB::table('events')->insertGetId([
            'title' => 'Private Architecture Briefing',
            'body' => 'Briefing',
            'start_date' => '2026-10-05',
            'start_time' => '11:00',
            'timezone' => 'CST',
            'url' => 'https://teams.microsoft.com/l/meetup-join/example',
            'event_type' => 'private',
            'private_slug' => 'private-architecture-briefing',
        ]);
        $registrationId = $this->registration([
            'event_id' => $eventId,
            'event_name' => 'Private Architecture Briefing',
        ]);

        $this->mock(AzureMailService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('sendEmail')->once()->andReturnTrue();
        });

        $this->postJson(route('admin.tables.event-registrations.status', $registrationId), [
            'status' => 'verified',
        ])
            ->assertOk()
            ->assertJsonPath('invitation_sent', true);

        $this->assertNotNull(
            DB::table('event_registrations')->where('id', $registrationId)->value('event_link_sent_at')
        );
    }

    public function test_event_link_is_only_sent_once_to_verified_attendees(): void
    {
        $verifiedId = $this->registration(['status' => 'verified', 'verified_at' => now()]);
        $this->registration([
            'work_email' => 'pending@county.gov',
            'status' => 'pending',
        ]);

        $eventId = DB::table('events')->insertGetId([
            'title' => 'Sovereign Data Clouds with Snowflake',
            'body' => 'Executive briefing',
            'start_date' => '30/07/2026',
            'url' => 'https://events.example.com/private-access',
        ]);

        $this->mock(AzureMailService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('sendEmail')->once()->andReturnTrue();
        });

        $this->postJson(route('admin.tables.event-registrations.send-link'), [
            'event_id' => $eventId,
        ])
            ->assertOk()
            ->assertJsonPath('sent', 1)
            ->assertJsonPath('failed', 0);

        $this->assertDatabaseHas('event_registrations', [
            'id' => $verifiedId,
            'event_id' => $eventId,
        ]);
        $this->assertNotNull(DB::table('event_registrations')->where('id', $verifiedId)->value('event_link_sent_at'));
        $this->assertNull(DB::table('event_registrations')->where('work_email', 'pending@county.gov')->value('event_link_sent_at'));
    }

    public function test_event_without_a_valid_url_cannot_be_sent(): void
    {
        $this->registration(['status' => 'verified', 'verified_at' => now()]);
        $eventId = DB::table('events')->insertGetId([
            'title' => 'Event without link',
            'body' => 'Not ready',
            'start_date' => '30/07/2026',
            'url' => '',
        ]);

        $this->postJson(route('admin.tables.event-registrations.send-link'), [
            'event_id' => $eventId,
        ])->assertUnprocessable()->assertJsonPath('success', false);
    }

    private function registration(array $overrides = []): int
    {
        return DB::table('event_registrations')->insertGetId(array_merge([
            'event_name' => 'Sovereign Data Clouds with Snowflake',
            'full_name' => 'Sarah Jenkins',
            'work_email' => 'sarah@city.gov',
            'organization' => 'City Technology Office',
            'job_title' => 'CIO / CTO',
            'compliance_focus' => null,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ], $overrides));
    }
}
