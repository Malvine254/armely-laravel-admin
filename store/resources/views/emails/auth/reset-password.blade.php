@extends('emails.layouts.modern', ['emailTitle' => 'Reset Your Password', 'emailBadge' => 'Security Request', 'emailAccent' => '#f59e0b'])
@section('content')
<p style="margin:0 0 14px;font-size:16px;">Hello <strong>{{ $user->name }}</strong>,</p>
<p style="margin:0 0 16px;color:#475569;">We received a request to reset the password for your Armely Store account.</p>
<div style="padding:14px 16px;background:#fff7ed;border:1px solid #fed7aa;border-radius:9px;color:#9a3412;"><strong>Security notice:</strong> This link expires in {{ $expiresIn }} minutes and works only once.</div>
@include('emails.partials.button', ['url' => $resetUrl, 'label' => 'Reset Password'])
<p style="margin:18px 0 0;color:#64748b;font-size:12px;word-break:break-all;">If the button does not work, copy this link:<br><a href="{{ $resetUrl }}" style="color:#2563eb;">{{ $resetUrl }}</a></p>
@endsection
@section('footer-note')If you did not request a reset, you can safely ignore this email. Your password remains unchanged.@endsection
