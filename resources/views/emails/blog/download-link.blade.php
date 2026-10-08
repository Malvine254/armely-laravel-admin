@extends('emails.layouts.modern', ['emailTitle' => 'Your article PDF is ready', 'emailBadge' => 'Armely Insights'])
@section('content')
<p style="margin:0 0 16px">Hello {{ $name }},</p>
<p style="margin:0 0 20px;color:#475569">Thank you for your interest in Armely's insights. Your requested article PDF is ready to download.</p>
<div style="margin:0 0 24px;padding:20px;background:#f4f8ff;border-left:4px solid #2f5597;border-radius:8px">
    <p style="margin:0 0 8px;color:#64748b;font-size:11px;font-weight:700;text-transform:uppercase">Article</p>
    <p style="margin:0;color:#172033;font-size:18px;font-weight:700">{{ \App\Support\EmailText::decode($blogTitle) }}</p>
    <p style="margin:12px 0 0;color:#64748b;font-size:12px">This secure download link expires in 24 hours.</p>
</div>
@include('emails.partials.button', ['buttonUrl' => $downloadUrl, 'buttonLabel' => 'Download article PDF'])
<p style="margin:20px 0;color:#64748b;font-size:12px">If the link expires, you can request a fresh one directly from the article page on our website.</p>
<p style="margin:0">Warm regards,<br><strong>Armely Team</strong></p>
@endsection
@section('footer')If you did not request this download, please ignore this email.<br>&copy; {{ date('Y') }} Armely &middot; <a href="{{ url('/') }}" style="color:#2f5597">armely.com</a>@endsection
