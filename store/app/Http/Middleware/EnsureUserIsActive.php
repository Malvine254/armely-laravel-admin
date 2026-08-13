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

        $restrictionReason = $this->resolveRestrictionReason($user, $company);
        if ($restrictionReason === null) {
            return $next($request);
        }

        // Always allow logout even for restricted accounts.
        if ($request->is('api/v1/auth/logout')) {
            return $next($request);
        }

        if ($this->isPendingApprovalReason($restrictionReason) && $this->isPendingAllowedRequest($request)) {
            return $next($request);
        }

        if ($this->isHardBlockReason($restrictionReason)) {
            // Revoke token only for hard-block states such as suspension.
            $user->currentAccessToken()?->delete();
        }

        $message = match ($restrictionReason) {
            'email_not_verified' => 'Please activate your account from the email link before performing this action.',
            'company_not_approved', 'user_not_active' => 'Your account is pending approval. This action is unavailable until approval.',
            'company_suspended' => 'Your company account is suspended. Please contact support.',
            'user_suspended' => 'Your user account is suspended. Please contact support.',
            default => 'Access is blocked for this account.',
        };

        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => [
                'restricted' => true,
                'restriction_reason' => $restrictionReason,
            ],
        ], 403);
    }

    private function resolveRestrictionReason($user, $company): ?string
    {
        if (!$user || !$company) {
            return 'account_unavailable';
        }

        if (is_null($user->email_verified_at)) {
            return 'email_not_verified';
        }

        if ($company->status === 'inactive') {
            return 'company_suspended';
        }

        if ($user->status === 'suspended') {
            return 'user_suspended';
        }

        if ($company->status !== 'approved') {
            return 'company_not_approved';
        }

        if ($user->status !== 'active') {
            return 'user_not_active';
        }

        return null;
    }

    private function isHardBlockReason(string $reason): bool
    {
        return in_array($reason, ['account_unavailable', 'company_suspended', 'user_suspended'], true);
    }

    private function isPendingApprovalReason(string $reason): bool
    {
        return in_array($reason, ['email_not_verified', 'company_not_approved', 'user_not_active'], true);
    }

    private function isPendingAllowedRequest(Request $request): bool
    {
        $path = trim((string) $request->path(), '/');

        return str_ends_with($path, 'api/v1/auth/me');
    }
}
