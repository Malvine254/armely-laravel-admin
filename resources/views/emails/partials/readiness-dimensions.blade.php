<h2 style="margin:24px 0 12px;font-size:16px;color:#172033">Dimension breakdown</h2>
@foreach($dimensions as $dimension)
<div style="margin:0 0 16px">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
        <tr>
            <td style="padding:0 8px 6px 0;color:#475569;font-size:13px">{{ $dimension['label'] }}</td>
            <td align="right" style="padding:0 0 6px;color:#172033;font-size:13px;font-weight:700">
                {{ $dimension['percent'] }}%@if($showScores ?? false) ({{ $dimension['score'] }}/{{ $dimension['max'] }})@endif
            </td>
        </tr>
    </table>
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" bgcolor="#e2e8f0" style="background:#e2e8f0">
        <tr>
            <td width="{{ $dimension['percent'] }}%" bgcolor="#2f5597" style="height:6px;background:#2f5597;font-size:0;line-height:0">&nbsp;</td>
            <td style="height:6px;font-size:0;line-height:0">&nbsp;</td>
        </tr>
    </table>
</div>
@endforeach
