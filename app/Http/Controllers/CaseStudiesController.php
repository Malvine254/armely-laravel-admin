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
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class CaseStudiesController extends Controller
{
    public function index(Request $request)
    {
        // Paginate case studies (6 per page)
        $caseStudies = $this->paginateCaseStudies($request);

        $caseStudies->getCollection()->transform(function ($caseStudy) {
            $caseStudy->preview = $this->makePreviewText((string) ($caseStudy->body ?? ''), 120);

            return $caseStudy;
        });

        // Paginate white papers (6 per page)
        $whitePapers = DB::table('white_paper')
            ->select('id', 'title', 'body', 'images', 'pdf')
            ->orderByDesc('id')
            ->paginate(6, ['*'], 'paper_page');

        $whitePapers->getCollection()->transform(function ($paper) {
            $paper->preview = $this->makePreviewText((string) ($paper->body ?? ''), 120);

            return $paper;
        });

        return view('case-studies.index', [
            'caseStudies' => $caseStudies,
            'whitePapers' => $whitePapers,
            'recaptchaSiteKey' => config('services.recaptcha.site_key', ''),
        ]);
    }

    public function submitLead(Request $request)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc,dns,spoof,filter', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'organization' => ['required', 'string', 'max:255'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:120'],
            'interest' => ['required', 'in:case-studies,white-papers,both'],
            'message' => ['nullable', 'string'],
            'website' => ['nullable', 'string', 'max:255'],
            'requested_resource' => ['nullable', 'string', 'max:255'],
            'case_study_id' => ['nullable', 'integer'],
            'white_paper_id' => ['nullable', 'integer', 'exists:white_paper,id'],
            'g-recaptcha-response' => ['required', 'string'],
        ];

        if ($this->isTableQueryable('industry_listings')) {
            $rules['case_study_id'][] = 'exists:industry_listings,id';
        }

        $data = $request->validate($rules, [
            'name.required' => 'Name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Please enter a valid work email with a valid domain.',
            'phone.required' => 'Phone number is required.',
            'organization.required' => 'Organization name is required.',
            'interest.required' => 'Please select what you are interested in.',
            'g-recaptcha-response.required' => 'Please verify that you are not a robot.',
        ]);

        if (!empty($data['website'])) {
            return back()->withErrors(['form' => 'Spam detected.'])->withInput();
        }

        if (!$this->verifyRecaptcha($data['g-recaptcha-response'])) {
            return back()->withErrors(['captcha' => 'reCAPTCHA verification failed. Please try again.'])->withInput();
        }

        $normalizedEmail = strtolower(trim((string) ($data['email'] ?? '')));
        if (!$this->isDeliverableEmail($normalizedEmail)) {
            return back()->withErrors(['email' => 'Please provide a valid business email that can receive messages.'])->withInput();
        }

        $downloadDetails = $this->buildDownloadDetails($data, $normalizedEmail);
        if ($downloadDetails === null) {
            return back()->withErrors(['resource' => 'The requested resource is unavailable. Please choose another resource.'])->withInput();
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
            'phone' => $data['phone'],
            'organization' => $data['organization'],
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
            'organization' => $data['organization'],
            'phone' => $data['phone'],
            'message' => $composedMessage,
            'subject' => $subject,
            'sent_date' => now()->format('Y-m-d H:i:s'),
        ]);

        $this->sendCaseStudyLeadEmail([
            'name' => $data['name'],
            'email' => $normalizedEmail,
            'phone' => $data['phone'],
            'organization' => $data['organization'],
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

        $message = 'Thanks! Your secure download link has been sent by email. It expires in 1 hour.';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'ok' => true,
                'message' => $message,
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
            abort(404);
        }

        $pdfUrl = (string) $item->pdf_url;
        if (str_starts_with($pdfUrl, 'http://') || str_starts_with($pdfUrl, 'https://')) {
            return $this->downloadRemotePdf($pdfUrl, 'case-study-' . $caseStudy . '.pdf');
        }

        $fileName = basename($pdfUrl);
        $privatePath = storage_path('app/private/case_docs/' . $fileName);
        $publicPath = public_path('case_docs/' . $fileName);

        if (is_file($privatePath)) {
            return response()->download($privatePath, $fileName, [
                'Content-Type' => 'application/pdf',
            ]);
        }

        if (is_file($publicPath)) {
            return response()->download($publicPath, $fileName, [
                'Content-Type' => 'application/pdf',
            ]);
        }

        abort(404);
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

        $item = DB::table('white_paper')
            ->select('id', 'pdf')
            ->where('id', $paper)
            ->first();

        if (!$item || empty($item->pdf)) {
            abort(404);
        }

        $pdfValue = (string) $item->pdf;
        if (str_starts_with($pdfValue, 'http://') || str_starts_with($pdfValue, 'https://')) {
            return $this->downloadRemotePdf($pdfValue, 'white-paper-' . $paper . '.pdf');
        }

        $fileName = basename($pdfValue);
        $privatePath = storage_path('app/private/white_paper_docs/' . $fileName);
        $publicPath = public_path('white_paper_docs/' . $fileName);

        if (is_file($privatePath)) {
            return response()->download($privatePath, $fileName, [
                'Content-Type' => 'application/pdf',
            ]);
        }

        if (is_file($publicPath)) {
            return response()->download($publicPath, $fileName, [
                'Content-Type' => 'application/pdf',
            ]);
        }

        abort(404);
    }

    public function legacyWhitePaperDoc(Request $request, string $file)
    {
        return redirect()->route('case-studies.index')
            ->withErrors(['access' => 'Direct document links are disabled. Please request a secure download link from the form.']);
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

    private function sendCaseStudyLeadEmail(array $payload): void
    {
        try {
            $fromEmail = AzureMailService::outboundFromEmail();
            $adminEmail = env('ADMIN_EMAIL', $fromEmail);
            if (!$fromEmail || !$adminEmail) {
                Log::warning('Case studies lead email skipped: missing NO_REPLY_EMAIL/FROM_EMAIL/ADMIN_EMAIL');
                return;
            }

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

            $mailer = new AzureMailService();
            $mailer->sendEmail($fromEmail, $adminEmail, 'Case Studies Lead: ' . $payload['interest'], $html);

            if (strtolower($adminEmail) !== 'ask.me@armely.com') {
                $mailer->sendEmail($fromEmail, 'ask.me@armely.com', 'Case Studies Lead: ' . $payload['interest'], $html);
            }

            if (!$this->isDeliverableEmail((string) $payload['email'])) {
                Log::warning('Case studies lead user email skipped due to deliverability verification failure', [
                    'email' => $payload['email'],
                ]);
                return;
            }

            $userHtml = view('emails.case-studies.resource-download', [
                'name' => $payload['name'],
                'resourceTitle' => $payload['resource_title'],
                'resourceTypeLabel' => $payload['resource_type_label'],
                'downloadUrl' => $payload['download_url'],
                'expiresAt' => $payload['expires_at'],
            ])->render();

            $sent = $mailer->sendEmail($fromEmail, $payload['email'], 'Your secure Armely download link', $userHtml);
            if (!$sent) {
                AzureMailService::markEmailAsSuppressed((string) $payload['email']);
            }
        } catch (\Throwable $e) {
            Log::warning('Case studies lead email failed', ['error' => $e->getMessage()]);
        }
    }

    private function buildDownloadDetails(array $data, string $email): ?array
    {
        $expiresAt = now()->addHour();
        $caseStudyId = (int) ($data['case_study_id'] ?? 0);
        if ($caseStudyId > 0 && $this->isTableQueryable('industry_listings')) {
            $record = DB::table('industry_listings')
                ->select('id', 'category')
                ->where('id', $caseStudyId)
                ->first();

            if ($record) {
                return [
                    'resource_title' => (string) ($record->category ?? ('Case Study #' . $caseStudyId)),
                    'resource_type_label' => 'Case Study',
                    'download_url' => URL::temporarySignedRoute('case-studies.access', $expiresAt, ['caseStudy' => $caseStudyId, 'em' => sha1($email)]),
                    'expires_at' => $expiresAt->format('M d, Y h:i A T'),
                ];
            }
        }

        $whitePaperId = (int) ($data['white_paper_id'] ?? 0);
        if ($whitePaperId > 0) {
            $record = DB::table('white_paper')
                ->select('id', 'title')
                ->where('id', $whitePaperId)
                ->first();

            if ($record) {
                return [
                    'resource_title' => (string) ($record->title ?? ('White Paper #' . $whitePaperId)),
                    'resource_type_label' => 'White Paper',
                    'download_url' => URL::temporarySignedRoute('white-papers.access', $expiresAt, ['paper' => $whitePaperId, 'em' => sha1($email)]),
                    'expires_at' => $expiresAt->format('M d, Y h:i A T'),
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
                abort(404);
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
            abort(404);
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
            return DB::table('industry_listings')
                ->select('id', 'category', 'listing_image', 'body', 'pdf_url')
                ->orderByDesc('id')
                ->paginate(6, ['*'], 'case_page');
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

    private function isTableQueryable(string $table): bool
    {
        try {
            return Schema::hasTable($table);
        } catch (QueryException $e) {
            if ($this->isMissingTableException($e)) {
                Log::warning('Database table unavailable during schema check', [
                    'table' => $table,
                    'error' => $e->getMessage(),
                ]);

                return false;
            }

            throw $e;
        }
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
}
