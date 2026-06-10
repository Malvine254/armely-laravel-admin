<?php

namespace App\Http\Controllers;

use App\Services\AzureMailService;
use Illuminate\Database\QueryException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class CaseStudiesController extends Controller
{
    public function index(Request $request)
    {
        // Paginate case studies (6 per page)
        $caseStudies = $this->paginateCaseStudies($request);

        if ($redirect = $this->redirectIfOutOfRangePage($request, $caseStudies, 'case_page')) {
            return $redirect;
        }

        $caseStudies->getCollection()->transform(function ($caseStudy) {
            $caseStudy->preview = $this->makePreviewText((string) ($caseStudy->body ?? ''), 120);
            $caseStudy->slug = $this->caseStudySlug($caseStudy);
            $caseStudy->industry_filter = $this->inferIndustryFilter($caseStudy);
            $caseStudy->technology_filters = $this->inferTechnologyFilters($caseStudy);
            $caseStudy->results = $this->caseStudyResults($caseStudy);

            return $caseStudy;
        });

        // Paginate white papers (6 per page)
        $whitePapers = $this->paginateWhitePapers($request);

        if ($redirect = $this->redirectIfOutOfRangePage($request, $whitePapers, 'paper_page')) {
            return $redirect;
        }

        $whitePapers->getCollection()->transform(function ($paper) {
            $paper->preview = $this->makePreviewText((string) ($paper->body ?? ''), 120);
            $paper->slug = $this->resourceSlug($paper);

            return $paper;
        });

        return view('case-studies.index', [
            'caseStudies' => $caseStudies,
            'whitePapers' => $whitePapers,
            'recaptchaSiteKey' => config('services.recaptcha.site_key', ''),
            'selectedIndustry' => (string) ($request->query('case_industry', $request->query('industry', ''))),
            'selectedTopic' => (string) $request->query('case_topic', $request->query('topic', '')),
            'selectedWhiteTopic' => (string) $request->query('white_topic', ''),
            'industryFilters' => $this->industryFilters(),
            'topicFilters' => $this->topicFilters(),
            'portfolioStats' => $this->portfolioStats(),
        ]);
    }

    public function showCaseStudy(Request $request, string $slug)
    {
        $caseStudy = $this->findCaseStudyBySlug($slug);
        if (!$caseStudy) {
            abort(404);
        }

        $caseStudy->preview = $this->makePreviewText((string) ($caseStudy->body ?? ''), 320);
        $caseStudy->slug = $this->caseStudySlug($caseStudy);
        $caseStudy->display_title = $this->caseStudyDisplayTitle($caseStudy);
        $caseStudy->industry_filter = $this->inferIndustryFilter($caseStudy);
        $caseStudy->technology_filters = $this->inferTechnologyFilters($caseStudy);
        $caseStudy->technology_label = $this->topicFilters()[$caseStudy->technology_filters[0] ?? ''] ?? 'Microsoft Platform';
        $caseStudy->results = $this->caseStudyResults($caseStudy);
        $caseStudy->services = $this->caseStudyServices($caseStudy);

        return view('case-studies.show', [
            'caseStudy' => $caseStudy,
            'relatedCaseStudies' => $this->relatedCaseStudies($caseStudy),
            'recaptchaSiteKey' => config('services.recaptcha.site_key', ''),
            'metaDescription' => $this->caseStudyMetaDescription($caseStudy),
        ]);
    }

    public function showResource(Request $request, string $slug)
    {
        $paper = $this->staticResourceBySlug($slug) ?? $this->findWhitePaperBySlug($slug);
        if (!$paper) {
            abort(404);
        }

        $paper->preview = $this->makePreviewText((string) ($paper->body ?? $paper->preview ?? ''), 320);
        $paper->slug = $slug;

        return view('case-studies.resource-show', [
            'paper' => $paper,
            'recaptchaSiteKey' => config('services.recaptcha.site_key', ''),
            'metaDescription' => (string) ($paper->meta_description ?? $this->whitePaperMetaDescription($paper)),
        ]);
    }

    public function submitLead(Request $request)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', $this->emailValidationRule(), 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'organization' => ['nullable', 'string', 'max:255'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:120'],
            'interest' => ['required', 'in:case-studies,white-papers,both'],
            'message' => ['nullable', 'string'],
            'website' => ['nullable', 'string', 'max:255'],
            'requested_resource' => ['nullable', 'string', 'max:255'],
            'case_study_id' => ['nullable', 'integer'],
            'white_paper_id' => ['nullable', 'integer'],
            'g-recaptcha-response' => ['required', 'string'],
        ];

        if ($this->isTableQueryable('industry_listings')) {
            $rules['case_study_id'][] = 'exists:industry_listings,id';
        }

        if ($this->isTableQueryable('white_paper')) {
            $rules['white_paper_id'][] = 'exists:white_paper,id';
        }

        $data = $request->validate($rules, [
            'name.required' => 'Name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Please enter a valid work email with a valid domain.',
            'interest.required' => 'Please select what you are interested in.',
            'g-recaptcha-response.required' => 'Please verify that you are not a robot.',
        ]);

        if (!empty($data['website'])) {
            return $this->leadErrorResponse($request, ['form' => ['Spam detected.']]);
        }

        if (!$this->verifyRecaptcha($data['g-recaptcha-response'])) {
            return $this->leadErrorResponse($request, ['captcha' => ['reCAPTCHA verification failed. Please try again.']]);
        }

        $normalizedEmail = strtolower(trim((string) ($data['email'] ?? '')));
        if (!$this->isDeliverableEmail($normalizedEmail)) {
            return $this->leadErrorResponse($request, ['email' => ['Please provide a valid business email that can receive messages.']]);
        }

        $downloadDetails = $this->buildDownloadDetails($data, $normalizedEmail);
        if ($downloadDetails === null) {
            return $this->leadErrorResponse($request, ['resource' => ['The requested resource is unavailable. Please choose another resource.']]);
        }

        $interestLabel = match ($data['interest']) {
            'case-studies' => 'Case Studies',
            'white-papers' => 'White Papers',
            default => 'Case Studies & White Papers',
        };

        $notes = trim((string) ($data['message'] ?? ''));
        $subject = 'Case Studies & White Papers Request';

        $composedMessage =
            "Interest: {$interestLabel}\n" .
            "Requested Resource: " . ($data['requested_resource'] ?? 'N/A') . "\n" .
            "Requested Case Study ID: " . ($data['case_study_id'] ?? 'N/A') . "\n" .
            "Requested White Paper ID: " . ($data['white_paper_id'] ?? 'N/A') . "\n" .
            "Job Title: " . ($data['job_title'] ?? '') . "\n" .
            "Country/Region: " . ($data['country'] ?? '') . "\n" .
            "Lead Source: Case Studies Modal\n\n" .
            "Additional Notes:\n" . ($notes !== '' ? $notes : 'N/A');

        DB::table('case_study_lead_requests')->insert([
            'name' => $data['name'],
            'email' => $normalizedEmail,
            'phone' => $data['phone'] ?? '',
            'organization' => $data['organization'] ?? '',
            'job_title' => $data['job_title'] ?? null,
            'country' => $data['country'] ?? null,
            'interest' => $data['interest'],
            'requested_resource' => $data['requested_resource'] ?? null,
            'case_study_id' => $data['case_study_id'] ?? null,
            'white_paper_id' => $data['white_paper_id'] ?? null,
            'message' => $notes !== '' ? $notes : null,
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 1000),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('contacts')->insert([
            'name' => $data['name'],
            'email' => $normalizedEmail,
            'organization' => $data['organization'] ?? '',
            'phone' => $data['phone'] ?? '',
            'message' => $composedMessage,
            'subject' => $subject,
            'sent_date' => now()->format('Y-m-d H:i:s'),
        ]);

        $emailSent = $this->sendCaseStudyLeadEmail([
            'name' => $data['name'],
            'email' => $normalizedEmail,
            'phone' => $data['phone'] ?? '',
            'organization' => $data['organization'] ?? '',
            'job_title' => $data['job_title'] ?? '',
            'country' => $data['country'] ?? '',
            'interest' => $interestLabel,
            'requested_resource' => $data['requested_resource'] ?? '',
            'case_study_id' => (string) ($data['case_study_id'] ?? ''),
            'white_paper_id' => (string) ($data['white_paper_id'] ?? ''),
            'message' => $notes,
            'resource_title' => $downloadDetails['resource_title'],
            'resource_type_label' => $downloadDetails['resource_type_label'],
            'download_url' => $downloadDetails['download_url'],
            'expires_at' => $downloadDetails['expires_at'],
        ]);

        $message = $emailSent
            ? 'Thanks! Your secure download link has been sent by email. It expires in 1 hour.'
            : 'Request received. We could not confirm email delivery right now, so use the secure link below. It expires in 1 hour.';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'ok' => true,
                'message' => $message,
                'email_sent' => $emailSent,
                'download_url' => $downloadDetails['download_url'],
                'expires_at' => $downloadDetails['expires_at'],
            ]);
        }

        return back()->with('status', $message);
    }

    public function accessCaseStudy(Request $request, int $caseStudy)
    {
        if ($normalized = $this->normalizeAmpEncodedSignatureQuery($request)) {
            return redirect()->to($normalized);
        }

        if (!$request->hasValidSignature()) {
            return redirect()->route('case-studies.index')
                ->withErrors(['access' => 'This download link is invalid or has expired. Please request a new one.']);
        }

        if (!$this->isTableQueryable('industry_listings')) {
            return redirect()->route('case-studies.index')
                ->withErrors(['access' => 'Case studies are temporarily unavailable.']);
        }

        $item = DB::table('industry_listings')
            ->select('id', 'pdf_url')
            ->where('id', $caseStudy)
            ->first();

        if (!$item || empty($item->pdf_url)) {
            Log::warning('Case study secure access failed: missing record or pdf_url', [
                'case_study_id' => $caseStudy,
            ]);

            return redirect()->route('case-studies.index')
                ->withErrors(['access' => 'This file could not be located. Please request a new secure download link.']);
        }

        $pdfUrl = (string) $item->pdf_url;
        if (str_starts_with($pdfUrl, 'http://') || str_starts_with($pdfUrl, 'https://')) {
            return $this->downloadRemotePdf($pdfUrl, 'case-study-' . $caseStudy . '.pdf');
        }

        $fileName = basename($pdfUrl);
        $candidatePaths = [
            storage_path('app/private/case_docs/' . $fileName),
            public_path('case_docs/' . $fileName),
            // Backward compatibility for older uploads saved under /public/admin/case_docs.
            public_path('admin/case_docs/' . $fileName),
            // Support values already containing relative folders.
            public_path(ltrim($pdfUrl, '/')),
        ];

        foreach ($candidatePaths as $path) {
            if (is_file($path)) {
                return response()->download($path, $fileName, [
                    'Content-Type' => 'application/pdf',
                ]);
            }
        }

        Log::warning('Case study secure access failed: file not found in any known path', [
            'case_study_id' => $caseStudy,
            'pdf_url' => $pdfUrl,
            'resolved_file' => $fileName,
        ]);

        return redirect()->route('case-studies.index')
            ->withErrors(['access' => 'This file could not be located. Please request a new secure download link.']);
    }

    public function legacyCaseDoc(Request $request, string $file)
    {
        return redirect()->route('case-studies.index')
            ->withErrors(['access' => 'Direct document links are disabled. Please request a secure download link from the form.']);
    }

    public function accessWhitePaper(Request $request, int $paper)
    {
        if ($normalized = $this->normalizeAmpEncodedSignatureQuery($request)) {
            return redirect()->to($normalized);
        }

        if (!$request->hasValidSignature()) {
            return redirect()->route('case-studies.index')
                ->withErrors(['access' => 'This download link is invalid or has expired. Please request a new one.']);
        }

        if (!$this->isTableQueryable('white_paper')) {
            return redirect()->route('case-studies.index')
                ->withErrors(['access' => 'White papers are temporarily unavailable.']);
        }

        $item = DB::table('white_paper')
            ->select('id', 'pdf')
            ->where('id', $paper)
            ->first();

        if (!$item || empty($item->pdf)) {
            Log::warning('White paper secure access failed: missing record or pdf', [
                'white_paper_id' => $paper,
            ]);

            return redirect()->route('case-studies.index')
                ->withErrors(['access' => 'This file could not be located. Please request a new secure download link.']);
        }

        $pdfValue = (string) $item->pdf;
        if (str_starts_with($pdfValue, 'http://') || str_starts_with($pdfValue, 'https://')) {
            return $this->downloadRemotePdf($pdfValue, 'white-paper-' . $paper . '.pdf');
        }

        $fileName = basename($pdfValue);
        $candidatePaths = [
            storage_path('app/private/white_paper_docs/' . $fileName),
            public_path('white_paper_docs/' . $fileName),
            // Backward compatibility for older uploads saved under /public/admin/white_paper_docs.
            public_path('admin/white_paper_docs/' . $fileName),
            // Support values already containing relative folders.
            public_path(ltrim($pdfValue, '/')),
        ];

        foreach ($candidatePaths as $path) {
            if (is_file($path)) {
                return response()->download($path, $fileName, [
                    'Content-Type' => 'application/pdf',
                ]);
            }
        }

        Log::warning('White paper secure access failed: file not found in any known path', [
            'white_paper_id' => $paper,
            'pdf_value' => $pdfValue,
            'resolved_file' => $fileName,
        ]);

        return redirect()->route('case-studies.index')
            ->withErrors(['access' => 'This file could not be located. Please request a new secure download link.']);
    }

    public function legacyWhitePaperDoc(Request $request, string $file)
    {
        return redirect()->route('case-studies.index')
            ->withErrors(['access' => 'Direct document links are disabled. Please request a secure download link from the form.']);
    }

    private function findCaseStudyBySlug(string $slug): ?object
    {
        if (!$this->isTableQueryable('industry_listings')) {
            return null;
        }

        try {
            $items = DB::table('industry_listings')
                ->select($this->caseStudySelectColumns())
                ->orderByDesc('id')
                ->get();
        } catch (QueryException $e) {
            return null;
        }

        return $items->first(function ($item) use ($slug) {
            return $this->caseStudySlug($item) === $slug || (string) ($item->id ?? '') === $slug;
        });
    }

    private function findWhitePaperBySlug(string $slug): ?object
    {
        if (!$this->isTableQueryable('white_paper')) {
            return null;
        }

        try {
            $items = DB::table('white_paper')
                ->select($this->whitePaperSelectColumns())
                ->orderByDesc('id')
                ->get();
        } catch (QueryException $e) {
            return null;
        }

        return $items->first(function ($item) use ($slug) {
            return $this->resourceSlug($item) === $slug || (string) ($item->id ?? '') === $slug;
        });
    }

    private function caseStudySlug(object $caseStudy): string
    {
        return Str::slug($this->caseStudyDisplayTitle($caseStudy)) ?: 'case-study-' . (string) ($caseStudy->id ?? 'resource');
    }

    private function resourceSlug(object $paper): string
    {
        return (string) ($paper->slug ?? (Str::slug((string) ($paper->title ?? 'resource')) ?: 'resource-' . (string) ($paper->id ?? 'guide')));
    }

    private function caseStudyDisplayTitle(object $caseStudy): string
    {
        $title = trim((string) ($caseStudy->title ?? ''));
        if ($title !== '') {
            return $title;
        }

        $category = trim((string) ($caseStudy->category ?? 'Case Study'));
        return Str::endsWith(Str::lower($category), 'case study') ? $category : $category . ' Case Study';
    }

    private function inferIndustryFilter(object $caseStudy): string
    {
        $category = trim((string) ($caseStudy->category ?? ''));
        if ($category !== '') {
            $normalized = $this->normalizedIndustryKey($category);
            if ($normalized !== null) {
                return $normalized;
            }

            $slug = Str::slug(Str::lower($category));
            if ($slug !== '' && !in_array($slug, ['education', 'high-tech', 'high-tech-consulting', 'power-platform'], true)) {
                return $slug;
            }
        }

        $haystack = Str::lower($this->caseStudyDisplayTitle($caseStudy) . ' ' . (string) ($caseStudy->category ?? '') . ' ' . strip_tags((string) ($caseStudy->body ?? '')));

        foreach ($this->industryFilters() as $key => $label) {
            $terms = match ($key) {
                'healthcare' => ['health', 'healthcare', 'swope', 'unmc', 'patient', 'medical'],
                'energy-oil-gas' => ['energy', 'oil', 'gas', 'utility', 'utilities', 'sage', 'butte'],
                'government-public-sector' => ['government', 'public sector', 'state', 'city', 'county', 'agency', 'municipal', 'plano', 'isd'],
                'legal-social-services' => ['legal', 'social services', 'social service', 'nonprofit', 'community'],
                'transportation-logistics' => ['transportation', 'logistics', 'supply chain', 'fleet', 'shipping', 'freight', 'mhc'],
                'agriculture-cannabis' => ['agriculture', 'agri', 'farming', 'farm', 'cannabis', 'cultivation'],
                default => [$label],
            };

            foreach ($terms as $term) {
                if (str_contains($haystack, $term)) {
                    return $key;
                }
            }
        }

        return '';
    }

    private function inferTechnologyFilters(object $caseStudy): array
    {
        $haystack = Str::lower($this->caseStudyDisplayTitle($caseStudy) . ' ' . (string) ($caseStudy->category ?? '') . ' ' . strip_tags((string) ($caseStudy->body ?? '')));
        $matches = [];

        foreach ($this->topicFilters() as $key => $label) {
            $terms = match ($key) {
                'fabric-data' => ['fabric', 'power bi', 'data', 'analytics', 'warehouse', 'lakehouse'],
                'power-platform' => ['power platform', 'power apps', 'power automate', 'power pages'],
                'ai-cognitive-services' => ['ai', 'copilot', 'cognitive', 'agent', 'automation'],
                'sharepoint-collaboration' => ['sharepoint', 'teams', 'collaboration', 'intranet'],
                default => [$label],
            };

            foreach ($terms as $term) {
                if (str_contains($haystack, $term)) {
                    $matches[] = $key;
                    break;
                }
            }
        }

        return $matches ?: ['fabric-data'];
    }

    private function caseStudyResults(object $caseStudy): array
    {
        $industry = $this->inferIndustryFilter($caseStudy);
        $topics = $this->inferTechnologyFilters($caseStudy);

        $results = [
            'healthcare' => ['Faster reporting cycles', 'Cleaner patient and operations data', 'Better executive visibility'],
            'energy-oil-gas' => ['Operational data consolidated', 'Field workflows simplified', 'Leadership reporting improved'],
            'government-public-sector' => ['Improved constituent service workflows', 'Modernized reporting', 'Lower manual follow-up'],
            'legal-social-services' => ['Reduced manual intake effort', 'Improved service coordination', 'Better outcome tracking'],
            'transportation-logistics' => ['Faster dispatch visibility', 'Reduced manual status updates', 'Improved delivery performance tracking'],
            'agriculture-cannabis' => ['Improved production visibility', 'Better compliance reporting', 'Simplified operations monitoring'],
        ][$industry] ?? ['Measurable productivity gains', 'Microsoft workloads deployed', 'Improved reporting confidence'];

        if (in_array('power-platform', $topics, true)) {
            $results[0] = 'Manual workflow effort reduced';
        }

        return $results;
    }

    private function caseStudyServices(object $caseStudy): array
    {
        $services = ['Microsoft solution architecture', 'Data and process modernization'];
        foreach ($this->inferTechnologyFilters($caseStudy) as $topic) {
            $services[] = $this->topicFilters()[$topic] ?? 'Microsoft platform delivery';
        }

        return array_values(array_unique($services));
    }

    private function caseStudyMetaDescription(object $caseStudy): string
    {
        $title = $this->caseStudyDisplayTitle($caseStudy);
        $technologyLabel = $this->topicFilters()[$this->inferTechnologyFilters($caseStudy)[0] ?? ''] ?? 'Microsoft Platform';
        $outcome = $this->caseStudyResults($caseStudy)[0] ?? 'measurable business outcomes';
        $summary = $this->makePreviewText((string) ($caseStudy->body ?? ''), 220);

        if ($summary !== '') {
            return Str::limit(
                $summary . ' Armely delivered this engagement using ' . $technologyLabel . ' to drive ' . Str::lower($outcome) . '.',
                160,
                ''
            );
        }

        return Str::limit(
            $title . ' challenge, solution, and quantified results. Armely used ' . $technologyLabel . ' to deliver ' . Str::lower($outcome) . '.',
            160,
            ''
        );
    }

    private function whitePaperMetaDescription(object $paper): string
    {
        return Str::limit($this->makePreviewText((string) ($paper->body ?? $paper->preview ?? ''), 160) ?: 'Armely white papers on Microsoft Copilot, Power Platform governance, and data strategy for CXO and technology leaders in regulated industries.', 160, '');
    }

    private function staticResourceBySlug(string $slug): ?object
    {
        $resources = [
            'microsoft-copilot-commercial-enterprise' => [
                'title' => 'Microsoft Copilot Commercial Enterprise Guide',
                'meta_description' => 'Armely guidance for commercial enterprises planning Microsoft Copilot adoption, governance, readiness, security, and measurable AI productivity outcomes.',
                'body' => 'A practical guide for commercial enterprise leaders planning Microsoft Copilot adoption. Preview the readiness questions, governance decisions, security model, and rollout patterns that help organizations move from experimentation to measurable productivity.',
            ],
            'microsoft-copilot-public-sector' => [
                'title' => 'Microsoft Copilot Public Sector Guide',
                'meta_description' => 'Armely public sector guidance for Microsoft Copilot readiness, governance, compliance, and responsible AI adoption across agencies.',
                'body' => 'A public sector planning guide for agency CIOs and technology leaders. The preview covers governance, data protection, responsible AI controls, and adoption planning for Microsoft Copilot in regulated environments.',
            ],
            'microsoft-copilot-commercial-cxo-guide' => [
                'title' => 'Microsoft Copilot Commercial CXO Guide',
                'meta_description' => 'A condensed Armely CXO guide to Microsoft Copilot strategy, business value, governance, and executive adoption planning.',
                'body' => 'A concise executive guide for commercial leaders evaluating Microsoft Copilot. It frames business value, readiness, governance, and rollout priorities in CXO language before the full gated download.',
            ],
            'microsoft-copilot-government-cxo-guide' => [
                'title' => 'Microsoft Copilot Government CXO Guide',
                'meta_description' => 'A condensed Armely CXO guide for government Microsoft Copilot strategy, compliance, governance, and responsible adoption.',
                'body' => 'A concise executive guide for government and public sector leaders evaluating Microsoft Copilot. It summarizes the governance, compliance, security, and adoption decisions that should be clear before deployment.',
            ],
            'microsoft-fabric-case-study-agricultural-operations' => [
                'title' => 'Microsoft Fabric Case Study for Agricultural Operations',
                'meta_description' => 'See how Microsoft Fabric can unify operational data, reporting, and analytics for agricultural operations with Armely implementation guidance.',
                'body' => 'A Microsoft Fabric case study preview for agricultural operations leaders. It outlines the challenge of fragmented operational data, the Fabric-based data and analytics solution, and the reporting outcomes leaders can use to improve decisions.',
            ],
        ];

        if (!isset($resources[$slug])) {
            foreach ($resources as $resourceSlug => $resource) {
                if (Str::slug($resource['title']) === $slug) {
                    $slug = $resourceSlug;
                    break;
                }
            }
        }

        if (!isset($resources[$slug])) {
            return null;
        }

        return (object) array_merge([
            'id' => null,
            'images' => null,
            'pdf' => null,
            'slug' => $slug,
        ], $resources[$slug]);
    }

    private function industryFilters(): array
    {
        return Cache::remember('case_studies_industry_filters', now()->addMinutes(15), function (): array {
            if (Schema::hasTable('case_study_categories')) {
                try {
                    $managed = DB::table('case_study_categories')
                        ->select('slug', 'name')
                        ->where('is_active', true)
                        ->orderBy('sort_order')
                        ->orderBy('name')
                        ->get();

                    if ($managed->isNotEmpty()) {
                        $filters = [];
                        foreach ($managed as $category) {
                            $key = trim((string) ($category->slug ?? ''));
                            $label = trim((string) ($category->name ?? ''));
                            if ($key === '' || $label === '') {
                                continue;
                            }

                            $filters[$key] = $label;
                        }

                        if (!empty($filters)) {
                            return $filters;
                        }
                    }
                } catch (QueryException $e) {
                    Log::warning('Failed to load managed case-study categories', [
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            return $this->defaultIndustryFilters();
        });
    }

    private function defaultIndustryFilters(): array
    {
        return [
            'healthcare' => 'Healthcare',
            'energy-oil-gas' => 'Energy (Oil & Gas)',
            'government-public-sector' => 'Government & Public Sector',
            'legal-social-services' => 'Legal (Social Services)',
            'transportation-logistics' => 'Transportation & Logistics',
            'agriculture-cannabis' => 'Agriculture/Cannabis',
        ];
    }

    private function normalizedIndustryKey(string $value): ?string
    {
        $slug = Str::slug(Str::lower(trim($value)));
        if ($slug === '') {
            return null;
        }

        $aliases = [
            'healthcare' => 'healthcare',
            'health-care' => 'healthcare',
            'energy' => 'energy-oil-gas',
            'energy-utilities' => 'energy-oil-gas',
            'oil-gas' => 'energy-oil-gas',
            'oil-and-gas' => 'energy-oil-gas',
            'government-public-sector' => 'government-public-sector',
            'public-sector' => 'government-public-sector',
            'state-local-government' => 'government-public-sector',
            'local-government' => 'government-public-sector',
            'legal' => 'legal-social-services',
            'social-services' => 'legal-social-services',
            'legal-social-services' => 'legal-social-services',
            'transportation' => 'transportation-logistics',
            'transportation-logistics' => 'transportation-logistics',
            'logistics' => 'transportation-logistics',
            'agriculture' => 'agriculture-cannabis',
            'agriculture-cannabis' => 'agriculture-cannabis',
            'cannabis' => 'agriculture-cannabis',
            'education' => 'government-public-sector',
            'high-tech' => null,
            'high-tech-consulting' => null,
            'power-platform' => null,
        ];

        if (array_key_exists($slug, $aliases)) {
            return $aliases[$slug];
        }

        return array_key_exists($slug, $this->defaultIndustryFilters()) ? $slug : null;
    }

    private function topicFilters(): array
    {
        return [
            'fabric-data' => 'Microsoft Fabric and Data',
            'power-platform' => 'Power Platform',
            'ai-cognitive-services' => 'AI and Cognitive Services',
            'sharepoint-collaboration' => 'SharePoint and Collaboration',
        ];
    }

    private function portfolioStats(): array
    {
        return [
            ['value' => '6+', 'label' => 'Industries served'],
            ['value' => '4', 'label' => 'Microsoft workload families'],
            ['value' => '8+', 'label' => 'Client transformation stories'],
            ['value' => '72%', 'label' => 'Customer retention signal'],
        ];
    }

    private function relatedCaseStudies(object $caseStudy): \Illuminate\Support\Collection
    {
        if (!$this->isTableQueryable('industry_listings')) {
            return collect();
        }

        try {
            return DB::table('industry_listings')
                ->select($this->caseStudySelectColumns())
                ->where('id', '!=', (int) ($caseStudy->id ?? 0))
                ->orderByDesc('id')
                ->limit(3)
                ->get()
                ->map(function ($item) {
                    $item->slug = $this->caseStudySlug($item);
                    $item->preview = $this->makePreviewText((string) ($item->body ?? ''), 90);
                    return $item;
                });
        } catch (QueryException $e) {
            return collect();
        }
    }

    private function caseStudySelectColumns(): array
    {
        $columns = ['id', 'category', 'listing_image', 'body', 'pdf_url'];
        if ($this->safeHasColumn('industry_listings', 'title')) {
            $columns[] = 'title';
        }

        return $columns;
    }

    private function whitePaperSelectColumns(): array
    {
        $columns = ['id'];
        foreach (['title', 'body', 'images', 'pdf', 'slug', 'meta_description'] as $column) {
            if ($this->safeHasColumn('white_paper', $column)) {
                $columns[] = $column;
            }
        }

        return $columns;
    }

    private function makePreviewText(string $value, int $limit): string
    {
        $decoded = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        $cleaned = Str::of(strip_tags($decoded))
            ->replace(["\u{2010}", "\u{2011}", "\u{2012}", "\u{2013}", "\u{2014}", "\u{2015}", "\u{00AD}", "\u{FFFD}", 'â€‘', 'â€“', 'â€”'], '-')
            ->replace("\u{00A0}", ' ')
            ->replaceMatches('/\s+/u', ' ')
            ->trim();

        return Str::limit((string) $cleaned, $limit);
    }

    private function verifyRecaptcha(string $token): bool
    {
        if (config('services.recaptcha.bypass', false)) {
            return true;
        }

        $secret = config('services.recaptcha.secret_key', env('CAPTURE_SECRET_KEY'));
        if (!$secret) {
            Log::warning('Case studies lead: missing reCAPTCHA secret key');
            return false;
        }

        try {
            $response = Http::asForm()->timeout(10)->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secret,
                'response' => $token,
                'remoteip' => request()->ip(),
            ]);

            return (bool) data_get($response->json(), 'success', false);
        } catch (\Throwable $e) {
            Log::error('Case studies lead reCAPTCHA exception', ['error' => $e->getMessage()]);
            return false;
        }
    }

    private function emailValidationRule(): string
    {
        // Keep request validation fast; DNS checks are handled in deliverability logic.
        $checks = ['rfc', 'filter'];

        // Egulias spoof validation depends on the PHP Intl extension.
        if (extension_loaded('intl')) {
            $checks[] = 'spoof';
        }

        return 'email:' . implode(',', $checks);
    }

    private function leadErrorResponse(Request $request, array $errors)
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'ok' => false,
                'message' => 'Please correct the highlighted fields and try again.',
                'errors' => $errors,
            ], 422);
        }

        return back()->withErrors($errors)->withInput();
    }

    private function sendCaseStudyLeadEmail(array $payload): bool
    {
        try {
            $fromEmail = trim((string) AzureMailService::outboundFromEmail());
            if ($fromEmail === '') {
                $fromEmail = trim((string) config('mail.from.address', ''));
            }

            if ($fromEmail === '') {
                Log::warning('Case studies lead email skipped: missing sender address');
                return false;
            }

            $adminEmail = trim((string) env('ADMIN_EMAIL', ''));

            $mailer = new AzureMailService();
            $subject = 'Case Studies Lead: ' . $payload['interest'];

            $userHtml = view('emails.case-studies.resource-download', [
                'name' => $payload['name'],
                'resourceTitle' => $payload['resource_title'],
                'resourceTypeLabel' => $payload['resource_type_label'],
                'downloadUrl' => $payload['download_url'],
                'expiresAt' => $payload['expires_at'],
            ])->render();

            // Prioritize customer delivery first, then send admin notifications best-effort.
            $userSent = $this->sendEmailWithFallback(
                $mailer,
                $fromEmail,
                (string) $payload['email'],
                'Your secure Armely download link',
                $userHtml,
                true,
                false
            );

            $html = view('emails.case-studies.admin-lead-notification', [
                'name' => (string) ($payload['name'] ?? ''),
                'email' => (string) ($payload['email'] ?? ''),
                'phone' => (string) ($payload['phone'] ?? ''),
                'organization' => (string) ($payload['organization'] ?? ''),
                'jobTitle' => (string) ($payload['job_title'] ?? ''),
                'country' => (string) ($payload['country'] ?? ''),
                'interest' => (string) ($payload['interest'] ?? ''),
                'requestedResource' => (string) ($payload['requested_resource'] ?? ''),
                'caseStudyId' => (string) ($payload['case_study_id'] ?? ''),
                'whitePaperId' => (string) ($payload['white_paper_id'] ?? ''),
                'expiresAt' => (string) ($payload['expires_at'] ?? ''),
                'message' => (string) ($payload['message'] ?? ''),
            ])->render();

            if ($adminEmail !== '') {
                $this->sendEmailWithFallback($mailer, $fromEmail, $adminEmail, $subject, $html);
            }

            if ($adminEmail === '' || strtolower($adminEmail) !== 'ask.me@armely.com') {
                $this->sendEmailWithFallback($mailer, $fromEmail, 'ask.me@armely.com', $subject, $html);
            }

            return $userSent;
        } catch (\Throwable $e) {
            Log::warning('Case studies lead email failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    private function sendEmailWithFallback(
        AzureMailService $mailer,
        string $fromEmail,
        string $toEmail,
        string $subject,
        string $html,
        bool $saveToSent = true,
        bool $validateRecipient = true
    ): bool {
        $azureSent = $mailer->sendEmail($fromEmail, $toEmail, $subject, $html, $saveToSent, $validateRecipient);
        if ($azureSent) {
            return true;
        }

        // Retry Graph once before falling back to SMTP/log driver.
        $azureRetrySent = (new AzureMailService())->sendEmail($fromEmail, $toEmail, $subject, $html, $saveToSent, $validateRecipient);
        if ($azureRetrySent) {
            Log::warning('Case studies email sent via Graph retry', [
                'to' => $toEmail,
                'subject' => $subject,
            ]);

            return true;
        }

        try {
            $resolvedFrom = trim($fromEmail) !== '' ? trim($fromEmail) : (string) config('mail.from.address', '');

            if (trim((string) $resolvedFrom) === '') {
                Log::warning('Case studies email fallback skipped: missing from address', [
                    'to' => $toEmail,
                    'subject' => $subject,
                ]);

                return false;
            }

            Mail::html($html, function ($message) use ($resolvedFrom, $toEmail, $subject) {
                $message->from($resolvedFrom, (string) config('mail.from.name', config('app.name', 'Armely')))
                    ->to($toEmail)
                    ->subject($subject);

                $replyTo = AzureMailService::replyToEmail();
                if ($replyTo !== null && $replyTo !== '') {
                    $message->replyTo($replyTo);
                }
            });

            Log::warning('Case studies email sent via fallback mailer', [
                'to' => $toEmail,
                'subject' => $subject,
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::warning('Case studies email fallback failed', [
                'to' => $toEmail,
                'subject' => $subject,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    private function buildDownloadDetails(array $data, string $email): ?array
    {
        $expiresAt = now()->addHour();
        $caseStudyId = (int) ($data['case_study_id'] ?? 0);
        if ($caseStudyId > 0 && $this->isTableQueryable('industry_listings')) {
            $query = DB::table('industry_listings')
                ->select('id', 'category', 'pdf_url')
                ->where('id', $caseStudyId);

            if (Schema::hasColumn('industry_listings', 'title')) {
                $query->addSelect('title');
            }

            $record = $query->first();

            if ($record && trim((string) ($record->pdf_url ?? '')) !== '') {
                $resourceTitle = trim((string) ($record->title ?? ''));
                if ($resourceTitle === '') {
                    $resourceTitle = (string) ($record->category ?? ('Case Study #' . $caseStudyId));
                }

                return [
                    'resource_title' => $resourceTitle,
                    'resource_type_label' => 'Case Study',
                    'download_url' => URL::temporarySignedRoute('case-studies.access', $expiresAt, ['caseStudy' => $caseStudyId, 'em' => sha1($email)]),
                    'expires_at' => $expiresAt->format('M d, Y h:i A T'),
                ];
            }
        }

        $whitePaperId = (int) ($data['white_paper_id'] ?? 0);
        if ($whitePaperId > 0 && $this->isTableQueryable('white_paper')) {
            $whitePaperFileColumn = null;
            if (Schema::hasColumn('white_paper', 'pdf')) {
                $whitePaperFileColumn = 'pdf';
            } elseif (Schema::hasColumn('white_paper', 'pdf_url')) {
                $whitePaperFileColumn = 'pdf_url';
            }

            $query = DB::table('white_paper')
                ->select('id', 'title')
                ->where('id', $whitePaperId);

            if ($whitePaperFileColumn !== null) {
                $query->addSelect($whitePaperFileColumn);
            }

            $record = $query->first();
            $whitePaperFileValue = $whitePaperFileColumn !== null
                ? trim((string) ($record->{$whitePaperFileColumn} ?? ''))
                : '';

            if ($record && $whitePaperFileValue !== '') {
                return [
                    'resource_title' => (string) ($record->title ?? ('White Paper #' . $whitePaperId)),
                    'resource_type_label' => 'White Paper',
                    'download_url' => URL::temporarySignedRoute('white-papers.access', $expiresAt, ['paper' => $whitePaperId, 'em' => sha1($email)]),
                    'expires_at' => $expiresAt->format('M d, Y h:i A T'),
                ];
            }
        }

        $requestedResource = trim((string) ($data['requested_resource'] ?? ''));
        if ($requestedResource !== '') {
            $slug = Str::slug($requestedResource);
            $staticResource = $this->staticResourceBySlug($slug);
            if ($staticResource) {
                return [
                    'resource_title' => (string) $staticResource->title,
                    'resource_type_label' => 'White Paper',
                    'download_url' => route('resources.show', ['slug' => $staticResource->slug, 'download' => 1]),
                    'expires_at' => $expiresAt->toDayDateTimeString(),
                ];
            }
        }

        return null;
    }

    private function downloadRemotePdf(string $url, string $fallbackName)
    {
        try {
            $response = Http::timeout(20)->get($url);
            if (!$response->successful()) {
                Log::warning('Failed to proxy remote PDF download: non-success status', [
                    'url' => $url,
                    'status' => $response->status(),
                ]);

                return redirect()->route('case-studies.index')
                    ->withErrors(['access' => 'This file could not be retrieved right now. Please request a fresh secure link and try again.']);
            }

            $filename = basename(parse_url($url, PHP_URL_PATH) ?: $fallbackName);
            if ($filename === '' || $filename === '/') {
                $filename = $fallbackName;
            }

            return response($response->body(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to proxy remote PDF download', ['url' => $url, 'error' => $e->getMessage()]);

            return redirect()->route('case-studies.index')
                ->withErrors(['access' => 'This file could not be retrieved right now. Please request a fresh secure link and try again.']);
        }
    }

    private function isDeliverableEmail(string $email): bool
    {
        return AzureMailService::isDeliverableEmail($email);
    }

    private function normalizeAmpEncodedSignatureQuery(Request $request): ?string
    {
        $query = $request->query();
        $normalized = [];
        $changed = false;

        foreach ($query as $key => $value) {
            $targetKey = (string) $key;
            if (str_starts_with($targetKey, 'amp;')) {
                $targetKey = substr($targetKey, 4);
                $changed = true;
            }

            if ($targetKey === '') {
                $changed = true;
                continue;
            }

            if (!array_key_exists($targetKey, $normalized)) {
                $normalized[$targetKey] = $value;
            }
        }

        if (!$changed) {
            return null;
        }

        return $request->url() . '?' . http_build_query($normalized);
    }

    private function grantCaseStudyAccess(Request $request, string $email, int $caseStudyId): void
    {
        $normalizedEmail = strtolower(trim($email));
        $sessionIds = array_values(array_unique(array_map('intval', (array) $request->session()->get('case_studies_access_ids', []))));
        if (!in_array($caseStudyId, $sessionIds, true)) {
            $sessionIds[] = $caseStudyId;
        }

        $request->session()->put('case_studies_access_ids', $sessionIds);
        $request->session()->put('case_studies_access_email', $normalizedEmail);

        $cacheKey = $this->caseStudyAccessCacheKey($normalizedEmail);
        $cachedIds = array_values(array_unique(array_map('intval', (array) Cache::get($cacheKey, []))));
        if (!in_array($caseStudyId, $cachedIds, true)) {
            $cachedIds[] = $caseStudyId;
        }

        Cache::put($cacheKey, $cachedIds, now()->addDays(30));
    }

    private function hasCaseStudyAccess(Request $request, int $caseStudyId): bool
    {
        $sessionIds = array_map('intval', (array) $request->session()->get('case_studies_access_ids', []));
        if (in_array($caseStudyId, $sessionIds, true)) {
            return true;
        }

        $email = strtolower(trim((string) $request->session()->get('case_studies_access_email', '')));
        if ($email === '') {
            return false;
        }

        $cachedIds = array_map('intval', (array) Cache::get($this->caseStudyAccessCacheKey($email), []));
        if (in_array($caseStudyId, $cachedIds, true)) {
            $request->session()->put('case_studies_access_ids', array_values(array_unique(array_merge($sessionIds, $cachedIds))));
            return true;
        }

        return false;
    }

    private function getGrantedCaseStudyIds(Request $request): array
    {
        $sessionIds = array_map('intval', (array) $request->session()->get('case_studies_access_ids', []));
        $email = strtolower(trim((string) $request->session()->get('case_studies_access_email', '')));

        if ($email === '') {
            return array_values(array_unique($sessionIds));
        }

        $cachedIds = array_map('intval', (array) Cache::get($this->caseStudyAccessCacheKey($email), []));
        return array_values(array_unique(array_merge($sessionIds, $cachedIds)));
    }

    private function caseStudyAccessCacheKey(string $email): string
    {
        return 'case_study_access:' . sha1(strtolower(trim($email)));
    }

    private function grantWhitePaperAccess(Request $request, string $email, int $whitePaperId): void
    {
        $normalizedEmail = strtolower(trim($email));
        $sessionIds = array_values(array_unique(array_map('intval', (array) $request->session()->get('white_papers_access_ids', []))));
        if (!in_array($whitePaperId, $sessionIds, true)) {
            $sessionIds[] = $whitePaperId;
        }

        $request->session()->put('white_papers_access_ids', $sessionIds);
        $request->session()->put('case_studies_access_email', $normalizedEmail);

        $cacheKey = $this->whitePaperAccessCacheKey($normalizedEmail);
        $cachedIds = array_values(array_unique(array_map('intval', (array) Cache::get($cacheKey, []))));
        if (!in_array($whitePaperId, $cachedIds, true)) {
            $cachedIds[] = $whitePaperId;
        }

        Cache::put($cacheKey, $cachedIds, now()->addDays(30));
    }

    private function hasWhitePaperAccess(Request $request, int $whitePaperId): bool
    {
        $sessionIds = array_map('intval', (array) $request->session()->get('white_papers_access_ids', []));
        if (in_array($whitePaperId, $sessionIds, true)) {
            return true;
        }

        $email = strtolower(trim((string) $request->session()->get('case_studies_access_email', '')));
        if ($email === '') {
            return false;
        }

        $cachedIds = array_map('intval', (array) Cache::get($this->whitePaperAccessCacheKey($email), []));
        if (in_array($whitePaperId, $cachedIds, true)) {
            $request->session()->put('white_papers_access_ids', array_values(array_unique(array_merge($sessionIds, $cachedIds))));
            return true;
        }

        return false;
    }

    private function getGrantedWhitePaperIds(Request $request): array
    {
        $sessionIds = array_map('intval', (array) $request->session()->get('white_papers_access_ids', []));
        $email = strtolower(trim((string) $request->session()->get('case_studies_access_email', '')));

        if ($email === '') {
            return array_values(array_unique($sessionIds));
        }

        $cachedIds = array_map('intval', (array) Cache::get($this->whitePaperAccessCacheKey($email), []));
        return array_values(array_unique(array_merge($sessionIds, $cachedIds)));
    }

    private function whitePaperAccessCacheKey(string $email): string
    {
        return 'white_paper_access:' . sha1(strtolower(trim($email)));
    }

    private function paginateCaseStudies(Request $request): LengthAwarePaginator
    {
        if (!$this->isTableQueryable('industry_listings')) {
            return $this->emptyPaginator($request, 6, 'case_page');
        }

        try {
            $query = DB::table('industry_listings')
                ->select($this->caseStudySelectColumns())
                ->orderByDesc('id');

            $industryFilters = $this->industryFilters();
            $industryParam = (string) $request->query('case_industry', $request->query('industry', ''));
            $industry = Str::slug(Str::lower($industryParam));
            $hasActiveFilter = false;
            if ($industry !== '' && array_key_exists($industry, $industryFilters)) {
                $hasActiveFilter = true;
                $industryLabel = trim((string) $industryFilters[$industry]);
                $industryTerms = array_values(array_unique(array_filter(array_map(
                    static fn ($term) => Str::lower(trim((string) $term)),
                    array_merge(
                        [$industryLabel, str_replace('-', ' ', $industry), $industry],
                        $this->industryQueryTerms($industry)
                    )
                ))));

                $query->where(function ($inner) use ($industryLabel, $industryTerms) {
                    if ($industryLabel !== '') {
                        $inner->orWhereRaw('LOWER(TRIM(category)) = ?', [Str::lower($industryLabel)]);
                    }

                    foreach ($industryTerms as $term) {
                        $inner->orWhere('category', 'like', '%' . $term . '%')
                            ->orWhere('body', 'like', '%' . $term . '%');
                    }
                });
            }

            $topic = (string) $request->query('case_topic', $request->query('topic', ''));
            if ($topic !== '' && array_key_exists($topic, $this->topicFilters())) {
                $hasActiveFilter = true;
                $terms = match ($topic) {
                    'fabric-data' => ['Fabric', 'Power BI', 'data', 'analytics'],
                    'power-platform' => ['Power Platform', 'Power Apps', 'Power Automate', 'Power Pages'],
                    'ai-cognitive-services' => ['AI', 'Copilot', 'Cognitive', 'agent'],
                    'sharepoint-collaboration' => ['SharePoint', 'Teams', 'collaboration'],
                    default => [$this->topicFilters()[$topic]],
                };
                $query->where(function ($inner) use ($terms) {
                    foreach ($terms as $term) {
                        $inner->orWhere('category', 'like', '%' . $term . '%')
                            ->orWhere('body', 'like', '%' . $term . '%');
                    }
                });
            }

            $perPage = $hasActiveFilter ? max((clone $query)->count(), 1) : 6;

            return $query->paginate($perPage, ['*'], 'case_page')->withQueryString();
        } catch (QueryException $e) {
            if ($this->isMissingTableException($e)) {
                Log::warning('Case studies table unavailable in database engine', [
                    'table' => 'industry_listings',
                    'error' => $e->getMessage(),
                ]);

                return $this->emptyPaginator($request, 6, 'case_page');
            }

            throw $e;
        }
    }

    private function paginateWhitePapers(Request $request): LengthAwarePaginator
    {
        if (!$this->isTableQueryable('white_paper')) {
            return $this->emptyPaginator($request, 6, 'paper_page');
        }

        try {
            $query = DB::table('white_paper')
                ->select($this->whitePaperSelectColumns())
                ->orderByDesc('id');

            $searchableColumns = $this->whitePaperSearchColumns();
            $topic = (string) $request->query('white_topic', '');
            $hasActiveFilter = false;
            if ($topic !== '' && array_key_exists($topic, $this->topicFilters()) && !empty($searchableColumns)) {
                $hasActiveFilter = true;
                $terms = match ($topic) {
                    'fabric-data' => ['fabric', 'power bi', 'data', 'analytics', 'warehouse', 'lakehouse'],
                    'power-platform' => ['power platform', 'power apps', 'power automate', 'power pages'],
                    'ai-cognitive-services' => ['ai', 'copilot', 'cognitive', 'agent'],
                    'sharepoint-collaboration' => ['sharepoint', 'teams', 'collaboration', 'intranet'],
                    default => [Str::lower((string) ($this->topicFilters()[$topic] ?? $topic))],
                };

                $query->where(function ($inner) use ($searchableColumns, $terms) {
                    foreach ($terms as $term) {
                        $needle = Str::lower((string) $term);
                        foreach ($searchableColumns as $column) {
                            $inner->orWhere($column, 'like', '%' . $needle . '%');
                        }
                    }
                });
            }

            $perPage = $hasActiveFilter ? max((clone $query)->count(), 1) : 6;

            return $query->paginate($perPage, ['*'], 'paper_page')->withQueryString();
        } catch (QueryException $e) {
            if ($this->isMissingTableException($e)) {
                Log::warning('White papers table unavailable in database engine', [
                    'table' => 'white_paper',
                    'error' => $e->getMessage(),
                ]);

                return $this->emptyPaginator($request, 6, 'paper_page');
            }

            throw $e;
        }
    }

    private function isTableQueryable(string $table): bool
    {
        try {
            return Schema::hasTable($table);
        } catch (\Throwable $e) {
            Log::warning('Database table unavailable during schema check', [
                'table' => $table,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    private function safeHasColumn(string $table, string $column): bool
    {
        try {
            return Schema::hasTable($table) && Schema::hasColumn($table, $column);
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function whitePaperSearchColumns(): array
    {
        $columns = [];
        foreach (['title', 'body', 'meta_description', 'slug', 'category', 'topic', 'industry'] as $column) {
            if ($this->safeHasColumn('white_paper', $column)) {
                $columns[] = $column;
            }
        }

        return $columns;
    }

    private function industryQueryTerms(string $industry): array
    {
        return match ($industry) {
            'healthcare' => ['Healthcare', 'health', 'Swope', 'UNMC', 'patient', 'medical'],
            'government-public-sector' => ['Government', 'Public Sector', 'State & Local Government', 'city', 'county', 'agency', 'municipal', 'Plano', 'ISD'],
            'energy-oil-gas' => ['Energy', 'Oil & Gas', 'Oil and Gas', 'utility', 'utilities', 'Sage', 'Butte'],
            'legal-social-services' => ['Legal', 'Social Services', 'social', 'nonprofit', 'community', 'Swope'],
            'transportation-logistics' => ['Transportation', 'Logistics', 'supply chain', 'fleet', 'shipping', 'freight', 'MHC'],
            'agriculture-cannabis' => ['Agriculture', 'agri', 'farming', 'farm', 'Cannabis', 'cultivation'],
            default => [$this->industryFilters()[$industry] ?? $industry, str_replace('-', ' ', $industry)],
        };
    }

    private function isMissingTableException(QueryException $e): bool
    {
        $message = strtolower($e->getMessage());

        return str_contains($message, 'sqlstate[42s02]')
            || str_contains($message, 'base table or view not found')
            || str_contains($message, "doesn't exist in engine")
            || str_contains($message, 'error 1932');
    }

    private function emptyPaginator(Request $request, int $perPage, string $pageName): LengthAwarePaginator
    {
        $currentPage = max((int) $request->query($pageName, 1), 1);

        return new LengthAwarePaginator(
            collect(),
            0,
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
                'pageName' => $pageName,
            ]
        );
    }

    private function redirectIfOutOfRangePage(Request $request, LengthAwarePaginator $paginator, string $pageName)
    {
        $requestedPage = (int) $request->query($pageName, 1);
        if ($requestedPage <= 1 || $requestedPage <= $paginator->lastPage()) {
            return null;
        }

        $query = $request->query();
        unset($query[$pageName]);

        if (empty($query)) {
            return redirect()->route('case-studies.index');
        }

        return redirect()->route('case-studies.index', $query);
    }
}
