<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class ExpireLegacyTrackingCookies
{
    /**
     * Remove old per-item analytics cookies that can make request headers too large.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        foreach ($request->cookies->keys() as $name) {
            if ($this->isLegacyTrackingCookie($name)) {
                Cookie::queue(Cookie::forget($name));
            }
        }

        return $response;
    }

    private function isLegacyTrackingCookie(string $name): bool
    {
        return str_starts_with($name, 'blog_viewed_')
            || str_starts_with($name, 'resource_click_')
            || str_starts_with($name, 'resource_download_');
    }
}
