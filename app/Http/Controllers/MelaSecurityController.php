<?php

namespace App\Http\Controllers;

use App\Services\AzureMailService;
use App\Services\NewsletterNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class MelaSecurityController extends Controller
{
    private const DOCUMENT_PATH = 'private/mela_docs/mela-meeting-assistant-security-guide.pdf';
    private const DOWNLOAD_FILENAME = 'Mela-Meeting-Assistant-Security-Guide.pdf';

    public function show()
    {
        return view('legal.mela-security-and-compliance', [
            'recaptchaSiteKey' => config('services.recaptcha.site_key', ''),
        ]);
    }

    public function submitRequest(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc,filter', 'max:255'],
            'organization' => ['nullable', 'string', 'max:255'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50', 'regex:/^\+?[0-9][0-9\s().-]{6,19}$/'],
            'message' => ['nullable', 'string', 'max:2000'],
            'website' => ['nullable', 'string', 'max:255'],
            'g-recaptcha-response' => ['required', 'string'],
        ], [
            'name.required' => 'Name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Please enter a valid work email address.',
            'phone.regex' => 'Please enter a valid phone number.',
            'g-recaptcha-response.required' => 'Please verify that you are not a robot.',
        ]);

        // Honeypot field.
        if (!empty($data['website'])) {
            return $this->errorResponse($request, ['form' => ['Spam detected.']]);
        }

        if (!$this->verifyRecaptcha($data['g-recaptcha-response'])) {
            return $this->errorResponse($request, ['captcha' => ['reCAPTCHA verification failed. Please try again.']]);
        }

        $normalizedEmail = strtolower(trim((string) $data['email']));
        if (!AzureMailService::isDeliverableEmail($normalizedEmail)) {
            return $this->errorResponse($request, ['email' => ['Please provide a valid business email that can receive messages.']]);
        }

        if (!is_file(storage_path('app/' . self::DOCUMENT_PATH))) {
            Log::error('Mela documentation request failed: source PDF missing on disk');
            return $this->errorResponse($request, ['form' => ['The documentation package is temporarily unavailable. Please try again shortly or email info@armely.com.']]);
        }

        $requestId = DB::table('mela_documentation_requests')->insertGetId([
            'name' => $data['name'],
            'email' => $normalizedEmail,
            'organization' => $data['organization'] ?? null,
            'job_title' => $data['job_title'] ?? null,
            'phone' => $data['phone'] ?? null,
            'message' => $data['message'] ?? null,
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
            'message' => "Mela Meeting Assistant Security & Compliance documentation requested.\n\nJob Title: " . ($data['job_title'] ?? 'N/A') . "\nNotes:\n" . ($data['message'] ?? 'N/A'),
            'subject' => 'Mela Security & Compliance Documentation Request',
            'sent_date' => now()->format('Y-m-d H:i:s'),
        ]);

        $expiresAt = now()->addDays(7);
        $downloadUrl = URL::temporarySignedRoute(
            'mela.security.download',
            $expiresAt,
            ['id' => $requestId]
        );

        $emailSent = $this->sendRequestEmails([
            'name' => $data['name'],
            'email' => $normalizedEmail,
            'organization' => $data['organization'] ?? '',
            'job_title' => $data['job_title'] ?? '',
            'phone' => $data['phone'] ?? '',
            'message' => $data['message'] ?? '',
            'download_url' => $downloadUrl,
            'expires_at' => $expiresAt->format('F j, Y'),
        ]);

        $message = $emailSent
            ? 'Thanks! We have emailed you a secure download link for the Mela Meeting Assistant Security & Compliance documentation. The link expires in 7 days.'
            : 'Request received, but we could not confirm email delivery. Use the secure link below, or contact info@armely.com if you need help.';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'ok' => true,
                'message' => $message,
                'email_sent' => $emailSent,
                'download_url' => $downloadUrl,
                'expires_at' => $expiresAt->toIso8601String(),
            ]);
        }

        return back()->with('status', $message);
    }

    public function download(Request $request, int $id)
    {
        if (!$request->hasValidSignature() && !$request->hasValidRelativeSignature()) {
            return redirect()->route('mela.security')
                ->withErrors(['access' => 'This download link is invalid or has expired. Please request a new one.']);
        }

        $documentRequest = DB::table('mela_documentation_requests')->where('id', $id)->first();
        if (!$documentRequest) {
            return redirect()->route('mela.security')
                ->withErrors(['access' => 'This download link is invalid or has expired. Please request a new one.']);
        }

        $path = storage_path('app/' . self::DOCUMENT_PATH);
        if (!is_file($path)) {
            Log::warning('Mela documentation download failed: file not found on disk', ['request_id' => $id]);
            return redirect()->route('mela.security')
                ->withErrors(['access' => 'This file could not be located. Please request a new secure download link.']);
        }

        DB::table('mela_documentation_requests')->where('id', $id)->update([
            'download_count' => $documentRequest->download_count + 1,
            'downloaded_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->download($path, self::DOWNLOAD_FILENAME, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    private function sendRequestEmails(array $payload): bool
    {
        try {
            $fromEmail = trim((string) AzureMailService::outboundFromEmail());
            if ($fromEmail === '') {
                $fromEmail = trim((string) config('mail.from.address', ''));
            }

            if ($fromEmail === '') {
                Log::warning('Mela documentation request email skipped: missing sender address');
                return false;
            }

            $mailer = new AzureMailService();

            $userHtml = view('emails.mela.security-guide-download', [
                'name' => $payload['name'],
                'downloadUrl' => $payload['download_url'],
                'expiresAt' => $payload['expires_at'],
            ])->render();

            $userSent = $mailer->sendEmail(
                $fromEmail,
                (string) $payload['email'],
                'Your Mela Meeting Assistant Security & Compliance Documentation',
                $userHtml,
                true,
                false
            );

            $adminHtml = view('emails.mela.security-guide-admin-notification', [
                'name' => (string) $payload['name'],
                'email' => (string) $payload['email'],
                'organization' => (string) $payload['organization'],
                'jobTitle' => (string) $payload['job_title'],
                'phone' => (string) $payload['phone'],
                'message' => (string) $payload['message'],
                'expiresAt' => (string) $payload['expires_at'],
                'downloadUrl' => (string) $payload['download_url'],
            ])->render();

            $adminRecipients = app(NewsletterNotificationService::class)->adminRecipientEmails();
            foreach ($adminRecipients as $adminRecipient) {
                $mailer->sendEmail($fromEmail, $adminRecipient, 'Mela Documentation Request: ' . $payload['name'], $adminHtml);
            }

            return $userSent;
        } catch (\Throwable $e) {
            Log::warning('Mela documentation request email failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    private function verifyRecaptcha(string $token): bool
    {
        if (config('services.recaptcha.bypass', false)) {
            return true;
        }

        $secret = config('services.recaptcha.secret_key', env('CAPTURE_SECRET_KEY'));
        if (!$secret) {
            Log::warning('Mela documentation request: missing reCAPTCHA secret key');
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
            Log::error('Mela documentation request reCAPTCHA exception', ['error' => $e->getMessage()]);
            return false;
        }
    }

    private function errorResponse(Request $request, array $errors)
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['ok' => false, 'errors' => $errors], 422);
        }

        return back()->withErrors($errors)->withInput();
    }
}
