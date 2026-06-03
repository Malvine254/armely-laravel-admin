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

    private function sendContentNotification(string $type, string $title, string $body, string $url): void
    {
        if (!Schema::hasTable('newsletter_subscribers')) {
            return;
        }

        $subscribers = DB::table('newsletter_subscribers')
            ->where('status', 'active')
            ->orderBy('id')
            ->get();

        if ($subscribers->isEmpty()) {
            return;
        }

        $fromEmail = AzureMailService::outboundFromEmail();
        if ($fromEmail === '') {
            Log::warning('Newsletter notification skipped: missing outbound sender.');
            return;
        }

        $mailer = new AzureMailService();
        $subject = $type === 'event'
            ? 'New Armely event: ' . $title
            : 'New Armely blog: ' . $title;

        foreach ($subscribers as $subscriber) {
            $email = AzureMailService::normalizeEmail((string) ($subscriber->email ?? ''));
            if (!AzureMailService::isDeliverableEmail($email)) {
                continue;
            }

            $html = view('emails.newsletter.content-notification', [
                'type' => $type,
                'title' => $title,
                'summary' => Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags($body)) ?? ''), 220),
                'url' => $url,
                'unsubscribeUrl' => route('newsletter.unsubscribe', ['token' => $subscriber->unsubscribe_token]),
            ])->render();

            if ($mailer->sendEmail($fromEmail, $email, $subject, $html)) {
                DB::table('newsletter_subscribers')
                    ->where('id', $subscriber->id)
                    ->update(['last_notified_at' => now(), 'updated_at' => now()]);
            }
        }
    }
}
