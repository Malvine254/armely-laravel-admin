<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class LogActivity
{
    /**
     * Handle an incoming request and log the page visit.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only log successful GET requests to avoid duplicate logs on form submissions
        if ($request->isMethod('GET') && $response->getStatusCode() === 200) {
            $this->logPageVisit($request);
        }

        return $response;
    }

    /**
     * Log the page visit to admin_activities table.
     */
    protected function logPageVisit(Request $request): void
    {
        try {
            $user = null;
            $userType = 'guest';
            $userId = null;

            // Check if admin is logged in
            if (Auth::guard('admin')->check()) {
                $user = Auth::guard('admin')->user();
                $userType = 'admin';
                $userId = $user->id;
            }
            // Check if regular user is logged in
            elseif (Auth::guard('web')->check()) {
                $user = Auth::guard('web')->user();
                $userType = 'user';
                $userId = $user->id;
            }

            // Get the route name or URL
            $routeName = $request->route()?->getName() ?? 'unknown';
            $url = $request->path();
            $fullUrl = $request->fullUrl();

            // Skip logging for assets, ajax calls, and certain routes
            if ($this->shouldSkipLogging($url)) {
                return;
            }

            // Get user details
            $userName = $user ? ($user->name ?? $user->email ?? 'Unknown') : 'Guest';
            $userEmail = $user ? ($user->email ?? 'N/A') : 'Guest';

            // Create description
            $description = "Visited: {$url}";
            if ($request->query()) {
                $description .= " (with query params)";
            }

            DB::table('admin_activities')->insert([
                'admin_id' => $userId,
                'action' => 'page_visit',
                'entity_type' => $userType,
                'entity_id' => $routeName,
                'description' => $description,
                'created_at' => now(),
            ]);

        } catch (\Throwable $e) {
            // Silently fail to not interrupt the request
            \Log::error('LogActivity middleware failed: ' . $e->getMessage());
        }
    }

    /**
     * Determine if this request should skip logging.
     */
    protected function shouldSkipLogging(string $url): bool
    {
        $skipPatterns = [
            'css/', 'js/', 'images/', 'fonts/', 'img/',
            'ckeditor/', 'downloads/', 'pdf/',
            'livewire/', '_debugbar/', 'favicon.ico',
        ];

        foreach ($skipPatterns as $pattern) {
            if (str_contains($url, $pattern)) {
                return true;
            }
        }

        return false;
    }
}
