<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    public function index()
    {
        $dbErrorMessage = null;

        [$offers, $industryListings, $blogs, $videos] = [
            $this->offers($dbErrorMessage),
            $this->industryListings($dbErrorMessage),
            $this->recentBlogs($dbErrorMessage),
            $this->recentVideos($dbErrorMessage),
        ];

        return view('home', [
            'offers' => $offers,
            'industryListings' => $industryListings,
            'blogs' => $blogs,
            'videos' => $videos,
            'recaptchaSiteKey' => env('CAPTURE_SITE_KEY', ''),
            'dbErrorMessage' => $dbErrorMessage,
        ]);
    }

    public function contact()
    {
        return view('contact', [
            'recaptchaSiteKey' => env('CAPTURE_SITE_KEY', ''),
        ]);
    }

    public function allPartners()
    {
        return view('partners');
    }

        public function events()
        {
            $events = collect();
            $dbErrorMessage = null;

            try {
                $events = DB::table('events')
                    ->select('start_date', 'title', 'body', 'url', 'recorded_url')
                    ->orderBy('id', 'desc')
                    ->get();
            } catch (\Throwable $e) {
                Log::warning('Events query failed; showing friendly fallback', ['error' => $e->getMessage()]);
                $dbErrorMessage = 'We are temporarily unable to load events. Please try again in a few moments.';
            }

            return view('events', [
                'events' => $events,
                'dbErrorMessage' => $dbErrorMessage,
            ]);
        }

    public function company()
    {
        $dbErrorMessage = null;
        $coreValues = $this->safeDb(function () {
            return DB::table('core_values')
                ->select('id', 'title', 'body', 'icon-font as icon')
                ->get();
        }, $dbErrorMessage);

        return view('company', [
            'coreValues' => $coreValues,
            'dbErrorMessage' => $dbErrorMessage,
        ]);
    }

    public function career()
    {
        $dbErrorMessage = null;
        $careerListings = $this->safeDb(function () {
            return DB::table('career')
                ->select('id', 'job_id', 'job_title as title', 'job_location as location', 'job_type', 'job_deadline')
                ->orderBy('id', 'desc')
                ->get();
        }, $dbErrorMessage);

        return view('career', [
            'careerListings' => $careerListings,
            'dbErrorMessage' => $dbErrorMessage,
        ]);
    }

    public function team()
    {
        $dbErrorMessage = null;
        $teamMembers = $this->safeDb(function () {
            return DB::table('team')
                ->select('id', 'team_name as name', 'team_title as position', 'team_body as bio', 'team_image as image', 'facebook', 'x', 'linkedin', 'instagram')
                ->get();
        }, $dbErrorMessage);

        // Group team members by hierarchy
        $hierarchy = [
            'Executive Leadership' => [],
            'Senior Management' => [],
            'Management' => [],
            'Technical Team' => [],
            'Other' => []
        ];

        foreach ($teamMembers as $member) {
            $title = strtolower($member->position);

            if (str_contains($title, 'ceo') || str_contains($title, 'president') || str_contains($title, 'founder')) {
                $hierarchy['Executive Leadership'][] = $member;
            } elseif (str_contains($title, 'director') || str_contains($title, 'dir.')) {
                $hierarchy['Senior Management'][] = $member;
            } elseif (str_contains($title, 'manager')) {
                $hierarchy['Management'][] = $member;
            } elseif (str_contains($title, 'engineer') || str_contains($title, 'developer')) {
                $hierarchy['Technical Team'][] = $member;
            } else {
                $hierarchy['Other'][] = $member;
            }
        }

        return view('team', [
            'hierarchy' => $hierarchy,
        ]);
    }

    public function customerStories()
    {
        $dbErrorMessage = null;
        $testimonials = $this->safeDb(function () {
            return DB::table('customer_stories')
                ->select('id', 'name', 'position', 'body_content', 'profile')
                ->orderBy('id', 'desc')
                ->get();
        }, $dbErrorMessage);

        return view('customer-stories', [
            'testimonials' => $testimonials,
            'dbErrorMessage' => $dbErrorMessage,
        ]);
    }

    public function socialImpact()
    {
        $dbErrorMessage = null;

        $gallery = $this->safeDb(function () {
            return DB::table('social_impact')
                ->select('id', 'secure_id', 'title', 'body', 'snippet', 'image_url', 'posted_date', 'category')
                ->orderBy('id', 'desc')
                ->get();
        }, $dbErrorMessage);

        $socialImpact = $this->safeDb(function () {
            return DB::table('social_impact')
                ->select('id', 'secure_id', 'title', 'body', 'snippet', 'image_url', 'posted_date', 'category')
                ->where('category', 'new')
                ->orderBy('id', 'desc')
                ->limit(4)
                ->get();
        }, $dbErrorMessage);

        return view('social-impact', [
            'gallery' => $gallery,
            'socialImpact' => $socialImpact,
            'dbErrorMessage' => $dbErrorMessage,
        ]);
    }

    public function services()
    {
        $dbErrorMessage = null;
        $services = $this->safeDb(function () {
            return DB::table('services_lists')
                ->select('id', 'title', 'image', 'body')
                ->paginate(6);
        }, $dbErrorMessage);

        return view('services', [
            'services' => $services,
            'dbErrorMessage' => $dbErrorMessage,
        ]);
    }

    public function serviceDetails($name)
    {
        $dbErrorMessage = null;

        $service = $this->safeDb(function () use ($name) {
            return DB::table('services_lists')
                ->whereRaw("LOWER(REPLACE(title, ' ', '-')) = ?", [strtolower($name)])
                ->orWhere('title', $name)
                ->first();
        }, $dbErrorMessage);

        if ($dbErrorMessage) {
            return response()->view('errors.service-unavailable', [], 503);
        }

        if (!$service) {
            return redirect()->route('services')->with('error', 'Service not found');
        }

        $relatedServices = $this->safeDb(function () use ($service) {
            return DB::table('services_lists')
                ->where('id', '!=', $service->id)
                ->orderBy('id', 'desc')
                ->limit(3)
                ->get();
        }, $dbErrorMessage);

        return view('service-details', [
            'service' => $service,
            'relatedServices' => $relatedServices,
            'serviceName' => $name,
            'dbErrorMessage' => $dbErrorMessage,
        ]);
    }

    public function submitConsultation(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'organization' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'service_type' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'website' => ['nullable', 'string', 'max:255'], // honeypot
            'g-recaptcha-response' => ['required', 'string'],
        ], [
            'name.required' => 'Name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Invalid email format.',
            'organization.required' => 'Organization is required.',
            'phone.required' => 'Phone is required.',
            'service_type.required' => 'Service of interest is required.',
            'message.required' => 'Message is required.',
            'g-recaptcha-response.required' => 'Please verify you are not a robot.',
        ]);

        // Honeypot trap
        if (!empty($data['website'])) {
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => 'Spam detected.'], 400)
                : back()->withErrors(['form' => 'Spam detected.'])->withInput();
        }

        if (!$this->verifyRecaptcha($data['g-recaptcha-response'])) {
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => 'reCAPTCHA verification failed. Please try again.'], 400)
                : back()->withErrors(['captcha' => 'reCAPTCHA verification failed. Please try again.'])->withInput();
        }

        $blockedDomains = ['registry.godaddy', 'kr.slembassy.gov.sl'];
        $email = strtolower($data['email']);
        foreach ($blockedDomains as $blocked) {
            if (Str::endsWith($email, '@' . $blocked)) {
                return $request->expectsJson()
                    ? response()->json(['success' => false, 'message' => 'Email domain is not allowed.'], 400)
                    : back()->withErrors(['email' => 'Email domain is not allowed.'])->withInput();
            }
        }

        $now = Carbon::now()->toIso8601String();

        $payload = [
            'name' => $data['name'],
            'email' => $email,
            'organization' => $data['organization'],
            'phone' => $data['phone'],
            'message' => $data['message'],
        ];

        if (Schema::hasColumn('consultation', 'service_type')) {
            $payload['service_type'] = $data['service_type'];
        } elseif (Schema::hasColumn('consultation', 'service_name')) {
            $payload['service_name'] = $data['service_type'];
        }

        if (Schema::hasColumn('consultation', 'created_at')) {
            $payload['created_at'] = $now;
        } elseif (Schema::hasColumn('consultation', 'date_now')) {
            $payload['date_now'] = Carbon::now()->format('Y-m-d');
        }

        // Handle legacy tables where id is not AUTO_INCREMENT
        if (Schema::hasColumn('consultation', 'id')) {
            $maxId = DB::table('consultation')->max('id');
            $payload['id'] = is_numeric($maxId) ? ((int) $maxId + 1) : 1;
        }

        DB::table('consultation')->insert($payload);

        $this->notifyConsultationViaGraph(
            $data['name'],
            $email,
            $data['organization'],
            $data['phone'],
            $data['service_type'],
            $data['message']
        );

        $successMessage = 'Your consultation request has been submitted successfully!';

        return $request->expectsJson()
            ? response()->json(['success' => true, 'message' => $successMessage])
            : back()->with('success', $successMessage);
    }

    public function jobBoard(Request $request)
    {
        $jobId = $request->query('job-details') ?? $request->query('id');
        $dbErrorMessage = null;
        
        if (!$jobId) {
            return redirect()->route('career.index');
        }

        $job = $this->safeDb(function () use ($jobId) {
            return DB::table('career')
                ->where('job_id', $jobId)
                ->first();
        }, $dbErrorMessage);

        if ($dbErrorMessage) {
            return response()->view('errors.service-unavailable', [], 503);
        }

        if (!$job) {
            return redirect()->route('career.index')->with('error', 'Job not found');
        }

        // Check if job deadline has passed
        if ($job->job_deadline && strtotime($job->job_deadline) < time()) {
            return redirect()->route('career.index')->with('error', 'This job posting has expired and is no longer accepting applications.');
        }

        return view('job-board', [
            'job' => $job,
        ]);
    }

    public function socialImpactDetails($secure_id)
    {
        $initiative = DB::table('social_impact')
            ->where('secure_id', $secure_id)
            ->first();

        if (!$initiative) {
            return redirect()->route('social-impact.index')->with('error', 'Initiative not found');
        }

        // Fetch related stories (excluding the current one, limit to 3)
        $relatedStories = DB::table('social_impact')
            ->where('secure_id', '!=', $secure_id)
            ->orderBy('id', 'desc')
            ->limit(3)
            ->get();

        return view('social-impact-details', [
            'initiative' => $initiative,
            'relatedStories' => $relatedStories,
        ]);
    }

    public function applications(Request $request)
    {
        $jobId = $request->query('job-details') ?? $request->query('id');
        $jobTitle = $request->query('title');
        $application = $request->query('application');

        if (!$jobId) {
            return redirect()->route('career.index');
        }

        // If title is missing, try to resolve from DB
        if (!$jobTitle) {
            $job = DB::table('career')->where('job_id', $jobId)->first();
            if (!$job) {
                return redirect()->route('career.index')->with('error', 'Job not found');
            }
            $jobTitle = $job->job_title ?? $jobTitle;
            
            // Check if job deadline has passed
            if ($job->job_deadline && strtotime($job->job_deadline) < time()) {
                return redirect()->route('career.index')->with('error', 'This job posting has expired and is no longer accepting applications.');
            }
        } else {
            // If title provided but need to validate deadline
            $job = DB::table('career')->where('job_id', $jobId)->first();
            if ($job && $job->job_deadline && strtotime($job->job_deadline) < time()) {
                return redirect()->route('career.index')->with('error', 'This job posting has expired and is no longer accepting applications.');
            }
        }

        // Do not hard-require application=true; just default to show form when job id exists
        return view('applications', [
            'jobId' => $jobId,
            'jobTitle' => $jobTitle,
            'applicationFlag' => $application,
        ]);
    }

    public function submitApplication(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'zip' => ['required', 'string', 'max:20'],
            'state' => ['required', 'string', 'max:100'],
            'cv' => ['required', 'file', 'mimes:pdf', 'max:5120'],
            'type' => ['required', 'string'],
            'position' => ['required', 'string'],
            'job_id' => ['required', 'string'],
            'website' => ['nullable', 'string', 'max:255'], // honeypot
            'g-recaptcha-response' => ['required', 'string'],
        ]);

        // Honeypot trap
        if (!empty($data['website'])) {
            return back()->withErrors(['form' => 'Spam detected.'])->withInput();
        }

        if (!$this->verifyRecaptcha($data['g-recaptcha-response'])) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'reCAPTCHA verification failed. Please try again.'], 400);
            }
            return back()->withErrors(['captcha' => 'reCAPTCHA verification failed. Please try again.'])->withInput();
        }

        $cvPath = null;
        $cvUrl = null;
        if ($request->hasFile('cv')) {
            $cvPath = $request->file('cv')->store('cv_uploads', 'public');
            $cvUrl = asset('storage/' . $cvPath);
        }

        $applicationDate = Carbon::now();
        $roleValue = strtolower($data['type']);

        // Build payload with flexible column mapping
        $payload = [
            'name' => $data['name'],
            'email' => strtolower($data['email']),
            'city' => $data['city'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'state' => $data['state'],
            'zip' => $data['zip'],
            'position' => $data['position'],
        ];

        if ($cvPath) {
            if (Schema::hasColumn('job_applications', 'cv')) {
                $payload['cv'] = basename($cvPath);
            } elseif (Schema::hasColumn('job_applications', 'cv_path')) {
                $payload['cv_path'] = $cvPath;
            } elseif (Schema::hasColumn('job_applications', 'resume')) {
                $payload['resume'] = basename($cvPath);
            }
        }

        if (Schema::hasColumn('job_applications', 'role')) {
            $payload['role'] = $roleValue;
        } elseif (Schema::hasColumn('job_applications', 'type')) {
            $payload['type'] = $data['type'];
        }

        if (Schema::hasColumn('job_applications', 'job_id')) {
            $payload['job_id'] = $data['job_id'];
        }

        if (Schema::hasColumn('job_applications', 'application_date')) {
            $payload['application_date'] = $applicationDate->format('Y-m-d H:i:s');
        } elseif (Schema::hasColumn('job_applications', 'created_at')) {
            $payload['created_at'] = $applicationDate;
        }

        // Handle legacy tables where id is not AUTO_INCREMENT
        if (Schema::hasColumn('job_applications', 'id')) {
            $maxId = DB::table('job_applications')->max('id');
            $payload['id'] = is_numeric($maxId) ? ((int) $maxId + 1) : 1;
        }

        try {
            if (Schema::hasTable('job_applications')) {
                DB::table('job_applications')->insert($payload);
            } else {
                Log::warning('job_applications table missing; skipping DB insert.');
            }
        } catch (\Throwable $e) {
            Log::error('Job application insert failed', ['error' => $e->getMessage()]);
        }

        $this->notifyJobApplicationViaGraph($payload, $cvUrl);
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Application submitted successfully!']);
        }

        return redirect()->route('career.index')->with('success', 'Application submitted successfully!');
    }

    public function submitContact(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'organization' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'message' => ['required', 'string'],
            'subject' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'string', 'max:255'], // honeypot
            'g-recaptcha-response' => ['required', 'string'],
        ], [
            'name.required' => 'Name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Invalid email format.',
            'message.required' => 'Message is required.',
            'g-recaptcha-response.required' => 'Please verify you are not a robot.',
        ]);

        // Honeypot trap
        if (!empty($data['website'])) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Spam detected.'], 400);
            }
            return back()->withErrors(['form' => 'Spam detected.'])->withInput();
        }

        if (!$this->verifyRecaptcha($data['g-recaptcha-response'])) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'reCAPTCHA verification failed. Please try again.'], 400);
            }
            return back()->withErrors(['captcha' => 'reCAPTCHA verification failed. Please try again.'])->withInput();
        }

        $blockedDomains = ['registry.godaddy', 'kr.slembassy.gov.sl'];
        $email = strtolower($data['email']);
        foreach ($blockedDomains as $blocked) {
            if (Str::endsWith($email, '@' . $blocked)) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Email domain is not allowed.'], 400);
                }
                return back()->withErrors(['email' => 'Email domain is not allowed.'])->withInput();
            }
        }

        $now = Carbon::now()->toIso8601String();

        DB::table('contacts')->insert([
            'name' => $data['name'],
            'email' => $email,
            'organization' => $data['organization'] ?? '',
            'phone' => $data['phone'] ?? '',
            'message' => $data['message'],
            'subject' => $data['subject'] ?? '',
            'sent_date' => $now,
        ]);

        $this->notifyViaGraph($data['name'], $email, $data['message'], $data['organization'] ?? '', $data['phone'] ?? '', $data['subject'] ?? '');

        $successMessage = 'Your message has been sent successfully. We will contact you soon.';
        
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $successMessage]);
        }
        return back()->with('status', $successMessage);
    }

    public function industries()
    {
        return view('industries');
    }

    public function privacyPolicy()
    {
        return view('privacy-policy');
    }

    private function offers(?string &$dbErrorMessage = null)
    {
        return $this->safeDb(function () {
            return DB::table('offers')
                ->select('id', 'title', 'body', 'image')
                ->orderBy('id')
                ->limit(3)
                ->get()
                ->map(function ($offer) {
                    $offer->image_path = $offer->image ? asset('images/offers/' . $offer->image) : asset('images/default-offer.png');
                    return $offer;
                });
        }, $dbErrorMessage);
    }

    private function industryListings(?string &$dbErrorMessage = null)
    {
        return $this->safeDb(function () {
            return DB::table('industry_listings')
                ->select('id', 'category', 'listing_image', 'body', 'pdf_url')
                ->orderByDesc('id')
                ->get()
                ->map(function ($listing) {
                    $listing->image_path = $listing->listing_image ? asset('images/case-study/' . $listing->listing_image) : asset('images/default-image.png');
                    $listing->pdf_link = $listing->pdf_url ? url('case_docs/' . $listing->pdf_url) : '#';
                    $listing->excerpt = Str::limit($listing->body ?? '', 150, '...');
                    return $listing;
                });
        }, $dbErrorMessage);
    }

    private function recentBlogs(?string &$dbErrorMessage = null)
    {
        return $this->safeDb(function () {
            return DB::table('blogs')
                ->select('blog_id', 'title', 'author', 'date', 'body', 'image_path')
                ->orderByDesc('id')
                ->limit(3)
                ->get()
                ->map(function ($blog) {
                    $blog->reading_time = $this->estimateReadingTime($blog->body ?? '');
                    return $blog;
                });
        }, $dbErrorMessage);
    }

    private function recentVideos(?string &$dbErrorMessage = null)
    {
        return $this->safeDb(function () {
            return DB::table('videos')
                ->select('url')
                ->orderByDesc('id')
                ->limit(3)
                ->get()
                ->map(function ($video) {
                    $video->video_id = $this->extractYouTubeId($video->url ?? '');
                    return $video;
                })
                ->filter(fn ($video) => !empty($video->video_id))
                ->values();
        }, $dbErrorMessage);
    }

    private function safeDb(callable $callback, ?string &$dbErrorMessage = null)
    {
        try {
            return $callback();
        } catch (\Throwable $e) {
            $dbErrorMessage = 'We are temporarily unable to load this content. Please try again in a few moments.';
            Log::warning('Database unavailable', ['error' => $e->getMessage()]);
            return collect();
        }
    }

    private function estimateReadingTime(string $html): int
    {
        $words = str_word_count(strip_tags($html));
        return (int) max(1, ceil($words / 200));
    }

    private function extractYouTubeId(string $html): string
    {
        if (preg_match('/src="([^"]+)"/', $html, $matches)) {
            $html = $matches[1];
        }

        $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/';
        if (preg_match($pattern, $html, $matches)) {
            return $matches[1];
        }

        return '';
    }

    private function verifyRecaptcha(string $token): bool
    {
        // Opt-in bypass for debugging/local scenarios
        if (env('RECAPTCHA_BYPASS', false)) {
            Log::warning('reCAPTCHA bypass enabled via RECAPTCHA_BYPASS. Skipping verification.');
            return true;
        }

        $secret = env('CAPTURE_SERVER_SIDE_KEY');

        // If no secret key is configured, skip verification (for testing/development)
        if (!$secret) {
            Log::warning('reCAPTCHA secret key not configured. Skipping verification.');
            return true; // Allow form submission for testing
        }

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => $secret,
            'response' => $token,
            'remoteip' => request()->ip(),
        ]);

        $success = $response->ok() && ($response->json('success') === true);
        
        if (!$success) {
            Log::warning('reCAPTCHA verification failed', [
                'response' => $response->json(),
                'token_length' => strlen($token),
            ]);
        }

        return $success;
    }

    private function notifyViaGraph(string $name, string $email, string $message, string $organization, string $phone, string $subject = ''): void
    {
        $tenantId = env('AZURE_TENANT_ID');
        $clientId = env('AZURE_CLIENT_ID');
        $clientSecret = env('AZURE_CLIENT_SECRET');
        $fromEmail = env('FROM_EMAIL');
        $adminEmail = env('ADMIN_EMAIL', $fromEmail);

        if (!$tenantId || !$clientId || !$clientSecret || !$fromEmail) {
            Log::warning('Graph email not sent: missing env configuration.');
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
                Log::error('Graph token request failed', ['response' => $tokenResponse->body()]);
                return;
            }

            $accessToken = $tokenResponse->json('access_token');
            if (!$accessToken) {
                Log::error('Graph token missing access_token', ['response' => $tokenResponse->json()]);
                return;
            }

            // Admin notification
            $adminBody = "<p><strong>New contact submission</strong></p>" .
                "<p><strong>Name:</strong> {$name}<br>" .
                "<strong>Email:</strong> {$email}<br>" .
                "<strong>Organization:</strong> {$organization}<br>" .
                "<strong>Phone:</strong> {$phone}<br>" .
                "<strong>Subject:</strong> {$subject}</p>" .
                "<p><strong>Message:</strong><br>" . nl2br(e($message)) . "</p>";

            $adminPayload = [
                'message' => [
                    'subject' => 'New contact form submission: ' . ($subject ?: 'No subject'),
                    'body' => [
                        'contentType' => 'HTML',
                        'content' => $adminBody,
                    ],
                    'toRecipients' => [
                        ['emailAddress' => ['address' => $adminEmail]],
                        ['emailAddress' => ['address' => 'ask.me@armely.com']],
                    ],
                    'ccRecipients' => [
                        ['emailAddress' => ['address' => 'ask.me@armely.com']],
                    ],
                ],
                'saveToSentItems' => true,
            ];

            Http::withToken($accessToken)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("https://graph.microsoft.com/v1.0/users/{$fromEmail}/sendMail", $adminPayload);

            // User confirmation
            $userBody = "<p>Dear {$name},</p><p>We’ve received your message and will get back to you soon.</p>" .
                "<p><strong>Your message:</strong><br>" . nl2br(e($message)) . "</p>" .
                "<p>Best regards,<br>Team Armely</p>";

            $userPayload = [
                'message' => [
                    'subject' => 'Thanks for contacting Armely',
                    'body' => [
                        'contentType' => 'HTML',
                        'content' => $userBody,
                    ],
                    'toRecipients' => [
                        ['emailAddress' => ['address' => $email]],
                    ],
                ],
                'saveToSentItems' => true,
            ];

            Http::withToken($accessToken)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("https://graph.microsoft.com/v1.0/users/{$fromEmail}/sendMail", $userPayload);
        } catch (\Throwable $e) {
            Log::error('Graph email send failed', ['error' => $e->getMessage()]);
        }
    }

    private function notifyConsultationViaGraph(string $name, string $email, string $organization, string $phone, string $serviceType, string $message): void
    {
        $tenantId = env('AZURE_TENANT_ID');
        $clientId = env('AZURE_CLIENT_ID');
        $clientSecret = env('AZURE_CLIENT_SECRET');
        $fromEmail = env('FROM_EMAIL');
        $adminEmail = env('ADMIN_EMAIL', $fromEmail);

        if (!$tenantId || !$clientId || !$clientSecret || !$fromEmail) {
            Log::warning('Consultation email not sent: missing env configuration.');
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
                Log::error('Consultation Graph token request failed', ['response' => $tokenResponse->body()]);
                return;
            }

            $accessToken = $tokenResponse->json('access_token');
            if (!$accessToken) {
                Log::error('Consultation Graph token missing access_token', ['response' => $tokenResponse->json()]);
                return;
            }

            $adminBody = "<p><strong>New consultation request</strong></p>" .
                "<p><strong>Name:</strong> {$name}<br>" .
                "<strong>Email:</strong> {$email}<br>" .
                "<strong>Organization:</strong> {$organization}<br>" .
                "<strong>Phone:</strong> {$phone}<br>" .
                "<strong>Service of interest:</strong> {$serviceType}</p>" .
                "<p><strong>Message:</strong><br>" . nl2br(e($message)) . "</p>";

            $adminPayload = [
                'message' => [
                    'subject' => 'New consultation request: ' . $serviceType,
                    'body' => [
                        'contentType' => 'HTML',
                        'content' => $adminBody,
                    ],
                    'toRecipients' => [
                        ['emailAddress' => ['address' => $adminEmail]],
                        ['emailAddress' => ['address' => 'ask.me@armely.com']],
                    ],
                    'ccRecipients' => [
                        ['emailAddress' => ['address' => 'ask.me@armely.com']],
                    ],
                ],
                'saveToSentItems' => true,
            ];

            Http::withToken($accessToken)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("https://graph.microsoft.com/v1.0/users/{$fromEmail}/sendMail", $adminPayload);

            $userBody = "<p>Dear {$name},</p><p>We’ve received your consultation request for <strong>{$serviceType}</strong>.</p>" .
                "<p><strong>Your message:</strong><br>" . nl2br(e($message)) . "</p>" .
                "<p>We will get back to you shortly.</p><p>Best regards,<br>Team Armely</p>";

            $userPayload = [
                'message' => [
                    'subject' => 'Thanks for reaching out to Armely',
                    'body' => [
                        'contentType' => 'HTML',
                        'content' => $userBody,
                    ],
                    'toRecipients' => [
                        ['emailAddress' => ['address' => $email]],
                    ],
                ],
                'saveToSentItems' => true,
            ];

            Http::withToken($accessToken)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("https://graph.microsoft.com/v1.0/users/{$fromEmail}/sendMail", $userPayload);
        } catch (\Throwable $e) {
            Log::error('Consultation Graph email send failed', ['error' => $e->getMessage()]);
        }
    }

    private function notifyJobApplicationViaGraph(array $payload, ?string $cvUrl = null): void
    {
        $tenantId = env('AZURE_TENANT_ID');
        $clientId = env('AZURE_CLIENT_ID');
        $clientSecret = env('AZURE_CLIENT_SECRET');
        $fromEmail = env('FROM_EMAIL');
        $adminEmail = env('ADMIN_EMAIL', $fromEmail);

        if (!$tenantId || !$clientId || !$clientSecret || !$fromEmail) {
            Log::warning('Job application email not sent: missing env configuration.');
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
                Log::error('Job application Graph token request failed', ['response' => $tokenResponse->body()]);
                return;
            }

            $accessToken = $tokenResponse->json('access_token');
            if (!$accessToken) {
                Log::error('Job application Graph token missing access_token', ['response' => $tokenResponse->json()]);
                return;
            }

            $cvSection = $cvUrl ? "<li><b>CV:</b> <a href='{$cvUrl}' target='_blank'>Download</a></li>" : '';
            $jobType = $payload['type'] ?? ($payload['role'] ?? '');

            $adminBody = "<p><strong>New Job Application Submitted</strong></p>" .
                "<ul>" .
                "<li><b>Name:</b> {$payload['name']}</li>" .
                "<li><b>Email:</b> {$payload['email']}</li>" .
                "<li><b>Phone:</b> {$payload['phone']}</li>" .
                "<li><b>City:</b> {$payload['city']}</li>" .
                "<li><b>Address:</b> {$payload['address']}</li>" .
                "<li><b>State:</b> {$payload['state']}</li>" .
                "<li><b>Zip:</b> {$payload['zip']}</li>" .
                "<li><b>Job Type:</b> {$jobType}</li>" .
                "<li><b>Position:</b> {$payload['position']}</li>" .
                "<li><b>Job ID:</b> {$payload['job_id']}</li>" .
                $cvSection .
                "</ul>";

            $adminPayload = [
                'message' => [
                    'subject' => 'New Job Application: ' . ($payload['position'] ?? 'Unknown Position'),
                    'body' => [
                        'contentType' => 'HTML',
                        'content' => $adminBody,
                    ],
                    'toRecipients' => [
                        ['emailAddress' => ['address' => $adminEmail]],
                        ['emailAddress' => ['address' => 'ask.me@armely.com']],
                    ],
                    'ccRecipients' => [
                        ['emailAddress' => ['address' => 'ask.me@armely.com']],
                    ],
                ],
                'saveToSentItems' => true,
            ];

            Http::withToken($accessToken)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("https://graph.microsoft.com/v1.0/users/{$fromEmail}/sendMail", $adminPayload);

            $userBody = "<p>Dear {$payload['name']},</p><p>Thank you for applying to Armely. We have received your application for <strong>{$payload['position']}</strong>.</p>" .
                ($cvUrl ? "<p>Your uploaded CV: <a href='{$cvUrl}' target='_blank'>Download</a></p>" : '') .
                "<p>Our team will review your CV and reach out if you are shortlisted.</p><p>Best regards,<br>HR Team</p>";

            $userPayload = [
                'message' => [
                    'subject' => 'Your Job Application at Armely',
                    'body' => [
                        'contentType' => 'HTML',
                        'content' => $userBody,
                    ],
                    'toRecipients' => [
                        ['emailAddress' => ['address' => $payload['email']]],
                    ],
                ],
                'saveToSentItems' => true,
            ];

            Http::withToken($accessToken)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("https://graph.microsoft.com/v1.0/users/{$fromEmail}/sendMail", $userPayload);
        } catch (\Throwable $e) {
            Log::error('Job application Graph email send failed', ['error' => $e->getMessage()]);
        }
    }
}
