<?php

namespace App\Services\Mela\Tools;

use App\Services\Mela\Telemetry\MelaLogger;
use App\Support\BlogUrl;
use DateTimeImmutable;
use DateTimeZone;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Fluent;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ListBlogArticlesTool implements MelaTool
{
    public function __construct(private readonly MelaLogger $logger) {}

    public function name(): string
    {
        return 'list_blog_articles';
    }

    public function description(): string
    {
        return 'Reads the live Armely blog catalogue and orders articles by their actual publication dates, not semantic similarity, database IDs, or modification dates. '
            .'Use for latest, newest, recent, oldest, date-based or author-filtered article requests, including follow-ups. '
            .'Returns exact titles, authors, publication dates, excerpts and canonical URLs. Optional query is a literal topic word or phrase in title/body, not the visitor question. '
            .'Missing or invalid publication dates make chronology incomplete; never claim an absolute latest article when chronology_complete is false.';
    }

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'order' => ['type' => 'string', 'enum' => ['newest', 'oldest'], 'description' => 'Publication-date order; defaults to newest.'],
                'limit' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 8, 'description' => 'Number of articles; use 1 for a single latest article.'],
                'query' => ['type' => 'string', 'description' => 'Optional literal topic word or phrase to match in the article title or body.'],
                'author' => ['type' => 'string', 'description' => 'Optional author name or part of the name.'],
                'published_from' => ['type' => 'string', 'description' => 'Inclusive publication date, YYYY-MM-DD.'],
                'published_to' => ['type' => 'string', 'description' => 'Inclusive publication date, YYYY-MM-DD.'],
            ],
            'required' => [],
            'additionalProperties' => false,
        ];
    }

    public function rules(): array
    {
        return [
            'order' => ['sometimes', 'string', 'in:newest,oldest'],
            'limit' => ['sometimes', 'integer', 'min:1', 'max:8'],
            'query' => ['sometimes', 'string', 'min:1', 'max:200'],
            'author' => ['sometimes', 'string', 'min:1', 'max:160'],
            'published_from' => ['sometimes', 'date_format:Y-m-d'],
            'published_to' => [
                'sometimes',
                'date_format:Y-m-d',
                Rule::when(static fn (Fluent $input) => ! empty($input->published_from), ['after_or_equal:published_from']),
            ],
        ];
    }

    public function execute(array $arguments, ToolContext $context): array
    {
        $table = collect(config('blog.tables', ['blogs', 'blog']))
            ->first(static fn ($candidate) => Schema::hasTable($candidate));
        if (! $table) {
            return $this->unavailable('blog_table_missing');
        }

        $columns = Schema::getColumnListing($table);
        $column = static fn (array $candidates) => collect($candidates)->first(static fn ($name) => in_array($name, $columns, true));
        $titleColumn = $column(['title', 'blog_title']);
        $dateColumn = $column(['date', 'blog_date', 'published_at']);
        $authorColumn = $column(['author', 'blog_author']);
        $bodyColumn = $column(['body', 'description', 'content']);
        if (! $titleColumn || ! $dateColumn || ! $column(['blog_id', 'id'])) {
            return $this->unavailable('blog_publication_schema_missing');
        }
        if (! empty($arguments['author']) && ! $authorColumn) {
            return $this->unavailable('blog_author_unavailable');
        }

        $query = DB::table($table);
        if (in_array('status', $columns, true)) {
            $query->where('status', 'published');
        }
        // Select only public article fields, never unrelated administrative data.
        $selected = array_values(array_unique(array_filter([
            $titleColumn, $dateColumn, $authorColumn, $bodyColumn,
            $column(['blog_id']), $column(['id']),
        ])));
        $articles = [];
        $unverified = 0;
        $now = now();
        foreach ($query->select($selected)->cursor() as $row) {
            $title = $this->plainText((string) $row->{$titleColumn});
            $author = $authorColumn ? $this->plainText((string) $row->{$authorColumn}) : '';
            $body = $bodyColumn ? $this->plainText((string) $row->{$bodyColumn}) : '';
            if (! empty($arguments['author']) && ! Str::contains(mb_strtolower($author), mb_strtolower(trim($arguments['author'])))) {
                continue;
            }
            if (! empty($arguments['query']) && ! Str::contains(mb_strtolower($title.' '.$body), mb_strtolower(trim($arguments['query'])))) {
                continue;
            }

            $published = $this->publicationDate((string) $row->{$dateColumn});
            $id = $row->blog_id ?? $row->id ?? null;
            if (! $published || $title === '' || $id === null || $id === '') {
                $unverified++;

                continue;
            }
            if ($published > $now) {
                continue;
            }
            $date = $published->format('Y-m-d');
            if ((! empty($arguments['published_from']) && $date < $arguments['published_from'])
                || (! empty($arguments['published_to']) && $date > $arguments['published_to'])) {
                continue;
            }
            $articles[] = [
                'title' => $title,
                'author' => $author,
                'published_at' => $published->format(DATE_ATOM),
                'publication_date' => $date,
                'url' => BlogUrl::url(['blog_id' => $id, 'title' => $title]),
                'excerpt' => Str::limit($body, 500),
                'timestamp' => $published->getTimestamp(),
            ];
        }

        $order = $arguments['order'] ?? 'newest';
        usort($articles, static fn ($a, $b) => $order === 'oldest'
            ? $a['timestamp'] <=> $b['timestamp']
            : $b['timestamp'] <=> $a['timestamp']);
        $matching = count($articles);
        $articles = array_slice($articles, 0, $arguments['limit'] ?? 3);
        foreach ($articles as &$article) {
            unset($article['timestamp']);
        }
        unset($article);
        $context->addSources($articles);
        if ($unverified > 0) {
            $this->logger->event('BLOG_CHRONOLOGY_INCOMPLETE', ['unverified_count' => $unverified], 'warning');
        }

        return [
            'ok' => true,
            'checked_at' => $now->toIso8601String(),
            'order' => $order,
            'matching_count' => $matching,
            'chronology_complete' => $unverified === 0,
            'unverified_count' => $unverified,
            'articles' => $articles,
            'note' => $unverified > 0
                ? 'Some matching articles have unverified publication dates or metadata. These are only the dated results, not a verified absolute latest/oldest. Explain that limitation; do not guess missing dates.'
                : ($articles === []
                    ? 'No published articles match these filters. Do not invent an article; clarify or broaden the filters when appropriate.'
                    : 'Results are ordered by verified publication date. Use only these titles, dates, authors and URLs. Excerpts are not full articles; use knowledge search before claiming details beyond them. Equally dated articles may be equally recent.'),
        ];
    }

    private function publicationDate(string $value): ?DateTimeImmutable
    {
        $value = trim($value);
        $timezone = new DateTimeZone(config('app.timezone', 'UTC'));
        foreach (['Y-m-d', 'Y-m-d H:i:s', 'Y-m-d H:i:s.u', DATE_ATOM, 'Y-m-d\TH:i:s.uP', 'M d, Y', 'F j, Y', 'F jS, Y', 'd-m-Y'] as $format) {
            $date = DateTimeImmutable::createFromFormat('!'.$format, $value, $timezone);
            $errors = DateTimeImmutable::getLastErrors();
            if ($date !== false && ($errors === false || ($errors['warning_count'] === 0 && $errors['error_count'] === 0))) {
                return $date;
            }
        }

        return null;
    }

    private function plainText(string $value): string
    {
        return Str::squish(html_entity_decode(strip_tags($value), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }

    private function unavailable(string $reason): array
    {
        $this->logger->event('BLOG_CATALOGUE_UNAVAILABLE', ['reason' => $reason], 'warning');

        return [
            'ok' => false,
            'status' => 'unavailable',
            'chronology_complete' => false,
            'articles' => [],
            'note' => 'Live blog publication data cannot be verified. Say so plainly. Do not substitute semantic search rankings, old messages, record IDs or modification dates as proof of which article is latest.',
        ];
    }
}
