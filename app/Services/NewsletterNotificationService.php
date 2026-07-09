<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use App\Support\BlogUrl;

class NewsletterNotificationService
{
    public function sendBlogNotification(object $blog, string $idColumn = 'id'): void
    {
        $blogId = $blog->{$idColumn} ?? $blog->blog_id ?? $blog->id ?? null;
        $this->sendContentNotification(
            'blog',
            trim((string) ($blog->title ?? $blog->blog_title ?? 'New Armely blog article')),
            trim((string) ($blog->body ?? $blog->content ?? '')),
            $blogId ? BlogUrl::url($blog, $idColumn, 'title') : route('blog.index')
        );
    }

    public function sendCaseStudyNotification(object $caseStudy): void
    {
        $caseStudyId = $caseStudy->id ?? $caseStudy->case_study_id ?? null;
        $this->sendContentNotification(
            'case-study',
            trim((string) ($caseStudy->title ?? $caseStudy->display_title ?? $caseStudy->category ?? 'New Armely case study')),
            trim((string) ($caseStudy->body ?? $caseStudy->content ?? '')),
            $caseStudyId ? url(URL::temporarySignedRoute('case-studies.access', now()->addDays(7), ['caseStudy' => $caseStudyId], false)) : route('case-studies.index')
        );
    }

    public function sendWhitePaperNotification(object $whitePaper): void
    {
        $whitePaperId = $whitePaper->id ?? $whitePaper->white_paper_id ?? null;
        $this->sendContentNotification(
            'white-paper',
            trim((string) ($whitePaper->title ?? $whitePaper->display_title ?? 'New Armely white paper')),
            trim((string) ($whitePaper->body ?? $whitePaper->content ?? '')),
            $whitePaperId ? url(URL::temporarySignedRoute('white-papers.access', now()->addDays(7), ['paper' => $whitePaperId], false)) : route('case-studies.index')
        );
    }

    public function sendEventNotification(object $event): void
    {
        $url = trim((string) ($event->url ?? ''));
        $this->sendContentNotification(
            'event',
            trim((string) ($event->title ?? 'New Armely event')),
            trim((string) ($event->body ?? '')),
            $url !== '' ? $url : route('events.index')
        );
    }

    public function adminRecipientEmails(): array
    {
        $emails = array_merge(
            $this->adminEmailsFromEnv(),
            ['ask.me@armely.com'],
            $this->activeAdminTableEmails()
        );

        return collect($emails)
            ->filter()
            ->map(fn ($email) => AzureMailService::normalizeEmail((string) $email))
            ->filter(fn ($email) => AzureMailService::isDeliverableEmail($email))
            ->unique()
            ->values()
            ->all();
    }

    private function sendContentNotification(string $type, string $title, string $body, string $url): void
    {
        $fromEmail = AzureMailService::outboundFromEmail();
        if ($fromEmail === '') {
            Log::warning('Newsletter notification skipped: missing outbound sender.');
            return;
        }

        $recipients = $this->contentRecipients();
        if ($recipients->isEmpty()) {
            Log::info('Newsletter notification skipped: no active subscribers or admins found.', [
                'type' => $type,
                'title' => $title,
            ]);
            return;
        }

        $mailer = app(AzureMailService::class);
        $subject = $this->contentSubject($type, $title);
        $summary = Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags($body)) ?? ''), 220);

        foreach ($recipients as $recipient) {
            $email = (string) ($recipient['email'] ?? '');
            if ($email === '' || !AzureMailService::isDeliverableEmail($email)) {
                continue;
            }

            $html = view('emails.newsletter.content-notification', [
                'type' => $type,
                'title' => $title,
                'summary' => $summary,
                'url' => $url,
                'recipientKind' => $recipient['kind'] ?? 'subscriber',
                'recipientReason' => $recipient['reason'] ?? '',
                'unsubscribeUrl' => $recipient['unsubscribeUrl'] ?? null,
            ])->render();

            $internetMessageHeaders = [];
            $unsubscribeUrl = trim((string) ($recipient['unsubscribeUrl'] ?? ''));
            if ($unsubscribeUrl !== '') {
                $internetMessageHeaders = [
                    [
                        'name' => 'X-List-Unsubscribe',
                        'value' => '<' . $unsubscribeUrl . '>',
                    ],
                    [
                        'name' => 'X-List-Unsubscribe-Post',
                        'value' => 'List-Unsubscribe=One-Click',
                    ],
                ];
            }

            if ($mailer->sendEmail($fromEmail, $email, $subject, $html, true, true, $internetMessageHeaders)) {
                if (($recipient['kind'] ?? '') === 'subscriber' && isset($recipient['subscriber_id'])) {
                    DB::table('newsletter_subscribers')
                        ->where('id', $recipient['subscriber_id'])
                        ->update(['last_notified_at' => now(), 'updated_at' => now()]);
                }
            } else {
                Log::warning('Newsletter content notification was not sent.', [
                    'type' => $type,
                    'title' => $title,
                    'recipient_email' => $email,
                    'recipient_kind' => $recipient['kind'] ?? 'subscriber',
                ]);
            }
        }
    }

    private function contentRecipients()
    {
        $recipients = [];

        if (Schema::hasTable('newsletter_subscribers')) {
            $subscriberQuery = DB::table('newsletter_subscribers');

            if (Schema::hasColumn('newsletter_subscribers', 'status')) {
                $subscriberQuery->whereRaw('LOWER(status) = ?', ['active']);
            }

            $subscribers = $subscriberQuery->orderBy('id')->get();
            $suppressed = array_flip($this->suppressedNotificationEmails());

            foreach ($subscribers as $subscriber) {
                $email = AzureMailService::normalizeEmail((string) ($subscriber->email ?? ''));
                if (!AzureMailService::isDeliverableEmail($email)) {
                    continue;
                }
                if (isset($suppressed[$email])) {
                    continue;
                }

                $recipients[$email] = [
                    'kind' => 'subscriber',
                    'email' => $email,
                    'subscriber_id' => $subscriber->id,
                    'reason' => 'You are receiving this because you subscribed to Armely newsletter updates for blogs, events, case studies, and related resources.',
                    'unsubscribeUrl' => route('newsletter.unsubscribe', ['token' => $subscriber->unsubscribe_token]),
                ];
            }
        }

        foreach ($this->activeAdminRecipients() as $email) {
            if (!array_key_exists($email, $recipients)) {
                $recipients[$email] = [
                    'kind' => 'admin',
                    'email' => $email,
                    'reason' => 'You are receiving this because you are on the Armely admin team.',
                    'unsubscribeUrl' => URL::signedRoute('newsletter.admin.unsubscribe', ['email' => $email]),
                ];
            }
        }

        return collect(array_values($recipients));
    }

    private function suppressedNotificationEmails(): array
    {
        if (!Schema::hasTable('newsletter_notification_unsubscribes')) {
            return [];
        }

        $query = DB::table('newsletter_notification_unsubscribes')
            ->select('email');

        if (Schema::hasColumn('newsletter_notification_unsubscribes', 'unsubscribed_at')) {
            $query->whereNotNull('unsubscribed_at');
        }

        return $query->pluck('email')
            ->map(fn ($email) => AzureMailService::normalizeEmail((string) $email))
            ->filter()
            ->values()
            ->all();
    }

    private function adminEmailsFromEnv(): array
    {
        $emails = [];

        $single = trim((string) env('ADMIN_EMAIL', ''));
        if ($single !== '') {
            $emails[] = $single;
        }

        $multi = trim((string) env('ADMIN_EMAILS', ''));
        if ($multi !== '') {
            $emails = array_merge($emails, preg_split('/[,\s]+/', $multi) ?: []);
        }

        return array_values(array_filter(array_map('trim', $emails)));
    }

    private function activeAdminTableEmails(): array
    {
        if (!Schema::hasTable('admin')) {
            return [];
        }

        $query = DB::table('admin')
            ->whereNotNull('email')
            ->where('email', '!=', '');

        if (Schema::hasColumn('admin', 'status')) {
            $query->whereRaw('LOWER(status) = ?', ['active']);
        }

        return $query->pluck('email')->all();
    }

    private function activeAdminRecipients(): array
    {
        $suppressed = array_flip($this->suppressedNotificationEmails());

        return collect($this->adminRecipientEmails())
            ->reject(fn ($email) => isset($suppressed[$email]))
            ->values()
            ->all();
    }

    private function contentSubject(string $type, string $title): string
    {
        $label = match ($type) {
            'case-study' => 'case study',
            'white-paper' => 'white paper',
            'event' => 'event',
            default => 'blog update',
        };

        return 'Armely ' . $label . ': ' . $title;
    }
}
