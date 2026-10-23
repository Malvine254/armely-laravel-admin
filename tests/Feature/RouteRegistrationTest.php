<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\MelaKnowledgeController;
use App\Http\Controllers\CaseStudiesController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MelaSecurityController;
use App\Http\Middleware\ReindexMelaKnowledgeAfterContentChange;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RouteRegistrationTest extends TestCase
{
    public function test_literal_named_routes_in_application_views_and_controllers_are_registered(): void
    {
        $missing = [];

        foreach ([resource_path('views'), app_path('Http/Controllers')] as $directory) {
            foreach (File::allFiles($directory) as $file) {
                if ($file->getExtension() !== 'php') {
                    continue;
                }

                $source = $file->getContents();
                preg_match_all(
                    '/(?<!->)\b(?:route|to_route|temporarySignedRoute|signedRoute)\(\s*[\'"]([^\'"]+)[\'"]/',
                    $source,
                    $matches
                );

                foreach ($matches[1] as $name) {
                    if (! Route::has($name) && ! str_contains($source, "Route::has('$name')")) {
                        $missing[] = $file->getRelativePathname().': '.$name;
                    }
                }
            }
        }

        $this->assertSame([], $missing, implode(PHP_EOL, $missing));
    }

    public function test_restored_admin_routes_keep_the_original_actions_and_authentication(): void
    {
        $expected = [
            '/admin/case-studies/3/download' => [CaseStudiesController::class, 'adminCaseStudyDownload'],
            '/admin/white-papers/5/download' => [CaseStudiesController::class, 'adminWhitePaperDownload'],
            '/admin/mela/knowledge' => [MelaKnowledgeController::class, 'status'],
            '/admin/mela/knowledge/manage' => [MelaKnowledgeController::class, 'index'],
            '/admin/mela/knowledge/status' => [MelaKnowledgeController::class, 'status'],
        ];

        foreach ($expected as $uri => [$controller, $method]) {
            $route = Route::getRoutes()->match(Request::create($uri));
            $this->assertSame($controller.'@'.$method, $route->getActionName(), $uri);
            $this->assertContains('admin', $route->gatherMiddleware(), $uri);
            $this->getJson($uri)->assertUnauthorized();
        }

        $this->assertSame('/admin/mela/knowledge/manage', route('admin.mela.knowledge.manage', [], false));
        $this->assertSame('/admin/mela/knowledge', route('admin.mela.knowledge.status', [], false));
    }

    public function test_admin_content_routes_retain_automatic_knowledge_refresh(): void
    {
        foreach (Route::getRoutes() as $route) {
            if (in_array('admin', $route->gatherMiddleware(), true)) {
                $this->assertContains(ReindexMelaKnowledgeAfterContentChange::class, $route->gatherMiddleware(), $route->uri());
            }
        }
    }

    public function test_shared_admin_layout_can_render_for_an_admin_with_a_photo(): void
    {
        $admin = new Admin(['name' => 'Test Admin', 'status' => 'active', 'role' => 'super admin']);
        $admin->profile_photo_path = 'admin-photos/test.png';
        $this->actingAs($admin, 'admin');

        $html = view('admin.layouts.admin')->render();

        $this->assertStringContainsString(route('admin.profile.photo'), $html);
        $this->assertStringContainsString(route('admin.mela.knowledge.manage'), $html);
        $this->assertStringContainsString(route('admin.tables'), $html);
    }

    public function test_removed_public_pages_and_legacy_security_paths_are_restored(): void
    {
        $this->get(route('help'))->assertOk()->assertSee('Mela Meeting Assistant Help Center');
        $this->get('/mela')->assertRedirect('/mela-meeting-assistant');

        $jobRoute = Route::getRoutes()->match(Request::create('/job-board/example-token'));
        $this->assertSame(HomeController::class.'@jobBoard', $jobRoute->getActionName());
        $this->assertSame('example-token', $jobRoute->parameter('publicToken'));
        $this->assertSame('/job-board/example-token', route('job-board.show', ['publicToken' => 'example-token'], false));

        $this->get('/mela-meeting-assistant-security')->assertOk();
        $this->postJson('/mela-meeting-assistant-security/request', [])
            ->assertUnprocessable()->assertJsonValidationErrors(['name', 'email']);
        $download = Route::getRoutes()->match(Request::create('/mela-meeting-assistant-security/download/1'));
        $this->assertSame(MelaSecurityController::class.'@download', $download->getActionName());
        $this->get('/mela-meeting-assistant-security/download/1')
            ->assertRedirect(route('mela.security'))->assertSessionHasErrors('access');

        foreach ([
            '/mela-security-compliance-documents',
            '/mela-security-documents',
            '/mela-soc2',
            '/mela-privacy-documents',
            '/mela-meeting-assistant/security-documents',
        ] as $uri) {
            $this->get($uri)->assertStatus(301)->assertRedirect('/mela-meeting-assistant-security');
        }
    }
}
