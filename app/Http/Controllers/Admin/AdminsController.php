<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

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
        
        Admin::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'status' => $validated['status'],
            'joined_date' => now(),
        ]);
        
        return redirect()->route('admin.admins')->with('success', 'Admin created successfully');
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
        
        return redirect()->route('admin.admins')->with('success', 'Admin deleted successfully');
    }
}
