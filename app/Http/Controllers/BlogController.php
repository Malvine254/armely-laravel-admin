<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function index(Request $request, $blogId = null)
    {
        // Support both URL formats: /blog/672561550 and /blog?blogId=672561550
        if (!$blogId) {
            $blogId = $request->query('blogId');
        }

        $dbErrorMessage = null;
        $recent = collect();
        $main = null;
        $blogTable = $this->resolveBlogTable();

        if (!$blogTable) {
            return view('blog.index', [
                'main' => null,
                'recent' => collect(),
                'dbErrorMessage' => null,
            ]);
        }

        $blogIdColumn = $this->columnExists($blogTable, 'blog_id') ? 'blog_id' : 'id';
        $titleColumn = $this->firstExistingColumn($blogTable, ['title', 'blog_title']);
        $authorColumn = $this->firstExistingColumn($blogTable, ['author', 'blog_author']);
        $dateColumn = $this->firstExistingColumn($blogTable, ['date', 'blog_date', 'created_at']);
        $bodyColumn = $this->firstExistingColumn($blogTable, ['body', 'description', 'content']);
        $imageColumn = $this->firstExistingColumn($blogTable, ['image_path', 'image', 'image_url']);
        $viewsColumn = $this->firstExistingColumn($blogTable, ['clicks', 'views']);
        $orderColumn = $this->firstExistingColumn($blogTable, ['id', 'blog_id', 'created_at']);

        // Increment views only if user hasn't viewed this blog before (cookie-based tracking)
        if ($blogId) {
            $cookieName = 'blog_viewed_' . $blogId;
            try {
                if (!$request->cookie($cookieName) && $viewsColumn) {
                    DB::table($blogTable)
                        ->where($blogIdColumn, $blogId)
                        ->increment($viewsColumn);
                }
            } catch (\Throwable $e) {
                Log::warning('Blog click increment failed', ['error' => $e->getMessage()]);
            }
        }

        try {
            $baseQuery = $this->buildBlogQuery(
                $blogTable,
                $blogIdColumn,
                $titleColumn,
                $authorColumn,
                $dateColumn,
                $bodyColumn,
                $imageColumn,
                $viewsColumn
            );

            // If a specific blog ID was requested, fetch it directly (fast lookup).
            if ($blogId) {
                $main = (clone $baseQuery)
                    ->where($blogTable . '.' . $blogIdColumn, $blogId)
                    ->first();
            }

            // Load a limited set of recent posts for the sidebar to avoid scanning a huge table.
            $recent = (clone $baseQuery)
                ->orderByDesc($blogTable . '.' . $orderColumn)
                ->limit(50)
                ->get();

            $authorImageMap = $this->resolveAuthorImageMap();

            if ($main) {
                $main->author_image = $authorImageMap[$main->author ?? ''] ?? null;
            }

            $recent = $recent->map(function ($post) use ($authorImageMap) {
                $post->author_image = $authorImageMap[$post->author ?? ''] ?? null;
                return $post;
            });

            // If specific blog wasn't found above (maybe null), fall back to sidebar list's first item
            if (empty($main) && $recent->count() > 0) {
                $main = $recent->first();
            }
        } catch (\Throwable $e) {
            $dbErrorMessage = 'We are temporarily unable to load blogs. Please try again in a few moments.';
            Log::warning('Blog list query failed', ['error' => $e->getMessage()]);
        }

        // Set cookie for this blog view (expires in 30 days)
        if ($blogId) {
            $response = response()->view('blog.index', [
                'main' => $main,
                'recent' => $recent,
                'dbErrorMessage' => $dbErrorMessage,
            ]);
            
            $cookieName = 'blog_viewed_' . $blogId;
            $response->cookie($cookieName, true, 60 * 24 * 30); // 30 days
            
            return $response;
        }

        return view('blog.index', [
            'main' => $main,
            'recent' => $recent,
            'dbErrorMessage' => $dbErrorMessage,
        ]);
    }

    // API endpoint to increment blog clicks with cookie tracking
    public function incrementClicks(Request $request, $blogId)
    {
        try {
            $blogTable = $this->resolveBlogTable();
            if (!$blogTable) {
                return response()->json([
                    'success' => false,
                    'message' => 'Blog table is not available.',
                    'error' => 'blog_table_unavailable'
                ], 503);
            }

            $blogIdColumn = $this->columnExists($blogTable, 'blog_id') ? 'blog_id' : 'id';
            $viewsColumn = $this->firstExistingColumn($blogTable, ['clicks', 'views']);

            $cookieName = 'blog_viewed_' . $blogId;
            
            // Only increment if user hasn't viewed this blog before
            if (!$request->cookie($cookieName) && $viewsColumn) {
                DB::table($blogTable)
                    ->where($blogIdColumn, $blogId)
                    ->increment($viewsColumn);
            }

            $blog = DB::table($blogTable)
                ->where($blogIdColumn, $blogId)
                ->first();

            $response = response()->json([
                'success' => true,
                'clicks' => ($viewsColumn && $blog) ? ($blog->{$viewsColumn} ?? 0) : 0,
                'message' => 'Views updated successfully'
            ]);
            
            // Set cookie to track this view
            $response->cookie($cookieName, true, 60 * 24 * 30); // 30 days
            
            return $response;
        } catch (\Throwable $e) {
            Log::warning('Blog incrementClicks failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Service temporarily unavailable. Please try again in a few moments.',
                'error' => 'database_unavailable'
            ], 503);
        }
    }

    private function estimateReadingTime(string $html): int
    {
        $words = str_word_count(strip_tags($html));
        return (int) max(1, ceil($words / 200));
    }

    private function resolveBlogTable(): ?string
    {
        foreach (['blogs', 'blog'] as $table) {
            if (Schema::hasTable($table)) {
                return $table;
            }
        }

        return null;
    }

    private function columnExists(string $table, string $column): bool
    {
        return Schema::hasColumn($table, $column);
    }

    private function firstExistingColumn(string $table, array $columns): ?string
    {
        foreach ($columns as $column) {
            if ($this->columnExists($table, $column)) {
                return $column;
            }
        }

        return null;
    }

    private function buildBlogQuery(
        string $blogTable,
        string $blogIdColumn,
        ?string $titleColumn,
        ?string $authorColumn,
        ?string $dateColumn,
        ?string $bodyColumn,
        ?string $imageColumn,
        ?string $viewsColumn
    ) {
        $query = DB::table($blogTable);

        $query->selectRaw($blogTable . '.' . $blogIdColumn . ' as blog_id');
        $query->selectRaw(($titleColumn ? $blogTable . '.' . $titleColumn : 'NULL') . ' as title');
        $query->selectRaw(($authorColumn ? $blogTable . '.' . $authorColumn : 'NULL') . ' as author');
        $query->selectRaw(($dateColumn ? $blogTable . '.' . $dateColumn : 'NULL') . ' as date');
        $query->selectRaw(($bodyColumn ? $blogTable . '.' . $bodyColumn : 'NULL') . ' as body');
        $query->selectRaw(($imageColumn ? $blogTable . '.' . $imageColumn : 'NULL') . ' as image_path');
        $query->selectRaw(($viewsColumn ? $blogTable . '.' . $viewsColumn : '0') . ' as clicks');
        $query->selectRaw('NULL as author_image');

        return $query;
    }

    private function resolveAuthorImageMap(): array
    {
        if (!Schema::hasTable('team') || !Schema::hasColumn('team', 'team_name') || !Schema::hasColumn('team', 'team_image')) {
            return [];
        }

        return DB::table('team')
            ->whereNotNull('team_name')
            ->pluck('team_image', 'team_name')
            ->toArray();
    }
}
