<?php

namespace Tests\Feature;

use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Every outbound email (events, Mela, newsletter, admin security) renders in
 * the shared branded layout, and visitor-supplied text stays escaped.
 */
class EmailLayoutConsistencyTest extends TestCase
{
    private const INJECTED = '<script>alert(1)</script>';

    public static function templates(): array
    {
        return [
            ['emails.events.registration-confirmation'],
            ['emails.events.registration-notification'],
            ['emails.events.reminder'],
            ['emails.events.thank-you'],
            ['emails.events.verified-event-link'],
            ['emails.mela.assistant-escalation'],
            ['emails.mela.assistant-visitor-confirmation'],
            ['emails.newsletter.welcome'],
            ['emails.newsletter.admin-subscription-notification'],
            ['emails.newsletter.content-notification'],
            ['admin.auth.reset-email'],
        ];
    }

    /** @dataProvider templates */
    public function test_template_uses_shared_layout_and_escapes_visitor_text(string $template): void
    {
        $x = self::INJECTED;
        $url = 'https://armely.com/example?token=abc&next=1';

        $html = view($template, [
            // events
            'full_name' => $x, 'event_name' => $x, 'work_email' => 'a@example.com', 'organization' => $x,
            'job_title' => $x, 'compliance_focus' => $x, 'unsubscribe_url' => $url,
            'name' => $x, 'eventTitle' => $x, 'eventDate' => 'Dec 4, 2026 10:00 AM', 'eventUrl' => $url, 'unsubscribeUrl' => $url,
            // mela
            'email' => 'visitor@example.com', 'company' => $x, 'phone' => $x, 'preferredContact' => $x,
            'requestType' => $x, 'topic' => $x, 'reference' => 'MELA-123', 'conversationId' => 'conv-1',
            'timestamp' => 'Nov 28, 2026', 'landingPage' => $url, 'businessNeed' => $x, 'requestedAction' => $x,
            'summary' => $x, 'technologies' => [$x], 'services' => [$x],
            'transcript' => [['role' => 'user', 'content' => $x], ['role' => 'assistant', 'content' => 'Hello']],
            'contactEmail' => 'info@armely.com',
            // newsletter
            'statusLabel' => 'New subscriber', 'subscribedAt' => Carbon::parse('2026-11-28 10:00'),
            'title' => $x, 'type' => 'blog', 'url' => $url, 'recipientReason' => '', 'recipientKind' => 'subscriber',
            // admin
            'resetLink' => $url,
        ])->render();

        $this->assertStringContainsString('src="https://armely.com/images/logo/armely-logo-email.png"', $html);
        $this->assertStringContainsString('max-width:640px', $html);
        $this->assertSame(1, substr_count(strtolower($html), '<!doctype html>'), 'Exactly one document (no nested standalone HTML).');
        $this->assertStringNotContainsString('<script>', $html);
    }
}
