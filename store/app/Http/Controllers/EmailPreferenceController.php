<?php

namespace App\Http\Controllers;

use App\Models\SuppressionEvent;
use App\Services\UserEmailPreferenceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
                'notification_email_enabled' => (bool) $pref->notification_email_enabled,
                'quotes_notifications_enabled' => (bool) $pref->quotes_notifications_enabled,
                'orders_notifications_enabled' => (bool) $pref->orders_notifications_enabled,
                'invoices_notifications_enabled' => (bool) $pref->invoices_notifications_enabled,
                'marketing_enabled' => (bool) $pref->marketing_enabled,
                'ai_marketing_enabled' => (bool) $pref->ai_marketing_enabled,
                'ai_marketing_consent_at' => $pref->ai_marketing_consent_at?->toISOString(),
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
            'notification_email_enabled' => ['sometimes', 'boolean'],
            'quotes_notifications_enabled' => ['sometimes', 'boolean'],
            'orders_notifications_enabled' => ['sometimes', 'boolean'],
            'invoices_notifications_enabled' => ['sometimes', 'boolean'],
            'marketing_enabled' => ['sometimes', 'boolean'],
            'ai_marketing_enabled' => ['sometimes', 'boolean'],
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

        if (array_key_exists('marketing_enabled', $validated) && ! $validated['marketing_enabled']) {
            $validated['ai_marketing_enabled'] = false;
        }

        $pref = $service->ensurePreference($user);
        $wasAiMarketingEnabled = (bool) $pref->ai_marketing_enabled;
        $pref->fill($validated);
        if (array_key_exists('ai_marketing_enabled', $validated)
            && $validated['ai_marketing_enabled']
            && !$wasAiMarketingEnabled) {
            $pref->ai_marketing_consent_at = now();
        }
        $pref->save();

        if ($wasAiMarketingEnabled && ! $pref->ai_marketing_enabled) {
            SuppressionEvent::query()->create([
                'user_id' => (int) $user->id,
                'email' => (string) $user->email,
                'event_type' => 'unsubscribe',
                'channel' => 'email',
                'reason' => 'ai_marketing_opt_out',
                'source' => 'account_preferences',
                'metadata' => ['scope' => 'ai_marketing'],
                'occurred_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'transactional_enabled' => (bool) $pref->transactional_enabled,
                'notification_email_enabled' => (bool) $pref->notification_email_enabled,
                'quotes_notifications_enabled' => (bool) $pref->quotes_notifications_enabled,
                'orders_notifications_enabled' => (bool) $pref->orders_notifications_enabled,
                'invoices_notifications_enabled' => (bool) $pref->invoices_notifications_enabled,
                'marketing_enabled' => (bool) $pref->marketing_enabled,
                'ai_marketing_enabled' => (bool) $pref->ai_marketing_enabled,
                'ai_marketing_consent_at' => $pref->ai_marketing_consent_at?->toISOString(),
                'price_alerts_enabled' => (bool) $pref->price_alerts_enabled,
                'cart_reminders_enabled' => (bool) $pref->cart_reminders_enabled,
                'browse_reminders_enabled' => (bool) $pref->browse_reminders_enabled,
                'timezone' => $pref->timezone,
                'quiet_hours_start' => $pref->quiet_hours_start,
                'quiet_hours_end' => $pref->quiet_hours_end,
            ],
        ]);
    }

    /**
     * Read-only confirmation page. Corporate mail filters (Defender Safe
     * Links, Mimecast, Proofpoint) open every link in a message; applying the
     * unsubscribe on GET silently opted customers out the moment a reminder
     * was delivered.
     */
    public function confirmUnsubscribe(string $token, UserEmailPreferenceService $service): Response
    {
        $scope = $service->peekUnsubscribeScope($token);

        if ($scope === null) {
            return $this->unsubscribePage('Link expired', 'This unsubscribe link is invalid or has expired. You can manage email preferences from your account settings.', false, 404);
        }

        $label = match ($scope) {
            'price_alerts' => 'price-drop alerts',
            'cart_reminders' => 'cart reminders',
            'browse_reminders' => 'viewed and saved item reminders',
            default => 'all marketing emails',
        };

        return $this->unsubscribePage(
            'Unsubscribe',
            "Confirm that you no longer want to receive {$label} from Armely Store. Order, quote and invoice emails are not affected.",
            true
        );
    }

    public function unsubscribe(string $token, Request $request, UserEmailPreferenceService $service): JsonResponse|Response
    {
        $result = $service->applyUnsubscribeToken($token, $request->ip());
        $wantsHtml = !$request->expectsJson() && $request->has('confirm');

        if (!$result) {
            return $wantsHtml
                ? $this->unsubscribePage('Link expired', 'This unsubscribe link is invalid or has expired.', false, 404)
                : response()->json(['success' => false, 'message' => 'Unsubscribe link is invalid or expired.'], 404);
        }

        if ($wantsHtml) {
            return $this->unsubscribePage('You are unsubscribed', 'Your email preferences have been updated. You can turn emails back on at any time from your account settings.');
        }

        return response()->json([
            'success' => true,
            'message' => 'Your email preferences have been updated.',
            'data' => $result,
        ]);
    }

    private function unsubscribePage(string $title, string $message, bool $showForm = false, int $status = 200): Response
    {
        $safeTitle = e($title);
        $safeMessage = e($message);
        // No action attribute: the browser posts back to this exact URL, which
        // keeps the /store prefix when the app runs behind the path bridge.
        $form = $showForm
            ? "<form method='post'><input type='hidden' name='confirm' value='1'><button type='submit'>Confirm unsubscribe</button></form>"
            : '';

        $html = "<!doctype html><html lang='en'><head><meta charset='utf-8'><meta name='viewport' content='width=device-width,initial-scale=1'><meta name='robots' content='noindex'><title>{$safeTitle} · Armely Store</title>"
            . "<style>body{margin:0;font-family:Segoe UI,Arial,sans-serif;background:#f4f7fb;color:#1f2937;display:flex;min-height:100vh;align-items:center;justify-content:center}"
            . "main{background:#fff;border:1px solid #dbe5f3;border-radius:14px;padding:32px;max-width:440px;margin:16px;box-shadow:0 8px 24px rgba(15,42,82,.08)}"
            . "h1{margin:0 0 12px;font-size:22px;color:#1e3a6e}p{margin:0 0 20px;line-height:1.5;color:#4b5563}"
            . "button{background:#2F5597;color:#fff;border:0;border-radius:8px;padding:11px 18px;font-size:15px;font-weight:600;cursor:pointer}</style></head>"
            . "<body><main><h1>{$safeTitle}</h1><p>{$safeMessage}</p>{$form}</main></body></html>";

        return response($html, $status, [
            'Content-Type' => 'text/html; charset=UTF-8',
            'Cache-Control' => 'no-store',
            'X-Robots-Tag' => 'noindex',
        ]);
    }
}
