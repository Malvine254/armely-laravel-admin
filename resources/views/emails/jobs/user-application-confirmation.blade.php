@extends('emails.layouts.modern', ['emailTitle' => 'Application received', 'emailBadge' => 'Armely Careers'])
@section('content')
<p style="margin:0 0 16px">Dear {{ $name }},</p>
<p style="margin:0 0 16px;color:#475569">Thank you for applying to Armely for the role of <strong>{{ \App\Support\EmailText::decode($position) }}</strong>. Your application has been submitted successfully.</p>
@include('emails.partials.details', ['plainText' => true, 'rows' => ['Position' => \App\Support\EmailText::decode($position), 'Job ID' => $jobId]])
<p style="margin:20px 0;color:#475569">Our recruiting team will review your profile and contact you within one month if your application is shortlisted.</p>
<p style="margin:0">Best regards,<br><strong>Armely HR Team</strong></p>
@endsection
@section('footer')This is an automated acknowledgement from Armely.@endsection
