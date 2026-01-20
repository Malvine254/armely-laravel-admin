<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogActivity
{
    /**
     * Handle an incoming request and log the page visit.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Write to a separate test file to confirm middleware runs
        file_put_contents(storage_path('logs/middleware-test.txt'), 
            date('Y-m-d H:i:s') . " - Middleware executed: " . $request->path() . "\n", 
            FILE_APPEND
        );
        
        Log::info('LogActivity: Middleware executed', [
            'url' => $request->path(),
            'method' => $request->method()
        ]);

        $response = $next($request);

        // Only log successful GET requests to avoid duplicate logs on form submissions
        if ($request->isMethod('GET') && $response->getStatusCode() === 200) {
            Log::info('LogActivity: Attempting to log', [
                'url' => $request->path(),
                'status' => $response->getStatusCode(),
                'method' => $request->method()
            ]);
            $this->logPageVisit($request);
        } else {
            Log::info('LogActivity: Skipped', [
                'url' => $request->path(),
                'status' => $response->getStatusCode(),
                'method' => $request->method(),
                'reason' => !$request->isMethod('GET') ? 'Not GET' : 'Status not 200'
            ]);
        }

        return $response;
    }

    /**
     * Log the page visit to admin_activities table with enhanced analytics.
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

            // Get IP address (handles proxies and load balancers)
            $ipAddress = $this->getClientIp($request);

            // Get user agent
            $userAgent = $request->userAgent() ?? 'Unknown';

            // Get referrer
            $referrer = $request->header('referer') ?? null;

            // Try to get country from IP (basic implementation)
            $country = $this->getCountryFromIp($ipAddress);

            DB::table('admin_activities')->insert([
                'admin_id' => $userId,
                'action' => 'page_visit',
                'entity_type' => $userType,
                'entity_id' => $routeName,
                'description' => $description,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'page_url' => $url,
                'referrer' => $referrer,
                'country' => $country,
                'visit_count' => 1,
                'created_at' => now(),
            ]);

        } catch (\Throwable $e) {
            // Silently fail to not interrupt the request
            \Log::error('LogActivity middleware failed: ' . $e->getMessage());
        }
    }

    /**
     * Get the client's IP address, accounting for proxies.
     */
    protected function getClientIp(Request $request): ?string
    {
        if ($request->header('CF-Connecting-IP')) {
            return $request->header('CF-Connecting-IP');
        }

        if ($request->header('X-Forwarded-For')) {
            $ips = explode(',', $request->header('X-Forwarded-For'));
            return trim($ips[0]);
        }

        if ($request->header('X-Forwarded')) {
            return $request->header('X-Forwarded');
        }

        if ($request->header('X-Client-IP')) {
            return $request->header('X-Client-IP');
        }

        return $request->ip() ?? 'Unknown';
    }

    /**
     * Get country information from IP (basic implementation).
     * For production, consider using a service like geoip2 or maxmind.
     */
    protected function getCountryFromIp(?string $ip): ?string
    {
        if (!$ip || $ip === 'Unknown' || $ip === '127.0.0.1' || $ip === '::1') {
            return 'Local';
        }

        // Basic check for private IPs
        if ($this->isPrivateIp($ip)) {
            return 'Private Network';
        }

        // You can integrate with MaxMind GeoIP2 or other services here
        // For now, return null to indicate IP is available in database
        return null;
    }

    /**
     * Check if IP is private/local.
     */
    protected function isPrivateIp(string $ip): bool
    {
        $privateRanges = [
            '10.0.0.0|10.255.255.255',
            '172.16.0.0|172.31.255.255',
            '192.168.0.0|192.168.255.255',
            '127.0.0.0|127.255.255.255',
        ];

        $ip = ip2long($ip);
        if ($ip === false) {
            return false;
        }

        foreach ($privateRanges as $range) {
            [$start, $end] = explode('|', $range);
            if ($ip >= ip2long($start) && $ip <= ip2long($end)) {
                return true;
            }
        }

        return false;
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
            'api/analytics/', // Skip API analytics endpoints to avoid duplicate logging
            'api/search', // Skip search API
        ];

        foreach ($skipPatterns as $pattern) {
            if (str_contains($url, $pattern)) {
                return true;
            }
        }

        return false;
    }
}
