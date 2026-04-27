<?php

namespace App\Http\Controllers;

use App\Models\DataReadinessLead;
use App\Services\AzureMailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Torann\GeoIP\Facades\GeoIP;

class DataReadinessLeadController extends Controller
{
    /**
     * Submit assessment results from the Data Readiness pop-up.
     */
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'first' => 'required|string|max:255',
            'last' => 'nullable|string|max:255',
            'email' => 'required|email:rfc,dns,filter|max:255',
            'company' => 'nullable|string|max:255',
            'role' => 'nullable|string|max:255',
            'score' => 'required|integer',
            'pScores' => 'required|array',
        ]);

        $validated['email'] = AzureMailService::normalizeEmail((string) $validated['email']);
        if (!AzureMailService::isDeliverableEmail($validated['email'])) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide a valid email that can receive messages.',
            ], 422);
        }

        try {
            // Get GeoIP location details
            $location = GeoIP::getLocation($request->ip());
            
            $lead = DataReadinessLead::create([
                'first_name' => $validated['first'],
                'last_name' => $validated['last'],
                'email' => $validated['email'],
                'company' => $validated['company'],
                'role' => $validated['role'],
                'overall_score' => $validated['score'],
                'dimension_scores' => $validated['pScores'],
                'ip_address' => $request->ip(),
                'country' => $location->country ?? 'Unknown',
                'city' => $location->city ?? 'Unknown',
            ]);

            // Save also to consultation table for general record keeping (if table exists)
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('consultation')) {
                    \Illuminate\Support\Facades\DB::table('consultation')->insert([
                        'name' => $validated['first'] . ' ' . ($validated['last'] ?? ''),
                        'email' => $validated['email'],
                        'organization' => $validated['company'],
                        'message' => 'AI Data Readiness Assessment completed. Score: ' . $validated['score'] . '/360. Role: ' . $validated['role'],
                        'created_at' => now(),
                    ]);
                }
            } catch (\Throwable $e) {
                Log::warning('Data Readiness: Failed to save to consultation table: ' . $e->getMessage());
            }

            // Notify admin
            Log::info('Data Readiness: Attempting to notify admin for lead', ['lead_id' => $lead->id]);
            $this->notifyAdmin($lead);

            // Notify User with the full report
            $this->notifyUser($lead);

            return response()->json([
                'success' => true,
                'message' => 'Assessment submitted successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('Data Readiness Submission error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during submission.',
            ], 500);
        }
    }

    /**
     * Send email notification to admin via Microsoft Graph.
     */
    protected function notifyAdmin(DataReadinessLead $lead)
    {
        $tenantId = env('AZURE_TENANT_ID');
        $clientId = env('AZURE_CLIENT_ID');
        $clientSecret = env('AZURE_CLIENT_SECRET');
        $fromEmail = AzureMailService::outboundFromEmail();
        $adminEmail = env('ADMIN_EMAIL', $fromEmail);
        $replyTo = AzureMailService::graphReplyToRecipients();

        if (!$tenantId || !$clientId || !$clientSecret || !$fromEmail) {
            Log::warning('Data Readiness Admin Notification: missing env configuration.');
            return;
        }

        if (!AzureMailService::isDeliverableEmail((string) $adminEmail)) {
            Log::warning('Data Readiness Admin Notification: undeliverable admin email, send skipped', [
                'email' => $adminEmail,
            ]);
            return;
        }

        try {
            $tokenResponse = Http::asForm()->post("https://login.microsoftonline.com/{$tenantId}/oauth2/v2.0/token", [
                'client_id' => $clientId,
                'scope' => 'https://graph.microsoft.com/.default',
                'client_secret' => $clientSecret,
                'grant_type' => 'client_credentials',
            ]);

            if (!$tokenResponse->ok()) {
                Log::error('Data Readiness Admin Notification Graph token request failed', ['response' => $tokenResponse->body()]);
                return;
            }

            $accessToken = $tokenResponse->json('access_token');
            if (!$accessToken) {
                Log::error('Data Readiness Admin Notification: No access token in response');
                return;
            }

            Log::info('Data Readiness: Token acquired, preparing email payload');

            $dimensions = $this->buildDimensionBreakdown($lead);
            $scorePercent = (int) round(($lead->overall_score / 360) * 100);

            $adminBody = view('emails.data-readiness.admin-notification', [
                'fullName' => trim($lead->first_name . ' ' . $lead->last_name),
                'email' => $lead->email,
                'company' => $lead->company,
                'role' => $lead->role,
                'overallScore' => $lead->overall_score,
                'scorePercent' => $scorePercent,
                'dimensions' => $dimensions,
                'ipAddress' => $lead->ip_address,
                'city' => $lead->city,
                'country' => $lead->country,
                'submittedAt' => optional($lead->created_at)->toDateTimeString(),
            ])->render();

            $payload = [
                'message' => [
                    'subject' => "New Assessment: AI Readiness ({$lead->first_name} - {$lead->overall_score} pts)",
                    'body' => [
                        'contentType' => 'HTML',
                        'content' => $adminBody,
                    ],
                    'toRecipients' => [
                        ['emailAddress' => ['address' => $adminEmail]],
                    ],
                    'ccRecipients' => [
                        ['emailAddress' => ['address' => 'ask.me@armely.com']],
                        ['emailAddress' => ['address' => 'sales@armely.com']],
                    ],
                ],
                'saveToSentItems' => true,
            ];

            if ($replyTo !== []) {
                $payload['message']['replyTo'] = $replyTo;
            }

            $response = Http::withToken($accessToken)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("https://graph.microsoft.com/v1.0/users/{$fromEmail}/sendMail", $payload);

            if ($response->ok()) {
                Log::info('Data Readiness: Admin notification sent successfully via Graph API');
            } else {
                Log::error('Data Readiness: Graph API sendMail failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
            }

        } catch (\Throwable $e) {
            Log::error('Data Readiness Graph email send failed', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Send assessment report to the user via Microsoft Graph.
     */
    protected function notifyUser(DataReadinessLead $lead)
    {
        $tenantId = env('AZURE_TENANT_ID');
        $clientId = env('AZURE_CLIENT_ID');
        $clientSecret = env('AZURE_CLIENT_SECRET');
        $fromEmail = AzureMailService::outboundFromEmail();
        $replyTo = AzureMailService::graphReplyToRecipients();

        if (!$tenantId || !$clientId || !$clientSecret || !$fromEmail) {
            return;
        }

        try {
            $tokenResponse = Http::asForm()->post("https://login.microsoftonline.com/{$tenantId}/oauth2/v2.0/token", [
                'client_id' => $clientId,
                'scope' => 'https://graph.microsoft.com/.default',
                'client_secret' => $clientSecret,
                'grant_type' => 'client_credentials',
            ]);

            if (!$tokenResponse->ok()) return;
            $accessToken = $tokenResponse->json('access_token');

            $dimensions = $this->buildDimensionBreakdown($lead);
            $scorePercent = (int) round(($lead->overall_score / 360) * 100);
            $tier = $this->resolveTier($scorePercent);

            $emailBody = view('emails.data-readiness.user-report', [
                'firstName' => $lead->first_name,
                'overallScore' => $lead->overall_score,
                'scorePercent' => $scorePercent,
                'tier' => $tier,
                'dimensions' => $dimensions,
                'contactUrl' => rtrim((string) config('app.url'), '/') . '/contact',
            ])->render();

            $payload = [
                'message' => [
                    'subject' => "Your AI Data Readiness Report - Armely",
                    'body' => ['contentType' => 'HTML', 'content' => $emailBody],
                    'toRecipients' => [['emailAddress' => ['address' => $lead->email]]],
                ],
                'saveToSentItems' => true,
            ];

            if ($replyTo !== []) {
                $payload['message']['replyTo'] = $replyTo;
            }

            if (AzureMailService::isDeliverableEmail((string) $lead->email)) {
                Http::withToken($accessToken)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post("https://graph.microsoft.com/v1.0/users/{$fromEmail}/sendMail", $payload);

                Log::info('Data Readiness: User report sent successfully');
            } else {
                Log::warning('Data Readiness: user report email skipped due to undeliverable address', [
                    'email' => $lead->email,
                ]);
            }

        } catch (\Throwable $e) {
            Log::error('Data Readiness User Report failed', ['error' => $e->getMessage()]);
        }
    }

    private function buildDimensionBreakdown(DataReadinessLead $lead): array
    {
        $labels = ['Data Collection', 'Data Quality', 'Infrastructure', 'Governance', 'AI Readiness'];
        $maxScores = [6, 6, 6, 6, 12];

        return collect((array) $lead->dimension_scores)
            ->map(function ($scoreValue, $idx) use ($labels, $maxScores) {
                $maxPossible = $maxScores[$idx] ?? 1;
                $score = (int) $scoreValue;
                $percent = (int) round(($score / $maxPossible) * 100);

                return [
                    'label' => $labels[$idx] ?? ('Phase ' . ($idx + 1)),
                    'score' => $score,
                    'max' => $maxPossible,
                    'percent' => $percent,
                ];
            })
            ->values()
            ->all();
    }

    private function resolveTier(int $scorePercent): array
    {
        if ($scorePercent >= 75) {
            return [
                'label' => 'AI Vanguard',
                'summary' => 'Your data foundations are strong and ready for advanced AI execution.',
            ];
        }

        if ($scorePercent >= 50) {
            return [
                'label' => 'AI Building',
                'summary' => 'You have a workable base and a clear path to production-ready AI maturity.',
            ];
        }

        return [
            'label' => 'Getting Ready',
            'summary' => 'A focused foundation sprint will unlock stronger AI outcomes and lower delivery risk.',
        ];
    }
}
