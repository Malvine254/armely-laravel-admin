<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show()
    {
        $admin = Auth::guard('admin')->user();

        // Basic activity/history placeholders
        $loginHistory = [
            [
                'device' => 'Chrome - Windows 10',
                'location' => 'Paris, France',
                'ip' => '192.168.1.10',
                'time' => 'Apr 24, 2019'
            ],
            [
                'device' => 'App - Mac OS',
                'location' => 'New York, USA',
                'ip' => '10.0.0.10',
                'time' => 'Apr 24, 2019'
            ],
            [
                'device' => 'Chrome - iOS',
                'location' => 'London, UK',
                'ip' => '255.255.255.0',
                'time' => 'Apr 24, 2019'
            ],
        ];

        return view('admin.profile', compact('admin', 'loginHistory'));
    }

    public function update(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $admin->name = $validated['name'];
        $admin->phone = $validated['phone'] ?? $admin->phone;

        if ($request->filled('password')) {
            $admin->password = Hash::make($validated['password']);
        }

        $admin->save();

        return redirect()->route('admin.profile')->with('success', 'Profile updated successfully');
    }
}
