<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class NewsletterNotificationService
{
    public function sendBlogNotification(object $blog, string $idColumn = 'id'): void
    {
        $blogId = $blog->{$idColumn} ?? $blog->blog_id ?? $blog->id ?? null;
        $this->sendContentNotification(
            'blog',
            trim((string) ($blog->title ?? $blog->blog_title ?? 'New Armely blog article')),
            trim((string) ($blog->body ?? $blog->content ?? '')),
            $blogId ? route('blog.index', ['blogId' => $blogId]) : route('blog.index')
        );
    }

    public function sendCaseStudyNotification(object $caseStudy): void
    {
        $this->sendContentNotification(
            'case-study',
            trim((string) ($caseStudy->title ?? $caseStudy->display_title ?? $caseStudy->category ?? 'New Armely case study')),
            trim((string) ($caseStudy->body ?? $caseStudy->content ?? '')),
            route('case-studies.index')
        );
    }

    public function sendWhitePaperNotification(object $whitePaper): void
    {
        $this->sendContentNotification(
            'white-paper',
            trim((string) ($whitePaper->title ?? $whitePaper->display_title ?? 'New Armely white paper')),
            trim((string) ($whitePaper->body ?? $whitePaper->content ?? '')),
            route('resources.index')
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
        $emails = [
            env('ADMIN_EMAIL'),
            'ask.me@armely.com',
        ];

        if (Schema::hasTable('admin')) {
            $adminQuery = DB::table('admin')->whereNotNull('email');

            if (Schema::hasColumn('admin', 'status')) {
                $adminQuery->whereRaw('LOWER(status) = ?', ['active']);
            }

            $emails = array_merge($emails, $adminQuery->pluck('email')->all());
        }

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
                'recipientReason' => $recipient['reason'] ?? '',
                'unsubscribeUrl' => $recipient['unsubscribeUrl'] ?? null,
            ])->render();

            if ($mailer->sendEmail($fromEmail, $email, $subject, $html)) {
                if (($recipient['kind'] ?? '') === 'subscriber' && isset($recipient['subscriber_id'])) {
                    DB::table('newsletter_subscribers')
                        ->where('id', $recipient['subscriber_id'])
                        ->update(['last_notified_at' => now(), 'updated_at' => now()]);
                }
            }
        }
    }

    private function contentRecipients()
    {
        $recipients = [];

        if (Schema::hasTable('newsletter_subscribers')) {
            $subscribers = DB::table('newsletter_subscribers')
                ->whereRaw('LOWER(status) = ?', ['active'])
                ->orderBy('id')
                ->get();

            foreach ($subscribers as $subscriber) {
                $email = AzureMailService::normalizeEmail((string) ($subscriber->email ?? ''));
                if (!AzureMailService::isDeliverableEmail($email)) {
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

        foreach ($this->adminRecipientEmails() as $email) {
            if (!array_key_exists($email, $recipients)) {
                $recipients[$email] = [
                    'kind' => 'admin',
                    'email' => $email,
                    'reason' => 'You are receiving this because you are on the Armely admin team.',
                    'unsubscribeUrl' => null,
                ];
            }
        }

        return collect(array_values($recipients));
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
