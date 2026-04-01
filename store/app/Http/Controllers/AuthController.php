<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Company;
use App\Models\User;
use App\Models\Address;
use App\Services\AzureGraphMailService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    private const ACTIVATION_LINK_EXPIRY_HOURS = 24;

    public function __construct(private AzureGraphMailService $azureGraphMailService)
    {
    }

    private array $publicEmailDomains = [
        'gmail.com',
        'yahoo.com',
        'hotmail.com',
        'outlook.com',
        'icloud.com',
        'aol.com',
        'proton.me',
        'protonmail.com',
    ];

    private function resolveDefaultShippingAddress(?Company $company): ?Address
    {
        if (!$company) {
            return null;
        }

        return $company->addresses()
            ->where('is_active', true)
            ->where('is_default', true)
            ->whereIn('type', ['shipping', 'both'])
            ->first();
    }

    private function strongPasswordRule(): Password
    {
        return Password::min(8)
            ->mixedCase()
            ->numbers()
            ->symbols()
            ->uncompromised();
    }

    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', $this->strongPasswordRule()],
        ]);

        $domain = $this->extractDomain($data['email']);
        if (!$domain || $this->isPublicEmailDomain($domain)) {
            return response()->json([
                'success' => false,
                'message' => 'Please use your company email address',
            ], 422);
        }

        $company = Company::where('domain', $domain)->first();
        $isNewCompany = false;

        if (!$company) {
            $company = Company::create([
                'name' => $data['company_name'],
                'domain' => $domain,
                'status' => 'pending',
            ]);
            $isNewCompany = true;
        }

        $role = $isNewCompany ? 'owner' : 'buyer';

        $user = User::create([
            'name' => $data['full_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'company_id' => $company->id,
            'role' => $role,
            'status' => 'pending',
        ]);

        $activationToken = $this->createActivationToken($user);
        $activationUrl = $this->buildActivationUrl($user->email, $activationToken);
        $this->sendActivationEmail($user, $activationUrl);

        $message = $company->status === 'approved'
            ? 'Registration successful. Please check your email and activate your account before logging in.'
            : 'Registration successful. Please activate your email. Your company access is still pending admin approval.';

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'activation_required' => true,
                'email' => $user->email,
                'company_status' => $company->status,
            ],
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (!$user->email_verified_at) {
            return response()->json([
                'success' => false,
                'message' => 'Please activate your account from the email link before logging in.',
                'data' => [
                    'activation_required' => true,
                    'email' => $user->email,
                ],
            ], 403);
        }

        $company = $user->company;
        if (!$company) {
            return response()->json([
                'success' => false,
                'message' => 'No company is associated with this account.',
            ], 403);
        }

        $restricted = $company->status !== 'approved' || $user->status !== 'active';

        $restrictionReason = null;
        if ($company->status !== 'approved') {
            $restrictionReason = $company->status === 'inactive'
                ? 'company_suspended'
                : 'company_not_approved';
        } elseif ($user->status !== 'active') {
            $restrictionReason = $user->status === 'suspended'
                ? 'user_suspended'
                : 'user_not_active';
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        $message = $restricted
            ? 'Login successful. Your account is suspended or pending approval. Access is restricted.'
            : 'Login successful';

        $shippingAddress = $this->resolveDefaultShippingAddress($company);
        $userPayload = $user->toArray();
        $userPayload['shipping_address'] = $shippingAddress;

        Activity::log($user->id, 'login', 'logged_in', 'Signed in to account');

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'token' => $token,
                'user' => $userPayload,
                'company' => $company,
                'shipping_address' => $shippingAddress,
                'restricted' => $restricted,
                'restriction_reason' => $restrictionReason,
            ],
        ]);
    }

    public function activateAccount(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'token' => ['required', 'string'],
        ]);

        $wantsJson = $request->expectsJson();

        $user = User::where('email', $data['email'])->first();
        if (!$user) {
            if (!$wantsJson) {
                return $this->activationRedirect(false, 'Invalid activation request.');
            }

            return response()->json([
                'success' => false,
                'message' => 'Invalid activation request.',
            ], 422);
        }

        $record = DB::table('email_activation_tokens')
            ->where('email', $data['email'])
            ->first();

        if (!$record) {
            if (!$wantsJson) {
                return $this->activationRedirect(false, 'Activation token is invalid or expired.');
            }

            return response()->json([
                'success' => false,
                'message' => 'Activation token is invalid or expired.',
            ], 422);
        }

        if (now()->diffInHours($record->created_at) > self::ACTIVATION_LINK_EXPIRY_HOURS) {
            DB::table('email_activation_tokens')->where('email', $data['email'])->delete();

            if (!$wantsJson) {
                return $this->activationRedirect(false, 'Activation link has expired. Please request a new one.');
            }

            return response()->json([
                'success' => false,
                'message' => 'Activation link has expired. Please request a new one.',
            ], 422);
        }

        if (!Hash::check($data['token'], $record->token)) {
            if (!$wantsJson) {
                return $this->activationRedirect(false, 'Activation token is invalid.');
            }

            return response()->json([
                'success' => false,
                'message' => 'Activation token is invalid.',
            ], 422);
        }

        $updateData = ['email_verified_at' => now()];
        if ($user->company && $user->company->status === 'approved' && $user->status !== 'suspended') {
            $updateData['status'] = 'active';
        }

        $user->update($updateData);
        DB::table('email_activation_tokens')->where('email', $data['email'])->delete();

        \Log::info('Account activation completed', [
            'email' => $user->email,
            'user_id' => $user->id,
            'email_verified_at' => $user->fresh()->email_verified_at,
            'status' => $user->fresh()->status,
        ]);

        if (!$wantsJson) {
            return $this->activationRedirect(true, 'Account activated successfully. You can now log in.');
        }

        return response()->json([
            'success' => true,
            'message' => 'Account activated successfully. You can now log in.',
            'data' => [
                'email_verified_at' => $user->fresh()->email_verified_at,
            ],
        ]);
    }

    public function resendActivation(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $data['email'])->first();
        if (!$user) {
            return response()->json([
                'success' => true,
                'message' => 'If the account exists, an activation email has been sent.',
            ]);
        }

        if ($user->email_verified_at) {
            return response()->json([
                'success' => true,
                'message' => 'Account is already activated. You can log in.',
            ]);
        }

        $token = $this->createActivationToken($user);
        $activationUrl = $this->buildActivationUrl($user->email, $token);
        $this->sendActivationEmail($user, $activationUrl);

        return response()->json([
            'success' => true,
            'message' => 'If the account exists, an activation email has been sent.',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        $company = $user?->company;
        $shippingAddress = $this->resolveDefaultShippingAddress($company);

        $restricted = !$user || !$company || !$user->email_verified_at || $user->status !== 'active' || $company->status !== 'approved';

        $restrictionReason = null;
        if ($user && $company) {
            if (!$user->email_verified_at) {
                $restrictionReason = 'email_not_verified';
            } elseif ($company->status !== 'approved') {
                $restrictionReason = $company->status === 'inactive'
                    ? 'company_suspended'
                    : 'company_not_approved';
            } elseif ($user->status !== 'active') {
                $restrictionReason = $user->status === 'suspended'
                    ? 'user_suspended'
                    : 'user_not_active';
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'user' => array_merge($user?->toArray() ?? [], [
                    'shipping_address' => $shippingAddress,
                ]),
                'company' => $company,
                'shipping_address' => $shippingAddress,
                'restricted' => $restricted,
                'restriction_reason' => $restrictionReason,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()?->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out',
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();
        $company = $user?->company;

        if (!$user || !$company || $user->status !== 'active' || $company->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Your account is suspended or pending approval. You have read-only access.',
            ], 403);
        }
        
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['sometimes', 'string', 'max:50'],
            'shipping_address' => ['sometimes', 'array'],
            'shipping_address.label' => ['nullable', 'string', 'max:255'],
            'shipping_address.contact_name' => ['nullable', 'string', 'max:255'],
            'shipping_address.contact_phone' => ['nullable', 'string', 'max:50'],
            'shipping_address.street_1' => ['nullable', 'string', 'max:255'],
            'shipping_address.street_2' => ['nullable', 'string', 'max:255'],
            'shipping_address.city' => ['nullable', 'string', 'max:100'],
            'shipping_address.state' => ['nullable', 'string', 'max:50'],
            'shipping_address.postal_code' => ['nullable', 'string', 'max:20'],
            'shipping_address.country' => ['nullable', 'string', 'size:2'],
        ]);

        $shippingAddressPayload = $data['shipping_address'] ?? null;
        unset($data['shipping_address']);

        $user->update($data);

        $shippingAddress = $this->resolveDefaultShippingAddress($company);
        if (is_array($shippingAddressPayload)) {
            $street = trim((string)($shippingAddressPayload['street_1'] ?? ''));
            $city = trim((string)($shippingAddressPayload['city'] ?? ''));
            $state = trim((string)($shippingAddressPayload['state'] ?? ''));
            $postalCode = trim((string)($shippingAddressPayload['postal_code'] ?? ''));

            // Only save/update when required shipping fields are present.
            if ($street !== '' && $city !== '' && $state !== '' && $postalCode !== '') {
                $addressData = [
                    'label' => $shippingAddressPayload['label'] ?? 'Default Shipping',
                    'contact_name' => $shippingAddressPayload['contact_name'] ?? $user->name,
                    'contact_phone' => $shippingAddressPayload['contact_phone'] ?? $user->phone,
                    'street_1' => $street,
                    'street_2' => $shippingAddressPayload['street_2'] ?? null,
                    'city' => $city,
                    'state' => $state,
                    'postal_code' => $postalCode,
                    'country' => strtoupper((string)($shippingAddressPayload['country'] ?? 'US')),
                    'type' => 'shipping',
                    'is_default' => true,
                    'is_active' => true,
                ];

                if ($shippingAddress) {
                    $shippingAddress->update($addressData);
                } else {
                    $shippingAddress = Address::create(array_merge($addressData, [
                        'company_id' => $company->id,
                    ]));
                }

                $company->addresses()
                    ->where('id', '!=', $shippingAddress->id)
                    ->whereIn('type', ['shipping', 'both'])
                    ->update(['is_default' => false]);
            }
        }

        $freshUser = $user->fresh()?->toArray() ?? [];
        $freshUser['shipping_address'] = $this->resolveDefaultShippingAddress($company);

        Activity::log($user->id, 'profile', 'updated', 'Updated account profile');

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'user' => $freshUser,
            'shipping_address' => $freshUser['shipping_address'],
        ]);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $user = $request->user();
        $company = $user?->company;

        if (!$user || !$company || $user->status !== 'active' || $company->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Your account is suspended or pending approval. You have read-only access.',
            ], 403);
        }
        
        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', $this->strongPasswordRule()],
            'new_password_confirmation' => ['required', 'string', 'same:new_password'],
        ]);

        if (!Hash::check($data['current_password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect',
            ], 422);
        }

        $user->update([
            'password' => Hash::make($data['new_password']),
        ]);

        Activity::log($user->id, 'profile', 'password_changed', 'Changed account password');

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully',
        ]);
    }

    /**
     * Send password reset link
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user) {
            // Don't reveal if email exists or not for security
            return response()->json([
                'success' => true,
                'message' => 'If the email exists, a password reset link has been sent',
            ]);
        }

        // Delete any existing tokens for this user
        DB::table('password_reset_tokens')->where('email', $user->email)->delete();

        // Generate reset token
        $token = Str::random(64);

        // Store token in database
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        $frontendBaseUrl = rtrim((string) env('FRONTEND_URL', config('app.url')), '/');
        $resetUrl = $frontendBaseUrl . '/reset-password?token=' . urlencode($token) . '&email=' . urlencode($user->email);

        $sent = $this->azureGraphMailService->sendPasswordResetEmail(
            $user->email,
            $user->name,
            $resetUrl
        );

        if ($sent) {
            return response()->json([
                'success' => true,
                'message' => 'If the email exists, a password reset link has been sent',
            ]);
        }

        // Send email with reset link (when email is configured)
        try {
            Mail::to($user->email)->send(new \App\Mail\PasswordResetMail($user, $token));
        } catch (\Exception $e) {
            \Log::warning('Failed to send password reset email: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'If the email exists, a password reset link has been sent',
        ]);
    }

    /**
     * Reset password using token
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'token' => ['required', 'string'],
            'password' => ['required', 'string', 'confirmed', $this->strongPasswordRule()],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid reset token or email',
            ], 422);
        }

        // Get reset token from database
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $data['email'])
            ->first();

        if (!$resetRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired reset token',
            ], 422);
        }

        // Check if token is valid (not older than 60 minutes)
        if (now()->diffInMinutes($resetRecord->created_at) > 60) {
            DB::table('password_reset_tokens')->where('email', $data['email'])->delete();
            return response()->json([
                'success' => false,
                'message' => 'Reset token has expired',
            ], 422);
        }

        // Verify token
        if (!Hash::check($data['token'], $resetRecord->token)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid reset token',
            ], 422);
        }

        // Update password
        $user->update([
            'password' => Hash::make($data['password']),
        ]);

        // Delete the used token
        DB::table('password_reset_tokens')->where('email', $data['email'])->delete();

        // Revoke all existing tokens
        $user->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password has been reset successfully',
        ]);
    }

    private function extractDomain(string $email): ?string
    {
        $parts = explode('@', $email);
        return $parts[1] ?? null;
    }

    private function isPublicEmailDomain(string $domain): bool
    {
        return in_array(strtolower($domain), $this->publicEmailDomains, true);
    }

    private function createActivationToken(User $user): string
    {
        DB::table('email_activation_tokens')->where('email', $user->email)->delete();

        $plainToken = Str::random(64);
        DB::table('email_activation_tokens')->insert([
            'email' => $user->email,
            'token' => Hash::make($plainToken),
            'created_at' => now(),
        ]);

        return $plainToken;
    }

    private function buildActivationUrl(string $email, string $token): string
    {
        $backendBaseUrl = rtrim((string) config('app.url'), '/');
        return $backendBaseUrl . '/api/v1/auth/activate?token=' . urlencode($token) . '&email=' . urlencode($email);
    }

    private function activationRedirect(bool $success, string $message)
    {
        $frontendBaseUrl = rtrim((string) env('FRONTEND_URL', config('app.url')), '/');
        $query = http_build_query([
            'activation' => $success ? 'success' : 'failed',
            'message' => $message,
            'next' => 'login',
        ]);

        // Use store root to avoid web-server deep-link issues on /store/login.
        return redirect()->to($frontendBaseUrl . '/?' . $query);
    }

    private function sendActivationEmail(User $user, string $activationUrl): void
    {
        $sent = $this->azureGraphMailService->sendActivationEmail($user->email, $user->name, $activationUrl);

        if ($sent) {
            return;
        }

        try {
            Mail::send('emails.auth.account-activation', [
                'user' => $user,
                'activationUrl' => $activationUrl,
                'expiresIn' => self::ACTIVATION_LINK_EXPIRY_HOURS,
            ], function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Activate your Armely Store account');
            });
        } catch (\Throwable $e) {
            \Log::warning('Failed to send activation email: ' . $e->getMessage());
        }
    }
}
