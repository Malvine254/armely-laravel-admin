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

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Email is required',
            'email.email' => 'Please enter a valid email',
            'password.required' => 'Password is required',
        ]);

        try {
            if (Auth::guard('admin')->attempt($credentials, $request->filled('remember'))) {
                $request->session()->regenerate();
                return redirect()->intended(route('admin.dashboard'));
            }
        } catch (\Exception $e) {
            // Handle bcrypt errors (usually plain text passwords)
            \Log::error('Admin login error: ' . $e->getMessage());
            
            // Check if admin exists to give targeted error
            $admin = \App\Models\Admin::where('email', $credentials['email'])->first();
            if ($admin && !Hash::check($credentials['password'], $admin->password)) {
                return back()->withErrors([
                    'email' => 'The password is incorrect.',
                ])->onlyInput('email');
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('admin.login');
    }

    public function showReset()
    {
        return view('admin.auth.reset');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:admin,email',
        ], [
            'email.exists' => 'We could not find an admin account with that email address.',
        ]);

        // Generate reset token
        $token = Str::random(64);
        
        // Store token in database
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($token),
                'created_at' => now()
            ]
        );

        // Send email with reset link
        $resetLink = route('admin.password.reset', ['token' => $token, 'email' => $request->email]);
        
        $mailer = new AzureMailService();
        $sent = $mailer->sendResetEmail($request->email, $resetLink);

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
            'email' => 'required|email|exists:admin,email',
            'password' => 'required|min:8|confirmed',
        ]);

        // Verify token
        $passwordReset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$passwordReset || !Hash::check($request->token, $passwordReset->token)) {
            return back()->withErrors(['email' => 'Invalid or expired reset token.']);
        }

        // Update password
        $admin = Admin::where('email', $request->email)->first();
        $admin->password = Hash::make($request->password);
        $admin->save();

        // Delete token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('admin.login')->with('success', 'Your password has been reset successfully!');
    }
}
