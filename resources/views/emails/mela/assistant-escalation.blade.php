@extends('emails.layouts.modern', ['emailTitle' => 'A visitor requested follow-up', 'emailBadge' => 'Mela AI · Website Assistant', 'emailAccent' => '#2f5597'])
@section('content')
@php
    $rows = [
        'Name' => $name ?: 'Not provided',
        'Email' => $email ?: 'Not provided',
        'Company' => $company ?: 'Not provided',
        'Phone' => $phone ?: 'Not provided',
        'Preferred contact' => $preferredContact ?: 'Not specified',
        'Request type' => $requestType,
        'Area of interest' => $topic,
        'Reference' => $reference,
        'Conversation ID' => $conversationId,
        'Timestamp' => $timestamp,
    ];
    if ($landingPage) {
        $rows['Started on page'] = $landingPage;
    }
    $sectionLabel = 'margin:20px 0 0;font-size:12px;color:#64748b;text-transform:uppercase;letter-spacing:.05em;font-weight:700;';
    $sectionText = 'margin:6px 0 0;font-size:14px;line-height:1.6;color:#334155;white-space:pre-line;';
@endphp
@include('emails.partials.details', ['plainText' => true, 'rows' => $rows])

<p style="{{ $sectionLabel }}">Business need</p>
<p style="{{ $sectionText }}">{{ $businessNeed }}</p>

<p style="{{ $sectionLabel }}">Requested next action</p>
<p style="{{ $sectionText }}">{{ $requestedAction }}</p>

@if($summary)
<p style="{{ $sectionLabel }}">Conversation summary</p>
<p style="{{ $sectionText }}">{{ $summary }}</p>
@endif

@if(!empty($technologies) || !empty($services))
<p style="{{ $sectionLabel }}">Context</p>
<p style="margin:6px 0 0;font-size:14px;line-height:1.6;color:#334155;">
    @if(!empty($technologies))Technologies: {{ implode(', ', $technologies) }}<br>@endif
    @if(!empty($services))Armely services discussed: {{ implode(', ', $services) }}@endif
</p>
@endif

@if(!empty($transcript))
<p style="margin:22px 0 8px;font-size:12px;color:#64748b;text-transform:uppercase;letter-spacing:.05em;font-weight:700;">Recent conversation</p>
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="font-size:13px;line-height:1.55;">
    @foreach($transcript as $line)
    <tr>
        <td style="padding:8px 10px;border-radius:8px;background:{{ $line['role'] === 'user' ? '#eef4ff' : '#f7f9fc' }};color:#26364a;white-space:pre-line;">
            <strong style="color:{{ $line['role'] === 'user' ? '#1f4d99' : '#667085' }};">{{ $line['role'] === 'user' ? 'Visitor' : 'Mela AI' }}:</strong> {{ $line['content'] }}
        </td>
    </tr>
    <tr><td style="height:6px;"></td></tr>
    @endforeach
</table>
@endif
@endsection
@section('footer')This enquiry was also saved to the consultation leads list. Reply directly to the visitor's email address to follow up.@endsection
