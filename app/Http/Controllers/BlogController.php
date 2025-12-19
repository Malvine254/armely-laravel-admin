<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

        // Increment views only if user hasn't viewed this blog before (cookie-based tracking)
        if ($blogId) {
            $cookieName = 'blog_viewed_' . $blogId;
            try {
                if (!$request->cookie($cookieName)) {
                    DB::table('blogs')
                        ->where('blog_id', $blogId)
                        ->increment('clicks');
                }
            } catch (\Throwable $e) {
                $dbErrorMessage = 'We are temporarily unable to load blog details. Please try again soon.';
                Log::warning('Blog click increment failed', ['error' => $e->getMessage()]);
            }
        }

        try {
            $recent = DB::table('blogs')
                ->select('blog_id', 'title', 'author', 'date', 'body', 'image_path', 'clicks')
                ->orderByDesc('id')
                ->get();
        } catch (\Throwable $e) {
            $dbErrorMessage = 'We are temporarily unable to load blogs. Please try again in a few moments.';
            Log::warning('Blog list query failed', ['error' => $e->getMessage()]);
        }

        if ($blogId && $recent->count() > 0) {
            $main = $recent->first(fn($b) => $b->blog_id === $blogId) ?: $recent->first();
        } elseif ($recent->count() > 0) {
            $main = $recent->first();
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
            $cookieName = 'blog_viewed_' . $blogId;
            
            // Only increment if user hasn't viewed this blog before
            if (!$request->cookie($cookieName)) {
                DB::table('blogs')
                    ->where('blog_id', $blogId)
                    ->increment('clicks');
            }

            $blog = DB::table('blogs')
                ->where('blog_id', $blogId)
                ->first();

            $response = response()->json([
                'success' => true,
                'clicks' => $blog->clicks ?? 0,
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
}
