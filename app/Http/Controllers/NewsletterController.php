<?php

namespace App\Http\Controllers;

use App\Services\AzureMailService;
use App\Services\NewsletterNotificationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        if (!Schema::hasTable('newsletter_subscribers')) {
            return $this->subscriptionResponse($request, false, 'Newsletter signup is temporarily unavailable.');
        }

        $data = $request->validate([
            'email' => ['required', 'email:filter', 'max:255'],
            'name' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'string', 'max:255'],
        ], [
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
        ]);

        if (!empty($data['website'])) {
            return $this->subscriptionResponse($request, false, 'Unable to process this signup.');
        }

        $email = AzureMailService::normalizeEmail((string) $data['email']);
        if (!AzureMailService::isDeliverableEmail($email)) {
            return $this->subscriptionResponse($request, false, 'Please use a valid email address that can receive messages.');
        }

        $existing = DB::table('newsletter_subscribers')->where('email', $email)->first();
        $token = $existing->unsubscribe_token ?? Str::random(48);
        $payload = [
            'email' => $email,
            'name' => trim((string) ($data['name'] ?? '')),
            'source' => 'footer',
            'status' => 'active',
            'unsubscribe_token' => $token,
            'subscribed_at' => now(),
            'unsubscribed_at' => null,
            'updated_at' => now(),
        ];

        if ($existing) {
            DB::table('newsletter_subscribers')->where('id', $existing->id)->update($payload);
        } else {
            $payload['created_at'] = now();
            DB::table('newsletter_subscribers')->insert($payload);
        }

        if (Schema::hasTable('newsletter_notification_unsubscribes')) {
            DB::table('newsletter_notification_unsubscribes')->where('email', $email)->delete();
        }

        $this->sendSignupEmails($email, $payload['name'], $token, !$existing);

        return $this->subscriptionResponse($request, true, 'You are subscribed. We will send new Armely blogs and events as they are published.');
    }

    public function unsubscribe(string $token): RedirectResponse
    {
        $email = null;
        $unsubscribed = false;

        if (Schema::hasTable('newsletter_subscribers')) {
            $subscriber = DB::table('newsletter_subscribers')
                ->where('unsubscribe_token', $token)
                ->first();

            if ($subscriber) {
                $email = (string) ($subscriber->email ?? '');
                DB::table('newsletter_subscribers')
                    ->where('email', $email)
                    ->update([
                        'status' => 'unsubscribed',
                        'unsubscribed_at' => now(),
                        'updated_at' => now(),
                    ]);
                $unsubscribed = true;

                if (Schema::hasTable('newsletter_notification_unsubscribes')) {
                    DB::table('newsletter_notification_unsubscribes')->updateOrInsert(
                        ['email' => $email],
                        [
                            'source' => 'newsletter',
                            'unsubscribed_at' => now(),
                            'updated_at' => now(),
                            'created_at' => now(),
                        ]
                    );
                }
            }
        }

        return redirect()->route('newsletter.unsubscribe.confirmation', [
            'source' => 'newsletter',
            'success' => $unsubscribed ? '1' : '0',
            'email' => $email,
        ]);
    }

    public function unsubscribeAdmin(Request $request): RedirectResponse
    {
        $email = AzureMailService::normalizeEmail((string) $request->query('email', ''));
        $unsubscribed = false;

        if ($email === '' || !AzureMailService::isDeliverableEmail($email)) {
            return redirect()->route('newsletter.unsubscribe.confirmation', [
                'source' => 'admin',
                'success' => '0',
            ]);
        }

        if (Schema::hasTable('newsletter_notification_unsubscribes')) {
            DB::table('newsletter_notification_unsubscribes')->updateOrInsert(
                ['email' => $email],
                [
                    'source' => 'admin',
                    'unsubscribed_at' => now(),
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
            $unsubscribed = true;
        }

        if (Schema::hasTable('newsletter_subscribers')) {
            $updatedRows = DB::table('newsletter_subscribers')
                ->where('email', $email)
                ->update([
                    'status' => 'unsubscribed',
                    'unsubscribed_at' => now(),
                    'updated_at' => now(),
                ]);
            $unsubscribed = $updatedRows > 0 || $unsubscribed;
        }

        return redirect()->route('newsletter.unsubscribe.confirmation', [
            'source' => 'admin',
            'success' => $unsubscribed ? '1' : '0',
            'email' => $email,
        ]);
    }

    public function unsubscribeConfirmation(Request $request): View
    {
        $source = (string) $request->query('source', 'newsletter');
        $email = AzureMailService::normalizeEmail((string) $request->query('email', ''));
        $success = (string) $request->query('success', '1') === '1';

        return view('newsletter.unsubscribe-confirmation', [
            'source' => in_array($source, ['admin', 'newsletter'], true) ? $source : 'newsletter',
            'email' => $email,
            'success' => $success,
        ]);
    }

    private function subscriptionResponse(Request $request, bool $success, string $message)
    {
        if ($request->expectsJson()) {
            return response()->json(['success' => $success, 'message' => $message], $success ? 200 : 422);
        }

        return back()->with($success ? 'status' : 'newsletter_error', $message);
    }

    private function sendSignupEmails(string $email, ?string $name, string $token, bool $isNewSubscriber): void
    {
        $fromEmail = AzureMailService::outboundFromEmail();

        if ($fromEmail === '') {
            Log::warning('Newsletter signup email skipped: missing outbound sender.');
            return;
        }

        $mail = app(AzureMailService::class);
        $unsubscribeUrl = route('newsletter.unsubscribe', ['token' => $token]);
        $unsubscribeHeaders = [
            [
                'name' => 'X-List-Unsubscribe',
                'value' => '<' . $unsubscribeUrl . '>',
            ],
            [
                'name' => 'X-List-Unsubscribe-Post',
                'value' => 'List-Unsubscribe=One-Click',
            ],
        ];

        try {
            $welcomeBody = view('emails.newsletter.welcome', [
                'name' => $name,
                'email' => $email,
                'unsubscribeUrl' => $unsubscribeUrl,
            ])->render();

            $sentToSubscriber = $mail->sendEmail(
                $fromEmail,
                $email,
                'You are subscribed to Armely updates',
                $welcomeBody,
                true,
                true,
                $unsubscribeHeaders
            );

            if (!$sentToSubscriber) {
                Log::warning('Newsletter welcome email was not sent.', ['email' => $email]);
            }
        } catch (\Throwable $e) {
            Log::error('Newsletter welcome email failed: ' . $e->getMessage(), ['email' => $email, 'exception' => $e]);
        }

        foreach ($this->newsletterAdminRecipients() as $adminEmail) {
            try {
                $adminBody = view('emails.newsletter.admin-subscription-notification', [
                    'name' => $name,
                    'email' => $email,
                    'statusLabel' => $isNewSubscriber ? 'New subscriber' : 'Subscriber reactivated',
                    'subscribedAt' => now(),
                ])->render();

                $sentToAdmin = $mail->sendEmail(
                    $fromEmail,
                    $adminEmail,
                    ($isNewSubscriber ? 'New' : 'Reactivated') . ' Armely newsletter subscriber',
                    $adminBody
                );

                if (!$sentToAdmin) {
                    Log::warning('Newsletter admin notification was not sent.', ['admin_email' => $adminEmail, 'subscriber_email' => $email]);
                }
            } catch (\Throwable $e) {
                Log::error('Newsletter admin notification failed: ' . $e->getMessage(), [
                    'admin_email' => $adminEmail,
                    'subscriber_email' => $email,
                    'exception' => $e,
                ]);
            }
        }
    }

    private function newsletterAdminRecipients(): array
    {
        return app(NewsletterNotificationService::class)->adminRecipientEmails();
    }
}
