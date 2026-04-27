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

        // Always allow logout even for restricted accounts.
        if ($request->is('api/v1/auth/logout')) {
            return $next($request);
        }

        // Revoke the current access token so restricted users immediately lose API access.
        $user->currentAccessToken()?->delete();

        $message = !$isEmailVerified
            ? 'Please activate your account from the email link before performing this action.'
            : 'Your account is suspended or pending approval. Access is blocked.';

        return response()->json([
            'success' => false,
            'message' => $message,
        ], 403);
    }
}
