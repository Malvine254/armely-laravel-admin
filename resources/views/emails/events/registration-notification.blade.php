@extends('emails.layouts.modern', ['emailTitle' => 'New event invitation request', 'emailBadge' => 'Event Registration', 'emailAccent' => '#0d233a'])
@section('content')
<p style="margin:0 0 6px">A registration request was submitted for <strong>{{ $event_name ?? 'Sovereign Data Clouds with Snowflake' }}</strong>.</p>
@include('emails.partials.details', ['plainText' => true, 'rows' => [
    'Name' => $full_name,
    'Work email' => $work_email,
    'Organization' => $organization,
    'Role' => $job_title,
    'Priority' => $compliance_focus ?: 'Not provided',
]])
@endsection
@section('footer')Review the request in the admin panel and send an invitation if approved.@endsection
