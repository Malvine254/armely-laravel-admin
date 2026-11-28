@extends('emails.layouts.modern', ['emailTitle' => $statusLabel, 'emailBadge' => 'Newsletter Signup', 'emailAccent' => '#2f5597'])
@section('content')
@php
    $rows = [];
    if (!empty($name)) {
        $rows['Name'] = e($name);
    }
    $rows['Email'] = '<a href="mailto:' . e($email) . '" style="color:#2F5597">' . e($email) . '</a>';
    $rows['Submitted'] = e($subscribedAt->format('M d, Y g:i A'));
@endphp
@include('emails.partials.details', ['rows' => $rows])
@endsection
@section('footer')This subscriber is now active in the admin Newsletter tab.@endsection
