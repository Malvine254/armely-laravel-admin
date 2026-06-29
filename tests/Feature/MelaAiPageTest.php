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

    public function test_mela_ai_page_uses_database_titles(): void
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
        $response->assertSee('<title>Agentic Capabilities in Action</title>', false);
        $response->assertSee('Meeting Assistant Demo', false);
        $response->assertDontSee('Agentic Demo 1', false);
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
