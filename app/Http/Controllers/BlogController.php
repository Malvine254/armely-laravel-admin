<?php

namespace App\Http\Controllers;

use App\Services\AzureMailService;
use App\Support\BlogUrl;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $blogId = trim((string) $request->query('blogId', ''));

        if ($blogId !== '') {
            return $this->show($request, $blogId);
        }

        [$main, $recent, $dbErrorMessage] = $this->prepareBlogListingData();

        return view('blog.index', [
            'main' => $main,
            'recent' => $recent,
            'dbErrorMessage' => $dbErrorMessage,
        ]);
    }

    public function show(Request $request, string $blog)
    {
        $blogIdentifier = trim($blog);
        if ($blogIdentifier === '') {
            return redirect()->route('blog.index', [], 301);
        }

        [$main, $recent, $dbErrorMessage, $canonicalUrl] = $this->prepareBlogDetailData($request, $blogIdentifier);

        if ($canonicalUrl !== null && $request->query('blogId') !== null) {
            return redirect()->to($canonicalUrl, 301);
        }

        if ($canonicalUrl !== null) {
            $requestedPath = rtrim($request->getPathInfo(), '/');
            $canonicalPath = rtrim(parse_url($canonicalUrl, PHP_URL_PATH) ?: '/blog', '/');

            if ($requestedPath !== $canonicalPath) {
                return redirect()->to($canonicalUrl, 301);
            }
        }

        if ($main !== null) {
            $blogTable = $this->resolveBlogTable();
            if ($blogTable) {
                $blogIdColumn = $this->columnExists($blogTable, 'blog_id') ? 'blog_id' : 'id';
                $viewsColumn = $this->firstExistingColumn($blogTable, ['clicks', 'views']);
                $trackedBlogId = (string) ($main->blog_id ?? $main->id ?? $blogIdentifier);

                if ($viewsColumn && !$this->hasTrackedSessionItem($request, 'blog_viewed_ids', $trackedBlogId)) {
                    try {
                        DB::table($blogTable)
                            ->where($blogIdColumn, $trackedBlogId)
                            ->increment($viewsColumn);
                        $this->trackSessionItem($request, 'blog_viewed_ids', $trackedBlogId);
                    } catch (\Throwable $e) {
                        Log::warning('Blog click increment failed', ['error' => $e->getMessage()]);
                    }
                }
            }
        }

        if ($main === null) {
            if ($dbErrorMessage !== null) {
                return view('blog.index', [
                    'main' => null,
                    'recent' => $recent,
                    'dbErrorMessage' => $dbErrorMessage,
                ]);
            }

            abort(404);
        }

        return view('blog.index', [
            'main' => $main,
            'recent' => $recent,
            'dbErrorMessage' => $dbErrorMessage,
        ]);
    }

    private function prepareBlogListingData(): array
    {
        $dbErrorMessage = null;
        $recent = collect();
        $main = null;
        $blogTable = $this->resolveBlogTable();

        if (!$blogTable) {
            return [null, collect(), null];
        }

        $blogIdColumn = $this->columnExists($blogTable, 'blog_id') ? 'blog_id' : 'id';
        $titleColumn = $this->firstExistingColumn($blogTable, ['title', 'blog_title']);
        $authorColumn = $this->firstExistingColumn($blogTable, ['author', 'blog_author']);
        $dateColumn = $this->firstExistingColumn($blogTable, ['date', 'blog_date', 'created_at']);
        $bodyColumn = $this->firstExistingColumn($blogTable, ['body', 'description', 'content']);
        $imageColumn = $this->firstExistingColumn($blogTable, ['image_path', 'image', 'image_url']);
        $viewsColumn = $this->firstExistingColumn($blogTable, ['clicks', 'views']);
        $orderColumn = $this->firstExistingColumn($blogTable, ['id', 'blog_id', 'created_at']);

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

            $recent = (clone $baseQuery)
                ->orderByDesc($blogTable . '.' . $orderColumn)
                ->get();

            $authorImageMap = $this->resolveAuthorImageMap();
            $recent = $recent->map(function ($post) use ($authorImageMap) {
                $post->author_image = $this->resolveAuthorImageForName((string) ($post->author ?? ''), $authorImageMap);
                return $post;
            });

            if ($recent->count() > 0) {
                $main = $recent->first();
            }
        } catch (\Throwable $e) {
            $dbErrorMessage = 'We are temporarily unable to load blogs. Please try again in a few moments.';
            Log::warning('Blog list query failed', ['error' => $e->getMessage()]);
        }

        return [$main, $recent, $dbErrorMessage];
    }

    private function prepareBlogDetailData(Request $request, string $blogIdentifier): array
    {
        $dbErrorMessage = null;
        $recent = collect();
        $main = null;
        $canonicalUrl = null;
        $blogTable = $this->resolveBlogTable();

        if (!$blogTable) {
            return [null, collect(), null, null];
        }

        $blogIdColumn = $this->columnExists($blogTable, 'blog_id') ? 'blog_id' : 'id';
        $titleColumn = $this->firstExistingColumn($blogTable, ['title', 'blog_title']);
        $authorColumn = $this->firstExistingColumn($blogTable, ['author', 'blog_author']);
        $dateColumn = $this->firstExistingColumn($blogTable, ['date', 'blog_date', 'created_at']);
        $bodyColumn = $this->firstExistingColumn($blogTable, ['body', 'description', 'content']);
        $imageColumn = $this->firstExistingColumn($blogTable, ['image_path', 'image', 'image_url']);
        $viewsColumn = $this->firstExistingColumn($blogTable, ['clicks', 'views']);
        $orderColumn = $this->firstExistingColumn($blogTable, ['id', 'blog_id', 'created_at']) ?? $blogIdColumn;

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

            $recent = (clone $baseQuery)
                ->orderByDesc($blogTable . '.' . $orderColumn)
                ->get();

            $authorImageMap = $this->resolveAuthorImageMap();
            $recent = $recent->map(function ($post) use ($authorImageMap) {
                $post->author_image = $this->resolveAuthorImageForName((string) ($post->author ?? ''), $authorImageMap);
                return $post;
            });

            $main = $this->resolveBlogFromRecent($recent, $blogIdentifier);
            if ($main !== null) {
                $canonicalUrl = BlogUrl::url($main);
            }
        } catch (\Throwable $e) {
            $dbErrorMessage = 'We are temporarily unable to load blogs. Please try again in a few moments.';
            Log::warning('Blog detail query failed', ['error' => $e->getMessage()]);
        }

        return [$main, $recent, $dbErrorMessage, $canonicalUrl];
    }

    private function resolveBlogFromRecent(Collection $recent, string $blogIdentifier): ?object
    {
        $blogIdentifier = trim($blogIdentifier);
        if ($blogIdentifier === '') {
            return null;
        }

        $numericId = $this->extractBlogId($blogIdentifier);
        if ($numericId !== null) {
            $match = $recent->first(function ($post) use ($numericId) {
                return (string) ($post->blog_id ?? $post->id ?? '') === $numericId;
            });

            if ($match) {
                return $match;
            }
        }

        $slug = Str::slug($blogIdentifier);
        if ($slug === '') {
            return null;
        }

        return $recent->first(function ($post) use ($slug) {
            return Str::slug((string) ($post->title ?? $post->blog_title ?? '')) === $slug;
        });
    }

    private function extractBlogId(string $blogIdentifier): ?string
    {
        $blogIdentifier = trim($blogIdentifier);

        if (ctype_digit($blogIdentifier)) {
            return $blogIdentifier;
        }

        if (preg_match('/-(\d+)$/', $blogIdentifier, $matches) === 1) {
            return $matches[1];
        }

        return null;
    }

    // API endpoint to increment blog clicks with session tracking.
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

            // Only increment if user hasn't viewed this blog before
            if (!$this->hasTrackedSessionItem($request, 'blog_viewed_ids', $blogId) && $viewsColumn) {
                DB::table($blogTable)
                    ->where($blogIdColumn, $blogId)
                    ->increment($viewsColumn);
                $this->trackSessionItem($request, 'blog_viewed_ids', $blogId);
            }

            $blog = DB::table($blogTable)
                ->where($blogIdColumn, $blogId)
                ->first();

            $response = response()->json([
                'success' => true,
                'clicks' => ($viewsColumn && $blog) ? ($blog->{$viewsColumn} ?? 0) : 0,
                'message' => 'Views updated successfully'
            ]);

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

    public function requestDownload(Request $request, $blogId)
    {
        $data = $request->validate([
            'name'  => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:255'],
        ], [
            'name.required'  => 'Please enter your name.',
            'email.required' => 'Please enter your email address.',
            'email.email'    => 'Please enter a valid email address.',
        ]);

        $blogTable = $this->resolveBlogTable();
        if (!$blogTable) {
            return response()->json(['success' => false, 'message' => 'Blog not found.'], 404);
        }

        $blogIdColumn = $this->columnExists($blogTable, 'blog_id') ? 'blog_id' : 'id';
        $titleColumn  = $this->firstExistingColumn($blogTable, ['title', 'blog_title']);

        $blog = DB::table($blogTable)->where($blogIdColumn, $blogId)->first();
        if (!$blog) {
            return response()->json(['success' => false, 'message' => 'Blog post not found.'], 404);
        }

        $blogTitle = $titleColumn ? ($blog->{$titleColumn} ?? 'Article') : 'Article';

        // Record the download request
        try {
            DB::table('blog_download_requests')->insert([
                'blog_id'      => (string) $blogId,
                'blog_title'   => $blogTitle,
                'name'         => $data['name'],
                'email'        => $data['email'],
                'ip_address'   => $request->ip(),
                'link_sent_at' => now(),
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Blog download request insert failed: ' . $e->getMessage());
        }

        // Generate a secure token valid for 24 hours stored in cache
        $token = Str::random(64);
        Cache::put('blog_download:' . $token, [
            'blog_id'    => (string) $blogId,
            'expires_at' => now()->addHours(24)->timestamp,
        ], now()->addHours(24));

        $downloadUrl = route('blog.download-pdf', ['blogId' => $blogId, 'token' => $token]);

        // Send the download link via email
        try {
            $mailer   = app(AzureMailService::class);
            $fromEmail = config('mail.from.address', 'no-reply@armely.com');
            $subject   = 'Your Armely Article: ' . $blogTitle;
            $htmlBody  = view('emails.blog.download-link', [
                'name'        => $data['name'],
                'blogTitle'   => $blogTitle,
                'downloadUrl' => $downloadUrl,
            ])->render();

            $sent = $mailer->sendEmail($fromEmail, $data['email'], $subject, $htmlBody);
            if (!$sent) {
                Log::error('Blog download email not sent (sendEmail returned false)', ['to' => $data['email']]);
                return response()->json([
                    'success' => false,
                    'message' => 'We could not send the email. Please try again.',
                ], 500);
            }
        } catch (\Throwable $e) {
            Log::error('Blog download email failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'We could not send the email. Please try again.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'A download link has been sent to ' . $data['email'],
        ]);
    }

    public function downloadPdf(Request $request, $blogId)
    {
        $token = $request->query('token', '');
        if (!$token) {
            abort(403, 'Invalid download link. Please return to the article and request a fresh download link.');
        }

        $cached = Cache::get('blog_download:' . $token);

        if (!$cached || $cached['blog_id'] !== (string) $blogId || now()->timestamp > $cached['expires_at']) {
            abort(403, 'This download link has expired or is invalid. Please return to the article and request a new one.');
        }

        $blogTable = $this->resolveBlogTable();
        if (!$blogTable) {
            abort(404, 'Blog not found.');
        }

        $blogIdColumn = $this->columnExists($blogTable, 'blog_id') ? 'blog_id' : 'id';
        $titleColumn  = $this->firstExistingColumn($blogTable, ['title', 'blog_title']);
        $authorColumn = $this->firstExistingColumn($blogTable, ['author', 'blog_author']);
        $dateColumn   = $this->firstExistingColumn($blogTable, ['date', 'blog_date', 'created_at']);
        $bodyColumn   = $this->firstExistingColumn($blogTable, ['body', 'description', 'content']);
        $imageColumn  = $this->firstExistingColumn($blogTable, ['image_path', 'image', 'image_url']);

        $raw = DB::table($blogTable)->where($blogIdColumn, $blogId)->first();
        if (!$raw) {
            abort(404, 'Blog post not found.');
        }

        // Build a normalised object for the PDF view
        $blog = (object) [
            'blog_id'    => $raw->{$blogIdColumn},
            'title'      => $titleColumn  ? ($raw->{$titleColumn}  ?? 'Article') : 'Article',
            'author'     => $authorColumn ? ($raw->{$authorColumn} ?? null) : null,
            'date'       => $dateColumn   ? ($raw->{$dateColumn}   ?? null) : null,
            'image_path' => $imageColumn  ? ($raw->{$imageColumn}  ?? null) : null,
        ];

        // Process body HTML: make image src absolute filesystem paths so dompdf can embed them
        $rawBody = $bodyColumn ? ($raw->{$bodyColumn} ?? '') : '';
        $bodyHtml = $this->prepareBodyForPdf((string) $rawBody);

        $pdf = Pdf::loadView('blog.pdf', compact('blog', 'bodyHtml'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isRemoteEnabled'      => true,
                'isHtml5ParserEnabled' => true,
                'defaultFont'          => 'DejaVu Sans',
                'dpi'                  => 150,
                'chroot'               => base_path(),
                'allowed_protocols'    => ['http://', 'https://', 'file://'],
            ]);

        $filename = Str::slug($blog->title ?: 'armely-article') . '.pdf';

        return $pdf->download($filename);
    }

    private function prepareBodyForPdf(string $html): string
    {
        if (trim($html) === '') {
            return '';
        }

        // Strip scripts, styles, iframes
        $html = preg_replace('/<(script|style|iframe|noscript)\b[^>]*>.*?<\/\1>/is', '', $html) ?? $html;
        $html = preg_replace('/<(script|style|iframe)\b[^>]*\/?>/is', '', $html) ?? $html;

        // Convert img src to absolute filesystem paths so dompdf can embed them
        $html = preg_replace_callback(
            '/<img\b([^>]*?)>/i',
            function ($m) {
                $tag = $m[1];

                // Extract src value (handles both quotes and no-quote edge cases)
                if (!preg_match('/\bsrc\s*=\s*(["\']?)([^"\'>\s]+)\1/i', $tag, $srcMatch)) {
                    return ''; // img with no src — drop it
                }
                $src = html_entity_decode($srcMatch[2], ENT_QUOTES);

                $absSrc = \App\Support\BlogMedia::filesystemPath($src);

                // Only include img if the file actually exists (skip broken local paths)
                if (!str_starts_with($absSrc, 'http') && !str_starts_with($absSrc, 'data:') && !file_exists($absSrc)) {
                    return '';
                }

                // Rebuild tag with safe src, preserve alt, strip everything else
                $alt = '';
                if (preg_match('/\balt\s*=\s*(["\'])(.*?)\1/i', $tag, $altMatch)) {
                    $alt = ' alt="' . htmlspecialchars($altMatch[2], ENT_QUOTES) . '"';
                }

                return '<img src="' . htmlspecialchars($absSrc, ENT_QUOTES) . '"' . $alt . ' style="max-width:100%;height:auto;display:block;margin:10px 0;">';
            },
            $html
        );

        return $html;
    }

    private function resolveBlogTable(): ?string
    {
        try {
            foreach (['blogs', 'blog'] as $table) {
                if (Schema::hasTable($table)) {
                    return $table;
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Blog table availability check failed', ['error' => $e->getMessage()]);
        }

        return null;
    }

    private function columnExists(string $table, string $column): bool
    {
        try {
            return Schema::hasColumn($table, $column);
        } catch (\Throwable $e) {
            Log::warning('Blog column availability check failed', [
                'table' => $table,
                'column' => $column,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
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

    private function hasTrackedSessionItem(Request $request, string $key, string|int $id): bool
    {
        return in_array((string) $id, (array) $request->session()->get($key, []), true);
    }

    private function trackSessionItem(Request $request, string $key, string|int $id): void
    {
        $items = array_values(array_unique(array_merge(
            (array) $request->session()->get($key, []),
            [(string) $id]
        )));

        $request->session()->put($key, array_slice($items, -250));
    }

    private function resolveAuthorImageMap(): array
    {
        if (!Schema::hasTable('team') || !Schema::hasColumn('team', 'team_name') || !Schema::hasColumn('team', 'team_image')) {
            return [];
        }

        return DB::table('team')
            ->whereNotNull('team_name')
            ->get(['team_name', 'team_image'])
            ->reduce(function (array $map, object $member) {
                $name = trim((string) ($member->team_name ?? ''));
                $image = trim((string) ($member->team_image ?? ''));

                if ($name === '' || $image === '') {
                    return $map;
                }

                $filename = basename($image);
                if (!is_file(public_path('images/team/' . $filename))) {
                    return $map;
                }

                $map[$name] = $filename;
                $map[Str::lower($name)] = $filename;

                return $map;
            }, []);
    }

    private function resolveAuthorImageForName(string $authorName, array $authorImageMap): ?string
    {
        $authorName = trim($authorName);
        if ($authorName === '') {
            return null;
        }

        return $authorImageMap[$authorName] ?? $authorImageMap[Str::lower($authorName)] ?? null;
    }
}
