<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="margin:12px 0 20px;border-collapse:collapse">
    @foreach($rows as $label => $value)
    <tr>
        <td class="mail-detail-label" width="34%" style="width:34%;padding:12px 14px 12px 0;border-bottom:1px solid #e2e8f0;color:#64748b;font-size:12px;vertical-align:top">{{ $label }}</td>
        <td class="mail-detail-value" style="padding:12px 0;border-bottom:1px solid #e2e8f0;color:#172033;font-size:14px;font-weight:600;text-align:left;vertical-align:top;overflow-wrap:anywhere;word-break:break-word">@if($plainText ?? false){{ $value }}@else{!! $value !!}@endif</td>
    </tr>
    @endforeach
</table>
