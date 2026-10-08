@extends('emails.layouts.modern', ['emailTitle' => 'New resource request', 'emailBadge' => 'Lead Alert'])
@section('content')
<p style="margin:0 0 16px;color:#475569">A visitor requested a resource from the Case Studies page. Review their details below and follow up.</p>
<h2 style="margin:24px 0 8px;font-size:16px;color:#172033">Contact details</h2>
@include('emails.partials.details', ['plainText' => true, 'rows' => [
    'Name' => $name,
    'Email' => $email,
    'Phone' => $phone ?: 'Not provided',
    'Organization' => $organization ?: 'Not provided',
    'Job title' => $jobTitle ?: 'Not provided',
    'Country / region' => $country ?: 'Not provided',
]])
<h2 style="margin:24px 0 8px;font-size:16px;color:#172033">Resource request</h2>
@include('emails.partials.details', ['plainText' => true, 'rows' => [
    'Interest' => $interest,
    'Resource' => \App\Support\EmailText::decode($requestedResource ?: 'Not specified'),
    'Case study ID' => $caseStudyId ?: 'N/A',
    'White paper ID' => $whitePaperId ?: 'N/A',
    'Link expires' => $expiresAt ?: 'N/A',
    'Source' => 'Case Studies Modal',
]])
<h2 style="margin:24px 0 8px;font-size:16px;color:#172033">Additional notes</h2>
<div style="padding:16px 18px;background:#f8fafc;border-left:3px solid #cbd5e1;color:#475569">{!! nl2br(e($message ?: 'No additional notes.')) !!}</div>
@endsection
@section('footer')Internal resource request notification from armely.com.@endsection
