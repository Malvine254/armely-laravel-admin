<?php

namespace App\Http\Controllers;

use App\Services\AzureMailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CaseStudiesController extends Controller
{
    public function index(Request $request)
    {
        // Paginate case studies (6 per page)
        $caseStudies = DB::table('industry_listings')
            ->select('id', 'category', 'listing_image', 'body', 'pdf_url')
            ->orderByDesc('id')
            ->paginate(6, ['*'], 'case_page');

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
            'grantedCaseStudyIds' => $this->getGrantedCaseStudyIds($request),
            'grantedWhitePaperIds' => $this->getGrantedWhitePaperIds($request),
        ]);
    }

    public function submitLead(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc,dns', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'organization' => ['required', 'string', 'max:255'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:120'],
            'interest' => ['required', 'in:case-studies,white-papers,both'],
            'message' => ['nullable', 'string'],
            'website' => ['nullable', 'string', 'max:255'],
            'requested_resource' => ['nullable', 'string', 'max:255'],
            'case_study_id' => ['nullable', 'integer', 'exists:industry_listings,id'],
            'white_paper_id' => ['nullable', 'integer', 'exists:white_paper,id'],
            'g-recaptcha-response' => ['required', 'string'],
        ], [
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
            'email' => strtolower($data['email']),
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
            'email' => strtolower($data['email']),
            'organization' => $data['organization'],
            'phone' => $data['phone'],
            'message' => $composedMessage,
            'subject' => $subject,
            'sent_date' => now()->format('Y-m-d H:i:s'),
        ]);

        $this->sendCaseStudyLeadEmail([
            'name' => $data['name'],
            'email' => strtolower($data['email']),
            'phone' => $data['phone'],
            'organization' => $data['organization'],
            'job_title' => $data['job_title'] ?? '',
            'country' => $data['country'] ?? '',
            'interest' => $interestLabel,
            'requested_resource' => $data['requested_resource'] ?? '',
            'case_study_id' => (string) ($data['case_study_id'] ?? ''),
            'white_paper_id' => (string) ($data['white_paper_id'] ?? ''),
            'message' => $notes,
        ]);

        $caseStudyId = (int) ($data['case_study_id'] ?? 0);
        if ($caseStudyId > 0) {
            $email = strtolower(trim($data['email']));
            $this->grantCaseStudyAccess($request, $email, $caseStudyId);

            return redirect()->route('case-studies.access', ['caseStudy' => $caseStudyId]);
        }

        $whitePaperId = (int) ($data['white_paper_id'] ?? 0);
        if ($whitePaperId > 0) {
            $email = strtolower(trim($data['email']));
            $this->grantWhitePaperAccess($request, $email, $whitePaperId);

            return redirect()->route('white-papers.access', ['paper' => $whitePaperId]);
        }

        return back()->with('status', 'Thanks! We will share the relevant case studies and white papers shortly.');
    }

    public function accessCaseStudy(Request $request, int $caseStudy)
    {
        if (!$this->hasCaseStudyAccess($request, $caseStudy)) {
            return redirect()->route('case-studies.index')
                ->withErrors(['access' => 'Please complete the form to unlock this case study first.']);
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
            return redirect()->away($pdfUrl);
        }

        $fileName = basename($pdfUrl);
        $privatePath = storage_path('app/private/case_docs/' . $fileName);
        $publicPath = public_path('case_docs/' . $fileName);

        if (is_file($privatePath)) {
            return response()->file($privatePath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $fileName . '"',
            ]);
        }

        if (is_file($publicPath)) {
            return response()->file($publicPath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $fileName . '"',
            ]);
        }

        abort(404);
    }

    public function legacyCaseDoc(Request $request, string $file)
    {
        $caseStudy = DB::table('industry_listings')
            ->select('id')
            ->where('pdf_url', $file)
            ->orWhere('pdf_url', 'like', '%/' . $file)
            ->first();

        if (!$caseStudy) {
            return redirect()->route('case-studies.index')
                ->withErrors(['access' => 'This case study document is not available.']);
        }

        return redirect()->route('case-studies.access', ['caseStudy' => (int) $caseStudy->id]);
    }

    public function accessWhitePaper(Request $request, int $paper)
    {
        if (!$this->hasWhitePaperAccess($request, $paper)) {
            return redirect()->route('case-studies.index')
                ->withErrors(['access' => 'Please complete the form to unlock this white paper first.']);
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
            return redirect()->away($pdfValue);
        }

        $fileName = basename($pdfValue);
        $privatePath = storage_path('app/private/white_paper_docs/' . $fileName);
        $publicPath = public_path('white_paper_docs/' . $fileName);

        if (is_file($privatePath)) {
            return response()->file($privatePath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $fileName . '"',
            ]);
        }

        if (is_file($publicPath)) {
            return response()->file($publicPath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $fileName . '"',
            ]);
        }

        abort(404);
    }

    public function legacyWhitePaperDoc(Request $request, string $file)
    {
        $paper = DB::table('white_paper')
            ->select('id')
            ->where('pdf', $file)
            ->orWhere('pdf', 'like', '%/' . $file)
            ->first();

        if (!$paper) {
            return redirect()->route('case-studies.index')
                ->withErrors(['access' => 'This white paper document is not available.']);
        }

        return redirect()->route('white-papers.access', ['paper' => (int) $paper->id]);
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
            $fromEmail = env('FROM_EMAIL', env('MAIL_FROM_ADDRESS'));
            $adminEmail = env('ADMIN_EMAIL', $fromEmail);
            if (!$fromEmail || !$adminEmail) {
                Log::warning('Case studies lead email skipped: missing FROM_EMAIL/ADMIN_EMAIL');
                return;
            }

            $html =
                '<p><strong>New resource request from Case Studies page</strong></p>' .
                '<p><strong>Name:</strong> ' . e($payload['name']) . '<br>' .
                '<strong>Email:</strong> ' . e($payload['email']) . '<br>' .
                '<strong>Phone:</strong> ' . e($payload['phone']) . '<br>' .
                '<strong>Organization:</strong> ' . e($payload['organization']) . '<br>' .
                '<strong>Job Title:</strong> ' . e($payload['job_title']) . '<br>' .
                '<strong>Country/Region:</strong> ' . e($payload['country']) . '<br>' .
                '<strong>Interest:</strong> ' . e($payload['interest']) . '<br>' .
                '<strong>Requested Resource:</strong> ' . e($payload['requested_resource']) . '<br>' .
                '<strong>Requested Case Study ID:</strong> ' . e($payload['case_study_id']) . '<br>' .
                '<strong>Requested White Paper ID:</strong> ' . e($payload['white_paper_id']) . '<br>' .
                '<strong>Lead Source:</strong> Case Studies Modal</p>' .
                '<p><strong>Additional Notes:</strong><br>' . nl2br(e($payload['message'] ?: 'N/A')) . '</p>';

            $mailer = new AzureMailService();
            $mailer->sendEmail($fromEmail, $adminEmail, 'Case Studies Lead: ' . $payload['interest'], $html);

            if (strtolower($adminEmail) !== 'ask.me@armely.com') {
                $mailer->sendEmail($fromEmail, 'ask.me@armely.com', 'Case Studies Lead: ' . $payload['interest'], $html);
            }

            $userHtml =
                '<p>Hi ' . e($payload['name']) . ',</p>' .
                '<p>Thanks for your interest in Armely resources. We received your request for <strong>' . e($payload['interest']) . '</strong> and our team will share the relevant materials soon.</p>' .
                '<p>Best regards,<br>Armely Team</p>';
            $mailer->sendEmail($fromEmail, $payload['email'], 'Your Armely resource request', $userHtml);
        } catch (\Throwable $e) {
            Log::warning('Case studies lead email failed', ['error' => $e->getMessage()]);
        }
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
}
