<?php

namespace Tests\Feature;

use App\Models\MelaConversation;
use App\Services\Mela\Tools\ListBlogArticlesTool;
use App\Services\Mela\Tools\ToolContext;
use App\Services\Mela\Tools\ToolRegistry;
use App\Support\BlogUrl;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MelaBlogArticlesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->travelTo(now()->setDate(2026, 11, 10)->startOfDay());
        config(['blog.tables' => ['blogs', 'blog']]);
    }

    public function test_latest_articles_use_publication_dates_not_ids_or_modification_dates(): void
    {
        $newest = $this->article('Newest recap', '2026-11-01', ['updated_at' => '2026-11-01 00:00:00']);
        $this->article('Cloud data', 'Oct 20, 2026');
        $this->article('Old invoice article', 'October 28th, 2025', ['updated_at' => '2026-11-10 00:00:00']);
        $this->article('Draft', '2026-11-09', ['status' => 'draft']);
        $this->article('Future article', '2026-12-01');

        $context = new ToolContext(new MelaConversation);
        $result = app(ListBlogArticlesTool::class)->execute(['limit' => 3], $context);

        $this->assertTrue($result['ok']);
        $this->assertTrue($result['chronology_complete']);
        $this->assertSame(3, $result['matching_count']);
        $this->assertSame(['Newest recap', 'Cloud data', 'Old invoice article'], array_column($result['articles'], 'title'));
        $this->assertSame(['2026-11-01', '2026-10-20', '2025-10-28'], array_column($result['articles'], 'publication_date'));
        $this->assertSame(BlogUrl::url(['blog_id' => $newest, 'title' => 'Newest recap']), $result['articles'][0]['url']);
        $this->assertCount(3, $context->sources);
        $this->assertArrayNotHasKey('timestamp', $result['articles'][0]);
    }

    public function test_newly_published_articles_are_visible_without_reindexing(): void
    {
        $this->article('Earlier article', '2026-10-01');
        $tool = app(ListBlogArticlesTool::class);
        $this->assertSame('Earlier article', $tool->execute(['limit' => 1], new ToolContext(new MelaConversation))['articles'][0]['title']);

        $this->article('Just published', '2026-11-09');
        $this->assertSame('Just published', $tool->execute(['limit' => 1], new ToolContext(new MelaConversation))['articles'][0]['title']);
    }

    public function test_unknown_and_impossible_dates_do_not_become_a_false_latest_claim(): void
    {
        $this->article('Known article', '2026-10-01');
        foreach (['', 'not a date', 'tomorrow', '2026-02-30'] as $date) {
            $this->article('Unverified article', $date);
        }

        $result = $this->execute();

        $this->assertFalse($result['chronology_complete']);
        $this->assertSame(4, $result['unverified_count']);
        $this->assertCount(1, $result['articles']);
        $this->assertStringContainsString('not a verified absolute latest', $result['note']);
    }

    public function test_tool_supports_oldest_order_and_topic_author_and_date_filters(): void
    {
        $this->article('AI automation', '2026-10-05', ['author' => 'Martin Example']);
        $this->article('AI reporting', '2026-11-01', ['author' => 'Martin Example']);
        $this->article('AI elsewhere', '2026-10-20', ['author' => 'Other Author']);
        $this->article('Cloud database', '2026-10-21', ['author' => 'Martin Example']);

        $result = $this->execute([
            'order' => 'oldest',
            'query' => 'AI',
            'author' => 'martin',
            'published_from' => '2026-10-01',
            'published_to' => '2026-11-01',
        ]);
        $this->assertSame(['AI automation', 'AI reporting'], array_column($result['articles'], 'title'));
        $this->assertSame('oldest', $result['order']);
        $this->assertTrue($result['chronology_complete']);
    }

    public function test_empty_catalogue_is_not_a_fabricated_answer(): void
    {
        $result = $this->execute();
        $this->assertTrue($result['ok']);
        $this->assertSame([], $result['articles']);
        $this->assertSame(0, $result['matching_count']);
    }

    public function test_configured_legacy_table_uses_real_blog_dates_and_urls(): void
    {
        Schema::create('legacy_blog', function (Blueprint $table) {
            $table->integer('blog_id');
            $table->string('blog_title');
            $table->string('blog_author');
            $table->string('blog_date');
            $table->text('description');
        });
        config(['blog.tables' => ['legacy_blog', 'blogs']]);
        DB::table('legacy_blog')->insert([
            ['blog_id' => 100, 'blog_title' => 'Legacy older', 'blog_author' => 'Test Author', 'blog_date' => '2025-12-31', 'description' => 'Old'],
            ['blog_id' => 2, 'blog_title' => 'Legacy newest', 'blog_author' => 'Test Author', 'blog_date' => 'November 1, 2026', 'description' => '<p>Actual &amp; current content</p>'],
        ]);

        $result = $this->execute(['limit' => 1]);
        $this->assertSame('Legacy newest', $result['articles'][0]['title']);
        $this->assertSame('Test Author', $result['articles'][0]['author']);
        $this->assertSame('Actual & current content', $result['articles'][0]['excerpt']);
        $this->assertSame(url('/blog/legacy-newest-2'), $result['articles'][0]['url']);
    }

    public function test_missing_publication_date_schema_never_uses_creation_time(): void
    {
        Schema::create('undated_blog', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->timestamps();
        });
        config(['blog.tables' => ['undated_blog']]);
        DB::table('undated_blog')->insert(['title' => 'Not verifiable', 'created_at' => now()]);

        $result = $this->execute();
        $this->assertFalse($result['ok']);
        $this->assertFalse($result['chronology_complete']);
        $this->assertSame([], $result['articles']);
    }

    public function test_publication_timestamps_respect_timezones_and_never_use_updated_at(): void
    {
        Schema::create('timestamped_blog', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('published_at');
            $table->timestamp('updated_at')->nullable();
        });
        config(['blog.tables' => ['timestamped_blog']]);
        DB::table('timestamped_blog')->insert([
            ['title' => 'Earlier instant', 'published_at' => '2026-11-01T10:00:00+03:00', 'updated_at' => '2026-11-09 00:00:00'],
            ['title' => 'Later instant', 'published_at' => '2026-11-01T08:00:00+00:00', 'updated_at' => null],
        ]);

        $result = $this->execute();
        $this->assertSame(['Later instant', 'Earlier instant'], array_column($result['articles'], 'title'));
        $this->assertTrue($result['chronology_complete']);
    }

    public function test_missing_author_data_is_not_treated_as_an_empty_author_search(): void
    {
        Schema::create('no_author_blog', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('date');
        });
        config(['blog.tables' => ['no_author_blog']]);
        $result = $this->execute(['author' => 'Unknown Author']);

        $this->assertFalse($result['ok']);
        $this->assertFalse($result['chronology_complete']);
    }

    public function test_registry_validates_arguments_and_accepts_an_end_date_without_a_start(): void
    {
        $registry = app(ToolRegistry::class);
        $context = new ToolContext(new MelaConversation);
        $this->article('Allowed', '2026-10-01');
        $this->article('Too new', '2026-11-01');

        $result = $registry->execute('list_blog_articles', json_encode(['published_to' => '2026-10-31']), $context);
        $this->assertTrue($result['ok']);
        $this->assertSame(['Allowed'], array_column($result['articles'], 'title'));
        foreach ([['limit' => 9], ['order' => 'relevance'], ['published_from' => 'yesterday'], ['published_from' => '2026-11-01', 'published_to' => '2026-10-01']] as $arguments) {
            $this->assertFalse($registry->execute('list_blog_articles', json_encode($arguments), $context)['ok']);
        }
    }

    public function test_missing_table_and_database_failure_are_explicit_tool_failures(): void
    {
        config(['blog.tables' => ['missing_blog_table']]);
        $this->assertFalse($this->execute()['ok']);

        Schema::shouldReceive('hasTable')->andThrow(new \RuntimeException('Database unavailable'));
        $result = app(ToolRegistry::class)->execute('list_blog_articles', '{}', new ToolContext(new MelaConversation));
        $this->assertFalse($result['ok']);
        $this->assertStringContainsString('failed', $result['error']);
    }

    private function execute(array $arguments = []): array
    {
        return app(ListBlogArticlesTool::class)->execute($arguments, new ToolContext(new MelaConversation));
    }

    private function article(string $title, string $date, array $extra = []): int
    {
        return DB::table('blogs')->insertGetId(array_merge([
            'title' => $title,
            'date' => $date,
            'author' => 'Test Author',
            'body' => 'Article body.',
            'status' => 'published',
        ], $extra));
    }
}
