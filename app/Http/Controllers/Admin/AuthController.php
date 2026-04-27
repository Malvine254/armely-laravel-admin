<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Services\AzureMailService;
use Illuminate\Support\Str;
use App\Models\Admin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('admin')->check()) {
            // Keep redirects host-relative to avoid localhost/127.0.0.1 session cookie mismatches.
            return redirect('/admin/dashboard');
        }
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email:rfc,filter',
            'password' => 'required',
        ], [
            'email.required' => 'Email is required',
            'email.email' => 'Please enter a valid email',
            'password.required' => 'Password is required',
        ]);

        Log::debug('Admin login attempt', ['email' => $credentials['email']]);

        try {
            $attemptResult = Auth::guard('admin')->attempt($credentials, $request->filled('remember'));
            Log::debug('Auth attempt result', ['result' => $attemptResult, 'email' => $credentials['email']]);
            
            if ($attemptResult) {
                Log::info('Admin logged in successfully', ['email' => $credentials['email']]);
                $request->session()->regenerate();
                // Always land on admin dashboard to avoid stale intended URLs from store browsing session.
                return redirect('/admin/dashboard');
            }
        } catch (\Exception $e) {
            // Handle bcrypt errors (usually plain text passwords)
            Log::error('Admin login error: ' . $e->getMessage(), ['email' => $credentials['email'], 'trace' => $e->getTraceAsString()]);
            
            // Check if admin exists to give targeted error
            $admin = \App\Models\Admin::where('email', $credentials['email'])->first();
            if ($admin && !Hash::check($credentials['password'], $admin->password)) {
                Log::debug('Password mismatch for existing admin', ['email' => $credentials['email']]);
                return back()->withErrors([
                    'email' => 'The password is incorrect.',
                ])->onlyInput('email');
            }
        }

        Log::warning('Admin login failed', ['email' => $credentials['email']]);
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/admin/login');
    }

    public function showReset()
    {
        return view('admin.auth.reset');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email:rfc,dns,filter|exists:admin,email',
        ], [
            'email.exists' => 'We could not find an admin account with that email address.',
        ]);

        $email = AzureMailService::normalizeEmail((string) $request->email);
        if (!AzureMailService::isDeliverableEmail($email)) {
            return back()->withErrors(['email' => 'Please provide a valid email that can receive messages.'])->withInput();
        }

        // Generate reset token
        $token = Str::random(64);

        // Store token in database (use configured table name)
        $resetTable = config('auth.passwords.users.table', 'password_reset_tokens');
        try {
            DB::table($resetTable)->updateOrInsert(
                ['email' => $email],
                [
                    'token' => Hash::make($token),
                    'created_at' => now()
                ]
            );
        } catch (QueryException $e) {
            // If the underlying table doesn't exist, log and return a helpful error
            Log::error('Password reset storage error: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Password reset is currently unavailable. Please contact the site administrator.']);
        }

        // Send email with reset link
        $resetLink = route('admin.password.reset', ['token' => $token, 'email' => $email]);
        
        $mailer = new AzureMailService();
        $sent = $mailer->sendResetEmail($email, $resetLink);

        if ($sent) {
            return back()->with('success', 'Password reset link has been sent to your email!');
        }

        return back()->withErrors(['email' => 'Failed to send reset email. Please try again.']);
    }

    public function showResetForm(Request $request, $token)
    {
        return view('admin.auth.reset-password', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email:rfc,filter|exists:admin,email',
            'password' => 'required|min:8|confirmed',
        ]);

        // Verify token (use configured table name and handle missing table)
        $resetTable = config('auth.passwords.users.table', 'password_reset_tokens');
        try {
            $passwordReset = DB::table($resetTable)
                ->where('email', $request->email)
                ->first();
        } catch (QueryException $e) {
            Log::error('Password reset lookup error: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Password reset is currently unavailable. Please contact the site administrator.']);
        }

        if (!$passwordReset || !Hash::check($request->token, $passwordReset->token)) {
            return back()->withErrors(['email' => 'Invalid or expired reset token.']);
        }

        // Update password
        $admin = Admin::where('email', $request->email)->first();
        $admin->password = Hash::make($request->password);
        $admin->save();

        // Delete token (best-effort)
        try {
            DB::table($resetTable)->where('email', $request->email)->delete();
        } catch (QueryException $e) {
            // Log and continue
            Log::warning('Failed to delete password reset token: ' . $e->getMessage());
        }

        return redirect()->route('admin.login')->with('success', 'Your password has been reset successfully!');
    }
}
