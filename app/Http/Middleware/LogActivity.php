<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
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
            // X-Forwarded-For lists client first, then proxies. Use the first non-private IP if available.
            foreach ($ips as $candidate) {
                $candidate = trim($candidate);
                if ($candidate && !$this->isPrivateIp($candidate)) {
                    return $candidate;
                }
            }
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
        if (!$ip || $ip === 'Unknown' || in_array($ip, ['127.0.0.1', '::1'], true)) {
            return 'Local';
        }

        // Basic check for private IPs
        if ($this->isPrivateIp($ip)) {
            return 'Private';
        }

        // Cache country lookups to reduce external calls
        $cacheKey = 'geoip_country_' . md5($ip);
        $cached = Cache::get($cacheKey);
        if ($cached !== null) {
            Log::info('GeoIP cache hit', ['ip' => $ip, 'country' => $cached]);
            return $cached;
        }

        // Try to resolve using ipapi.co which returns a 2-letter country code for /country
        $attempted = false;
        try {
            $attempted = true;
            $url = "https://ipapi.co/{$ip}/country/";
            $opts = ['http' => ['timeout' => 3]]; // short timeout
            $context = stream_context_create($opts);
            $result = file_get_contents($url, false, $context);
            if ($result !== false) {
                $code = strtoupper(trim($result));
                if (preg_match('/^[A-Z]{2}$/', $code)) {
                    // Cache for 7 days
                    Cache::put($cacheKey, $code, now()->addDays(7));
                    Log::info('GeoIP lookup success', ['ip' => $ip, 'country' => $code]);
                    return $code;
                }
                Log::info('GeoIP lookup returned unexpected data', ['ip' => $ip, 'raw' => $result]);
            }
        } catch (\Throwable $e) {
            Log::warning('GeoIP lookup exception', ['ip' => $ip, 'error' => $e->getMessage()]);
        }

        // Fallback: try ipapi JSON endpoint for country_code field
        try {
            $attempted = true;
            $url = "https://ipapi.co/{$ip}/json/";
            $opts = ['http' => ['timeout' => 3]];
            $context = stream_context_create($opts);
            $json = file_get_contents($url, false, $context);
            if ($json) {
                $data = json_decode($json, true);
                if (!empty($data['country_code'])) {
                    $code = strtoupper($data['country_code']);
                    Cache::put($cacheKey, $code, now()->addDays(7));
                    Log::info('GeoIP JSON lookup success', ['ip' => $ip, 'country' => $code]);
                    return $code;
                }
                Log::info('GeoIP JSON returned no country_code', ['ip' => $ip, 'json' => $data]);
            }
        } catch (\Throwable $e) {
            Log::warning('GeoIP JSON lookup exception', ['ip' => $ip, 'error' => $e->getMessage()]);
        }

        // If external lookups were attempted but failed, store and return 'Unknown'
        if ($attempted) {
            Cache::put($cacheKey, 'Unknown', now()->addHours(6));
            Log::info('GeoIP lookup failed - caching Unknown', ['ip' => $ip]);
            return 'Unknown';
        }

        return 'Unknown';
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
