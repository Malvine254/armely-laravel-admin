@php
    $phaseColor = match($phase) { 'completed' => '#16a34a', 'failed' => '#dc2626', default => '#2563eb' };
@endphp
@extends('emails.layouts.modern', ['emailTitle' => $jobName, 'emailBadge' => strtoupper($phase), 'emailAccent' => $phaseColor])
@section('content')
<p style="margin:0 0 16px;color:#475569;">The automated catalog job <strong>{{ $phase }}</strong> at {{ $time }}.</p>
@if(!empty($stats))
<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;margin:8px 0 18px;">
    @foreach(array_chunk(array_filter($stats, fn($value) => !is_array($value)), 2, true) as $row)
    <tr>
        @foreach($row as $label => $value)<td class="mobile-block" width="50%" style="width:50%;padding:6px;vertical-align:top;"><div style="padding:14px;background:#f4f8ff;border:1px solid #d8e4f6;border-radius:10px;"><p style="margin:0;color:#64748b;font-size:10px;text-transform:uppercase;">{{ str_replace('_', ' ', $label) }}</p><p style="margin:4px 0 0;color:#172033;font-size:22px;font-weight:800;">{{ $value }}</p></div></td>@endforeach
    </tr>
    @endforeach
</table>
@endif
@if(!empty($stats['log']) && is_array($stats['log']))
<p style="margin:0 0 8px;font-weight:700;">Recent changes</p>
@foreach(array_slice($stats['log'], 0, 20) as $entry)<div style="margin:5px 0;padding:8px 11px;background:#fffbeb;border-left:3px solid #f59e0b;border-radius:5px;font-size:12px;">{{ $entry }}</div>@endforeach
@endif
@endsection
@section('footer-note')Automated Armely Store catalog operations notification.@endsection
