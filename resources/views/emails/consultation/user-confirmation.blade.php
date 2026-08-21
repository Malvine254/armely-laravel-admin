@extends('emails.layouts.modern', ['emailTitle' => 'Your Consultation Request Is In', 'emailBadge' => 'Consultation Desk', 'emailAccent' => '#16a34a'])
@section('content')
<p style="margin:0 0 14px;font-size:16px">Hello <strong>{{ $name }}</strong>,</p>
<p style="margin:0 0 16px;color:#475569">Thank you for considering Armely for <strong>{{ $serviceType }}</strong>. We received your request and a specialist will follow up within 24–48 business hours.</p>
<div style="padding:14px 16px;background:#f4f8ff;border-left:4px solid #2F5597;border-radius:8px"><p style="margin:0 0 5px;color:#64748b;font-size:11px;font-weight:700;text-transform:uppercase">What you shared</p><div>{!! nl2br(e($message)) !!}</div></div>
<p style="margin:16px 0 0;color:#475569">We look forward to learning more about your goals.</p>
@endsection
@section('footer')Reply to this email if you would like to add details before your consultation.@endsection
