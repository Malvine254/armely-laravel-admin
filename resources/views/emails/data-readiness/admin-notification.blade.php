@extends('emails.layouts.modern', ['emailTitle' => 'New AI data readiness assessment', 'emailBadge' => 'Lead Alert'])
@section('content')
<p style="margin:0 0 16px;color:#475569">A visitor completed the AI Data Readiness assessment. Review their profile and results below.</p>
@include('emails.partials.details', ['plainText' => true, 'rows' => [
    'Name' => $fullName ?: 'Not provided',
    'Email' => $email,
    'Company' => $company ?: 'Not provided',
    'Role' => $role ?: 'Not provided',
    'Overall score' => $overallScore . ' / 360 (' . $scorePercent . '%)',
    'Location' => ($city ?: 'Unknown') . ', ' . ($country ?: 'Unknown'),
    'IP address' => $ipAddress ?: 'N/A',
    'Submitted' => $submittedAt ?: 'N/A',
]])
@include('emails.partials.readiness-dimensions', ['showScores' => true])
@endsection
@section('footer')Internal assessment notification from armely.com.@endsection
