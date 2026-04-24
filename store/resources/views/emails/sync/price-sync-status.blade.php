<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $jobName }} {{ ucfirst($phase) }}</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .wrapper { max-width: 600px; margin: 30px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .header { padding: 28px 32px; color: #fff; }
        .header.started   { background: #2563eb; }
        .header.completed { background: #16a34a; }
        .header.failed    { background: #dc2626; }
        .header h1 { margin: 0; font-size: 22px; font-weight: 700; }
        .header p  { margin: 6px 0 0; opacity: 0.85; font-size: 14px; }
        .body { padding: 28px 32px; }
        .stat-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin: 20px 0; }
        .stat-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 16px; }
        .stat-card .label { font-size: 12px; color: #64748b; text-transform: uppercase; letter-spacing: .5px; }
        .stat-card .value { font-size: 24px; font-weight: 700; color: #1e293b; margin-top: 4px; }
        .changes { margin-top: 20px; }
        .changes h3 { font-size: 14px; font-weight: 600; color: #374151; margin: 0 0 10px; }
        .change-item { background: #fef9e7; border-left: 3px solid #f59e0b; padding: 8px 12px; margin-bottom: 6px; font-size: 13px; color: #374151; border-radius: 0 4px 4px 0; }
        .footer { padding: 18px 32px; background: #f8fafc; border-top: 1px solid #e2e8f0; font-size: 12px; color: #94a3b8; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 999px; font-size: 12px; font-weight: 600; text-transform: uppercase; }
        .badge.started   { background: #dbeafe; color: #1d4ed8; }
        .badge.completed { background: #dcfce7; color: #15803d; }
        .badge.failed    { background: #fee2e2; color: #b91c1c; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header {{ $phase }}">
        <h1>
            @if($phase === 'started')   🔄
            @elseif($phase === 'completed') ✅
            @else ❌
            @endif
            {{ $jobName }}
        </h1>
        <p>{{ ucfirst($phase) }} at {{ $time }}</p>
    </div>

    <div class="body">
        <p><span class="badge {{ $phase }}">{{ strtoupper($phase) }}</span></p>

        @if(!empty($stats))
        <div class="stat-grid">
            @foreach($stats as $label => $value)
            @if(!is_array($value))
            <div class="stat-card">
                <div class="label">{{ ucwords(str_replace('_', ' ', $label)) }}</div>
                <div class="value">{{ $value }}</div>
            </div>
            @endif
            @endforeach
        </div>
        @endif

        @if(!empty($stats['log']) && is_array($stats['log']))
        <div class="changes">
            <h3>Changes Applied ({{ count($stats['log']) }} total)</h3>
            @foreach(array_slice($stats['log'], 0, 20) as $entry)
            <div class="change-item">{{ $entry }}</div>
            @endforeach
            @if(count($stats['log']) > 20)
            <p style="font-size:13px;color:#6b7280;margin-top:8px;">
                ... and {{ count($stats['log']) - 20 }} more — check the application log for the full list.
            </p>
            @endif
        </div>
        @endif

        @if($phase === 'started')
        <p style="color:#6b7280;font-size:14px;margin-top:20px;">
            This is an automated notification. You will receive another email when the job completes.
        </p>
        @endif
    </div>

    <div class="footer">
        Armely Store &mdash; Automated Price Sync &mdash; {{ $time }}
    </div>
</div>
</body>
</html>
