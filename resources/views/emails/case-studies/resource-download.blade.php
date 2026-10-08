@extends('emails.layouts.modern', ['emailTitle' => 'Your download is ready', 'emailBadge' => 'Armely Resources'])
@section('content')
<p style="margin:0 0 16px">Hello {{ $name }},</p>
<p style="margin:0 0 20px;color:#475569">Thank you for your interest in Armely. Your requested {{ $resourceTypeLabel }} is ready to download.</p>
<div style="margin:0 0 24px;padding:20px;background:#f4f8ff;border-left:4px solid #2f5597;border-radius:8px">
    <p style="margin:0 0 8px;color:#64748b;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px">Requested resource</p>
    <p style="margin:0;color:#172033;font-size:18px;font-weight:700">{{ \App\Support\EmailText::decode($resourceTitle) }}</p>
    <p style="margin:12px 0 0;color:#64748b;font-size:12px">Available until {{ $expiresAt }}</p>
</div>
@include('emails.partials.button', ['buttonUrl' => $downloadUrl, 'buttonLabel' => 'Download your resource'])
<p style="margin:20px 0 0;color:#64748b;font-size:12px">This secure link expires in 24 hours. If it has expired, <a href="{{ route('case-studies.index') }}" style="color:#2f5597">request a fresh link</a>.</p>
<p style="margin:20px 0 0">Warm regards,<br><strong>Armely Team</strong></p>
@endsection
@section('footer')If you did not request this file, you can safely ignore this email.@endsection
