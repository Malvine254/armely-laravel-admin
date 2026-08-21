@extends('emails.layouts.modern', ['emailTitle' => 'We Received Your Message', 'emailBadge' => 'Contact Desk', 'emailAccent' => '#0ea5e9'])
@section('content')
<p style="margin:0 0 14px;font-size:16px">Hello <strong>{{ $name }}</strong>,</p>
<p style="margin:0 0 16px;color:#475569">Thank you for contacting Armely. Your message is with our team and we will respond shortly.</p>
<div style="padding:14px 16px;background:#f4f8ff;border-left:4px solid #2F5597;border-radius:8px"><p style="margin:0 0 5px;color:#64748b;font-size:11px;font-weight:700;text-transform:uppercase">Your message</p><div>{!! nl2br(e($message)) !!}</div></div>
<p style="margin:16px 0 0;color:#64748b">Need to add context? Simply reply to this email.</p>
@endsection
@section('footer')A member of the Armely team will follow up as soon as possible.@endsection
