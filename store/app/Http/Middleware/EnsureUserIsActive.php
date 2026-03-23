<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    /**
    * Put suspended/inactive accounts in read-only mode.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        $company = $user->company;

        $isEmailVerified = !is_null($user->email_verified_at);
        $isUserActive = $user->status === 'active';
        $isCompanyApproved = $company && $company->status === 'approved';

        $isRestricted = !$isEmailVerified || !$isUserActive || !$isCompanyApproved;

        if (!$isRestricted) {
            return $next($request);
        }

        // Allow read-only endpoints so users can still sign in and view account status.
        if ($request->isMethod('GET') || $request->isMethod('HEAD') || $request->isMethod('OPTIONS')) {
            return $next($request);
        }

        // Always allow logout even for restricted accounts.
        if ($request->is('api/v1/auth/logout')) {
            return $next($request);
        }

        $message = !$isEmailVerified
            ? 'Please activate your account from the email link before performing this action.'
            : 'Your account is suspended or pending approval. You have read-only access.';

        return response()->json([
            'success' => false,
            'message' => $message,
        ], 403);
    }
}
