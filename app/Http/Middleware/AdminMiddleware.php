<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('admin')->check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your admin session has expired. Please sign in again and retry.',
                ], 401);
            }

            return redirect('/admin/login')->with('error', 'Please login to access the admin panel.');
        }

        $admin = Auth::guard('admin')->user();

        if (!$admin->isActive()) {
            Auth::guard('admin')->logout();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your admin account is not active.',
                ], 403);
            }

            return redirect('/admin/login')->with('error', 'Your account is not active.');
        }

        return $next($request);
    }
}
