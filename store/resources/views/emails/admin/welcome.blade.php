@extends('emails.layouts.modern', ['emailTitle' => 'Administrator Account Created', 'emailBadge' => 'Admin Portal', 'emailAccent' => '#7c3aed'])
@section('content')
<p style="margin:0 0 14px;font-size:16px;">Hello <strong>{{ $admin->name }}</strong>,</p>
<p style="margin:0 0 16px;color:#475569;">An Armely Store administrator account has been created for you.</p>
@include('emails.partials.details', ['rows' => ['Email' => e($admin->email), 'Temporary password' => '<span style="font-family:Consolas,monospace">'.e($plainPassword).'</span>', 'Role' => $admin->role === 'super_admin' ? 'Super Admin' : 'Admin']])
<div style="padding:13px 15px;background:#fff7ed;border:1px solid #fed7aa;border-radius:9px;color:#9a3412;font-size:13px;"><strong>Required:</strong> Change this temporary password after your first sign-in and keep these credentials private.</div>
@include('emails.partials.button', ['url' => rtrim(config('app.url'), '/') . '/admin/login', 'label' => 'Open Admin Portal'])
@endsection
@section('footer-note')Confidential administrator access. If unexpected, contact the system administrator immediately.@endsection
