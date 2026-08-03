<?php

namespace App\Http\Controllers;

use App\Services\AzureMailService;
use App\Services\NewsletterNotificationService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class EventRegistrationController extends Controller
{
    private const EVENT_NAME = 'Sovereign Data Clouds with Snowflake';

    private const PERSONAL_EMAIL_DOMAINS = [
        'aol.com', 'comcast.net', 'gmail.com', 'googlemail.com', 'hotmail.com',
        'icloud.com', 'live.com', 'mail.com', 'me.com', 'msn.com', 'outlook.com',
        'proton.me', 'protonmail.com', 'yahoo.com', 'ymail.com', 'zoho.com',
    ];

    public function create(): View
    {
        return view('events.sovereign-data-cloud-registration', $this->defaultEventData());
    }

    public function createPrivate(string $slug): View
    {
        $event = $this->privateEvent($slug);

        return view('events.sovereign-data-cloud-registration', [
            'event' => $event,
            'eventName' => $event->title,
            'eventDate' => $event->start_date,
            'formAction' => route('events.private.register.store', $event->private_slug),
            'showEventSelector' => false,
            'activeEvents' => collect(),
            'defaultEventId' => null,
        ]);
    }

    public function store(
        Request $request,
        AzureMailService $mailer,
        NewsletterNotificationService $notificationService
    ): RedirectResponse|JsonResponse
    {
        return $this->handleStore($request, $mailer, $notificationService, null);
    }

    public function storePrivate(
        Request $request,
        AzureMailService $mailer,
        NewsletterNotificationService $notificationService,
        string $slug
    ): RedirectResponse|JsonResponse
    {
        return $this->handleStore($request, $mailer, $notificationService, $this->privateEvent($slug));
    }

    private function handleStore(
        Request $request,
        AzureMailService $mailer,
        NewsletterNotificationService $notificationService,
        ?object $event
    ): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:150'],
            'work_email' => [
                'required',
                'string',
                'email:rfc',
                'max:255',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $domain = strtolower((string) strrchr((string) $value, '@'));
                    $domain = ltrim($domain, '@');

                    if ($domain === '' || in_array($domain, self::PERSONAL_EMAIL_DOMAINS, true)) {
                        $fail('Please use your company, government, or organizational email address. Personal email addresses are not permitted.');
                    }
                },
            ],
            'organization' => ['required', 'string', 'max:200'],
            'job_title' => [
                'required',
                Rule::in([
                    'CIO / CTO',
                    'CISO / IT Security Director',
                    'Chief Data Officer / Director of Analytics',
                    'IT Director / Infrastructure Manager',
                    'Enterprise / Data Architect',
                    'Data Engineer / Technical Lead',
                    'Other Technology Leader',
                ]),
            ],
            'compliance_focus' => [
                'nullable',
                Rule::in([
                    'CJIS Compliance & Law Enforcement Data',
                    'FedRAMP / Sovereign Cloud Boundaries',
                    'HIPAA / PHI Data Isolation',
                    'Zero-Trust & Multi-Agency Data Sharing',
                    'General Cloud Modernization',
                ]),
            ],
            'event_id' => ['nullable', 'integer'],
            'website' => ['nullable', 'max:0'], // Honeypot.
            'g-recaptcha-response' => ['required', 'string'],
        ], [
            'g-recaptcha-response.required' => 'Please verify that you are not a robot.',
        ]);

        if ($event === null && !empty($data['event_id'])) {
            $event = $this->activePrivateEvents()->firstWhere('id', (int) $data['event_id']);
            if (!$event) {
                throw ValidationException::withMessages([
                    'event_id' => ['Please select an active upcoming event.'],
                ]);
            }
        }

        $data['work_email'] = AzureMailService::normalizeEmail($data['work_email']);

        if (!AzureMailService::isDeliverableEmail($data['work_email'])) {
            if ($request->expectsJson()) {
                throw ValidationException::withMessages([
                    'work_email' => ['Please enter a valid, deliverable company email address.'],
                ]);
            }

            return back()->withInput()->withErrors([
                'work_email' => 'Please enter a valid, deliverable company email address.',
            ]);
        }

        if (!$this->verifyRecaptcha($data['g-recaptcha-response'], $request->ip())) {
            if ($request->expectsJson()) {
                throw ValidationException::withMessages([
                    'captcha' => ['reCAPTCHA verification failed. Please try again.'],
                ]);
            }

            return back()->withInput()->withErrors([
                'captcha' => 'reCAPTCHA verification failed. Please try again.',
            ]);
        }

        $unsubscribeToken = Str::random(64);
        DB::table('event_registrations')->insert([
            'event_name' => $event->title ?? self::EVENT_NAME,
            'event_id' => $event->id ?? null,
            'full_name' => $data['full_name'],
            'work_email' => $data['work_email'],
            'organization' => $data['organization'],
            'job_title' => $data['job_title'],
            'compliance_focus' => $data['compliance_focus'] ?? null,
            'ip_address' => $request->ip(),
            'unsubscribe_token' => $unsubscribeToken,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $data['event_name'] = $event->title ?? self::EVENT_NAME;
        $data['unsubscribe_url'] = URL::signedRoute('events.emails.unsubscribe', ['token' => $unsubscribeToken]);
        $this->sendEmails($mailer, $notificationService, $data);

        $message = 'Thank you, '.$data['full_name'].'. Your request has been received. Our team will review your details and issue your access link within 24–48 hours.';

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return redirect()
            ->to($event
                ? route('events.private.register', $event->private_slug)
                : route('events.sovereign-data-cloud.register'))
            ->with('success', $message);
    }

    private function sendEmails(
        AzureMailService $mailer,
        NewsletterNotificationService $notificationService,
        array $data
    ): void
    {
        $from = AzureMailService::outboundFromEmail();
        $configuredRecipient = AzureMailService::normalizeEmail((string) (
            config('mail.event_registration_to')
            ?: env('CONTACT_NOTIFICATION_EMAIL', 'ask.me@armely.com')
        ));
        $adminRecipients = collect(array_merge(
            [$configuredRecipient],
            $notificationService->adminRecipientEmails()
        ))
            ->filter(fn ($email) => AzureMailService::isDeliverableEmail((string) $email))
            ->unique()
            ->values();

        try {
            $isUnsubscribed = DB::table('event_email_unsubscribes')
                ->where('email', $data['work_email'])
                ->exists();
            $userSent = $isUnsubscribed ?: $mailer->sendEmail(
                    $from,
                    $data['work_email'],
                    'Request Received: '.$data['event_name'],
                    view('emails.events.registration-confirmation', $data)->render()
                );

            $adminResults = $adminRecipients->mapWithKeys(
                fn ($admin) => [(string) $admin => $mailer->sendEmail(
                    $from,
                    $admin,
                    'New event invitation request: '.$data['event_name'],
                    view('emails.events.registration-notification', $data)->render()
                )]
            );
            $adminsSent = $adminResults->isNotEmpty() && $adminResults->every();

            if (!$userSent || !$adminsSent) {
                Log::warning('Event registration email was not delivered', [
                    'registrant_sent' => $userSent,
                    'admin_results' => $adminResults->all(),
                    'email' => $data['work_email'],
                ]);
            }
        } catch (\Throwable $exception) {
            Log::warning('Event registration email failed', [
                'email' => $data['work_email'],
                'error' => $exception->getMessage(),
            ]);
        }
    }

    private function privateEvent(string $slug): object
    {
        return DB::table('events')
            ->where('private_slug', $slug)
            ->where('event_type', 'private')
            ->firstOrFail();
    }

    private function defaultEventData(): array
    {
        $activeEvents = $this->activePrivateEvents();

        return [
            'event' => null,
            'eventName' => self::EVENT_NAME,
            'eventDate' => null,
            'formAction' => route('events.sovereign-data-cloud.register.store'),
            'showEventSelector' => true,
            'activeEvents' => $activeEvents,
            'defaultEventId' => $activeEvents->first()?->id,
        ];
    }

    private function activePrivateEvents()
    {
        return DB::table('events')
            ->where('event_type', 'private')
            ->get()
            ->map(function ($event) {
                $event->event_timestamp = $this->eventTimestamp(
                    (string) ($event->start_date ?? ''),
                    (string) ($event->start_time ?? ''),
                    (string) ($event->timezone ?? 'CST')
                );
                return $event;
            })
            ->filter(fn ($event) => $event->event_timestamp !== null
                && $event->event_timestamp >= now()->startOfDay()->timestamp)
            ->sortBy('event_timestamp')
            ->values();
    }

    private function eventTimestamp(string $value, string $time = '', string $timezone = 'CST'): ?int
    {
        foreach (['Y-m-d', 'd/m/Y', 'Y-m-d H:i:s'] as $format) {
            try {
                $date = Carbon::createFromFormat($format, trim($value));
                if ($date !== false) {
                    $date->setTimezone(match ($timezone) {
                        'EST' => 'America/New_York',
                        'MST' => 'America/Denver',
                        'PST' => 'America/Los_Angeles',
                        'UTC' => 'UTC',
                        default => 'America/Chicago',
                    });
                    if ($time !== '' && preg_match('/^\d{2}:\d{2}$/', $time)) {
                        [$hour, $minute] = array_map('intval', explode(':', $time));
                        $date->setTime($hour, $minute);
                    } else {
                        $date->startOfDay();
                    }
                    return $date->timestamp;
                }
            } catch (\Throwable) {
                // Try the next supported format.
            }
        }

        return null;
    }

    private function verifyRecaptcha(string $token, ?string $ipAddress): bool
    {
        if (config('services.recaptcha.bypass', false)) {
            return true;
        }

        $secret = (string) config('services.recaptcha.secret_key', '');
        if ($secret === '') {
            Log::warning('Event registration reCAPTCHA secret is not configured.');
            return false;
        }

        try {
            $response = Http::asForm()->timeout(10)->post(
                'https://www.google.com/recaptcha/api/siteverify',
                [
                    'secret' => $secret,
                    'response' => $token,
                    'remoteip' => $ipAddress,
                ]
            );

            return $response->ok() && $response->json('success') === true;
        } catch (\Throwable $exception) {
            Log::warning('Event registration reCAPTCHA verification failed', [
                'error' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    public function unsubscribe(string $token): View
    {
        $registration = DB::table('event_registrations')->where('unsubscribe_token', $token)->first();
        abort_unless($registration, 404);

        $email = AzureMailService::normalizeEmail((string) $registration->work_email);
        DB::table('event_email_unsubscribes')->updateOrInsert(
            ['email' => $email],
            [
                'unsubscribed_at' => now(),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        return view('events.email-unsubscribed', ['email' => $email]);
    }
}
