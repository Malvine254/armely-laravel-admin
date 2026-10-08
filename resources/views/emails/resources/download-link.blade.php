@extends('emails.layouts.modern', ['emailTitle' => 'Your resource is ready', 'emailBadge' => 'Armely Resources'])
@section('content')
<p style="margin:0 0 16px">Hi {{ $name }},</p>
<p style="margin:0 0 20px;color:#475569">Thanks for requesting <strong>{{ \App\Support\EmailText::decode($resource->title) }}</strong>. Use the secure download button below to get the file directly.</p>
<div style="margin:0 0 24px;padding:20px;background:#f4f8ff;border-left:4px solid #2f5597;border-radius:8px">
    <p style="margin:0;color:#172033;font-size:18px;font-weight:700">{{ \App\Support\EmailText::decode($resource->title) }}</p>
    <p style="margin:8px 0 0;color:#64748b;font-size:12px">{{ ucfirst($resource->resource_type) }}@if($resource->category) | {{ \App\Support\EmailText::decode($resource->category) }}@endif</p>
</div>
@include('emails.partials.button', ['buttonUrl' => $downloadUrl, 'buttonLabel' => 'Download file now'])
<p style="margin:16px 0"><a href="{{ $resourceUrl }}" style="color:#2f5597">View resource page</a></p>
<p style="margin:20px 0 0;color:#64748b;font-size:12px">This secure download link expires in 1 hour.</p>
<p style="margin:16px 0 0;color:#475569">If you have questions or want related material, reply to this email and the Armely team can help.</p>
@endsection
