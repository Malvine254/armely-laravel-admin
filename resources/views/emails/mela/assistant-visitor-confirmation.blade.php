@extends('emails.layouts.modern', ['emailTitle' => 'Thanks' . ($name ? ', ' . $name : '') . '. We\'ve got your request.', 'emailBadge' => 'Mela AI', 'emailAccent' => '#2f5597'])
@section('content')
<p style="margin:0 0 12px">You asked Mela AI on armely.com to have someone from our team follow up about:</p>
<p style="margin:0 0 14px;padding:12px 14px;background:#f4f7fc;border-left:4px solid #2F5597;border-radius:8px;font-weight:600;color:#1f4d99">{{ $topic }}</p>
<p style="margin:0 0 12px">A member of the Armely team will be in touch. Your reference is <strong>{{ $reference }}</strong>.</p>
<p style="margin:0">If anything changes, just reply to this email or reach us at <a href="mailto:{{ $contactEmail }}" style="color:#2F5597">{{ $contactEmail }}</a>.</p>
@endsection
@section('footer')You received this because you requested follow-up in a chat on armely.com.@endsection
