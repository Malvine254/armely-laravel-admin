<?php

namespace App\Http\Controllers;

use App\Services\AzureMailService;
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
            'recaptchaSiteKey' => config('services.recaptcha.site_key', ''),
            'dbErrorMessage' => $dbErrorMessage,
        ]);
    }

    public function contact()
    {
        return view('contact', [
            'recaptchaSiteKey' => config('services.recaptcha.site_key', ''),
        ]);
    }

    public function contactThankYou()
    {
        return view('contact-thank-you');
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

        $portfolioItems = $this->safeDb(function () {
            if (!Schema::hasTable('company_portfolios')) {
                return collect();
            }

            return DB::table('company_portfolios')
                ->where('is_active', 1)
                ->orderBy('display_order')
                ->orderBy('id')
                ->get()
                ->map(function ($item) {
                    $features = [];
                    if (!empty($item->features)) {
                        $decoded = json_decode((string) $item->features, true);
                        if (is_array($decoded)) {
                            $features = array_values(array_filter(array_map('trim', $decoded)));
                        }
                    }

                    return (object) [
                        'title' => $item->title,
                        'category' => $item->category,
                        'short_description' => $item->short_description,
                        'long_description' => $item->long_description,
                        'features' => $features,
                        'logo_url' => !empty($item->logo_path) ? asset('storage/' . ltrim((string) $item->logo_path, '/')) : null,
                        'cta_label' => $item->cta_label,
                        'cta_url' => $item->cta_url,
                    ];
                })
                ->values();
        }, $dbErrorMessage);

        if ($portfolioItems->isEmpty()) {
            $portfolioItems = $this->defaultPortfolioItems();
        }

        $adBanners = $this->safeDb(function () {
            if (!Schema::hasTable('website_ad_banners')) {
                return collect();
            }

            return DB::table('website_ad_banners')
                ->where('is_active', 1)
                ->whereIn('page', ['company', 'global'])
                ->where(function ($query) {
                    $query->whereNull('starts_at')->orWhere('starts_at', '<=', now());
                })
                ->where(function ($query) {
                    $query->whereNull('ends_at')->orWhere('ends_at', '>=', now());
                })
                ->orderBy('display_order')
                ->orderByDesc('id')
                ->get()
                ->map(function ($banner) {
                    return (object) [
                        'headline' => $banner->headline,
                        'message' => $banner->message,
                        'button_label' => $banner->button_label,
                        'button_url' => $banner->button_url,
                        'background_style' => $banner->background_style,
                        'image_url' => !empty($banner->image_path) ? asset('storage/' . ltrim((string) $banner->image_path, '/')) : null,
                    ];
                })
                ->values();
        }, $dbErrorMessage);

        $coreValues = $this->safeDb(function () {
            $table = null;

            foreach (['core_values', 'core_value'] as $candidateTable) {
                if (Schema::hasTable($candidateTable)) {
                    $table = $candidateTable;
                    break;
                }
            }

            if (!$table) {
                return collect();
            }

            return DB::table($table)
                ->orderBy('id')
                ->get()
                ->map(function ($value) {
                    $title = $value->title ?? $value->name ?? $value->core_value_title ?? null;
                    $body = $value->body ?? $value->description ?? $value->content ?? null;
                    $icon = $value->icon_font ?? $value->icon ?? null;

                    return (object) [
                        'title' => $title,
                        'body' => $body,
                        'icon' => $icon,
                    ];
                })
                ->filter(fn ($value) => !empty($value->title) || !empty($value->body))
                ->values();
        }, $dbErrorMessage);

        if ($coreValues->isEmpty()) {
            $coreValues = $this->defaultCoreValues();
            $dbErrorMessage = null;
        }

        return view('company', [
            'portfolioItems' => $portfolioItems,
            'adBanners' => $adBanners,
            'coreValues' => $coreValues,
            'dbErrorMessage' => $dbErrorMessage,
        ]);
    }

    public function melaAi()
    {
        $dbErrorMessage = null;
        $demoVideos = $this->recentVideos($dbErrorMessage);

        return view('mela-ai', [
            'demoVideos' => $demoVideos,
            'dbErrorMessage' => $dbErrorMessage,
        ]);
    }

    public function invoiceLens()
    {
        return view('invoice-lens');
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
        $name = $this->normalizeServiceDetailSlug($name);

        // Freemiums is a valid service-details page backed by a dedicated partial.
        if (strtolower($name) === 'freemiums') {
            $dbErrorMessage = null;

            $freemiums = $this->safeDb(function () {
                return DB::table('freemium')
                    ->select('title', 'body', 'image_url', 'url_get_name', 'snippet')
                    ->orderByDesc('id')
                    ->get();
            }, $dbErrorMessage);

            return view('service-details', [
                'service' => (object) ['title' => 'Freemiums'],
                'relatedServices' => collect(),
                'freemiums' => $freemiums ?? collect(),
                'serviceName' => $name,
                'dbErrorMessage' => $dbErrorMessage,
                'recaptchaSiteKey' => config('services.recaptcha.site_key', ''),
            ]);
        }

        $dbErrorMessage = null;

        $service = $this->safeDb(function () use ($name) {
            return DB::table('services_lists')
                ->whereRaw("LOWER(REPLACE(title, ' ', '-')) = ?", [strtolower($name)])
                ->orWhere('title', $name)
                ->first();
        }, $dbErrorMessage);

        if (!$service) {
            $service = (object) [
                'id' => 0,
                'title' => Str::headline(str_replace('-', ' ', $name)),
                'body' => 'This service overview is temporarily unavailable, but our team can still help. Use the form below and we will follow up with the right specialist.',
                'image' => null,
            ];
        }

        $relatedServices = $this->safeDb(function () use ($service) {
            if (empty($service->id)) {
                return collect();
            }

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
            'recaptchaSiteKey' => config('services.recaptcha.site_key', ''),
        ]);
    }

    private function normalizeServiceDetailSlug(string $name): string
    {
        $slug = strtolower(trim($name));

        return [
            'fabric' => 'microsoft-fabric',
            'data-science' => 'data-science-and-analytics',
            'powerapps' => 'microsoft-powerapps',
            'power-apps' => 'microsoft-powerapps',
            'powerautomate' => 'microsoft-power-automate',
            'power-automate' => 'microsoft-power-automate',
            'dynamics365' => 'microsoft-dynamics-365',
            'dynamics-365' => 'microsoft-dynamics-365',
            'virtualagents' => 'microsoft-power-virtual-agents',
            'virtual-agents' => 'microsoft-power-virtual-agents',
            'roboticprocessing' => 'robotic-processing-automation',
            'robotic-process-automation' => 'robotic-processing-automation',
            'rpa' => 'robotic-processing-automation',
            'powerplatform' => 'microsoft-power-pages',
            'power-platform' => 'microsoft-power-pages',
            'sharepointonline' => 'sharepoint-online',
            'sharepoint' => 'sharepoint-online',
            'custom-development' => 'custom-development'
        ][$slug] ?? $name;
    }

    public function submitConsultation(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc,dns,filter', 'max:255'],
            'organization' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'service_type' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'website' => ['nullable', 'string', 'max:255'], // honeypot
            'g-recaptcha-response' => ['required', 'string'],
        ], [
            'name.required' => 'Name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Invalid email format.',
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

        if (!AzureMailService::isDeliverableEmail($email)) {
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => 'Please provide a valid email that can receive messages.'], 422)
                : back()->withErrors(['email' => 'Please provide a valid email that can receive messages.'])->withInput();
        }

        $now = Carbon::now()->format('Y-m-d H:i:s');

        $payload = [
            'name' => $data['name'],
            'email' => $email,
            'organization' => $data['organization'] ?? '',
            'phone' => $data['phone'] ?? '',
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
            $data['organization'] ?? '',
            $data['phone'] ?? '',
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

        if (!$job) {
            $job = (object) [
                'job_id' => $jobId,
                'job_type' => 'Career Opportunity',
                'job_title' => 'Career Opportunity',
                'job_location' => 'See current openings',
                'job_deadline' => null,
                'job_description' => '<p>We are temporarily unable to load this job description. Please visit the Careers page for current openings or contact Armely for more information.</p>',
            ];
        }

        // Check if job deadline has passed
        if ($job->job_deadline && strtotime($job->job_deadline) < time()) {
            return redirect()->route('career.index')->with('error', 'This job posting has expired and is no longer accepting applications.');
        }

        return view('job-board', [
            'job' => $job,
            'dbErrorMessage' => $dbErrorMessage,
        ]);
    }

    public function socialImpactDetails($secure_id)
    {
        $dbErrorMessage = null;

        $initiative = $this->safeDb(function () use ($secure_id) {
            return DB::table('social_impact')
                ->where('secure_id', $secure_id)
                ->first();
        }, $dbErrorMessage);

        if (!$initiative) {
            $initiative = (object) [
                'secure_id' => $secure_id,
                'title' => 'Social Impact Story',
                'category' => 'community',
                'posted_date' => now()->toDateString(),
                'image_url' => '',
                'body' => 'This story is temporarily unavailable while our live content connection is offline. Please check back shortly or return to the Social Impact page.',
            ];
        }

        // Fetch related stories (excluding the current one, limit to 3)
        $relatedStories = $this->safeDb(function () use ($secure_id) {
            return DB::table('social_impact')
                ->where('secure_id', '!=', $secure_id)
                ->orderBy('id', 'desc')
                ->limit(3)
                ->get();
        }, $dbErrorMessage);

        return view('social-impact-details', [
            'initiative' => $initiative,
            'relatedStories' => $relatedStories,
            'dbErrorMessage' => $dbErrorMessage,
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
            $dbErrorMessage = null;
            $job = $this->safeDb(function () use ($jobId) {
                return DB::table('career')->where('job_id', $jobId)->first();
            }, $dbErrorMessage);

            if (!$job) {
                $jobTitle = 'Selected Position';
            } else {
                $jobTitle = $job->job_title ?? $jobTitle;
            }
            
            // Check if job deadline has passed
            if ($job && $job->job_deadline && strtotime($job->job_deadline) < time()) {
                return redirect()->route('career.index')->with('error', 'This job posting has expired and is no longer accepting applications.');
            }
        } else {
            // If title provided but need to validate deadline
            $dbErrorMessage = null;
            $job = $this->safeDb(function () use ($jobId) {
                return DB::table('career')->where('job_id', $jobId)->first();
            }, $dbErrorMessage);

            if ($job && $job->job_deadline && strtotime($job->job_deadline) < time()) {
                return redirect()->route('career.index')->with('error', 'This job posting has expired and is no longer accepting applications.');
            }
        }

        // Do not hard-require application=true; just default to show form when job id exists
        return view('applications', [
            'jobId' => $jobId,
            'jobTitle' => $jobTitle,
            'applicationFlag' => $application,
            'recaptchaSiteKey' => config('services.recaptcha.site_key', ''),
        ]);
    }

    public function submitApplication(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc,dns,filter', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
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
        ], [
            'cv.required' => 'Please upload your CV.',
            'cv.mimes' => 'The cv field must be a file of type: pdf.',
            'cv.max' => 'The CV file may not be larger than 5MB.',
            'type.required' => 'Please select a job type.',
            'position.required' => 'Job position is required.',
            'g-recaptcha-response.required' => 'Please verify that you are not a robot.',
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

        $normalizedEmail = AzureMailService::normalizeEmail((string) ($data['email'] ?? ''));
        if (!AzureMailService::isDeliverableEmail($normalizedEmail)) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Please provide a valid email that can receive messages.'], 422);
            }
            return back()->withErrors(['email' => 'Please provide a valid email that can receive messages.'])->withInput();
        }

        $cvPath = null;
        $cvUrl = null;
        if ($request->hasFile('cv')) {
            $cvFile = $request->file('cv');
            
            // Validate file exists and is readable
            if (!$cvFile->isValid()) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'CV file is invalid. Please try uploading again.'], 422);
                }
                return back()->withErrors(['cv' => 'CV file is invalid. Please try uploading again.'])->withInput();
            }
            
            // Additional validation for PDF
            if ($cvFile->getMimeType() !== 'application/pdf') {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'The cv field must be a file of type: pdf.'], 422);
                }
                return back()->withErrors(['cv' => 'The cv field must be a file of type: pdf.'])->withInput();
            }
            
            // Validate file size (max 10MB)
            if ($cvFile->getSize() > 10 * 1024 * 1024) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'CV file must not exceed 10MB.'], 422);
                }
                return back()->withErrors(['cv' => 'CV file must not exceed 10MB.'])->withInput();
            }
            
            // Store the file with error handling
            try {
                $cvPath = $cvFile->store('cv_uploads', 'public');
                
                // Verify file was actually stored
                if (!$cvPath) {
                    throw new \Exception('File storage returned empty path');
                }
                
                // Verify file exists at the expected location
                $fullPath = storage_path('app/public/' . $cvPath);
                if (!file_exists($fullPath)) {
                    Log::error('CV file not found after upload', [
                        'expected_path' => $fullPath,
                        'cv_path' => $cvPath,
                        'file_name' => $cvFile->getClientOriginalName(),
                    ]);
                    throw new \Exception('File was not stored correctly in the file system');
                }
                
                // Generate accessible URL
                $cvUrl = asset('storage/' . $cvPath);
                
                Log::info('CV file uploaded successfully', [
                    'cv_path' => $cvPath,
                    'full_path' => $fullPath,
                    'file_size' => $cvFile->getSize(),
                    'original_name' => $cvFile->getClientOriginalName(),
                    'url' => $cvUrl,
                ]);
            } catch (\Exception $e) {
                Log::error('CV file upload failed', [
                    'error' => $e->getMessage(),
                    'file_name' => $cvFile->getClientOriginalName(),
                    'file_size' => $cvFile->getSize(),
                ]);
                
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Failed to upload CV file. ' . $e->getMessage()], 500);
                }
                return back()->withErrors(['cv' => 'Failed to upload CV file. Please try again.'])->withInput();
            }
        }

        $applicationDate = Carbon::now();
        $roleValue = strtolower($data['type']);

        // Store values needed for email notification before building DB payload
        $emailData = [
            'name' => $data['name'],
            'email' => $normalizedEmail,
            'phone' => $data['phone'] ?? '',
            'city' => $data['city'],
            'address' => $data['address'],
            'state' => $data['state'],
            'zip' => $data['zip'],
            'type' => $data['type'],
            'position' => $data['position'],
            'job_id' => $data['job_id'],
        ];

        // Build payload with flexible column mapping
        $payload = [
            'name' => $data['name'],
            'email' => $normalizedEmail,
            'city' => $data['city'],
            'phone' => $data['phone'] ?? '',
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
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to submit application. Please try again.'], 500);
            }
            return back()->withErrors(['form' => 'Failed to submit application. Please try again.'])->withInput();
        }

        $this->notifyJobApplicationViaGraph($emailData, $cvUrl);
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Application submitted successfully!']);
        }

        return redirect()->route('career.index')->with('success', 'Application submitted successfully!');
    }

    public function submitContact(Request $request)
    {
        try {
            // Normalize common user input quirks before validation.
            $normalizedEmail = preg_replace('/\s+/', '', trim((string) $request->input('email', '')));
            if (Str::startsWith(strtolower($normalizedEmail), 'mailto:')) {
                $normalizedEmail = substr($normalizedEmail, 7);
            }
            $request->merge(['email' => $normalizedEmail]);

            $data = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email:filter', 'max:255'],
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

            if (!AzureMailService::isDeliverableEmail($email)) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Please provide a valid email that can receive messages.'], 422);
                }
                return back()->withErrors(['email' => 'Please provide a valid email that can receive messages.'])->withInput();
            }

            $now = now()->format('Y-m-d H:i:s');

            DB::table('contacts')->insert([
                'name' => $data['name'],
                'email' => $email,
                'organization' => $data['organization'] ?? '',
                'phone' => $data['phone'] ?? '',
                'message' => $data['message'],
                'subject' => $data['subject'] ?? '',
                'sent_date' => $now,
            ]);

            try {
                $this->notifyViaGraph($data['name'], $email, $data['message'], $data['organization'] ?? '', $data['phone'] ?? '', $data['subject'] ?? '');
            } catch (\Exception $e) {
                Log::warning('Failed to send notification email', ['error' => $e->getMessage()]);
                // Continue anyway, the contact was saved
            }

            $successMessage = 'Your message has been sent successfully. We will contact you soon.';
            
            // Generate unique token for thank you page access
            $thankYouToken = bin2hex(random_bytes(16));
            session(['contact_thank_you_token' => $thankYouToken, 'contact_thank_you_time' => time()]);
            
            // Force save the session immediately
            session()->save();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true, 
                    'message' => $successMessage,
                    'redirect_url' => '/contact/thank-you?token=' . $thankYouToken
                ]);
            }
            return back()->with('status', $successMessage);
        } catch (\Throwable $e) {
            Log::error('Contact form submission failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'An error occurred. Please try again later.'], 500);
            }
            return back()->withErrors(['form' => 'An error occurred. Please try again later.'])->withInput();
        }
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
            $query = DB::table('industry_listings')
                ->select('id', 'category', 'listing_image', 'body', 'pdf_url')
                ->orderByDesc('id');

            if (Schema::hasColumn('industry_listings', 'title')) {
                $query->addSelect('title');
            }

            return $query
                ->get()
                ->map(function ($listing) {
                    $title = trim((string) ($listing->title ?? ''));
                    $category = trim((string) ($listing->category ?? 'Case Study'));
                    $displayTitle = $title !== ''
                        ? $title
                        : (Str::endsWith(Str::lower($category), 'case study') ? $category : $category . ' Case Study');

                    $listing->image_path = $listing->listing_image ? asset('images/case-study/' . $listing->listing_image) : asset('images/default-image.png');
                    $listing->pdf_link = $listing->pdf_url ? url('case_docs/' . $listing->pdf_url) : '#';
                    $listing->excerpt = $this->makePreviewText((string) ($listing->body ?? ''), 150);
                    $listing->slug = Str::slug($displayTitle) ?: 'case-study-' . (string) ($listing->id ?? 'resource');
                    return $listing;
                });
        }, $dbErrorMessage);
    }

    private function recentBlogs(?string &$dbErrorMessage = null)
    {
        return $this->safeDb(function () {
            $blogTable = $this->resolveBlogTable();
            if (!$blogTable) {
                return collect();
            }

            $blogIdColumn = $this->firstExistingColumn($blogTable, ['blog_id', 'id']);
            $titleColumn = $this->firstExistingColumn($blogTable, ['title', 'blog_title']);
            $authorColumn = $this->firstExistingColumn($blogTable, ['author', 'blog_author']);
            $dateColumn = $this->firstExistingColumn($blogTable, ['date', 'blog_date', 'created_at']);
            $bodyColumn = $this->firstExistingColumn($blogTable, ['body', 'description', 'content']);
            $imageColumn = $this->firstExistingColumn($blogTable, ['image_path', 'image', 'image_url']);
            $orderColumn = $this->firstExistingColumn($blogTable, ['id', 'blog_id', 'created_at']) ?? $blogIdColumn;
            $authorImageMap = $this->resolveAuthorImageMap();

            return DB::table($blogTable)
                ->selectRaw(($blogIdColumn ? $blogTable . '.' . $blogIdColumn : 'NULL') . ' as blog_id')
                ->selectRaw(($titleColumn ? $blogTable . '.' . $titleColumn : 'NULL') . ' as title')
                ->selectRaw(($authorColumn ? $blogTable . '.' . $authorColumn : 'NULL') . ' as author')
                ->selectRaw(($dateColumn ? $blogTable . '.' . $dateColumn : 'NULL') . ' as date')
                ->selectRaw(($bodyColumn ? $blogTable . '.' . $bodyColumn : 'NULL') . ' as body')
                ->selectRaw(($imageColumn ? $blogTable . '.' . $imageColumn : 'NULL') . ' as image_path')
                ->orderByDesc($blogTable . '.' . $orderColumn)
                ->limit(3)
                ->get()
                ->map(function ($blog) use ($authorImageMap) {
                    $blog->author_image = $this->resolveAuthorImageForName((string) ($blog->author ?? ''), $authorImageMap);
                    $blog->reading_time = $this->estimateReadingTime($blog->body ?? '');
                    $blog->preview = $this->makePreviewText((string) ($blog->body ?? ''), 150);
                    return $blog;
                });
        }, $dbErrorMessage);
    }

    private function resolveBlogTable(): ?string
    {
        foreach (['blogs', 'blog'] as $table) {
            if (Schema::hasTable($table)) {
                return $table;
            }
        }

        return null;
    }

    private function firstExistingColumn(string $table, array $columns): ?string
    {
        foreach ($columns as $column) {
            if (Schema::hasColumn($table, $column)) {
                return $column;
            }
        }

        return null;
    }

    private function resolveAuthorImageMap(): array
    {
        if (!Schema::hasTable('team') || !Schema::hasColumn('team', 'team_name') || !Schema::hasColumn('team', 'team_image')) {
            return [];
        }

        return DB::table('team')
            ->whereNotNull('team_name')
            ->get(['team_name', 'team_image'])
            ->reduce(function (array $map, object $member) {
                $name = trim((string) ($member->team_name ?? ''));
                $image = trim((string) ($member->team_image ?? ''));

                if ($name === '' || $image === '') {
                    return $map;
                }

                $filename = basename($image);
                if (!is_file(public_path('images/team/' . $filename))) {
                    return $map;
                }

                $map[$name] = $filename;
                $map[Str::lower($name)] = $filename;

                return $map;
            }, []);
    }

    private function resolveAuthorImageForName(string $authorName, array $authorImageMap): ?string
    {
        $authorName = trim($authorName);
        if ($authorName === '') {
            return null;
        }

        return $authorImageMap[$authorName] ?? $authorImageMap[Str::lower($authorName)] ?? null;
    }

    private function makePreviewText(string $html, int $limit = 150): string
    {
        // Remove script/style blocks completely so their content never leaks into snippets.
        $withoutBlocks = preg_replace('/<\s*(script|style)\b[^>]*>.*?<\s*\/\s*\1\s*>/is', ' ', $html) ?? $html;

        $plainText = html_entity_decode(strip_tags($withoutBlocks), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $normalized = preg_replace('/\s+/u', ' ', $plainText) ?? $plainText;

        return Str::limit(trim($normalized), $limit, '...');
    }

    private function recentVideos(?string &$dbErrorMessage = null)
    {
        return $this->safeDb(function () {
            $videoTable = Schema::hasTable('videos') ? 'videos' : (Schema::hasTable('video') ? 'video' : null);

            if (!$videoTable) {
                return collect();
            }

            $selectColumns = ['url'];

            if (Schema::hasColumn($videoTable, 'video_title')) {
                $selectColumns[] = 'video_title';
            }

            if (Schema::hasColumn($videoTable, 'title')) {
                $selectColumns[] = 'title';
            }

            if (Schema::hasColumn($videoTable, 'video_name')) {
                $selectColumns[] = 'video_name';
            }

            return DB::table($videoTable)
                ->select($selectColumns)
                ->orderByDesc('id')
                ->limit(3)
                ->get()
                ->map(function ($video) {
                    $video->video_id = $this->extractYouTubeId($video->url ?? '');
                    $video->video_title = trim((string) ($video->video_title ?? $video->title ?? $video->video_name ?? ''));
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

    private function defaultCoreValues()
    {
        return collect([
            (object) [
                'title' => 'Integrity',
                'body' => 'We operate with transparency, honesty, and accountability in every engagement.',
                'icon' => 'ui-check',
            ],
            (object) [
                'title' => 'Innovation',
                'body' => 'We continuously adopt modern technologies and practical ideas to solve business challenges.',
                'icon' => 'light-bulb',
            ],
            (object) [
                'title' => 'Customer Success',
                'body' => 'We align every solution to measurable client outcomes and long-term value.',
                'icon' => 'users-alt-5',
            ],
        ]);
    }

    private function defaultPortfolioItems()
    {
        return collect([
            (object) [
                'title' => 'Mela - Your AI CoPilot',
                'category' => 'AI & Machine Learning',
                'short_description' => "Mela represents Armely's AI experience for demonstrating how intelligent copilots and automation can be embedded into modern business workflows.",
                'long_description' => 'It showcases practical delivery patterns from Copilot Studio use cases to Azure OpenAI and enterprise AI governance.',
                'features' => [
                    'Copilot Studio development',
                    'Retrieval-Augmented Generation (RAG)',
                    'Natural Language Processing (NLP)',
                    'AI governance and security',
                    'Azure OpenAI integration',
                ],
                'logo_url' => asset('images/logo/mela-logo.jpg'),
                'cta_label' => 'Explore Mela',
                'cta_url' => '/mela-ai',
            ],
            (object) [
                'title' => 'Step & Sip - Data-Driven Coffee',
                'category' => 'Data Analytics & BI',
                'short_description' => "Step & Sip represents Armely's analytics experience, showing how modern retail operations can be improved through connected insights and automation.",
                'long_description' => 'We demonstrate how coffee and data blend through real-time insights powered by Microsoft Fabric and Power Platform.',
                'features' => [
                    'Microsoft Fabric Lakehouse architecture',
                    'Power BI dashboards and insights',
                    'Customer segmentation and behavior',
                    'Inventory and sales forecasting',
                    'Workflow automation with Power Automate',
                ],
                'logo_url' => asset('images/logo/logo-step.png'),
                'cta_label' => 'Visit Experience',
                'cta_url' => '/store',
            ],
        ]);
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
        // Log the token length for debugging
        Log::debug('reCAPTCHA verification attempt', [
            'token_length' => strlen($token),
            'token_empty' => empty($token),
        ]);

        // Opt-in bypass for debugging/local scenarios
        if (config('services.recaptcha.bypass', false)) {
            Log::warning('reCAPTCHA bypass enabled via RECAPTCHA_BYPASS. Skipping verification.');
            return true;
        }

        $secret = config('services.recaptcha.secret_key');

        // If no secret key is configured, skip verification (for testing/development)
        if (!$secret) {
            Log::warning('reCAPTCHA secret key not configured. Skipping verification.');
            return true; // Allow form submission for testing
        }

        // Validate token is not empty
        if (empty($token)) {
            Log::error('reCAPTCHA token is empty', [
                'request_ip' => request()->ip(),
            ]);
            return false;
        }

        try {
            $response = Http::timeout(10)->asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secret,
                'response' => $token,
                'remoteip' => request()->ip(),
            ]);

            $responseData = $response->json();
            $success = $response->ok() && ($responseData['success'] === true);
            
            Log::info('reCAPTCHA verification response', [
                'success' => $success,
                'score' => $responseData['score'] ?? null,
                'action' => $responseData['action'] ?? null,
                'challenge_ts' => $responseData['challenge_ts'] ?? null,
                'hostname' => $responseData['hostname'] ?? null,
                'error-codes' => $responseData['error-codes'] ?? [],
                'request_ip' => request()->ip(),
            ]);
            
            if (!$success) {
                Log::warning('reCAPTCHA verification failed', [
                    'full_response' => $responseData,
                    'token_length' => strlen($token),
                    'status_code' => $response->status(),
                ]);
            }

            return $success;
        } catch (\Exception $e) {
            Log::error('reCAPTCHA verification exception', [
                'error' => $e->getMessage(),
                'exception' => get_class($e),
                'token_length' => strlen($token),
            ]);
            return false;
        }
    }

    private function notifyViaGraph(string $name, string $email, string $message, string $organization, string $phone, string $subject = ''): void
    {
        $tenantId = env('AZURE_TENANT_ID');
        $clientId = env('AZURE_CLIENT_ID');
        $clientSecret = env('AZURE_CLIENT_SECRET');
        $fromEmail = AzureMailService::outboundFromEmail();
        $adminEmail = env('ADMIN_EMAIL', $fromEmail);
        $replyTo = AzureMailService::graphReplyToRecipients();

        if (!$tenantId || !$clientId || !$clientSecret || !$fromEmail) {
            Log::warning('Graph email not sent: missing env configuration.');
            return;
        }

        if (!AzureMailService::isDeliverableEmail((string) $adminEmail)) {
            Log::warning('Graph admin email skipped: undeliverable admin address', ['email' => $adminEmail]);
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
            $adminBody = view('emails.contact.admin-notification', [
                'name' => $name,
                'email' => $email,
                'organization' => $organization,
                'phone' => $phone,
                'subject' => $subject,
                'message' => $message,
            ])->render();

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

            if ($replyTo !== []) {
                $adminPayload['message']['replyTo'] = $replyTo;
            }

            Http::withToken($accessToken)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("https://graph.microsoft.com/v1.0/users/{$fromEmail}/sendMail", $adminPayload);

            // User confirmation
            $userBody = view('emails.contact.user-confirmation', [
                'name' => $name,
                'message' => $message,
            ])->render();

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

            if ($replyTo !== []) {
                $userPayload['message']['replyTo'] = $replyTo;
            }

            if (AzureMailService::isDeliverableEmail($email)) {
                Http::withToken($accessToken)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post("https://graph.microsoft.com/v1.0/users/{$fromEmail}/sendMail", $userPayload);
            } else {
                Log::warning('Contact user confirmation email skipped: undeliverable address', ['email' => $email]);
            }
        } catch (\Throwable $e) {
            Log::error('Graph email send failed', ['error' => $e->getMessage()]);
        }
    }

    private function notifyConsultationViaGraph(string $name, string $email, string $organization, string $phone, string $serviceType, string $message): void
    {
        $tenantId = env('AZURE_TENANT_ID');
        $clientId = env('AZURE_CLIENT_ID');
        $clientSecret = env('AZURE_CLIENT_SECRET');
        $fromEmail = AzureMailService::outboundFromEmail();
        $adminEmail = env('ADMIN_EMAIL', $fromEmail);
        $replyTo = AzureMailService::graphReplyToRecipients();

        if (!$tenantId || !$clientId || !$clientSecret || !$fromEmail) {
            Log::warning('Consultation email not sent: missing env configuration.');
            return;
        }

        if (!AzureMailService::isDeliverableEmail((string) $adminEmail)) {
            Log::warning('Consultation admin email skipped: undeliverable admin address', ['email' => $adminEmail]);
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

            $adminBody = view('emails.consultation.admin-notification', [
                'name' => $name,
                'email' => $email,
                'organization' => $organization,
                'phone' => $phone,
                'serviceType' => $serviceType,
                'message' => $message,
            ])->render();

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

            if ($replyTo !== []) {
                $adminPayload['message']['replyTo'] = $replyTo;
            }

            Http::withToken($accessToken)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("https://graph.microsoft.com/v1.0/users/{$fromEmail}/sendMail", $adminPayload);

            $userBody = view('emails.consultation.user-confirmation', [
                'name' => $name,
                'serviceType' => $serviceType,
                'message' => $message,
            ])->render();

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

            if ($replyTo !== []) {
                $userPayload['message']['replyTo'] = $replyTo;
            }

            if (AzureMailService::isDeliverableEmail($email)) {
                Http::withToken($accessToken)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post("https://graph.microsoft.com/v1.0/users/{$fromEmail}/sendMail", $userPayload);
            } else {
                Log::warning('Consultation user confirmation email skipped: undeliverable address', ['email' => $email]);
            }
        } catch (\Throwable $e) {
            Log::error('Consultation Graph email send failed', ['error' => $e->getMessage()]);
        }
    }

    private function notifyJobApplicationViaGraph(array $payload, ?string $cvUrl = null): void
    {
        $tenantId = env('AZURE_TENANT_ID');
        $clientId = env('AZURE_CLIENT_ID');
        $clientSecret = env('AZURE_CLIENT_SECRET');
        $fromEmail = AzureMailService::outboundFromEmail();
        $adminEmail = env('ADMIN_EMAIL', $fromEmail);
        $replyTo = AzureMailService::graphReplyToRecipients();

        if (!$tenantId || !$clientId || !$clientSecret || !$fromEmail) {
            Log::warning('Job application email not sent: missing env configuration.');
            return;
        }

        if (!AzureMailService::isDeliverableEmail((string) $adminEmail)) {
            Log::warning('Job application admin email skipped: undeliverable admin address', ['email' => $adminEmail]);
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
            $jobType = $payload['type'] ?? ($payload['role'] ?? 'Not specified');
            $jobId = $payload['job_id'] ?? 'Not specified';

            // Ensure the CV URL is accessible (full URL with correct path)
            $cvUrlForEmail = $cvUrl;
            if ($cvUrl && !str_starts_with($cvUrl, 'http')) {
                // If it's a relative URL, ensure it's properly formatted
                $cvUrlForEmail = rtrim(env('APP_URL', 'https://armely.com'), '/') . '/' . ltrim($cvUrl, '/');
            }
            
            $cvSectionForEmail = $cvUrlForEmail ? "<p><b>Your uploaded CV:</b> <a href='{$cvUrlForEmail}' target='_blank'>Download</a></p>" : '';

            $adminBody = view('emails.jobs.admin-application-notification', [
                'name' => (string) ($payload['name'] ?? ''),
                'email' => (string) ($payload['email'] ?? ''),
                'phone' => (string) ($payload['phone'] ?? ''),
                'city' => (string) ($payload['city'] ?? ''),
                'address' => (string) ($payload['address'] ?? ''),
                'state' => (string) ($payload['state'] ?? ''),
                'zip' => (string) ($payload['zip'] ?? ''),
                'jobType' => (string) $jobType,
                'position' => (string) ($payload['position'] ?? ''),
                'jobId' => (string) $jobId,
                'cvUrl' => (string) ($cvUrlForEmail ?? ''),
            ])->render();

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

            if ($replyTo !== []) {
                $adminPayload['message']['replyTo'] = $replyTo;
            }

            Http::withToken($accessToken)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("https://graph.microsoft.com/v1.0/users/{$fromEmail}/sendMail", $adminPayload);

            $userBody = view('emails.jobs.user-application-confirmation', [
                'name' => (string) ($payload['name'] ?? 'Candidate'),
                'position' => (string) ($payload['position'] ?? 'the selected role'),
                'jobId' => (string) $jobId,
            ])->render();

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

            if ($replyTo !== []) {
                $userPayload['message']['replyTo'] = $replyTo;
            }

            if (AzureMailService::isDeliverableEmail((string) ($payload['email'] ?? ''))) {
                Http::withToken($accessToken)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post("https://graph.microsoft.com/v1.0/users/{$fromEmail}/sendMail", $userPayload);
            } else {
                Log::warning('Job application user confirmation email skipped: undeliverable address', [
                    'email' => $payload['email'] ?? null,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Job application Graph email send failed', ['error' => $e->getMessage()]);
        }
    }
}
