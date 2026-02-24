<?php

namespace App\Http\Controllers;

use App\Models\DataReadinessLead;
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
            'email' => 'required|email|max:255',
            'company' => 'nullable|string|max:255',
            'role' => 'nullable|string|max:255',
            'score' => 'required|integer',
            'pScores' => 'required|array',
        ]);

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
        $fromEmail = env('FROM_EMAIL');
        $adminEmail = env('ADMIN_EMAIL', $fromEmail);

        if (!$tenantId || !$clientId || !$clientSecret || !$fromEmail) {
            Log::warning('Data Readiness Admin Notification: missing env configuration.');
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

            $dimensions = ["Data Collection", "Data Quality", "Infrastructure", "Governance", "AI Readiness"];
            $maxScores = [6, 6, 6, 6, 12]; // Max units per dimension (JS state)
            $dimensionRows = "";
            foreach ($lead->dimension_scores as $idx => $scoreValue) {
                $maxPossible = $maxScores[$idx] ?? 1;
                $pct = round(($scoreValue / $maxPossible) * 100);
                $label = $dimensions[$idx] ?? "Phase " . ($idx + 1);
                $dimensionRows .= "<li><b>{$label}:</b> {$pct}% ({$scoreValue})</li>";
            }

            $adminBody = "
                <h2>New AI Data Readiness Assessment</h2>
                <p><b>Name:</b> {$lead->first_name} {$lead->last_name}</p>
                <p><b>Email:</b> {$lead->email}</p>
                <p><b>Company:</b> {$lead->company}</p>
                <p><b>Role:</b> {$lead->role}</p>
                <hr>
                <p><b>Overall Score:</b> {$lead->overall_score} / 360</p>
                <ul>{$dimensionRows}</ul>
                <hr>
                <p><b>IP Address:</b> {$lead->ip_address}</p>
                <p><b>Location:</b> {$lead->city}, {$lead->country}</p>
                <p><b>Submitted at:</b> {$lead->created_at->toDateTimeString()}</p>
            ";

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
        $fromEmail = env('FROM_EMAIL');

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

            $dimensions = ["Data Collection", "Data Quality", "Infrastructure", "Governance", "AI Readiness"];
            $maxScores = [6, 6, 6, 6, 12];
            $pct = round(($lead->overall_score / 360) * 100);
            
            $tier = $pct >= 75 ? ['l' => 'AI Vanguard', 's' => "Your data foundations are world-class. Armely can help you sharpen your models for maximum ROI."]
                  : ($pct >= 50 ? ['l' => 'AI Building', 's' => "You're on the right track. A few focused engineering sprints will get you ready for production AI."]
                  : ['l' => 'Getting Ready', 's' => "The right foundations now will pay off massively. Our strategy team can help bridge these gaps."]);

            $dimensionCards = "";
            foreach ($lead->dimension_scores as $idx => $scoreValue) {
                $maxPossible = $maxScores[$idx] ?? 1;
                $p = round(($scoreValue / $maxPossible) * 100);
                $label = $dimensions[$idx] ?? "Phase " . ($idx + 1);
                $dimensionCards .= "
                    <div style='background:#f8f9fa; border:1px solid #eeeeee; border-radius:12px; padding:15px; margin-bottom:10px;'>
                        <div style='display:flex; justify-content:space-between; margin-bottom:5px;'>
                            <span style='font-weight:600; font-size:13px;'>{$label}</span>
                            <span style='font-weight:700; color:#1E62AD;'>{$p}%</span>
                        </div>
                        <div style='height:6px; background:#e9ecef; border-radius:10px; overflow:hidden;'>
                            <div style='height:100%; background:#1E62AD; width:{$p}%;'></div>
                        </div>
                    </div>";
            }

            $emailBody = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; border: 1px solid #e2e8f0; border-radius: 20px; overflow: hidden; color: #0d1f3c;'>
                    <div style='background: #f4f8fd; text-align: center; padding: 25px;'>
                        <img src='https://armely.com/images/logo/logo-replace.png' alt='Armely' style='max-height: 40px;'>
                    </div>
                    <div style='padding: 30px;'>
                        <h2 style='text-align: center; color: #1E62AD; font-size: 24px;'>Your AI Data Readiness Report</h2>
                        <p>Hi {$lead->first_name},</p>
                        <p>Thank you for completing the Armely AI Data Readiness Assessment. Here is your current profile ranking:</p>
                        
                        <div style='text-align: center; margin: 30px 0;'>
                            <div style='font-size: 48px; font-weight: 800; color: #1E62AD; line-height: 1;'>{$pct}%</div>
                            <div style='display: inline-block; padding: 5px 15px; border-radius: 50px; background: #eef4fb; color: #1E62AD; font-weight: 700; margin-top: 10px;'>{$tier['l']}</div>
                        </div>

                        <p style='text-align: center; font-style: italic; color: #64748b; margin-bottom: 30px;'>\"{$tier['s']}\"</p>
                        
                        <h3 style='font-size: 16px; border-bottom: 1px solid #eeeeee; padding-bottom: 8px; margin-bottom: 15px;'>Your Dimension Breakdown</h3>
                        {$dimensionCards}

                        <div style='margin-top: 40px; background: linear-gradient(135deg, #1E62AD, #0891b2); padding: 30px; border-radius: 15px; text-align: center; color: #ffffff;'>
                            <h3 style='margin-top: 0;'>Ready to Build Your Roadmap?</h3>
                            <p>Let's turn these scores into a production-ready AI strategy.</p>
                            <a href='https://armely.com/contact' style='display: inline-block; background: #ffffff; color: #1E62AD; padding: 12px 25px; border-radius: 8px; font-weight: 700; text-decoration: none; margin-top: 10px;'>Book a Strategy Session</a>
                        </div>
                        
                        <p style='margin-top: 30px; font-size: 12px; color: #94a3b8; text-align: center;'>&copy; " . date('Y') . " Armely. All rights reserved.</p>
                    </div>
                </div>
            ";

            $payload = [
                'message' => [
                    'subject' => "Your AI Data Readiness Report - Armely",
                    'body' => ['contentType' => 'HTML', 'content' => $emailBody],
                    'toRecipients' => [['emailAddress' => ['address' => $lead->email]]],
                ],
                'saveToSentItems' => true,
            ];

            Http::withToken($accessToken)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("https://graph.microsoft.com/v1.0/users/{$fromEmail}/sendMail", $payload);

            Log::info('Data Readiness: User report sent successfully');

        } catch (\Throwable $e) {
            Log::error('Data Readiness User Report failed', ['error' => $e->getMessage()]);
        }
    }
}
