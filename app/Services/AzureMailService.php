<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
            $resolvedFromEmail = self::normalizeEmail($fromEmail !== '' ? $fromEmail : self::outboundFromEmail());
            $normalizedToEmail = self::normalizeEmail($toEmail);
            if (!self::isDeliverableEmail($normalizedToEmail)) {
                Log::warning('AzureMailService: blocked send to undeliverable recipient', [
                    'to' => $normalizedToEmail,
                    'subject' => $subject,
                ]);
                return false;
            }

            if ($resolvedFromEmail === '') {
                Log::warning('AzureMailService: missing outbound sender address');
                return false;
            }

            $token = $this->getAccessToken();
            if (!$token) {
                Log::error('AzureMailService: Failed to obtain access token');
                return false;
            }

            $endpoint = "https://graph.microsoft.com/v1.0/users/" . rawurlencode($resolvedFromEmail) . "/sendMail";

            $payload = [
                'message' => [
                    'subject' => $subject,
                    'body' => [
                        'contentType' => 'HTML',
                        'content' => $htmlBody,
                    ],
                    'from' => [
                        'emailAddress' => ['address' => $resolvedFromEmail],
                    ],
                    'sender' => [
                        'emailAddress' => ['address' => $resolvedFromEmail],
                    ],
                    'toRecipients' => [
                        ['emailAddress' => ['address' => $normalizedToEmail]],
                    ],
                ],
                'saveToSentItems' => $saveToSent,
            ];

            $replyTo = self::graphReplyToRecipients();
            if ($replyTo !== []) {
                $payload['message']['replyTo'] = $replyTo;
            }

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
            self::markEmailAsSuppressed($toEmail);
            Log::error('AzureMailService sendEmail error: ' . $e->getMessage(), ['exception' => $e]);
            return false;
        }
    }

    public static function normalizeEmail(string $email): string
    {
        return strtolower(trim($email));
    }

    public static function outboundFromEmail(): string
    {
        return self::normalizeEmail((string) (env('NO_REPLY_EMAIL') ?: env('FROM_EMAIL', env('MAIL_FROM_ADDRESS', ''))));
    }

    public static function replyToEmail(): ?string
    {
        $replyTo = self::normalizeEmail((string) env('REPLY_TO_EMAIL', ''));

        return $replyTo !== '' ? $replyTo : null;
    }

    public static function graphReplyToRecipients(): array
    {
        $replyTo = self::replyToEmail();

        return $replyTo !== null
            ? [['emailAddress' => ['address' => $replyTo]]]
            : [];
    }

    public static function isDeliverableEmail(string $email): bool
    {
        $email = self::normalizeEmail($email);
        if ($email === '') {
            return false;
        }

        if (Cache::get(self::suppressionKey($email), false) === true) {
            return false;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        if (!str_contains($email, '@')) {
            return false;
        }

        $domain = (string) Str::after($email, '@');
        if ($domain === '') {
            return false;
        }

        $disposableDomains = [
            'mailinator.com',
            'tempmail.com',
            'guerrillamail.com',
            '10minutemail.com',
            'yopmail.com',
            'trashmail.com',
            'sharklasers.com',
            'dispostable.com',
        ];

        if (in_array($domain, $disposableDomains, true)) {
            return false;
        }

        try {
            if (function_exists('checkdnsrr') && !checkdnsrr($domain, 'MX') && !checkdnsrr($domain, 'A')) {
                return false;
            }
        } catch (\Throwable) {
            // DNS lookups can fail on restricted hosting — allow the email through
        }

        return true;
    }

    public static function markEmailAsSuppressed(string $email, int $hours = 24): void
    {
        $normalized = self::normalizeEmail($email);
        if ($normalized === '') {
            return;
        }

        Cache::put(self::suppressionKey($normalized), true, now()->addHours($hours));
    }

    private static function suppressionKey(string $email): string
    {
        return 'mail_suppressed:' . sha1(self::normalizeEmail($email));
    }

    /**
     * Convenience method for the admin reset email.
     */
    public function sendResetEmail(string $toEmail, string $resetLink): bool
    {
        $fromEmail = self::outboundFromEmail();
        $subject = 'Reset Your Armely Admin Password';
        $htmlBody = view('admin.auth.reset-email', ['resetLink' => $resetLink])->render();

        return $this->sendEmail($fromEmail, $toEmail, $subject, $htmlBody);
    }

    /**
     * Send a welcome email to a newly created admin with activation link.
     */
    public function sendWelcomeAdminEmail(string $toEmail, string $activationLink, ?string $name = null): bool
    {
        $fromEmail = self::outboundFromEmail();
        $subject = 'Your Armely Admin Access';
        $htmlBody = view('admin.auth.welcome-admin', [
            'activationLink' => $activationLink,
            'loginLink' => route('admin.login'),
            'name' => $name,
        ])->render();

        return $this->sendEmail($fromEmail, $toEmail, $subject, $htmlBody);
    }
}
