@extends('emails.layouts.modern', ['emailTitle' => 'New documentation request', 'emailBadge' => 'Mela Meeting Assistant'])
@section('content')
<p style="margin:0 0 16px;color:#475569">A visitor requested Mela's security and compliance documentation.</p>
@include('emails.partials.details', ['plainText' => true, 'rows' => [
    'Name' => $name,
    'Email' => $email,
    'Organization' => $organization ?: 'Not provided',
    'Job title' => $jobTitle ?: 'Not provided',
    'Phone' => $phone ?: 'Not provided',
    'Link expires' => $expiresAt,
]])
<h2 style="margin:24px 0 8px;font-size:16px;color:#172033">Notes</h2>
<div style="margin:0 0 20px;padding:16px;background:#f8fafc;border-left:3px solid #cbd5e1">{!! nl2br(e($message ?: 'No additional notes.')) !!}</div>
@include('emails.partials.button', ['buttonUrl' => $downloadUrl, 'buttonLabel' => 'Download documentation (PDF)'])
@endsection
@section('footer')The requester has already been emailed this same secure download link automatically.@endsection
