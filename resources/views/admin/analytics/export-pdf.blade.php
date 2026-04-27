<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Analytics Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }
        
        .header {
            border-bottom: 3px solid #0066cc;
            margin-bottom: 30px;
            padding-bottom: 20px;
        }
        
        h1 {
            color: #0066cc;
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .meta {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
        }
        
        .section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        
        h2 {
            color: #0066cc;
            font-size: 18px;
            margin-bottom: 15px;
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 8px;
        }
        
        .metrics {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        
        .metric-card {
            flex: 1;
            min-width: 150px;
            background: #f5f5f5;
            padding: 15px;
            border-radius: 4px;
            border-left: 4px solid #0066cc;
        }
        
        .metric-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        
        .metric-value {
            font-size: 24px;
            font-weight: bold;
            color: #0066cc;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 12px;
        }
        
        th {
            background: #0066cc;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        
        td {
            padding: 10px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            font-size: 12px;
            color: #666;
            text-align: center;
        }
        
        code {
            background: #f0f0f0;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
            font-size: 11px;
        }
        
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📊 Analytics Report</h1>
        <div class="meta">Report Period: Last {{ $dateRange }} days</div>
        <div class="meta">Generated: {{ now()->format('M d, Y - H:i:s') }}</div>
    </div>

    <!-- Summary Metrics -->
    <div class="section">
        <h2>Summary Metrics</h2>
        <div class="metrics">
            <div class="metric-card">
                <div class="metric-label">Total Visits</div>
                <div class="metric-value">{{ number_format($analytics['total_visits']) }}</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Unique Visitors</div>
                <div class="metric-value">{{ number_format($analytics['unique_visitors']) }}</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Unique IPs</div>
                <div class="metric-value">{{ number_format($analytics['unique_ips']) }}</div>
            </div>
        </div>
    </div>

    <!-- Top Pages -->
    @if(!empty($analytics['top_pages']))
    <div class="section">
        <h2>Top 10 Pages</h2>
        <table>
            <thead>
                <tr>
                    <th>Page URL</th>
                    <th style="text-align: right; width: 100px;">Visits</th>
                </tr>
            </thead>
            <tbody>
                @foreach($analytics['top_pages'] as $page)
                <tr>
                    <td><code>{{ $page->page_url ?? 'Unknown' }}</code></td>
                    <td style="text-align: right;">{{ number_format($page->visits ?? 0) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Top Countries -->
    @if(!empty($analytics['top_countries']))
    <div class="section">
        <h2>Top 10 Countries</h2>
        <table>
            <thead>
                <tr>
                    <th>Country</th>
                    <th style="text-align: right; width: 100px;">Visits</th>
                </tr>
            </thead>
            <tbody>
                @foreach($analytics['top_countries'] as $country)
                <tr>
                    <td>{{ $country->country ?? 'Unknown' }}</td>
                    <td style="text-align: right;">{{ number_format($country->visits ?? 0) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        <p>This report was automatically generated by Armely Analytics System</p>
        <p>© {{ date('Y') }} - All rights reserved</p>
    </div>
</body>
</html>
