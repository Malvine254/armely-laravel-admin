@component('mail::message')
# {{ $senderName }} shared a quote cart with you

Hello,

**{{ $senderName }}** has shared a quote cart with you on **{{ $appName }}** containing **{{ $itemCount }} item(s)**.

@if($note)
**Note from {{ $senderName }}:**
{{ $note }}

@endif

Click the button below to open the shared cart and import the items into your own quote.

@component('mail::button', ['url' => $shareUrl, 'color' => 'primary'])
Open Shared Cart
@endcomponent

If the button doesn't work, copy and paste this link into your browser:
{{ $shareUrl }}

Thanks,
{{ $appName }}
@endcomponent
