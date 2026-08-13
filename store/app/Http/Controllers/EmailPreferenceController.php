<?php

namespace App\Http\Controllers;

use App\Services\UserEmailPreferenceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmailPreferenceController extends Controller
{
    public function show(Request $request, UserEmailPreferenceService $service): JsonResponse
    {
        $user = $request->user('sanctum');
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $pref = $service->ensurePreference($user);

        return response()->json([
            'success' => true,
            'data' => [
                'transactional_enabled' => (bool) $pref->transactional_enabled,
                'marketing_enabled' => (bool) $pref->marketing_enabled,
                'price_alerts_enabled' => (bool) $pref->price_alerts_enabled,
                'cart_reminders_enabled' => (bool) $pref->cart_reminders_enabled,
                'browse_reminders_enabled' => (bool) $pref->browse_reminders_enabled,
                'timezone' => $pref->timezone,
                'quiet_hours_start' => $pref->quiet_hours_start,
                'quiet_hours_end' => $pref->quiet_hours_end,
            ],
        ]);
    }

    public function update(Request $request, UserEmailPreferenceService $service): JsonResponse
    {
        $user = $request->user('sanctum');
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'transactional_enabled' => ['sometimes', 'boolean'],
            'marketing_enabled' => ['sometimes', 'boolean'],
            'price_alerts_enabled' => ['sometimes', 'boolean'],
            'cart_reminders_enabled' => ['sometimes', 'boolean'],
            'browse_reminders_enabled' => ['sometimes', 'boolean'],
            'timezone' => ['sometimes', 'nullable', 'string', 'max:64'],
            'quiet_hours_start' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:1439'],
            'quiet_hours_end' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:1439'],
        ]);

        if (array_key_exists('timezone', $validated)) {
            $tz = $validated['timezone'];
            if ($tz !== null && !in_array((string) $tz, timezone_identifiers_list(), true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid timezone provided.',
                ], 422);
            }
        }

        $pref = $service->ensurePreference($user);
        $pref->fill($validated);
        $pref->save();

        return response()->json([
            'success' => true,
            'data' => [
                'transactional_enabled' => (bool) $pref->transactional_enabled,
                'marketing_enabled' => (bool) $pref->marketing_enabled,
                'price_alerts_enabled' => (bool) $pref->price_alerts_enabled,
                'cart_reminders_enabled' => (bool) $pref->cart_reminders_enabled,
                'browse_reminders_enabled' => (bool) $pref->browse_reminders_enabled,
                'timezone' => $pref->timezone,
                'quiet_hours_start' => $pref->quiet_hours_start,
                'quiet_hours_end' => $pref->quiet_hours_end,
            ],
        ]);
    }

    public function unsubscribe(string $token, Request $request, UserEmailPreferenceService $service): JsonResponse
    {
        $result = $service->applyUnsubscribeToken($token, $request->ip());

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'Unsubscribe link is invalid or expired.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Your email preferences have been updated.',
            'data' => $result,
        ]);
    }
}
