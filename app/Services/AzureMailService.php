<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class AzureMailService
{
    protected Client $http;

    public function __construct(?Client $http = null)
    {
        $this->http = $http ?? new Client(['timeout' => 15]);
    }

    /**
     * Acquire an app-only access token for Microsoft Graph.
     */
    protected function getAccessToken(): string
    {
        $tenantId = env('AZURE_TENANT_ID');
        $clientId = env('AZURE_CLIENT_ID');
        $clientSecret = env('AZURE_CLIENT_SECRET');

        $tokenUrl = "https://login.microsoftonline.com/{$tenantId}/oauth2/v2.0/token";

        $resp = $this->http->post($tokenUrl, [
            'form_params' => [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'grant_type' => 'client_credentials',
                'scope' => 'https://graph.microsoft.com/.default',
            ],
        ]);

        $data = json_decode((string) $resp->getBody(), true);
        return $data['access_token'] ?? '';
    }

    /**
     * Send an HTML email via Microsoft Graph using application permissions.
     * Requires Mail.Send application permission and a licensed mailbox for FROM address.
     */
    public function sendEmail(string $fromEmail, string $toEmail, string $subject, string $htmlBody, bool $saveToSent = true): bool
    {
        try {
            $token = $this->getAccessToken();
            if (!$token) {
                Log::error('AzureMailService: Failed to obtain access token');
                return false;
            }

            $endpoint = "https://graph.microsoft.com/v1.0/users/" . rawurlencode($fromEmail) . "/sendMail";

            $payload = [
                'message' => [
                    'subject' => $subject,
                    'body' => [
                        'contentType' => 'HTML',
                        'content' => $htmlBody,
                    ],
                    'from' => [
                        'emailAddress' => ['address' => $fromEmail],
                    ],
                    'sender' => [
                        'emailAddress' => ['address' => $fromEmail],
                    ],
                    'toRecipients' => [
                        ['emailAddress' => ['address' => $toEmail]],
                    ],
                ],
                'saveToSentItems' => $saveToSent,
            ];

            $resp = $this->http->post($endpoint, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload,
            ]);

            // Graph returns 202 Accepted on success
            return $resp->getStatusCode() >= 200 && $resp->getStatusCode() < 300;
        } catch (\Throwable $e) {
            Log::error('AzureMailService sendEmail error: ' . $e->getMessage(), ['exception' => $e]);
            return false;
        }
    }

    /**
     * Convenience method for the admin reset email.
     */
    public function sendResetEmail(string $toEmail, string $resetLink): bool
    {
        $fromEmail = env('FROM_EMAIL', env('MAIL_FROM_ADDRESS'));
        $subject = 'Reset Your Armely Admin Password';
        $htmlBody = view('admin.auth.reset-email', ['resetLink' => $resetLink])->render();

        return $this->sendEmail($fromEmail, $toEmail, $subject, $htmlBody);
    }

    /**
     * Send a welcome email to a newly created admin with activation link.
     */
    public function sendWelcomeAdminEmail(string $toEmail, string $activationLink, ?string $name = null): bool
    {
        $fromEmail = env('FROM_EMAIL', env('MAIL_FROM_ADDRESS'));
        $subject = 'Your Armely Admin Access';
        $htmlBody = view('admin.auth.welcome-admin', [
            'activationLink' => $activationLink,
            'loginLink' => route('admin.login'),
            'name' => $name,
        ])->render();

        return $this->sendEmail($fromEmail, $toEmail, $subject, $htmlBody);
    }
}
