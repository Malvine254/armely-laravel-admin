<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;

class ActivityController extends Controller
{
    /**
     * Get recent activities for the authenticated user
     */
    public function getActivities(Request $request)
    {
        $limit = $request->query('limit', 5);
        
        $activities = Activity::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'type' => $activity->type,
                    'action' => $activity->action,
                    'description' => $activity->description,
                    'metadata' => $activity->metadata,
                    'created_at' => $activity->created_at,
                    'time_ago' => $this->getTimeAgo($activity->created_at),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $activities,
            'count' => count($activities),
        ]);
    }

    /**
     * Convert timestamp to human-readable "time ago" format
     */
    private function getTimeAgo($date)
    {
        $now = now();
        $diff = $now->diff($date);

        if ($diff->days > 0) {
            return $diff->days . ' day' . ($diff->days > 1 ? 's' : '') . ' ago';
        }
        if ($diff->h > 0) {
            return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
        }
        if ($diff->i > 0) {
            return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
        }
        return 'just now';
    }
}
