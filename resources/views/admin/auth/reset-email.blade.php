@extends('emails.layouts.modern', ['emailTitle' => 'Password Reset Request', 'emailBadge' => 'Admin Security', 'emailAccent' => '#d97706'])
@section('content')
<p style="margin:0 0 14px;font-size:16px">Hello,</p>
<p style="margin:0 0 16px;color:#334155">We received a request to reset your Armely Admin password. Use the button below to securely set a new password.</p>
@include('emails.partials.button', ['buttonUrl' => $resetLink, 'buttonLabel' => 'Reset Your Password'])
<div style="margin:0 0 14px;padding:12px 14px;background:#fff7df;border:1px solid #f6dda5;border-radius:10px">
    <p style="margin:0;color:#7a5a13;font-size:13px;line-height:1.6"><strong>Security notice:</strong> This reset link expires in 60 minutes.</p>
</div>
<p style="margin:0 0 12px;font-size:14px;color:#475569">If you did not request this reset, you can safely ignore this message.</p>
<p style="margin:16px 0 4px;font-size:13px;color:#64748b"><strong>Button not working?</strong> Copy this secure link into your browser:</p>
<p style="margin:0;font-size:13px;line-height:1.6;word-break:break-all"><a href="{{ $resetLink }}" style="color:#2F5597">{{ $resetLink }}</a></p>
@endsection
@section('footer')Armely Admin Panel · Automated security notification. Please do not reply.@endsection
