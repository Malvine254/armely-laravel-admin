@extends('emails.layouts.modern', ['emailTitle' => 'Your assessment report is ready', 'emailBadge' => 'AI Data Readiness'])
@section('content')
<p style="margin:0 0 16px">Dear {{ $firstName }},</p>
<p style="margin:0 0 20px;color:#475569">Thank you for completing Armely's AI Data Readiness assessment. Your score indicates how prepared your data foundation is for reliable AI delivery.</p>
<div style="padding:24px;background:#f4f8ff;border-left:4px solid #2f5597;border-radius:8px">
    <p style="margin:0;color:#64748b;font-size:11px;font-weight:700;text-transform:uppercase">Overall readiness</p>
    <p style="margin:8px 0;color:#0f2f63;font-size:40px;line-height:1.2;font-weight:700">{{ $scorePercent }}%</p>
    <p style="margin:0;color:#2f5597;font-size:16px;font-weight:700">{{ $tier['label'] }}</p>
    <p style="margin:12px 0;color:#475569">{{ $tier['summary'] }}</p>
    <p style="margin:0;color:#64748b;font-size:12px">Score: {{ $overallScore }} / 360</p>
</div>
@include('emails.partials.readiness-dimensions')
<h2 style="margin:24px 0 8px;font-size:16px;color:#172033">Your next step</h2>
<p style="margin:0 0 20px;color:#475569">Our team can review your results and recommend a practical roadmap to strengthen your data foundation.</p>
@include('emails.partials.button', ['buttonUrl' => $contactUrl, 'buttonLabel' => 'Book a strategy session'])
<p style="margin:20px 0 0">Warm regards,<br><strong>Team Armely</strong></p>
@endsection
@section('footer')This is an automated assessment summary from Armely.@endsection
