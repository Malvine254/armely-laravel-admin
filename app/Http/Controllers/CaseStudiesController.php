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
            $caseStudy->outcome_tag = $this->caseStudyOutcomeTag($caseStudy);
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
        $caseStudy->outcome_tag = $this->caseStudyOutcomeTag($caseStudy);
        $caseStudy->pdf_preview_has_attachment = $this->caseStudyHasAttachment($caseStudy);
        $caseStudy->pdf_preview_url = $this->caseStudyPreviewUrl($caseStudy);
        $caseStudy->pdf_preview_text = $this->caseStudyFirstPageText($caseStudy);
        $caseStudy->pdf_preview_source = $caseStudy->pdf_preview_text === '' ? 'PDF unavailable' : 'PDF text';
        $caseStudy->pdf_preview_sections = $caseStudy->pdf_preview_source === 'PDF text'
            ? $this->caseStudyPreviewSections($caseStudy->pdf_preview_text)
            : [];
        $caseStudy->pdf_preview_paragraphs = $this->caseStudyPreviewParagraphs($caseStudy->pdf_preview_text);
        $caseStudy->results = $this->caseStudyResults($caseStudy);
        $caseStudy->services = $this->caseStudyServices($caseStudy);
        $caseStudy->hero_copy = $this->caseStudyHeroCopy($caseStudy);

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
        $paper->pdf_preview_url = $this->whitePaperPreviewUrl($paper);
        $caseStudyViewModel = $this->whitePaperCaseStudyViewModel($paper);

        return view('case-studies.show', [
            'caseStudy' => $caseStudyViewModel,
            'relatedCaseStudies' => collect(),
            'recaptchaSiteKey' => config('services.recaptcha.site_key', ''),
            'metaDescription' => (string) ($paper->meta_description ?? $this->whitePaperMetaDescription($paper)),
            'isWhitePaperPage' => true,
            'detailRequestAction' => route('case-studies.lead.submit'),
            'detailLeadInterest' => 'white-papers',
            'detailLeadIdField' => 'white_paper_id',
            'detailLeadIdValue' => $paper->id ?? null,
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

        $inlinePreview = $request->boolean('preview');

        $query = DB::table('industry_listings')
            ->select('id', 'pdf_url')
            ->where('id', $caseStudy);

        if ($this->safeHasColumn('industry_listings', 'pdf')) {
            $query->addSelect('pdf');
        }

        $item = $query->first();

        if (!$item || $this->caseStudyPdfValue($item) === '') {
            Log::warning('Case study secure access failed: missing record or pdf_url', [
                'case_study_id' => $caseStudy,
            ]);

            return redirect()->route('case-studies.index')
                ->withErrors(['access' => 'This file could not be located. Please request a new secure download link.']);
        }

        $pdfUrl = $this->caseStudyPdfValue($item);
        if (str_starts_with($pdfUrl, 'http://') || str_starts_with($pdfUrl, 'https://')) {
            return $this->downloadRemotePdf($pdfUrl, 'case-study-' . $caseStudy . '.pdf', $inlinePreview);
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
                return $inlinePreview
                    ? response()->file($path, [
                        'Content-Type' => 'application/pdf',
                    ])
                    : response()->download($path, $fileName, [
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

        $selectColumns = ['id'];
        if (Schema::hasColumn('white_paper', 'pdf')) {
            $selectColumns[] = 'pdf';
        }
        if (Schema::hasColumn('white_paper', 'pdf_url')) {
            $selectColumns[] = 'pdf_url';
        }

        $item = DB::table('white_paper')
            ->select($selectColumns)
            ->where('id', $paper)
            ->first();

        $pdfValue = $item ? $this->whitePaperPdfValue($item) : '';
        if (!$item || $pdfValue === '') {
            Log::warning('White paper secure access failed: missing record or pdf', [
                'white_paper_id' => $paper,
            ]);

            return redirect()->route('case-studies.index')
                ->withErrors(['access' => 'This file could not be located. Please request a new secure download link.']);
        }

        $inlinePreview = $request->boolean('preview');
        if (str_starts_with($pdfValue, 'http://') || str_starts_with($pdfValue, 'https://')) {
            return $this->downloadRemotePdf($pdfValue, 'white-paper-' . $paper . '.pdf', $inlinePreview);
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
                return $inlinePreview
                    ? response()->file($path, [
                        'Content-Type' => 'application/pdf',
                    ])
                    : response()->download($path, $fileName, [
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

    private function caseStudyOutcomeTag(object $caseStudy): string
    {
        foreach (['outcome_tag', 'outcome_tags', 'results'] as $property) {
            if (!isset($caseStudy->{$property}) || $caseStudy->{$property} === null) {
                continue;
            }

            $value = $caseStudy->{$property};
            $values = is_array($value)
                ? $value
                : preg_split('/[\r\n,]+/', (string) $value);

            $values = array_values(array_filter(array_map(static fn ($item) => trim((string) $item), $values ?: [])));
            if (!empty($values)) {
                return $values[0];
            }
        }

        $fallback = $this->caseStudyResults($caseStudy);
        return trim((string) ($fallback[0] ?? ''));
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
        $outcome = $this->caseStudyOutcomeTag($caseStudy) ?: 'measurable business outcomes';
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

    private function caseStudyHeroCopy(object $caseStudy): string
    {
        $title = $this->caseStudyDisplayTitle($caseStudy);
        $summary = $this->caseStudyLeadParagraph($caseStudy);

        if ($summary !== '') {
            return $title . ' preview: ' . $summary;
        }

        return 'Preview how ' . $title . ' addresses the challenge, solution approach, and measurable results before requesting the full PDF.';
    }

    private function caseStudyLeadParagraph(object $caseStudy): string
    {
        $body = html_entity_decode((string) ($caseStudy->body ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        $paragraph = '';

        if (preg_match('/<p\b[^>]*>(.*?)<\/p>/is', $body, $matches)) {
            $paragraph = (string) ($matches[1] ?? '');
        } elseif (preg_match('/(?:\r?\n){2,}/', $body)) {
            $chunks = preg_split('/(?:\r?\n){2,}/', $body);
            $paragraph = (string) ($chunks[0] ?? '');
        } else {
            $paragraph = $body;
        }

        $cleaned = Str::of(strip_tags($paragraph))
            ->replace(["\u{2010}", "\u{2011}", "\u{2012}", "\u{2013}", "\u{2014}", "\u{2015}", "\u{00AD}", "\u{FFFD}", 'Ã¢â‚¬â€˜', 'Ã¢â‚¬â€œ', 'Ã¢â‚¬â€'], '-')
            ->replace("\u{00A0}", ' ')
            ->replaceMatches('/\s+/u', ' ')
            ->trim();

        return (string) $cleaned;
    }

    private function whitePaperMetaDescription(object $paper): string
    {
        return Str::limit($this->makePreviewText((string) ($paper->body ?? $paper->preview ?? ''), 160) ?: 'Armely white papers on Microsoft Copilot, Power Platform governance, and data strategy for CXO and technology leaders in regulated industries.', 160, '');
    }

    private function staticResourceBySlug(string $slug): ?object
    {
        $resources = $this->whitePaperSampleCatalog() + [
            'microsoft-fabric-case-study-agricultural-operations' => [
                'title' => 'Microsoft Fabric Case Study for Agricultural Operations',
                'meta_description' => 'See how Microsoft Fabric can unify operational data, reporting, and analytics for agricultural operations with Armely implementation guidance.',
                'body' => 'A Microsoft Fabric case study preview for agricultural operations leaders. It outlines the challenge of fragmented operational data, the Fabric-based data and analytics solution, and the reporting outcomes leaders can use to improve decisions.',
                'topic' => 'fabric-data',
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

    private function caseStudyHasAttachment(object $caseStudy): bool
    {
        return $this->caseStudyPdfValue($caseStudy) !== '';
    }

    private function caseStudyPdfValue(object $caseStudy): string
    {
        foreach (['pdf_url', 'pdf'] as $column) {
            $value = trim((string) ($caseStudy->{$column} ?? ''));
            if ($value !== '') {
                return $value;
            }
        }

        return '';
    }

    private function caseStudyPreviewUrl(object $caseStudy): string
    {
        if (!$this->caseStudyHasAttachment($caseStudy) || empty($caseStudy->id)) {
            return '';
        }

        return URL::temporarySignedRoute(
            'case-studies.access',
            now()->addMinutes(30),
            ['caseStudy' => (int) $caseStudy->id, 'preview' => 1]
        );
    }

    private function caseStudyFirstPageText(object $caseStudy): string
    {
        $source = $this->caseStudyPdfSource($caseStudy);
        if ($source === null) {
            return '';
        }

        $cacheKey = 'case_study_first_page_text:' . sha1((string) ($source['cache_key'] ?? ''));

        return Cache::remember($cacheKey, now()->addHours(12), function () use ($source): string {
            $pdfBytes = $this->readPdfSourceBytes($source);
            if ($pdfBytes === '') {
                return '';
            }

            $text = $this->extractPdfFirstPageText($pdfBytes);
            $text = $this->normalizeExtractedPdfText((string) $text);
            $text = $this->stripPdfPreviewNoise($text);
            if ($this->isGenericPdfPreviewText($text)) {
                return '';
            }

            return $text === '' ? '' : Str::limit($text, 1400, '');
        });
    }

    private function caseStudyPreviewParagraphs(string $text): array
    {
        $text = trim($text);
        if ($text === '') {
            return [];
        }

        $sentences = preg_split('/(?<=[.!?])\s+(?=[A-Z0-9(])/', $text) ?: [];
        $sentences = array_values(array_filter(array_map('trim', $sentences), static fn ($sentence) => $sentence !== ''));

        if (empty($sentences)) {
            return [$text];
        }

        $paragraphs = [];
        $current = [];
        foreach ($sentences as $sentence) {
            $current[] = $sentence;
            $sentenceCount = count($current);
            $remaining = count($sentences) - count($paragraphs) * 2 - $sentenceCount;

            if ($sentenceCount >= 2 || $remaining <= 1) {
                $paragraphs[] = trim(implode(' ', $current));
                $current = [];
            }
        }

        if (!empty($current)) {
            $paragraphs[] = trim(implode(' ', $current));
        }

        return array_values(array_filter($paragraphs, static fn ($paragraph) => $paragraph !== ''));
    }

    private function caseStudyPreviewSections(string $text): array
    {
        $text = trim(preg_replace('/\s+/u', ' ', $text) ?? $text);
        if ($text === '') {
            return [];
        }

        $tokens = preg_split('/\s+/', $text) ?: [];
        if (count($tokens) < 6) {
            return [];
        }

        $sections = [];
        $currentHeading = null;
        $currentBodyTokens = [];
        $foundHeading = false;
        $tokenCount = count($tokens);

        for ($i = 0; $i < $tokenCount; ) {
            $headingCount = $this->caseStudyPreviewHeadingTokenCount($tokens, $i);

            if ($headingCount >= 2) {
                if ($currentHeading !== null || !empty($currentBodyTokens)) {
                    $sections[] = $this->buildCaseStudyPreviewSection($currentHeading, $currentBodyTokens);
                    $currentBodyTokens = [];
                }

                $currentHeading = implode(' ', array_slice($tokens, $i, $headingCount));
                $foundHeading = true;
                $i += $headingCount;
                continue;
            }

            $currentBodyTokens[] = $tokens[$i];
            $i++;
        }

        if ($currentHeading !== null || !empty($currentBodyTokens)) {
            $sections[] = $this->buildCaseStudyPreviewSection($currentHeading, $currentBodyTokens);
        }

        $sections = array_values(array_filter($sections, static function (array $section): bool {
            return trim((string) ($section['heading'] ?? '')) !== '' || !empty($section['paragraphs']);
        }));

        return $foundHeading ? $sections : [];
    }

    private function buildCaseStudyPreviewSection(?string $heading, array $bodyTokens): array
    {
        $body = trim(implode(' ', $bodyTokens));
        $paragraphs = $this->caseStudyPreviewParagraphs($body);

        return [
            'heading' => $heading !== null ? $this->caseStudyPreviewHeadingLabel($heading) : '',
            'body' => $body,
            'paragraphs' => $paragraphs,
        ];
    }

    private function caseStudyPreviewHeadingTokenCount(array $tokens, int $startIndex): int
    {
        $count = 0;
        $tokenCount = count($tokens);

        for ($i = $startIndex; $i < $tokenCount; $i++) {
            $token = trim((string) $tokens[$i]);
            if (!$this->caseStudyPreviewIsHeadingToken($token)) {
                break;
            }

            $count++;
        }

        return $count;
    }

    private function caseStudyPreviewIsHeadingToken(string $token): bool
    {
        if ($token === '') {
            return false;
        }

        if (preg_match('/^[A-Z0-9][A-Z0-9&\/\-\(\)]{1,}$/', $token) !== 1) {
            return false;
        }

        if (strlen(preg_replace('/[^A-Z]/', '', $token) ?? '') < 2) {
            return false;
        }

        return true;
    }

    private function caseStudyPreviewHeadingLabel(string $heading): string
    {
        $heading = trim(preg_replace('/\s+/u', ' ', $heading) ?? $heading);
        if ($heading === '') {
            return '';
        }

        return Str::title(Str::lower($heading));
    }

    private function caseStudyPdfSource(object $caseStudy): ?array
    {
        $pdfValue = $this->caseStudyPdfValue($caseStudy);
        if ($pdfValue === '') {
            return null;
        }

        if (preg_match('#^https?://#i', $pdfValue)) {
            return [
                'type' => 'url',
                'value' => $pdfValue,
                'cache_key' => $pdfValue,
            ];
        }

        $fileName = basename($pdfValue);
        $candidatePaths = [
            storage_path('app/private/case_docs/' . $fileName),
            public_path('case_docs/' . $fileName),
            public_path('admin/case_docs/' . $fileName),
            public_path(ltrim($pdfValue, '/')),
        ];

        foreach ($candidatePaths as $path) {
            if (is_file($path)) {
                return [
                    'type' => 'path',
                    'value' => $path,
                    'cache_key' => $path . '|' . (string) @filemtime($path),
                ];
            }
        }

        return null;
    }

    private function readPdfSourceBytes(array $source): string
    {
        $type = (string) ($source['type'] ?? '');
        $value = (string) ($source['value'] ?? '');

        if ($type === 'path') {
            if ($value === '' || !is_readable($value)) {
                return '';
            }

            $bytes = @file_get_contents($value);
            return $bytes === false ? '' : (string) $bytes;
        }

        if ($type === 'url' && $value !== '') {
            try {
                $response = Http::timeout(20)
                    ->retry(1, 200)
                    ->accept('application/pdf')
                    ->get($value);

                if (!$response->successful()) {
                    return '';
                }

                return (string) $response->body();
            } catch (\Throwable $e) {
                Log::warning('Case study PDF preview fetch failed', [
                    'url' => $value,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return '';
    }

    private function extractPdfFirstPageText(string $pdfBytes): string
    {
        $objects = $this->parsePdfObjects($pdfBytes);
        if (empty($objects)) {
            return $this->extractPdfTextFallback($pdfBytes);
        }

        $catalogObjectNumber = null;
        if (preg_match('/trailer\s*<<.*?\/Root\s+(\d+)\s+0\s+R.*?>>/s', $pdfBytes, $matches)) {
            $catalogObjectNumber = (int) $matches[1];
        } else {
            foreach ($objects as $objectNumber => $objectBody) {
                if (preg_match('/\/Type\s*\/Catalog\b/', $objectBody)) {
                    $catalogObjectNumber = (int) $objectNumber;
                    break;
                }
            }
        }

        if ($catalogObjectNumber === null || !isset($objects[$catalogObjectNumber])) {
            return $this->extractPdfTextFallback($pdfBytes);
        }

        $catalogBody = $objects[$catalogObjectNumber];
        if (!preg_match('/\/Pages\s+(\d+)\s+0\s+R/', $catalogBody, $matches)) {
            return $this->extractPdfTextFallback($pdfBytes);
        }

        $pageObjectNumber = $this->findFirstPageObject((int) $matches[1], $objects);
        if ($pageObjectNumber === null || !isset($objects[$pageObjectNumber])) {
            return $this->extractPdfTextFallback($pdfBytes);
        }

        $pageBody = $objects[$pageObjectNumber];
        $contentRefs = $this->extractPdfContentReferences($pageBody);
        if (empty($contentRefs)) {
            return $this->extractPdfTextFallback($pdfBytes);
        }

        $fontMap = $this->extractPdfFirstPageFontMap($pageBody, $objects);

        $segments = [];
        foreach ($contentRefs as $contentRef) {
            if (!isset($objects[$contentRef])) {
                continue;
            }

            $stream = $this->extractPdfStreamData($objects[$contentRef]);
            if ($stream === '') {
                continue;
            }

            $segments[] = $this->extractPdfTextFromContentStream($stream, $fontMap);
        }

        $text = trim(implode(' ', array_filter($segments, static fn ($segment) => trim((string) $segment) !== '')));
        if ($text === '') {
            return $this->extractPdfTextFallback($pdfBytes);
        }

        return $text;
    }

    private function parsePdfObjects(string $pdfBytes): array
    {
        $objects = [];

        if (!preg_match_all('/(\d+)\s+0\s+obj\s*(.*?)\s*endobj/s', $pdfBytes, $matches, PREG_SET_ORDER)) {
            return $objects;
        }

        foreach ($matches as $match) {
            $objects[(int) $match[1]] = (string) $match[2];
        }

        return $objects;
    }

    private function findFirstPageObject(int $pagesObjectNumber, array $objects): ?int
    {
        if (!isset($objects[$pagesObjectNumber])) {
            return null;
        }

        $body = $objects[$pagesObjectNumber];
        if (preg_match('/\/Type\s*\/Page\b/', $body)) {
            return $pagesObjectNumber;
        }

        if (!preg_match('/\/Kids\s*\[(.*?)\]/s', $body, $matches)) {
            return null;
        }

        if (!preg_match_all('/(\d+)\s+0\s+R/', $matches[1], $refs)) {
            return null;
        }

        foreach ($refs[1] as $reference) {
            $reference = (int) $reference;
            if (!isset($objects[$reference])) {
                continue;
            }

            $childBody = $objects[$reference];
            if (preg_match('/\/Type\s*\/Page\b/', $childBody)) {
                return $reference;
            }

            if (preg_match('/\/Type\s*\/Pages\b/', $childBody)) {
                $found = $this->findFirstPageObject($reference, $objects);
                if ($found !== null) {
                    return $found;
                }
            }
        }

        return null;
    }

    private function extractPdfContentReferences(string $pageBody): array
    {
        if (preg_match('/\/Contents\s+(\d+)\s+0\s+R/', $pageBody, $matches)) {
            return [(int) $matches[1]];
        }

        if (!preg_match('/\/Contents\s*\[(.*?)\]/s', $pageBody, $matches)) {
            return [];
        }

        if (!preg_match_all('/(\d+)\s+0\s+R/', $matches[1], $refs)) {
            return [];
        }

        return array_map('intval', $refs[1]);
    }

    private function extractPdfFirstPageFontMap(string $pageBody, array $objects): array
    {
        $resourceBodies = [$pageBody];
        if (preg_match('/\/Resources\s+(\d+)\s+0\s+R/', $pageBody, $matches) && isset($objects[(int) $matches[1]])) {
            $resourceBodies[] = $objects[(int) $matches[1]];
        }

        foreach ($resourceBodies as $resourceBody) {
            if (!preg_match('/\/Font\s*<<(.+?)>>/s', $resourceBody, $fontBlock)) {
                continue;
            }

            if (!preg_match_all('/\/([A-Za-z0-9#]+)\s+(\d+)\s+0\s+R/', $fontBlock[1], $fontRefs, PREG_SET_ORDER)) {
                continue;
            }

            foreach ($fontRefs as $fontRef) {
                $fontObjectNumber = (int) $fontRef[2];
                if (!isset($objects[$fontObjectNumber])) {
                    continue;
                }

                if (!preg_match('/\/ToUnicode\s+(\d+)\s+0\s+R/', $objects[$fontObjectNumber], $matches)) {
                    continue;
                }

                $toUnicodeRef = (int) $matches[1];
                if (!isset($objects[$toUnicodeRef])) {
                    continue;
                }

                $cmapStream = $this->extractPdfStreamData($objects[$toUnicodeRef]);
                if ($cmapStream === '') {
                    continue;
                }

                $map = $this->parsePdfToUnicodeMap($cmapStream);
                if (!empty($map['codes'])) {
                    return $map;
                }
            }
        }

        return [];
    }

    private function extractPdfStreamData(string $objectBody): string
    {
        if (!preg_match('/stream\r?\n(.*?)\r?\nendstream/s', $objectBody, $matches)) {
            return '';
        }

        $streamData = (string) $matches[1];
        $dictionary = strstr($objectBody, 'stream', true) ?: $objectBody;

        if (str_contains($dictionary, '/FlateDecode')) {
            $decoded = @gzuncompress($streamData);
            if ($decoded !== false) {
                return (string) $decoded;
            }

            $decoded = @gzinflate($streamData);
            if ($decoded !== false) {
                return (string) $decoded;
            }

            if (strlen($streamData) > 6) {
                $decoded = @gzinflate(substr($streamData, 2, -4));
                if ($decoded !== false) {
                    return (string) $decoded;
                }
            }
        }

        return $streamData;
    }

    private function extractPdfTextFromContentStream(string $streamData, array $fontMap = []): string
    {
        $segments = [];

        if (preg_match_all('/\((?:\\\\.|[^\\\\)])*\)\s*Tj/s', $streamData, $matches)) {
            foreach ($matches[0] as $token) {
                $segments[] = !empty($fontMap)
                    ? $this->decodePdfBytesWithMap($this->pdfStringTokenToBytes($token), $fontMap)
                    : $this->decodePdfStringToken($token);
            }
        }

        if (preg_match_all('/\((?:\\\\.|[^\\\\)])*\)\s*\'/s', $streamData, $matches)) {
            foreach ($matches[0] as $token) {
                $segments[] = !empty($fontMap)
                    ? $this->decodePdfBytesWithMap($this->pdfStringTokenToBytes($token), $fontMap)
                    : $this->decodePdfStringToken($token);
            }
        }

        if (preg_match_all('/\((?:\\\\.|[^\\\\)])*\)\s*"/s', $streamData, $matches)) {
            foreach ($matches[0] as $token) {
                $segments[] = !empty($fontMap)
                    ? $this->decodePdfBytesWithMap($this->pdfStringTokenToBytes($token), $fontMap)
                    : $this->decodePdfStringToken($token);
            }
        }

        if (preg_match_all('/\[(.*?)\]\s*TJ/s', $streamData, $arrays)) {
            foreach ($arrays[1] as $arrayBody) {
                if (!preg_match_all('/\((?:\\\\.|[^\\\\)])*\)|<[^>]+>/s', $arrayBody, $parts)) {
                    continue;
                }

                $combined = '';
                foreach ($parts[0] as $part) {
                    $combined .= !empty($fontMap)
                        ? $this->decodePdfBytesWithMap($this->pdfStringTokenToBytes($part), $fontMap)
                        : $this->decodePdfStringToken($part);
                }

                if (trim($combined) !== '') {
                    $segments[] = $combined;
                }
            }
        }

        return $this->normalizeExtractedPdfText(implode(' ', array_filter($segments, static fn ($segment) => trim((string) $segment) !== '')));
    }

    private function parsePdfToUnicodeMap(string $cmapStream): array
    {
        $map = [
            'codes' => [],
            'unit_bytes' => 1,
        ];

        if (preg_match('/begincodespacerange\s*(.*?)\s*endcodespacerange/s', $cmapStream, $matches)) {
            if (preg_match('/<([0-9A-Fa-f]+)>\s+<([0-9A-Fa-f]+)>/', $matches[1], $rangeMatch)) {
                $map['unit_bytes'] = max(strlen($rangeMatch[1]), strlen($rangeMatch[2])) / 2;
            }
        }

        if (preg_match_all('/beginbfchar\s*(.*?)\s*endbfchar/s', $cmapStream, $charBlocks)) {
            foreach ($charBlocks[1] as $block) {
                if (!preg_match_all('/<([0-9A-Fa-f]+)>\s*<([0-9A-Fa-f]+)>/', $block, $pairs, PREG_SET_ORDER)) {
                    continue;
                }

                foreach ($pairs as $pair) {
                    $code = hexdec($pair[1]);
                    $map['codes'][$code] = $this->unicodeHexToUtf8($pair[2]);
                }
            }
        }

        if (preg_match_all('/beginbfrange\s*(.*?)\s*endbfrange/s', $cmapStream, $rangeBlocks)) {
            foreach ($rangeBlocks[1] as $block) {
                if (!preg_match_all('/<([0-9A-Fa-f]+)>\s*<([0-9A-Fa-f]+)>\s*<([0-9A-Fa-f]+)>/', $block, $ranges, PREG_SET_ORDER)) {
                    continue;
                }

                foreach ($ranges as $range) {
                    $start = hexdec($range[1]);
                    $end = hexdec($range[2]);
                    $base = hexdec($range[3]);
                    for ($code = $start; $code <= $end; $code++) {
                        $map['codes'][$code] = $this->unicodeHexToUtf8(dechex($base + ($code - $start)));
                    }
                }
            }
        }

        return $map;
    }

    private function unicodeHexToUtf8(string $hex): string
    {
        $hex = preg_replace('/\s+/', '', $hex) ?? '';
        if ($hex === '') {
            return '';
        }

        if (strlen($hex) % 2 === 1) {
            $hex = '0' . $hex;
        }

        $bytes = @hex2bin($hex);
        if ($bytes === false) {
            return '';
        }

        $utf8 = @iconv('UTF-16BE', 'UTF-8', $bytes);
        return $utf8 === false ? $bytes : (string) $utf8;
    }

    private function pdfStringTokenToBytes(string $token): string
    {
        $token = trim($token);
        if ($token === '') {
            return '';
        }

        if ($token[0] === '(' && str_ends_with($token, ')')) {
            $body = substr($token, 1, -1);
            $decoded = '';
            $length = strlen($body);

            for ($i = 0; $i < $length; $i++) {
                $char = $body[$i];
                if ($char !== '\\') {
                    $decoded .= $char;
                    continue;
                }

                $i++;
                if ($i >= $length) {
                    break;
                }

                $next = $body[$i];
                if ($next === 'n') {
                    $decoded .= "\n";
                } elseif ($next === 'r') {
                    $decoded .= "\r";
                } elseif ($next === 't') {
                    $decoded .= "\t";
                } elseif ($next === 'b') {
                    $decoded .= "\b";
                } elseif ($next === 'f') {
                    $decoded .= "\f";
                } elseif ($next === '(' || $next === ')' || $next === '\\') {
                    $decoded .= $next;
                } elseif (preg_match('/[0-7]/', $next)) {
                    $octal = $next;
                    for ($j = 0; $j < 2 && ($i + 1) < $length && preg_match('/[0-7]/', $body[$i + 1]); $j++) {
                        $i++;
                        $octal .= $body[$i];
                    }

                    $decoded .= chr(octdec($octal));
                } elseif ($next === "\r" || $next === "\n") {
                    if ($next === "\r" && ($i + 1) < $length && $body[$i + 1] === "\n") {
                        $i++;
                    }
                } else {
                    $decoded .= $next;
                }
            }

            return $decoded;
        }

        if ($token[0] === '<' && str_ends_with($token, '>')) {
            $hex = preg_replace('/\s+/', '', substr($token, 1, -1));
            if ($hex === '') {
                return '';
            }

            if (strlen($hex) % 2 === 1) {
                $hex .= '0';
            }

            $bytes = @hex2bin($hex);
            return $bytes === false ? '' : (string) $bytes;
        }

        return $token;
    }

    private function decodePdfBytesWithMap(string $bytes, array $fontMap): string
    {
        $codes = $fontMap['codes'] ?? [];
        $unitBytes = max(1, (int) ($fontMap['unit_bytes'] ?? 1));
        if ($bytes === '') {
            return '';
        }

        $length = strlen($bytes);
        if ($unitBytes > 1 && ($length % $unitBytes) === 0 && !empty($codes)) {
            $decoded = '';
            for ($i = 0; $i < $length; $i += $unitBytes) {
                $code = 0;
                for ($j = 0; $j < $unitBytes; $j++) {
                    $code = ($code << 8) | ord($bytes[$i + $j]);
                }

                $decoded .= $codes[$code] ?? '';
            }

            if ($decoded !== '') {
                return $decoded;
            }
        }

        return $this->decodePdfStringToken($this->bytesToPdfLiteralToken($bytes));
    }

    private function bytesToPdfLiteralToken(string $bytes): string
    {
        return '(' . strtr($bytes, [
            '\\' => '\\\\',
            '(' => '\\(',
            ')' => '\\)',
        ]) . ')';
    }

    private function decodePdfStringToken(string $token): string
    {
        $token = trim($token);
        if ($token === '') {
            return '';
        }

        if ($token[0] === '(' && str_ends_with($token, ')')) {
            $body = substr($token, 1, -1);
            $decoded = '';
            $length = strlen($body);

            for ($i = 0; $i < $length; $i++) {
                $char = $body[$i];
                if ($char !== '\\') {
                    $decoded .= $char;
                    continue;
                }

                $i++;
                if ($i >= $length) {
                    break;
                }

                $next = $body[$i];
                if ($next === 'n') {
                    $decoded .= "\n";
                } elseif ($next === 'r') {
                    $decoded .= "\r";
                } elseif ($next === 't') {
                    $decoded .= "\t";
                } elseif ($next === 'b') {
                    $decoded .= "\b";
                } elseif ($next === 'f') {
                    $decoded .= "\f";
                } elseif ($next === '(' || $next === ')' || $next === '\\') {
                    $decoded .= $next;
                } elseif (preg_match('/[0-7]/', $next)) {
                    $octal = $next;
                    for ($j = 0; $j < 2 && ($i + 1) < $length && preg_match('/[0-7]/', $body[$i + 1]); $j++) {
                        $i++;
                        $octal .= $body[$i];
                    }

                    $decoded .= chr(octdec($octal));
                } elseif ($next === "\r" || $next === "\n") {
                    if ($next === "\r" && ($i + 1) < $length && $body[$i + 1] === "\n") {
                        $i++;
                    }
                } else {
                    $decoded .= $next;
                }
            }

            return $decoded;
        }

        if ($token[0] === '<' && str_ends_with($token, '>')) {
            $hex = preg_replace('/\s+/', '', substr($token, 1, -1));
            if ($hex === '') {
                return '';
            }

            if (strlen($hex) % 2 === 1) {
                $hex .= '0';
            }

            $decoded = '';
            for ($i = 0; $i < strlen($hex); $i += 2) {
                $pair = substr($hex, $i, 2);
                $byte = hexdec($pair);
                $decoded .= chr($byte);
            }

            return $decoded;
        }

        return $token;
    }

    private function extractPdfTextFallback(string $pdfBytes): string
    {
        if (!preg_match_all('/\((?:\\\\.|[^\\\\)])*\)/s', $pdfBytes, $matches)) {
            return '';
        }

        $segments = [];
        foreach (array_slice($matches[0], 0, 12) as $token) {
            $segments[] = $this->decodePdfStringToken($token);
        }

        return $this->normalizeExtractedPdfText(implode(' ', array_filter($segments, static fn ($segment) => trim((string) $segment) !== '')));
    }

    private function normalizeExtractedPdfText(string $text): string
    {
        $text = str_replace("\0", '', $text);
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]+/', ' ', $text) ?? $text;
        $text = preg_replace('/\s+([,.;:!?])/', '$1', $text) ?? $text;
        $text = preg_replace('/([(\[])\s+/', '$1', $text) ?? $text;
        $text = preg_replace('/\s+([)\]])/', '$1', $text) ?? $text;
        $text = preg_replace_callback('/\b(?:[A-Za-z]{1,3}\s+){2,}[A-Za-z]{1,3}\b/', function (array $matches): string {
            return preg_replace('/\s+/', '', $matches[0]) ?? $matches[0];
        }, $text) ?? $text;
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

        return trim($text);
    }

    private function stripPdfPreviewNoise(string $text): string
    {
        $text = preg_replace('/^(?:www\.[^\s]+\.com\s*)+/i', '', $text) ?? $text;
        $text = preg_replace('/^Page\s+\d+\s+of\s+\d+\s*/i', '', $text) ?? $text;
        $text = preg_replace('/^\s*SPECIFICATIONS\s+SCOPE\s*/i', 'SPECIFICATIONS SCOPE ', $text) ?? $text;
        $text = preg_replace('/\s+/', ' ', $text) ?? $text;

        return trim($text);
    }

    private function isGenericPdfPreviewText(string $text): bool
    {
        $haystack = strtolower($text);
        $markers = [
            'specifications scope',
            'pre-proposal conference',
            'request for proposal',
            'meeting id:',
            'passcode:',
            'dial in by phone',
            'harris county',
            'the responsibility of each vendor',
        ];

        foreach ($markers as $marker) {
            if (str_contains($haystack, $marker)) {
                return true;
            }
        }

        return false;
    }

    private function whitePaperHasAttachment(object $paper): bool
    {
        return $this->whitePaperPdfValue($paper) !== '';
    }

    private function whitePaperPdfValue(object $paper): string
    {
        foreach (['pdf', 'pdf_url'] as $column) {
            if (!Schema::hasColumn('white_paper', $column)) {
                continue;
            }

            $value = trim((string) ($paper->{$column} ?? ''));
            if ($value !== '') {
                return $value;
            }
        }

        return '';
    }

    private function whitePaperPreviewUrl(object $paper): string
    {
        if (!$this->whitePaperHasAttachment($paper) || empty($paper->id)) {
            return '';
        }

        return URL::temporarySignedRoute(
            'white-papers.access',
            now()->addMinutes(30),
            ['paper' => (int) $paper->id, 'preview' => 1]
        );
    }

    private function whitePaperCaseStudyViewModel(object $paper): object
    {
        $viewModel = new \stdClass();
        $title = trim((string) ($paper->title ?? 'White Paper'));
        $category = trim((string) ($paper->category ?? 'White Paper'));
        $previewSource = trim((string) ($paper->body ?? $paper->preview ?? ''));
        $previewText = $this->makePreviewText($previewSource !== '' ? $previewSource : $title, 320);
        $paragraphs = $this->splitWhitePaperPreviewParagraphs($previewSource !== '' ? $previewSource : $previewText);

        $viewModel->id = $paper->id ?? null;
        $viewModel->slug = $paper->slug ?? $this->resourceSlug($paper);
        $viewModel->title = $title;
        $viewModel->display_title = $title;
        $viewModel->category = $category !== '' ? $category : 'White Paper';
        $viewModel->technology_label = 'Microsoft Platform';
        $viewModel->listing_image = '';
        $viewModel->preview = $previewText;
        $viewModel->body = $previewSource;
        $viewModel->pdf_preview_text = $previewText;
        $viewModel->pdf_preview_source = $previewText !== '' ? 'PDF text' : 'PDF unavailable';
        $viewModel->pdf_preview_sections = $previewText !== ''
            ? [[
                'heading' => 'Overview',
                'paragraphs' => $paragraphs,
            ]]
            : [];
        $viewModel->pdf_preview_paragraphs = $paragraphs;
        $viewModel->outcome_tag = 'Full PDF access';
        $viewModel->results = [
            'Request secure access',
            'Review the previewed first page',
            'Download the full white paper',
        ];
        $viewModel->services = ['White Paper'];
        $viewModel->hero_copy = $previewText;

        return $viewModel;
    }

    private function splitWhitePaperPreviewParagraphs(string $text): array
    {
        $cleaned = trim((string) preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8')));
        if ($cleaned === '') {
            return [];
        }

        $parts = preg_split('/(?<=[.!?])\s+/', $cleaned) ?: [];
        $parts = array_values(array_filter(array_map(static function ($part) {
            return trim((string) $part);
        }, $parts)));

        return array_slice($parts, 0, 5);
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
        if ($this->safeHasColumn('industry_listings', 'pdf')) {
            $columns[] = 'pdf';
        }
        if ($this->safeHasColumn('industry_listings', 'outcome_tag')) {
            $columns[] = 'outcome_tag';
        }
        if ($this->safeHasColumn('industry_listings', 'title')) {
            $columns[] = 'title';
        }

        return $columns;
    }

    private function whitePaperSelectColumns(): array
    {
        $columns = ['id'];
        foreach (['title', 'body', 'images', 'pdf', 'slug', 'meta_description', 'topic', 'category', 'industry'] as $column) {
            if ($this->safeHasColumn('white_paper', $column)) {
                $columns[] = $column;
            }
        }

        return $columns;
    }

    private function whitePaperTopicTerms(string $topic): array
    {
        return match ($topic) {
            'fabric-data' => ['fabric', 'power bi', 'data', 'analytics', 'warehouse', 'lakehouse'],
            'power-platform' => ['power platform', 'power apps', 'power automate', 'power pages'],
            'ai-cognitive-services' => ['ai', 'copilot', 'cognitive', 'agent'],
            'sharepoint-collaboration' => ['sharepoint', 'teams', 'collaboration', 'intranet'],
            default => [Str::lower((string) ($this->topicFilters()[$topic] ?? $topic))],
        };
    }

    private function whitePaperSampleCatalog(): array
    {
        return [
            'microsoft-copilot-commercial-enterprise' => [
                'title' => 'Microsoft Copilot Commercial Enterprise Guide',
                'meta_description' => 'Armely guidance for commercial enterprises planning Microsoft Copilot adoption, governance, readiness, security, and measurable AI productivity outcomes.',
                'body' => 'A practical guide for commercial enterprise leaders planning Microsoft Copilot adoption. Preview the readiness questions, governance decisions, security model, and rollout patterns that help organizations move from experimentation to measurable productivity.',
                'topic' => 'ai-cognitive-services',
            ],
            'microsoft-copilot-public-sector' => [
                'title' => 'Microsoft Copilot Public Sector Guide',
                'meta_description' => 'Armely public sector guidance for Microsoft Copilot readiness, governance, compliance, and responsible AI adoption across agencies.',
                'body' => 'A public sector planning guide for agency CIOs and technology leaders. The preview covers governance, data protection, responsible AI controls, and adoption planning for Microsoft Copilot in regulated environments.',
                'topic' => 'ai-cognitive-services',
            ],
            'microsoft-copilot-commercial-cxo-guide' => [
                'title' => 'Microsoft Copilot Commercial CXO Guide',
                'meta_description' => 'A condensed Armely CXO guide to Microsoft Copilot strategy, business value, governance, and executive adoption planning.',
                'body' => 'A concise executive guide for commercial leaders evaluating Microsoft Copilot. It frames business value, readiness, governance, and rollout priorities in CXO language before the full gated download.',
                'topic' => 'ai-cognitive-services',
            ],
            'microsoft-copilot-government-cxo-guide' => [
                'title' => 'Microsoft Copilot Government CXO Guide',
                'meta_description' => 'A condensed Armely CXO guide for government Microsoft Copilot strategy, compliance, governance, and responsible adoption.',
                'body' => 'A concise executive guide for government and public sector leaders evaluating Microsoft Copilot. It summarizes the governance, compliance, security, and adoption decisions that should be clear before deployment.',
                'topic' => 'ai-cognitive-services',
            ],
            'power-platform-governance-playbook' => [
                'title' => 'Power Platform Governance Playbook',
                'meta_description' => 'A practical guide to Power Platform governance, environment strategy, and maker enablement for enterprise teams.',
                'body' => 'A practical governance playbook for teams standardizing Power Apps, Power Automate, and Power Pages. The preview focuses on environment strategy, makers, approvals, and how to scale responsibly without blocking delivery.',
                'topic' => 'power-platform',
            ],
            'fabric-analytics-readiness-guide' => [
                'title' => 'Microsoft Fabric Analytics Readiness Guide',
                'meta_description' => 'See how organizations can prepare for Microsoft Fabric adoption with a practical readiness and governance approach.',
                'body' => 'A readiness guide for data leaders evaluating Microsoft Fabric. It outlines where Fabric fits, what to plan before implementation, and how to connect data, governance, and reporting without creating another silo.',
                'topic' => 'fabric-data',
            ],
        ];
    }

    private function whitePaperSampleCollection(): \Illuminate\Support\Collection
    {
        return collect($this->whitePaperSampleCatalog())->map(function (array $paper, string $slug) {
            return (object) array_merge([
                'id' => null,
                'images' => null,
                'pdf' => null,
                'slug' => $slug,
                'category' => 'White Paper',
                'industry' => '',
                'topic' => '',
            ], $paper, [
                'slug' => $slug,
                'preview' => $this->makePreviewText((string) ($paper['body'] ?? ''), 120),
            ]);
        })->values();
    }

    private function whitePaperSamplePaginator(Request $request): LengthAwarePaginator
    {
        $topic = (string) $request->query('white_topic', '');
        $hasActiveFilter = false;
        $items = $this->whitePaperSampleCollection();

        if ($topic !== '' && array_key_exists($topic, $this->topicFilters())) {
            $hasActiveFilter = true;
            $terms = $this->whitePaperTopicTerms($topic);
            $items = $items->filter(function (object $paper) use ($terms) {
                $haystack = Str::lower(trim(implode(' ', array_filter([
                    (string) ($paper->title ?? ''),
                    (string) ($paper->body ?? ''),
                    (string) ($paper->meta_description ?? ''),
                    (string) ($paper->slug ?? ''),
                    (string) ($paper->topic ?? ''),
                    (string) ($paper->category ?? ''),
                    (string) ($paper->industry ?? ''),
                ]))));

                foreach ($terms as $term) {
                    if ($term !== '' && str_contains($haystack, Str::lower((string) $term))) {
                        return true;
                    }
                }

                return false;
            })->values();
        }

        $perPage = $hasActiveFilter ? max($items->count(), 1) : 6;
        $currentPage = max((int) $request->query('paper_page', 1), 1);
        $pagedItems = $items->slice(($currentPage - 1) * $perPage, $perPage)->values();

        return new LengthAwarePaginator(
            $pagedItems,
            $items->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
                'pageName' => 'paper_page',
            ]
        );
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
            $selectColumns = ['id', 'category', 'pdf_url'];
            if ($this->safeHasColumn('industry_listings', 'pdf')) {
                $selectColumns[] = 'pdf';
            }

            $query = DB::table('industry_listings')
                ->select($selectColumns)
                ->where('id', $caseStudyId);

            if (Schema::hasColumn('industry_listings', 'title')) {
                $query->addSelect('title');
            }

            $record = $query->first();

            if ($record && $this->caseStudyPdfValue($record) !== '') {
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
            $query = DB::table('white_paper')
                ->select('id', 'title')
                ->where('id', $whitePaperId);

            if (Schema::hasColumn('white_paper', 'pdf')) {
                $query->addSelect('pdf');
            } elseif (Schema::hasColumn('white_paper', 'pdf_url')) {
                $query->addSelect('pdf_url');
            }

            $record = $query->first();

            if ($record && $this->whitePaperHasAttachment($record)) {
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
            if ($slug !== '') {
                $caseStudy = $this->findCaseStudyBySlug($slug);
                if ($caseStudy && $this->caseStudyHasAttachment($caseStudy)) {
                    return [
                        'resource_title' => $this->caseStudyDisplayTitle($caseStudy),
                        'resource_type_label' => 'Case Study',
                        'download_url' => URL::temporarySignedRoute('case-studies.access', $expiresAt, ['caseStudy' => (int) $caseStudy->id, 'em' => sha1($email)]),
                        'expires_at' => $expiresAt->format('M d, Y h:i A T'),
                    ];
                }

                $whitePaper = $this->findWhitePaperBySlug($slug);
                if ($whitePaper && $this->whitePaperHasAttachment($whitePaper)) {
                    return [
                        'resource_title' => (string) ($whitePaper->title ?? 'White Paper'),
                        'resource_type_label' => 'White Paper',
                        'download_url' => URL::temporarySignedRoute('white-papers.access', $expiresAt, ['paper' => (int) $whitePaper->id, 'em' => sha1($email)]),
                        'expires_at' => $expiresAt->format('M d, Y h:i A T'),
                    ];
                }
            }

            $staticResource = $this->staticResourceBySlug($slug);
            if ($staticResource && $this->isTableQueryable('white_paper')) {
                $resolvedPaper = $this->findWhitePaperBySlug((string) ($staticResource->slug ?? $slug));
                if ($resolvedPaper && $this->whitePaperHasAttachment($resolvedPaper)) {
                    return [
                        'resource_title' => (string) ($resolvedPaper->title ?? $staticResource->title),
                        'resource_type_label' => 'White Paper',
                        'download_url' => URL::temporarySignedRoute('white-papers.access', $expiresAt, ['paper' => (int) $resolvedPaper->id, 'em' => sha1($email)]),
                        'expires_at' => $expiresAt->format('M d, Y h:i A T'),
                    ];
                }
            }
        }

        return null;
    }

    private function downloadRemotePdf(string $url, string $fallbackName, bool $inlinePreview = false)
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
                'Content-Disposition' => ($inlinePreview ? 'inline' : 'attachment') . '; filename="' . $filename . '"',
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
            return $this->whitePaperSamplePaginator($request);
        }

        try {
            $query = DB::table('white_paper')
                ->select($this->whitePaperSelectColumns())
                ->orderByDesc('id');

            if ((clone $query)->count() === 0) {
                return $this->whitePaperSamplePaginator($request);
            }

            $searchableColumns = $this->whitePaperSearchColumns();
            $topic = (string) $request->query('white_topic', '');
            $hasActiveFilter = false;
            if ($topic !== '' && array_key_exists($topic, $this->topicFilters()) && !empty($searchableColumns)) {
                $hasActiveFilter = true;
                $terms = $this->whitePaperTopicTerms($topic);

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

                return $this->whitePaperSamplePaginator($request);
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
