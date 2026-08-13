@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="{{ rtrim(config('app.url'), '/') . '/images/logo/armely-store-logo.png' }}" class="logo" alt="Armely Store Logo" style="max-height:52px;width:auto;">
@else
{!! $slot !!}
@endif
</a>
</td>
</tr>
