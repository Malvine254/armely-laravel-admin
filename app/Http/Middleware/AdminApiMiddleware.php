<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminApiMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('admin')->check()) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Admin login required.',
            ], 401);
        }

        $admin = Auth::guard('admin')->user();
        if (!$admin || !$admin->isActive()) {
            Auth::guard('admin')->logout();

            return new JsonResponse([
                'success' => false,
                'message' => 'Your admin account is not active.',
            ], 403);
        }

        return $next($request);
    }
}