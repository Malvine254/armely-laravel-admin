<div style="margin:18px 0;padding:16px 18px;background:#f4f8ff;border:1px solid #d8e4f6;border-radius:12px;">
    @foreach($rows as $label => $value)
        <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;">
            <tr>
                <td style="padding:7px 0;color:#64748b;font-size:12px;text-transform:uppercase;letter-spacing:.03em;vertical-align:top;width:38%;">{{ $label }}</td>
                <td style="padding:7px 0;color:#172033;font-size:14px;font-weight:700;text-align:right;vertical-align:top;">{!! $value !!}</td>
            </tr>
        </table>
    @endforeach
</div>
