<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use Symfony\Component\HttpFoundation\Response;

class AdminApiMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('admin')->check() && !$this->authenticateDirectAdmin($request)) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Admin login required. Use the admin session or send admin credentials with the request.',
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

    private function authenticateDirectAdmin(Request $request): bool
    {
        $credentials = $this->resolveCredentials($request);
        if ($credentials === null) {
            return false;
        }

        $admin = Admin::query()
            ->where('email', $credentials['email'])
            ->first();

        if (!$admin || !Hash::check($credentials['password'], $admin->password)) {
            return false;
        }

        if (!$admin->isActive()) {
            return false;
        }

        Auth::guard('admin')->setUser($admin);

        return true;
    }

    private function resolveCredentials(Request $request): ?array
    {
        $email = trim((string) ($request->header('X-Admin-Email') ?: $request->getUser() ?: ''));
        $password = (string) ($request->header('X-Admin-Password') ?: $request->getPassword() ?: '');

        if ($email === '' || $password === '') {
            return null;
        }

        return [
            'email' => strtolower($email),
            'password' => $password,
        ];
    }
}