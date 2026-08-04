<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MelaAiPageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->prepareSchema();
    }

    public function test_mela_ai_page_presents_the_product_collection(): void
    {
        DB::table('company_portfolios')->insert([
            'title' => 'Agentic Capabilities in Action',
            'category' => 'AI & Machine Learning',
            'short_description' => 'DB-driven Mela page description.',
            'long_description' => 'A longer description from the database.',
            'features' => json_encode(['Feature one']),
            'cta_label' => 'Explore Mela',
            'cta_url' => '/mela-ai',
            'display_order' => 1,
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('videos')->insert([
            'title' => 'Meeting Assistant Demo',
            'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->get(route('mela-ai'));

        $response->assertOk();
        $response->assertSee('<title>Mela AI | AI Products Built for Modern Work</title>', false);
        $response->assertSee('The Mela AI Collection', false);
        $response->assertSee('Mela Meeting Assistant', false);
        $response->assertSee(route('mela-meeting-assistant'), false);
        $response->assertSee('Mela Organization Chat', false);
        $response->assertDontSee('Meeting Assistant Demo', false);
    }

    public function test_meeting_assistant_has_its_own_product_page(): void
    {
        DB::table('videos')->insert([
            'title' => 'Mela Meeting Assistant Demo',
            'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->get(route('mela-meeting-assistant'));

        $response->assertOk();
        $response->assertSee('<title>Mela Meeting Assistant | Microsoft Teams Meeting Automation</title>', false);
        $response->assertSee('Intelligence Embedded Into How Your Business Works', false);
        $response->assertSee('Turn Meeting Action Items into Microsoft Planner Tasks in One Click', false);
        $response->assertSee('How Mela Compares to the Competition', false);
        $response->assertSee('Deployed to Your Tenant in Under 10 Minutes', false);
        $response->assertSee(asset('images/mela/meeting-action-items.png'), false);
        $response->assertSee('https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ?rel=0', false);
        $response->assertSee('Mela Meeting Assistant Demo', false);
        $response->assertDontSee('The Mela AI Collection', false);
    }

    public function test_solutions_menu_links_directly_to_the_viable_mela_product(): void
    {
        $response = $this->get(route('mela-ai'));

        $response->assertOk();
        $response->assertSee('Mela Meeting Assistant', false);
        $response->assertSee('href="'.route('mela-meeting-assistant').'"', false);
    }

    public function test_meeting_assistant_uses_the_default_demo_when_no_database_video_exists(): void
    {
        $this->get(route('mela-meeting-assistant'))
            ->assertOk()
            ->assertSee('https://www.youtube-nocookie.com/embed/etFEuJzx6cA?rel=0', false);
    }

    private function prepareSchema(): void
    {
        Schema::dropIfExists('company_portfolios');
        Schema::dropIfExists('videos');

        Schema::create('company_portfolios', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category')->nullable();
            $table->text('short_description')->nullable();
            $table->text('long_description')->nullable();
            $table->text('features')->nullable();
            $table->string('cta_label')->nullable();
            $table->string('cta_url')->nullable();
            $table->unsignedInteger('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('url')->nullable();
            $table->timestamps();
        });
    }
}
