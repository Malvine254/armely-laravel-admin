<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class EmailTemplateTest extends TestCase
{
    #[DataProvider('notificationTemplates')]
    public function test_notification_templates_escape_once_and_preserve_action_urls(string $template): void
    {
        $url = 'https://armely.com/white-papers/access/5?em=abc&expires=1791428984&signature=def';
        $data = [
            'name' => 'R&D <script>alert(1)</script>',
            'firstName' => 'R&D <script>alert(1)</script>',
            'fullName' => 'R&D <script>alert(1)</script>',
            'email' => 'visitor@example.com',
            'phone' => '', 'organization' => 'Research & Development', 'company' => 'Research & Development',
            'jobTitle' => 'Director', 'role' => 'Director', 'country' => 'US', 'city' => 'Kansas City',
            'interest' => 'White Papers',
            'requestedResource' => 'Readiness &amp; Adoption',
            'resourceTitle' => 'Readiness &amp; Adoption',
            'resourceTypeLabel' => 'White Paper',
            'caseStudyId' => '', 'whitePaperId' => '5',
            'expiresAt' => 'Oct 08, 2026 03:09 AM UTC',
            'message' => "First line & details\n<script>alert(1)</script>",
            'position' => 'Research &amp; Development', 'jobType' => 'Full-time', 'jobId' => '42',
            'address' => '', 'state' => '', 'zip' => '',
            'cvUrl' => $url, 'downloadUrl' => $url, 'resourceUrl' => $url, 'contactUrl' => $url,
            'blogTitle' => 'Readiness &amp; Adoption',
            'scorePercent' => 65, 'overallScore' => 234, 'ipAddress' => '127.0.0.1',
            'submittedAt' => 'Oct 08, 2026 03:09 AM UTC',
            'tier' => ['label' => 'Ready & Growing', 'summary' => 'Research & development'],
            'dimensions' => [['label' => 'Governance & Security', 'percent' => 65, 'score' => 39, 'max' => 60]],
            'resource' => (object) ['title' => 'Readiness &amp; Adoption', 'resource_type' => 'whitepaper', 'category' => 'Data &amp; AI'],
            'subject' => 'Research & Development', 'serviceType' => 'Data & AI',
        ];

        $html = view($template, $data)->render();
        $this->assertStringContainsString('R&amp;D &lt;script&gt;alert(1)&lt;/script&gt;', $html);
        $this->assertStringNotContainsString('&amp;amp;', $html);
        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringContainsString('max-width:640px', $html);
        $this->assertStringContainsString('bgcolor="#0f2f63"', $html);
        $this->assertStringContainsString('src="https://armely.com/images/logo/logo-replace-v2.png"', $html);
        $this->assertStringNotContainsString('armely-store-logo.png', $html);

        $dom = new \DOMDocument;
        @$dom->loadHTML($html);
        foreach ((new \DOMXPath($dom))->query('//a[@class="mail-button"]') as $button) {
            $this->assertSame($url, $button->getAttribute('href'));
        }
    }

    public static function notificationTemplates(): array
    {
        return array_map(fn ($template) => [$template], [
            'emails.case-studies.resource-download',
            'emails.case-studies.admin-lead-notification',
            'emails.resources.download-link',
            'emails.blog.download-link',
            'emails.jobs.admin-application-notification',
            'emails.jobs.user-application-confirmation',
            'emails.mela.security-guide-download',
            'emails.mela.security-guide-admin-notification',
            'emails.data-readiness.admin-notification',
            'emails.data-readiness.user-report',
            'emails.contact.admin-notification',
            'emails.consultation.admin-notification',
        ]);
    }

    public function test_decoded_resource_title_is_still_escaped_and_does_not_inject_html(): void
    {
        $html = view('emails.case-studies.resource-download', [
            'name' => 'Visitor', 'resourceTitle' => 'Data &amp; AI &lt;img src=x onerror=alert(1)&gt;',
            'resourceTypeLabel' => 'White Paper', 'downloadUrl' => 'https://armely.com/download',
            'expiresAt' => 'Tomorrow',
        ])->render();

        $this->assertStringContainsString('Data &amp; AI &lt;img src=x onerror=alert(1)&gt;', $html);
        $this->assertStringNotContainsString('<img src=x', $html);
    }

    public function test_admin_notes_preserve_line_breaks_without_exposing_html(): void
    {
        $html = view('emails.mela.security-guide-admin-notification', [
            'name' => 'Visitor', 'email' => 'visitor@example.com', 'organization' => '',
            'jobTitle' => '', 'phone' => '', 'expiresAt' => 'Tomorrow', 'downloadUrl' => 'https://armely.com/download',
            'message' => "R&D\n<strong>Plain text</strong>",
        ])->render();

        $this->assertStringContainsString("R&amp;D<br />\n&lt;strong&gt;Plain text&lt;/strong&gt;", $html);
        $this->assertStringNotContainsString('<strong>Plain text</strong>', $html);
        $this->assertStringNotContainsString('&lt;br', $html);
    }
}
