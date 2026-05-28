<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RequestPerformanceLogger
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $start = microtime(true);
        $method = $request->method();
        $url = $request->fullUrl();

        // If PHP fatals on max_execution_time, capture route context for debugging.
        register_shutdown_function(function () use ($start, $method, $url) {
            $error = error_get_last();
            if (!$error) {
                return;
            }

            $message = strtolower((string) ($error['message'] ?? ''));
            if (str_contains($message, 'maximum execution time')) {
                Log::error('Request timeout detected', [
                    'method' => $method,
                    'url' => $url,
                    'duration_ms' => (int) ((microtime(true) - $start) * 1000),
                    'fatal' => $error,
                ]);
            }
        });

        $response = $next($request);

        $durationMs = (int) ((microtime(true) - $start) * 1000);
        $slowThresholdMs = (int) env('SLOW_REQUEST_MS', 4000);

        if ($durationMs >= $slowThresholdMs) {
            Log::warning('Slow request', [
                'method' => $method,
                'url' => $url,
                'status' => $response->getStatusCode(),
                'duration_ms' => $durationMs,
                'peak_memory_mb' => round(memory_get_peak_usage(true) / 1048576, 2),
            ]);
        }

        return $response;
    }
}
