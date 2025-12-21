<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\AzureMailService;
use App\Services\ActivityLogger;

class AdminsController extends Controller
{
    public function index()
    {
        $admins = Admin::orderBy('joined_date', 'desc')->get();
        
        $stats = [
            'total' => Admin::count(),
            'active' => Admin::where('status', 'active')->count(),
            'super_admins' => Admin::where('role', 'Super Admin')->count(),
            'inactive' => Admin::where('status', 'inactive')->count(),
        ];
        
        return view('admin.admins', compact('admins', 'stats'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admin,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:Super Admin,Admin',
            'status' => 'required|in:active,inactive',
        ]);
        
        $created = Admin::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'status' => $validated['status'],
            'joined_date' => now(),
        ]);
        ActivityLogger::log('create', 'Admin', $created->id, 'Created admin ' . $created->email . ' (' . $created->name . ')');
        // Generate activation (set-password) token and send welcome email
        try {
            $token = Str::random(64);
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $validated['email']],
                [
                    'token' => Hash::make($token),
                    'created_at' => now()
                ]
            );

            $activationLink = route('admin.password.reset', ['token' => $token, 'email' => $validated['email']]);
            $mailer = new AzureMailService();
            // Prefer a tailored welcome email when available; fallback to reset template
            if (method_exists($mailer, 'sendWelcomeAdminEmail')) {
                $sent = $mailer->sendWelcomeAdminEmail($validated['email'], $activationLink, $validated['name']);
            } else {
                $sent = $mailer->sendResetEmail($validated['email'], $activationLink);
            }

            $msg = $sent
                ? 'Admin created. Activation email sent.'
                : 'Admin created, but failed to send activation email.';
            return redirect()->route('admin.admins')->with($sent ? 'success' : 'error', $msg);
        } catch (\Throwable $e) {
            \Log::error('Failed to send admin activation email: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->route('admin.admins')->with('error', 'Admin created, but failed to send activation email.');
        }
    }
    
    public function update(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admin,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:Super Admin,Admin',
            'status' => 'required|in:active,inactive',
        ]);
        
        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'status' => $validated['status'],
        ];
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($validated['password']);
        }
        
        $admin->update($data);
        ActivityLogger::log('update', 'Admin', $admin->id, 'Updated admin ' . $admin->email);
        
        return redirect()->route('admin.admins')->with('success', 'Admin updated successfully');
    }
    
    public function destroy($id)
    {
        $admin = Admin::findOrFail($id);
        
        // Prevent deleting the last super admin
        if ($admin->isSuperAdmin()) {
            $superAdminCount = Admin::where('role', 'Super Admin')->count();
            if ($superAdminCount <= 1) {
                return redirect()->route('admin.admins')->with('error', 'Cannot delete the last Super Admin');
            }
        }
        
        $admin->delete();
        ActivityLogger::log('delete', 'Admin', $admin->id, 'Deleted admin ' . $admin->email);
        
        return redirect()->route('admin.admins')->with('success', 'Admin deleted successfully');
    }
}
