<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ActivityLogger
{
    /**
     * Log an admin activity.
     */
    public static function log(string $action, string $entityType, $entityId = null, ?string $description = null): void
    {
        try {
            $admin = Auth::guard('admin')->user();
            DB::table('admin_activities')->insert([
                'admin_id' => $admin?->id,
                'action' => $action,
                'entity_type' => $entityType,
                'entity_id' => is_null($entityId) ? null : (string) $entityId,
                'description' => $description,
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error('ActivityLogger failed: ' . $e->getMessage(), ['exception' => $e]);
        }
    }

    /**
     * Log a user activity (not admin).
     */
    public static function logUser(string $action, string $entityType, $entityId = null, ?string $description = null): void
    {
        try {
            $user = Auth::guard('web')->user();
            DB::table('admin_activities')->insert([
                'admin_id' => $user?->id,
                'action' => $action,
                'entity_type' => 'user_' . $entityType, // Prefix to distinguish from admin
                'entity_id' => is_null($entityId) ? null : (string) $entityId,
                'description' => $description,
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error('ActivityLogger (user) failed: ' . $e->getMessage(), ['exception' => $e]);
        }
    }

    /**
     * Log authentication events (login/logout).
     */
    public static function logAuth(string $action, $user, string $guardType = 'admin'): void
    {
        try {
            $userName = $user->name ?? $user->email ?? 'Unknown';
            $description = "{$userName} {$action}";
            
            DB::table('admin_activities')->insert([
                'admin_id' => $user->id,
                'action' => $action,
                'entity_type' => $guardType,
                'entity_id' => $user->id,
                'description' => $description,
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error('ActivityLogger (auth) failed: ' . $e->getMessage(), ['exception' => $e]);
        }
    }
}
