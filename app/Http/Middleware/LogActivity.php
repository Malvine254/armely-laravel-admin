<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use IP2Location\Database as IP2LocationDB;
use Symfony\Component\HttpFoundation\Response;

class LogActivity
{
    private const DB_FAILURE_COOLDOWN_SECONDS = 120;

    /**
     * Handle an incoming request and log the page visit.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only log successful GET requests and skip noisy/static paths.
        if (
            $request->isMethod('GET')
            && $response->getStatusCode() === 200
            && !$this->shouldSkipLogging($request->path())
            && !$this->isDatabaseInCooldown()
        ) {
            $this->logPageVisit($request);
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

            // Prefer Cloudflare header if present (gives country code)
            $countryHeader = $request->header('CF-IPCountry');
            if ($countryHeader && preg_match('/^[A-Za-z]{2}$/', $countryHeader)) {
                $country = strtoupper($countryHeader);
            } else {
                // Try to get country from IP (GeoIP helper / external API)
                $country = $this->getCountryFromIp($ipAddress);
            }

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

            // Database is healthy again.
            Cache::forget('activity_log_db_unavailable_until');

        } catch (\Throwable $e) {
            $this->markDatabaseCooldown($e->getMessage());
            // Silently fail to avoid affecting page response time.
            Log::warning('LogActivity skipped due to DB issue: ' . $e->getMessage());
        }
    }

    protected function isDatabaseInCooldown(): bool
    {
        $until = (int) Cache::get('activity_log_db_unavailable_until', 0);
        return $until > time();
    }

    protected function markDatabaseCooldown(string $errorMessage): void
    {
        $message = strtolower($errorMessage);
        $isConnectionFailure = str_contains($message, 'sqlstate')
            || str_contains($message, 'connection')
            || str_contains($message, 'refused')
            || str_contains($message, 'not allowed to connect');

        if ($isConnectionFailure) {
            Cache::put(
                'activity_log_db_unavailable_until',
                time() + self::DB_FAILURE_COOLDOWN_SECONDS,
                now()->addSeconds(self::DB_FAILURE_COOLDOWN_SECONDS)
            );
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

        // If torann/geoip or similar is installed, prefer local DB lookup
        try {
            if (function_exists('geoip')) {
                $loc = geoip($ip);
                if ($loc) {
                    $code = strtoupper($loc->iso_code ?? ($loc->country_code ?? ''));
                    if ($code && preg_match('/^[A-Z]{2}$/', $code)) {
                        Cache::put($cacheKey, $code, now()->addDays(7));
                        Log::info('GeoIP local lookup success', ['ip' => $ip, 'country' => $code]);
                        return $code;
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::debug('GeoIP local lookup exception: ' . $e->getMessage());
        }

        // Next: try IP2Location local DB (open-source LITE DB, no API keys)
        try {
            $dbPath = storage_path('app/geoip/IP2LOCATION-LITE.BIN');
            if (file_exists($dbPath)) {
                try {
                    $reader = new IP2LocationDB($dbPath, IP2LocationDB::FILE_IO);
                    $rec = $reader->lookup($ip, IP2LocationDB::COUNTRY_CODE);
                    $code = is_string($rec) ? strtoupper(trim($rec)) : null;
                    if ($code && preg_match('/^[A-Z]{2}$/', $code)) {
                        Cache::put($cacheKey, $code, now()->addDays(30));
                        Log::info('GeoIP IP2Location lookup success', ['ip' => $ip, 'country' => $code]);
                        return $code;
                    }
                } catch (\Throwable $e) {
                    Log::debug('IP2Location lookup failed: ' . $e->getMessage());
                }
            }
        } catch (\Throwable $e) {
            Log::debug('IP2Location outer exception: ' . $e->getMessage());
        }

        $attempted = false;

        if (!env('GEOIP_HTTP_LOOKUP', false)) {
            return 'Unknown';
        }

        // Use Laravel HTTP client to query ipapi (returns country code)
        try {
            $attempted = true;
            $resp = Http::timeout(3)->get("https://ipapi.co/{$ip}/country/");
            if ($resp->ok()) {
                $code = strtoupper(trim($resp->body()));
                if (preg_match('/^[A-Z]{2}$/', $code)) {
                    Cache::put($cacheKey, $code, now()->addDays(7));
                    Log::info('GeoIP http lookup success', ['ip' => $ip, 'country' => $code]);
                    return $code;
                }
                Log::info('GeoIP http lookup unexpected', ['ip' => $ip, 'body' => $resp->body()]);
            }
        } catch (\Throwable $e) {
            Log::warning('GeoIP http lookup exception', ['ip' => $ip, 'error' => $e->getMessage()]);
        }

        // Fallback to JSON endpoint
        try {
            $attempted = true;
            $resp = Http::timeout(3)->get("https://ipapi.co/{$ip}/json/");
            if ($resp->ok()) {
                $data = $resp->json();
                if (!empty($data['country_code'])) {
                    $code = strtoupper($data['country_code']);
                    Cache::put($cacheKey, $code, now()->addDays(7));
                    Log::info('GeoIP http json lookup success', ['ip' => $ip, 'country' => $code]);
                    return $code;
                }
                Log::info('GeoIP json returned no country_code', ['ip' => $ip, 'json' => $data]);
            }
        } catch (\Throwable $e) {
            Log::warning('GeoIP http json lookup exception', ['ip' => $ip, 'error' => $e->getMessage()]);
        }

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
