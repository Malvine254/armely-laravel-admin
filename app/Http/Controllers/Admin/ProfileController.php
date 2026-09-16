<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        $admin = Auth::guard('admin')->user();

        // Get real activity logs from admin_activities table
        $activityHistory = DB::table('admin_activities')
            ->where('admin_id', $admin->id)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->map(function($item) {
                return [
                    'type' => $item->action,
                    'entity_type' => $item->entity_type,
                    'description' => $item->description,
                    'timestamp' => $item->created_at
                ];
            })
            ->toArray();

        // Get page visit history from admin_activities
        $pageVisits = DB::table('admin_activities')
            ->where('admin_id', $admin->id)
            ->where('action', 'page_visit')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function($item) {
                return [
                    'page' => $item->description,
                    'timestamp' => $item->created_at
                ];
            })
            ->toArray();

        // Get login/logout history
        $loginHistory = DB::table('admin_activities')
            ->where('admin_id', $admin->id)
            ->where('entity_type', 'admin')
            ->whereIn('action', ['login', 'logout'])
            ->orderBy('created_at', 'desc')
            ->limit(15)
            ->get()
            ->map(function($item) {
                return [
                    'action' => ucfirst($item->action),
                    'timestamp' => $item->created_at,
                    'entity_type' => $item->entity_type,
                    'ip_address' => $item->ip_address ?? null,
                    'user_agent' => $item->user_agent ?? null,
                    ...\App\Support\LoginDevice::describe($item->user_agent ?? null),
                ];
            })
            ->toArray();

        return view('admin.profile', compact('admin', 'loginHistory', 'activityHistory', 'pageVisits'));
    }

    public function photo()
    {
        $path = Auth::guard('admin')->user()->profile_photo_path;
        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->response($path, null, [
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048|dimensions:max_width=4096,max_height=4096',
        ]);

        $admin = Auth::guard('admin')->user();
        $previous = $admin->profile_photo_path;
        $path = $request->file('photo')->store('admin-photos', 'local');
        if (!$path) {
            return back()->withErrors(['photo' => 'The photo could not be saved. Please try again.']);
        }

        try {
            $admin->profile_photo_path = $path;
            $admin->save();
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($path);
            throw $exception;
        }

        if ($previous && str_starts_with($previous, 'admin-photos/')) {
            Storage::disk('local')->delete($previous);
        }

        return redirect()->route('admin.profile')->with('success', 'Profile photo updated successfully.');
    }

    public function update(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => ['nullable', 'string', 'max:50', 'regex:/^\+?[0-9][0-9\s().-]{6,19}$/'],
            'password' => 'nullable|string|min:8|confirmed',
        ], [
            'phone.regex' => 'Please enter a valid phone number.',
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
