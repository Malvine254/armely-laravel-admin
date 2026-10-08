<?php

namespace Tests\Feature;

use App\Http\Controllers\CaseStudiesController;
use App\Http\Middleware\LogActivity;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class ResourceDownloadTest extends TestCase
{
    private string $pdfPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->freezeTime();
        $this->withoutMiddleware(LogActivity::class);
        Http::preventStrayRequests();
        $this->pdfPath = tempnam(sys_get_temp_dir(), 'armely-download-');
        file_put_contents($this->pdfPath, "%PDF-1.4\nTest resource\n%%EOF");
        $this->app->usePublicPath(dirname($this->pdfPath));

        Schema::create('white_paper', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('pdf')->nullable();
            $table->string('pdf_url')->nullable();
        });
        Schema::create('industry_listings', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->string('title');
            $table->string('pdf_url')->nullable();
            $table->string('pdf')->nullable();
        });
        DB::table('white_paper')->insert([
            'id' => 5, 'title' => 'Government Copilot Readiness &amp; Adoption',
            'pdf' => null, 'pdf_url' => basename($this->pdfPath),
        ]);
        DB::table('industry_listings')->insert([
            'id' => 3, 'title' => 'Data & AI', 'category' => 'Government',
            'pdf_url' => basename($this->pdfPath),
        ]);
    }

    protected function tearDown(): void
    {
        unlink($this->pdfPath);
        parent::tearDown();
    }

    public function test_generated_white_paper_email_button_downloads_the_file_when_pdf_url_is_the_fallback_column(): void
    {
        $controller = app(CaseStudiesController::class);
        $method = new \ReflectionMethod($controller, 'buildDownloadDetails');
        $details = $method->invoke($controller, ['white_paper_id' => 5], 'visitor@example.com');

        $this->assertNotNull($details);
        parse_str(parse_url($details['download_url'], PHP_URL_QUERY), $query);
        $this->assertSame(now()->addHour()->timestamp, (int) $query['expires']);
        $html = view('emails.case-studies.resource-download', [
            'name' => 'Visitor & Partner',
            'resourceTitle' => $details['resource_title'],
            'resourceTypeLabel' => $details['resource_type_label'],
            'downloadUrl' => $details['download_url'],
            'expiresAt' => $details['expires_at'],
        ])->render();
        $dom = new \DOMDocument;
        @$dom->loadHTML($html);
        $button = (new \DOMXPath($dom))->query('//a[@class="mail-button"]')->item(0);
        $this->assertSame($details['download_url'], $button->getAttribute('href'));
        $this->assertStringNotContainsString('&amp;amp;', $html);
        $this->assertStringContainsString('Government Copilot Readiness &amp; Adoption', $html);

        $response = $this->get($button->getAttribute('href'))
            ->assertOk()
            ->assertDownload(basename($this->pdfPath))
            ->assertHeader('Content-Type', 'application/pdf');
        $this->assertSame(file_get_contents($this->pdfPath), file_get_contents($response->baseResponse->getFile()->getPathname()));
    }

    public function test_relative_and_absolute_signed_case_study_links_download_the_file(): void
    {
        foreach ([false, true] as $absolute) {
            $url = URL::temporarySignedRoute('case-studies.access', now()->addHour(), ['caseStudy' => 3], $absolute);
            $this->get($url)->assertOk()->assertDownload(basename($this->pdfPath));
        }
    }

    public function test_legacy_html_encoded_links_are_normalized_and_still_require_a_valid_signature(): void
    {
        $url = $this->whitePaperUrl();
        foreach (['&amp;', '&amp;amp;'] as $separator) {
            $response = $this->get(str_replace('&', $separator, $url));
            $response->assertRedirect($url);
            $this->get($response->headers->get('Location'))
                ->assertOk()->assertDownload(basename($this->pdfPath));
        }

        $tampered = str_replace('em='.sha1('visitor@example.com'), 'em=tampered', $url);
        $this->get($tampered)->assertRedirect(route('case-studies.index'))->assertSessionHasErrors('access');
        $response = $this->get(str_replace('&', '&amp;', $tampered))->assertRedirect($tampered);
        $this->get($response->headers->get('Location'))
            ->assertRedirect(route('case-studies.index'))->assertSessionHasErrors('access');
    }

    public function test_expired_and_unsigned_links_do_not_download(): void
    {
        $expired = URL::temporarySignedRoute('white-papers.access', now()->subMinute(), ['paper' => 5], false);
        foreach ([$expired, route('white-papers.access', ['paper' => 5])] as $url) {
            $this->get($url)->assertRedirect(route('case-studies.index'))->assertSessionHasErrors('access');
        }
        $response = $this->get(str_replace('&', '&amp;amp;', $expired))->assertRedirect(url($expired));
        $this->get($response->headers->get('Location'))
            ->assertRedirect(route('case-studies.index'))->assertSessionHasErrors('access');
    }

    public function test_missing_file_reports_an_access_error(): void
    {
        DB::table('white_paper')->where('id', 5)->update(['pdf_url' => 'missing-armely-resource.pdf']);
        $this->get($this->whitePaperUrl())->assertRedirect(route('case-studies.index'))
            ->assertSessionHasErrors(['access' => 'This file could not be located. Please request a new secure download link.']);
    }

    public function test_signed_preview_remains_inline(): void
    {
        $url = URL::temporarySignedRoute('white-papers.access', now()->addHour(), ['paper' => 5, 'preview' => 1], false);
        $response = $this->get($url)->assertOk()->assertHeader('Content-Type', 'application/pdf');
        $this->assertFalse($response->headers->has('Content-Disposition'));
    }

    public function test_remote_pdf_download_returns_bytes_and_not_a_redirect(): void
    {
        DB::table('white_paper')->where('id', 5)->update(['pdf_url' => 'https://documents.example.com/guide.pdf']);
        Http::fake(['https://documents.example.com/guide.pdf' => Http::response('%PDF-1.4 remote file', 200)]);

        $this->get($this->whitePaperUrl())->assertOk()
            ->assertHeader('Content-Disposition', 'attachment; filename="guide.pdf"')
            ->assertContent('%PDF-1.4 remote file');
    }

    public function test_remote_error_and_html_response_are_not_presented_as_a_pdf(): void
    {
        DB::table('white_paper')->where('id', 5)->update(['pdf_url' => 'https://documents.example.com/guide.pdf']);
        foreach ([404, 200] as $status) {
            Http::fake(['https://documents.example.com/guide.pdf' => Http::response('<html>File unavailable</html>', $status)]);
            $this->get($this->whitePaperUrl())->assertRedirect(route('case-studies.index'))
                ->assertSessionHasErrors('access');
        }
    }

    public function test_access_failure_message_is_visible_on_the_case_studies_page(): void
    {
        $this->get(route('white-papers.access', ['paper' => 5]))->assertRedirect();
        $this->get(route('case-studies.index'))->assertOk()
            ->assertSee('Unable to download your resource.')
            ->assertSee('This download link is invalid or has expired.');
    }

    private function whitePaperUrl(): string
    {
        return url(URL::temporarySignedRoute(
            'white-papers.access', now()->addHour(),
            ['paper' => 5, 'em' => sha1('visitor@example.com')], false
        ));
    }
}
