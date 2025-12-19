<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
}
