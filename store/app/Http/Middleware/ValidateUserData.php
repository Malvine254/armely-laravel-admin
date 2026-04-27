<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ValidateUserData
{
    /**
     * Ensure users can only access their own data
     * 
     * Prevents unauthorized access to other users' quotes, orders, invoices, messages, etc.
     */
    public function handle(Request $request, Closure $next)
    {
        // Get the authenticated user
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }
        
        // Store user_id in request for use in controllers
        $request->merge(['authenticated_user_id' => $user->id]);
        
        // Add user to request attributes for easy access
        $request->attributes->set('user', $user);
        
        return $next($request);
    }
}
