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
}
