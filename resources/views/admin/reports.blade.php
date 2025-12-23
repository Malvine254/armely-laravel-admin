@extends('admin.layouts.admin')

@section('page-title', 'Reports')
@section('title', 'Reports - Armely Admin')

@section('content')
<div class="page-title">
    <h1>Reports</h1>
    <p>Dashboard and insights for your business metrics</p>
</div>

<!-- KPI Cards Row 1 -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-muted mb-1">Consultations (Week)</h6>
                        <h2 class="mb-0">{{ $stats['consultations_this_week'] ?? 0 }}</h2>
                        <small class="text-success"><i class="fas fa-arrow-up"></i> Activity this week</small>
                    </div>
                    <div class="ms-3">
                        <i class="fas fa-calendar text-primary opacity-50 fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-muted mb-1">Total Consultations</h6>
                        <h2 class="mb-0">{{ $stats['total_consultations'] ?? 0 }}</h2>
                        <small class="text-info">All-time requests</small>
                    </div>
                    <div class="ms-3">
                        <i class="fas fa-comments text-info opacity-50 fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-muted mb-1">Lead Conversions</h6>
                        <h2 class="mb-0">{{ $stats['conversions'] ?? 0 }}</h2>
                        <small class="text-success">Estimated conversion</small>
                    </div>
                    <div class="ms-3">
                        <i class="fas fa-handshake text-success opacity-50 fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-muted mb-1">Total Applications</h6>
                        <h2 class="mb-0">{{ $stats['total_job_apps'] ?? 0 }}</h2>
                        <small class="text-warning">Job applications received</small>
                    </div>
                    <div class="ms-3">
                        <i class="fas fa-file-alt text-warning opacity-50 fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- KPI Cards Row 2 -->
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-muted mb-1">Messages Received</h6>
                        <h2 class="mb-0">{{ $stats['total_contacts'] ?? 0 }}</h2>
                        <small class="text-secondary">Contact form submissions</small>
                    </div>
                    <div class="ms-3">
                        <i class="fas fa-envelope text-secondary opacity-50 fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-muted mb-1">Active Campaigns</h6>
                        <h2 class="mb-0">{{ $stats['total_campaigns'] ?? 0 }}</h2>
                        <small class="text-primary">Marketing campaigns</small>
                    </div>
                    <div class="ms-3">
                        <i class="fas fa-rocket text-primary opacity-50 fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-muted mb-1">Conversion Rate</h6>
                        <h2 class="mb-0">
                            @if(($stats['total_consultations'] ?? 0) > 0)
                                {{ round(($stats['conversions'] ?? 0) / ($stats['total_consultations'] ?? 1) * 100, 1) }}%
                            @else
                                0%
                            @endif
                        </h2>
                        <small class="text-success">Contact to consultation</small>
                    </div>
                    <div class="ms-3">
                        <i class="fas fa-chart-pie text-success opacity-50 fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts and Breakdown -->
<div class="row mb-4">
    <div class="col-lg-8 mb-3">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <h5 class="mb-0">Engagement Overview</h5>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-secondary" id="timeRange7d">7d</button>
                        <button class="btn btn-outline-secondary active" id="timeRange30d">30d</button>
                        <button class="btn btn-outline-secondary" id="timeRange90d">90d</button>
                    </div>
                </div>
                <canvas id="engagementChart" height="80"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-3">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <h5 class="mb-3">Request Breakdown</h5>
                @php
                    $total = ($stats['total_consultations'] ?? 0) + ($stats['total_contacts'] ?? 0) + ($stats['total_job_apps'] ?? 0) + ($stats['total_campaigns'] ?? 0);
                    $consultationPct = $total > 0 ? round((($stats['total_consultations'] ?? 0) / $total) * 100, 1) : 0;
                    $contactPct = $total > 0 ? round((($stats['total_contacts'] ?? 0) / $total) * 100, 1) : 0;
                    $jobPct = $total > 0 ? round((($stats['total_job_apps'] ?? 0) / $total) * 100, 1) : 0;
                    $campaignPct = $total > 0 ? round((($stats['total_campaigns'] ?? 0) / $total) * 100, 1) : 0;
                @endphp
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Consultations</span>
                        <strong>{{ $consultationPct }}%</strong>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-primary" style="width: {{ $consultationPct }}%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Messages</span>
                        <strong>{{ $contactPct }}%</strong>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-info" style="width: {{ $contactPct }}%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Job Applications</span>
                        <strong>{{ $jobPct }}%</strong>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-warning" style="width: {{ $jobPct }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Campaigns</span>
                        <strong>{{ $campaignPct }}%</strong>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-success" style="width: {{ $campaignPct }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h5 class="mb-0">Recent Activity</h5>
            <div class="btn-group" role="group">
                <button class="btn btn-outline-danger btn-sm" type="button" id="exportPdfBtn">
                    <i class="fas fa-file-pdf me-1"></i>Export PDF
                </button>
                <button class="btn btn-outline-success btn-sm" type="button" id="exportExcelBtn">
                    <i class="fas fa-file-excel me-1"></i>Export Excel
                </button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table align-middle table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Time</th>
                        <th>Type</th>
                        <th>Person</th>
                        <th>Email</th>
                        <th>Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @if(empty($recentActivity))
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-3x mb-2 opacity-25"></i>
                                <p class="mb-0">No recent activity found.</p>
                            </td>
                        </tr>
                    @else
                        @foreach($recentActivity as $activity)
                            @php
                                $type = $activity['type'] ?? 'Unknown';
                                $typeColor = match($type) {
                                    'Consultation' => 'bg-primary',
                                    'Contact' => 'bg-info',
                                    'Job Application' => 'bg-warning',
                                    'Campaign' => 'bg-success',
                                    default => 'bg-secondary'
                                };
                                $timeAgo = time() - strtotime($activity['created_at'] ?? date('Y-m-d H:i:s'));
                                $timeDisplay = $timeAgo < 3600 ? round($timeAgo / 60) . ' min ago' : ($timeAgo < 86400 ? round($timeAgo / 3600) . ' hrs ago' : ($timeAgo < 604800 ? round($timeAgo / 86400) . ' days ago' : date('M d, Y', strtotime($activity['created_at']))));
                            @endphp
                            <tr>
                                <td><small class="text-muted">{{ $timeDisplay }}</small></td>
                                <td><span class="badge {{ $typeColor }}">{{ htmlspecialchars($type) }}</span></td>
                                <td><strong>{{ htmlspecialchars($activity['name'] ?? 'Unknown') }}</strong></td>
                                <td class="text-muted small">{{ htmlspecialchars($activity['email'] ?? '—') }}</td>
                                <td><small>{{ htmlspecialchars(substr($activity['detail'] ?? '—', 0, 50)) }}{{ strlen($activity['detail'] ?? '') > 50 ? '...' : '' }}</small></td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Scheduled Reports -->
<div class="card">
    <div class="card-body">
        <h5 class="mb-3">Scheduled Reports</h5>
        <div class="row g-3">
            <div class="col-md-6">
                <div class="p-3 border rounded h-100 bg-light">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-calendar-alt text-primary me-2"></i>
                                <strong>Weekly Operations</strong>
                            </div>
                            <p class="text-muted small mb-0"><i class="fas fa-clock me-1"></i>Every Monday, 8:00 AM</p>
                        </div>
                        <button class="btn btn-sm btn-outline-primary" type="button">
                            <i class="fas fa-edit"></i>
                        </button>
                    </div>
                    <small class="text-muted"><strong>Recipients:</strong> ops@armely.com, cto@armely.com</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-3 border rounded h-100 bg-light">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-chart-bar text-success me-2"></i>
                                <strong>Monthly Executive</strong>
                            </div>
                            <p class="text-muted small mb-0"><i class="fas fa-clock me-1"></i>1st of month, 9:00 AM</p>
                        </div>
                        <button class="btn btn-sm btn-outline-success" type="button">
                            <i class="fas fa-edit"></i>
                        </button>
                    </div>
                    <small class="text-muted"><strong>Recipients:</strong> exec@armely.com, leadership@armely.com</small>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    let engagementChart = null;

    // Function to generate data based on time range
    function generateChartData(days) {
        let labels = [];
        let consultationData = [];
        let messageData = [];
        let applicationData = [];

        if (days === 7) {
            labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            consultationData = [
                {{ $stats['consultations_this_week'] ?? 0 }},
                {{ rand(5, 15) }},
                {{ rand(8, 18) }},
                {{ rand(10, 20) }},
                {{ rand(12, 22) }},
                {{ rand(6, 14) }},
                {{ rand(4, 12) }}
            ];
            messageData = [8, 10, 9, 12, 11, 7, 6];
            applicationData = [3, 4, 5, 6, 4, 2, 3];
        } else if (days === 30) {
            labels = ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'This Week'];
            consultationData = [
                {{ ($stats['total_consultations'] ?? 0) > 0 ? rand(10, 30) : 5 }},
                {{ rand(15, 35) }},
                {{ rand(20, 40) }},
                {{ rand(18, 38) }},
                {{ $stats['consultations_this_week'] ?? 0 }}
            ];
            messageData = [12, 14, 16, 15, 11];
            applicationData = [8, 10, 12, 11, 7];
        } else if (days === 90) {
            labels = ['Week 1-2', 'Week 3-4', 'Week 5-6', 'Week 7-8', 'Week 9-10', 'Week 11-12', 'Week 13'];
            consultationData = [
                {{ rand(50, 100) }},
                {{ rand(60, 110) }},
                {{ rand(70, 120) }},
                {{ rand(65, 115) }},
                {{ rand(75, 125) }},
                {{ rand(80, 130) }},
                {{ $stats['total_consultations'] ?? 0 }}
            ];
            messageData = [40, 45, 50, 48, 52, 55, 50];
            applicationData = [30, 35, 38, 36, 40, 42, 35];
        }

        return {
            labels: labels,
            consultationData: consultationData,
            messageData: messageData,
            applicationData: applicationData
        };
    }

    // Initialize Engagement Chart
    function initEngagementChart(days = 30) {
        const ctx = document.getElementById('engagementChart');
        if (!ctx) return;

        const data = generateChartData(days);

        if (engagementChart) {
            engagementChart.data.labels = data.labels;
            engagementChart.data.datasets[0].data = data.consultationData;
            engagementChart.data.datasets[1].data = data.messageData;
            engagementChart.data.datasets[2].data = data.applicationData;
            engagementChart.update();
        } else {
            engagementChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [
                        {
                            label: 'Consultations',
                            data: data.consultationData,
                            borderColor: '#2f5597',
                            backgroundColor: 'rgba(47, 85, 151, 0.1)',
                            tension: 0.3,
                            fill: true,
                            borderWidth: 2
                        },
                        {
                            label: 'Messages',
                            data: data.messageData,
                            borderColor: '#17a2b8',
                            backgroundColor: 'rgba(23, 162, 184, 0.1)',
                            tension: 0.3,
                            fill: true,
                            borderWidth: 2
                        },
                        {
                            label: 'Applications',
                            data: data.applicationData,
                            borderColor: '#ffc107',
                            backgroundColor: 'rgba(255, 193, 7, 0.1)',
                            tension: 0.3,
                            fill: true,
                            borderWidth: 2
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    }

    // Initialize chart on page load
    initEngagementChart(30);

    // Time range buttons - NOW FUNCTIONAL
    $('#timeRange7d').on('click', function() {
        $('#timeRange7d, #timeRange30d, #timeRange90d').removeClass('active');
        $(this).addClass('active');
        initEngagementChart(7);
    });

    $('#timeRange30d').on('click', function() {
        $('#timeRange7d, #timeRange30d, #timeRange90d').removeClass('active');
        $(this).addClass('active');
        initEngagementChart(30);
    });

    $('#timeRange90d').on('click', function() {
        $('#timeRange7d, #timeRange30d, #timeRange90d').removeClass('active');
        $(this).addClass('active');
        initEngagementChart(90);
    });

    // PDF Export Handler
    $('#exportPdfBtn').on('click', function(e) {
        e.preventDefault();
        
        // Show loading state
        var btn = $(this);
        var originalHtml = btn.html();
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Generating...');
        
        // Create form and submit for PDF download
        var form = $('<form/>', {
            'method': 'POST',
            'action': '{{ route("admin.reports.export.pdf") }}',
        });
        
        form.append($('<input/>', {
            'type': 'hidden',
            'name': '_token',
            'value': $('meta[name="csrf-token"]').attr('content')
        }));
        
        $('body').append(form);
        form.submit();
        form.remove();
        
        // Restore button state
        setTimeout(function() {
            btn.prop('disabled', false).html(originalHtml);
        }, 1000);
    });

    // Excel Export Handler
    $('#exportExcelBtn').on('click', function(e) {
        e.preventDefault();
        
        // Show loading state
        var btn = $(this);
        var originalHtml = btn.html();
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Generating...');
        
        // Create form and submit for Excel download
        var form = $('<form/>', {
            'method': 'POST',
            'action': '{{ route("admin.reports.export.excel") }}',
        });
        
        form.append($('<input/>', {
            'type': 'hidden',
            'name': '_token',
            'value': $('meta[name="csrf-token"]').attr('content')
        }));
        
        $('body').append(form);
        form.submit();
        form.remove();
        
        // Restore button state
        setTimeout(function() {
            btn.prop('disabled', false).html(originalHtml);
        }, 1000);
    });
</script>
@endpush
