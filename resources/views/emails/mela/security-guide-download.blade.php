@extends('emails.layouts.modern', ['emailTitle' => 'Security & Compliance Documentation', 'emailBadge' => 'Mela Meeting Assistant'])
@section('content')
<p style="margin:0 0 16px">Hello {{ $name }},</p>
<p style="margin:0 0 24px;color:#475569">Thank you for your interest in Mela Meeting Assistant. Your requested User Guide, Privacy, Security &amp; Compliance documentation is ready to download.</p>
@include('emails.partials.button', ['buttonUrl' => $downloadUrl, 'buttonLabel' => 'Download documentation (PDF)'])
<p style="margin:20px 0;color:#64748b;font-size:12px">For your security, this link expires on {{ $expiresAt }}. If it expires, you can request a fresh link from the security &amp; compliance page.</p>
<p style="margin:0">Warm regards,<br><strong>Armely Team</strong></p>
@endsection
@section('footer')If you did not request this file, please ignore this email or contact info@armely.com.@endsection
