<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\MelaConversation;
use App\Services\Mela\Knowledge\MelaKnowledgeIndexRefresh;
use App\Services\Mela\Memory\ConversationStore;
use App\Services\Mela\Memory\VisitorCountry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MelaAdminTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): void
    {
        $this->actingAs(new Admin(['name' => 'Admin', 'email' => 'admin@example.test', 'status' => 'active', 'role' => 'super admin']), 'admin');
    }

    private function runner(): MelaKnowledgeIndexRefresh
    {
        $runner = \Mockery::mock(MelaKnowledgeIndexRefresh::class)->makePartial();
        $runner->shouldReceive('siteFingerprint')->andReturn('site-v1');
        $runner->settings();
        DB::table('mela_index_settings')->update(['site_fingerprint' => 'site-v1']);
        return $runner;
    }

    public function test_admin_can_save_recurring_hours_and_disable_them(): void
    {
        $this->admin();
        $this->postJson(route('admin.mela.knowledge.schedule'), ['interval' => 2, 'unit' => 'hours'])->assertCreated();
        Cache::flush();
        $this->getJson(route('admin.mela.knowledge.status'))->assertOk()->assertJsonPath('interval_minutes', 120);
        $this->postJson(route('admin.mela.knowledge.schedule'), ['interval' => 25, 'unit' => 'hours'])->assertUnprocessable();
        $this->postJson(route('admin.mela.knowledge.schedule'), ['interval' => 0, 'unit' => 'minutes'])->assertUnprocessable();
        $this->deleteJson(route('admin.mela.knowledge.schedule.cancel'))->assertOk();
        $this->assertDatabaseHas('mela_index_settings', ['interval_minutes' => null, 'next_run_at' => null]);
        $this->get(route('admin.mela.knowledge.manage'))->assertOk()->assertSee('Refresh schedule')->assertSee('Escalations');
    }

    public function test_recurring_index_runs_again_and_does_not_run_early(): void
    {
        $runner = $this->runner();
        $runner->scheduleEvery(30);
        Artisan::shouldReceive('call')->with('mela:index')->twice()->andReturn(0);
        $this->assertNull($runner->runScheduledIfDue());
        $this->travel(31)->minutes();
        $this->assertSame(0, $runner->runScheduledIfDue());
        $this->assertNull($runner->runScheduledIfDue());
        $this->travel(31)->minutes();
        $this->assertSame(0, $runner->runScheduledIfDue());
    }

    public function test_overlap_and_failure_preserve_due_work(): void
    {
        $runner = $this->runner();
        $runner->scheduleEvery(1);
        $this->travel(2)->minutes();
        Cache::put(MelaKnowledgeIndexRefresh::RUNNING_KEY, true, 60);
        $this->assertNull($runner->runScheduledIfDue());
        Cache::forget(MelaKnowledgeIndexRefresh::RUNNING_KEY);
        Artisan::shouldReceive('call')->with('mela:index')->twice()->andReturn(1, 0);
        $due = $runner->scheduledAt();
        $this->assertSame(1, $runner->runScheduledIfDue());
        $this->assertSame($due, $runner->scheduledAt());
        $this->assertSame(0, $runner->runScheduledIfDue());
        $this->assertNotSame($due, $runner->scheduledAt());
    }

    public function test_deployment_changes_and_edits_during_a_run_are_indexed_when_schedule_disabled(): void
    {
        $runner = $this->runner();
        $runner->cancelScheduled();
        DB::table('mela_index_settings')->update(['site_fingerprint' => 'old-site']);
        Artisan::shouldReceive('call')->with('mela:index')->once()->andReturnUsing(function () use ($runner) {
            $this->assertFalse($runner->dispatchAfterResponse('admin_content'));
            return 0;
        });
        $this->assertSame(0, $runner->runScheduledIfDue());
        Artisan::shouldReceive('call')->with('mela:index')->once()->andReturn(0);
        $this->assertSame(0, $runner->runScheduledIfDue());
        $this->assertNull($runner->runScheduledIfDue());
    }

    public function test_content_mutations_trigger_indexing_but_reads_and_errors_do_not(): void
    {
        $refresh = \Mockery::mock(MelaKnowledgeIndexRefresh::class);
        $refresh->shouldReceive('dispatchAfterResponse')->once()->with('admin_content')->andReturn(true);
        $middleware = new \App\Http\Middleware\ReindexMelaKnowledgeAfterContentChange($refresh);
        foreach ([['POST', 302], ['GET', 200], ['POST', 422]] as [$method, $status]) {
            $request = \Illuminate\Http\Request::create('/admin/company-content/banners', $method);
            $route = new \Illuminate\Routing\Route([$method], '/admin/company-content/banners', fn () => null);
            $route->name('admin.company-content.banners.store');
            $request->setRouteResolver(fn () => $route);
            $middleware->handle($request, fn () => response('', $status));
        }
    }
    public function test_sessions_are_private_filtered_and_include_only_shared_identity(): void
    {
        $store = app(ConversationStore::class);
        $created = $store->create('127.0.0.1', 'Test', 'https://armely.com/');
        $session = $created['conversation'];
        $session->update(['country_code' => 'KE', 'escalation_status' => 'submitted', 'memory' => ['visitor' => ['name' => ['value' => '<script>Visitor</script>', 'source' => 'visitor'], 'email' => ['value' => 'visitor@example.test'], 'company' => 'Legacy company', 'phone' => ['source' => 'visitor']]]]);
        $other = $store->create(null, null, null)['conversation'];
        $other->update(['last_activity_at' => now()->subMinutes(10)]);
        $this->getJson(route('admin.mela.sessions.index'))->assertUnauthorized();
        $this->getJson(route('admin.mela.sessions.show', $session))->assertUnauthorized();
        $this->admin();
        $response = $this->getJson(route('admin.mela.sessions.index', ['filter' => 'active']))->assertOk()
            ->assertJsonPath('sessions.total', 1)->assertJsonPath('sessions.data.0.country_code', 'KE')
            ->assertJsonPath('sessions.data.0.visitor.email', 'visitor@example.test')
            ->assertJsonPath('sessions.data.0.visitor.name', '<script>Visitor</script>')
            ->assertJsonPath('sessions.data.0.visitor.company', 'Legacy company')
            ->assertJsonPath('sessions.data.0.visitor.phone', null);
        $this->assertStringNotContainsString($session->token_hash, $response->getContent());
        $this->getJson(route('admin.mela.sessions.index', ['filter' => 'escalated']))->assertJsonPath('sessions.total', 1);
        $this->getJson(route('admin.mela.sessions.show', $session))->assertOk()->assertJsonStructure(['messages', 'escalation']);
        $this->assertNull(app(VisitorCountry::class)->resolve('127.0.0.1'));
    }
}
