@extends('emails.layouts.modern', ['emailTitle' => 'New job application', 'emailBadge' => 'Hiring Alert'])
@section('content')
<p style="margin:0 0 16px;color:#475569">A candidate has submitted an application through the careers page.</p>
@include('emails.partials.details', ['plainText' => true, 'rows' => [
    'Name' => $name,
    'Email' => $email,
    'Phone' => $phone ?: 'Not provided',
    'Position' => \App\Support\EmailText::decode($position ?: 'N/A'),
    'Job type' => $jobType ?: 'N/A',
    'Job ID' => $jobId ?: 'N/A',
    'City' => $city ?: 'Not provided',
    'Address' => $address ?: 'Not provided',
    'State / zip' => ($state ?: 'N/A') . ' / ' . ($zip ?: 'N/A'),
]])
@if(!empty($cvUrl))
@include('emails.partials.button', ['buttonUrl' => $cvUrl, 'buttonLabel' => 'Download candidate CV'])
@endif
@endsection
@section('footer')Internal hiring notification from armely.com.@endsection
