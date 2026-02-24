@extends('admin.layouts.admin')

@section('page-title', 'Business Intelligence')
@section('title', 'Advanced Reports - Armely Admin')

@push('styles')
<style>
    .report-hero {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        border-radius: 15px;
        padding: 40px;
        color: white;
        margin-bottom: 30px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(30, 60, 114, 0.2);
    }
    .report-hero::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 400px;
        height: 400px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
    }
    .kpi-card {
        border-radius: 12px;
        overflow: hidden;
        border: none;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }
    .kpi-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    .kpi-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    .bg-soft-primary { background: #eef2ff !important; color: #2f5597 !important; }
    .bg-soft-info { background: #e0f2fe !important; color: #0891b2 !important; }
    .bg-soft-success { background: #f0fdf4 !important; color: #16a34a !important; }
    .bg-soft-warning { background: #fffbeb !important; color: #d97706 !important; }
    
    .chart-container {
        padding: 20px;
        background: white;
        border-radius: 15px;
    }
    .activity-feed-item {
        border-left: 2px solid #eef2ff;
        padding-left: 20px;
        padding-bottom: 20px;
        position: relative;
    }
    .activity-feed-item:last-child {
        padding-bottom: 0;
    }
    .activity-feed-item::before {
        content: '';
        position: absolute;
        left: -7px;
        top: 0;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #2f5597;
        border: 2px solid white;
    }
    .btn-export {
        border-radius: 8px;
        padding: 8px 16px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .indicator-pulse {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #22c55e;
        display: inline-block;
        margin-right: 5px;
        box-shadow: 0 0 0 rgba(34, 197, 94, 0.4);
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.4); }
        70% { box-shadow: 0 0 0 10px rgba(34, 197, 94, 0); }
        100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
    }
</style>
@endpush

@section('content')
<div class="report-hero shadow">
    <div class="row align-items-center">
        <div class="col-md-7">
            <h5 class="text-white-50 mb-1">INTELLECTUAL INSIGHTS</h5>
            <h1 class="font-weight-bold mb-2">Business Operations Center</h1>
            <p class="mb-0 text-white-50">Real-time performance metrics and cross-platform interaction analysis.</p>
        </div>
        <div class="col-md-5 text-md-right mt-3 mt-md-0">
            <div class="d-inline-flex align-items-center bg-white p-2 rounded-pill shadow-sm">
                <span class="text-dark font-weight-bold px-3 small">
                    <span class="indicator-pulse"></span> SYSTEM LIVE
                </span>
                <div class="bg-primary px-3 py-1 rounded-pill text-white small font-weight-bold">
                    {{ now()->format('M Y') }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <!-- Main KPIs -->
    @php
        $kpis = [
            ['title' => 'Weekly Consultation', 'val' => $stats['consultations_this_week'], 'icon' => 'fa-calendar-check', 'class' => 'primary', 'desc' => 'Active this week'],
            ['title' => 'Client Engagement', 'val' => $stats['total_contacts'], 'icon' => 'fa-users', 'class' => 'info', 'desc' => 'Total lead volume'],
            ['title' => 'Pipeline Conversion', 'val' => $stats['conversions'], 'icon' => 'fa-funnel-dollar', 'class' => 'success', 'desc' => 'Verified prospects'],
            ['title' => 'Talent Acquisition', 'val' => $stats['total_job_apps'], 'icon' => 'fa-user-tie', 'class' => 'warning', 'desc' => 'Role applications']
        ];
    @endphp

    @foreach($kpis as $kpi)
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card kpi-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="kpi-icon bg-soft-{{ $kpi['class'] }}">
                        <i class="fas {{ $kpi['icon'] }}"></i>
                    </div>
                    <div class="text-right">
                        <span class="badge badge-light text-{{ $kpi['class'] }} font-weight-bold">
                            <i class="fas fa-chart-line mr-1"></i> Live
                        </span>
                    </div>
                </div>
                <h6 class="text-muted small text-uppercase font-weight-bold mb-1">{{ $kpi['title'] }}</h6>
                <h2 class="font-weight-bold mb-0 text-dark">{{ number_format($kpi['val']) }}</h2>
                <div class="mt-2 small text-muted">
                    <span class="text-{{ $kpi['class'] }} font-weight-bold mr-1">{{ $kpi['desc'] }}</span>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="row mb-4">
    <!-- Engagement Chart -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="font-weight-bold mb-1">Growth & Engagement</h5>
                        <p class="text-muted small mb-0">Cross-channel interaction volume over time</p>
                    </div>
                    <div class="btn-group btn-group-sm rounded-pill shadow-none border p-1" style="background: #f8f9fa;">
                        <button class="btn btn-transparent border-0 px-3 py-1 text-muted" id="timeRange7d">7D</button>
                        <button class="btn btn-primary border-0 px-3 py-1 rounded-pill shadow-sm" id="timeRange30d">30D</button>
                        <button class="btn btn-transparent border-0 px-3 py-1 text-muted" id="timeRange90d">90D</button>
                    </div>
                </div>
                <div style="height: 350px; width: 100%;">
                    <canvas id="engagementChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Distribution Sidebar -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-body p-4">
                <h5 class="font-weight-bold mb-4">Traffic Composition</h5>
                @php
                    $total = ($stats['total_consultations'] ?? 0) + ($stats['total_contacts'] ?? 0) + ($stats['total_job_apps'] ?? 0);
                    $dist = [
                        ['label' => 'Consultations', 'val' => $stats['total_consultations'], 'color' => '#2f5597', 'pct' => $total > 0 ? round(($stats['total_consultations'] / $total) * 100, 1) : 0],
                        ['label' => 'Messages', 'val' => $stats['total_contacts'], 'color' => '#0891b2', 'pct' => $total > 0 ? round(($stats['total_contacts'] / $total) * 100, 1) : 0],
                        ['label' => 'Applications', 'val' => $stats['total_job_apps'], 'color' => '#d97706', 'pct' => $total > 0 ? round(($stats['total_job_apps'] / $total) * 100, 1) : 0]
                    ];
                @endphp

                @foreach($dist as $item)
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="font-weight-bold text-dark">{{ $item['label'] }}</span>
                        <span class="text-muted font-weight-bold">{{ $item['pct'] }}%</span>
                    </div>
                    <div class="progress rounded-pill" style="height: 10px; background: #f1f5f9;">
                        <div class="progress-bar" style="width: {{ $item['pct'] }}%; background: {{ $item['color'] }};"></div>
                    </div>
                    <div class="mt-2">
                        <small class="text-muted">{{ number_format($item['val']) }} incoming items</small>
                    </div>
                </div>
                @endforeach

                <div class="mt-4 p-3 bg-soft-primary rounded-4 text-center">
                    <h6 class="mb-0 font-weight-bold">System Health: Optimal</h6>
                    <small>All sensors responding normally</small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-4 border-0 rounded-4">
    <div class="card-header bg-white border-0 py-4 px-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h5 class="font-weight-bold mb-1">Operational Activity Log</h5>
                <p class="text-muted small mb-0">Last 10 intelligence events captured by the system</p>
            </div>
            <div class="mt-3 mt-md-0">
                <button class="btn btn-outline-danger btn-export mr-2" id="exportPdfBtn">
                    <i class="fas fa-file-pdf"></i> Generate PDF
                </button>
                <button class="btn btn-outline-success btn-export" id="exportExcelBtn">
                    <i class="fas fa-file-excel"></i> Excel Spread
                </button>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: #f8faff;">
                    <tr>
                        <th class="px-4 py-3 text-uppercase small font-weight-bold">Timestamp</th>
                        <th class="py-3 text-uppercase small font-weight-bold">Channel</th>
                        <th class="py-3 text-uppercase small font-weight-bold">Entity</th>
                        <th class="py-3 text-uppercase small font-weight-bold">Reference Info</th>
                        <th class="py-3 text-uppercase small font-weight-bold">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentActivity as $activity)
                        @php
                            $type = $activity['type'] ?? 'Unknown';
                            $typeData = match($type) {
                                'Contact' => ['color' => 'info', 'icon' => 'fa-envelope'],
                                'Job Application' => ['color' => 'warning', 'icon' => 'fa-file-alt'],
                                'Login' => ['color' => 'primary', 'icon' => 'fa-key'],
                                'Page Visit' => ['color' => 'secondary', 'icon' => 'fa-eye'],
                                default => ['color' => 'dark', 'icon' => 'fa-circle']
                            };
                            $date = \Carbon\Carbon::parse($activity['created_at']);
                        @endphp
                        <tr>
                            <td class="px-4">
                                <div class="font-weight-bold text-dark">{{ $date->format('H:i') }}</div>
                                <div class="text-muted small">{{ $date->diffForHumans() }}</div>
                            </td>
                            <td>
                                <span class="badge badge-soft-{{ $typeData['color'] }} p-2 px-3">
                                    <i class="fas {{ $typeData['icon'] }} mr-1"></i> {{ $type }}
                                </span>
                            </td>
                            <td>
                                <div class="font-weight-bold text-dark">{{ $activity['name'] }}</div>
                                <div class="text-muted small">{{ $activity['email'] }}</div>
                            </td>
                            <td>
                                <span class="text-dark small"><i class="fas fa-info-circle mr-1 opacity-50"></i> {{ $activity['detail'] }}</span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-light border shadow-none rounded-pill px-3">View</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="opacity-25 mb-3">
                                    <i class="fas fa-database fa-4x text-muted"></i>
                                </div>
                                <h6 class="text-muted">No activity data available in the selected range.</h6>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- Executive Controls -->
<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0 rounded-4 bg-dark text-white overflow-hidden" style="min-height: 180px;">
            <div class="card-body p-4 position-relative" style="z-index: 2;">
                <h5 class="font-weight-bold mb-3">Intelligence Automation</h5>
                <p class="text-white-50 small mb-4">Configure automated distribution of performance payloads to key stakeholders.</p>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary rounded-pill px-4 btn-sm font-weight-bold mr-2">Configure Rules</button>
                    <button class="btn btn-outline-light rounded-pill px-4 btn-sm font-weight-bold">View History</button>
                </div>
            </div>
            <i class="fas fa-microchip position-absolute" style="bottom: -20px; right: -10px; font-size: 150px; opacity: 0.1; transform: rotate(-15deg);"></i>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0 rounded-4 p-1">
            <div class="card-body p-3">
                <h6 class="font-weight-bold mb-3 px-2">Scheduled Transmissions</h6>
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-0 px-2 py-2">
                        <div class="d-flex align-items-center">
                            <div class="bg-soft-primary p-2 rounded mr-3">
                                <i class="fas fa-file-invoice text-primary"></i>
                            </div>
                            <div>
                                <div class="text-dark font-weight-bold small">Weekly Ops Summary</div>
                                <div class="text-muted" style="font-size: 10px;">Targets: 4 Recipients</div>
                            </div>
                        </div>
                        <span class="badge badge-success rounded-pill">Active</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-0 px-2 py-2">
                        <div class="d-flex align-items-center">
                            <div class="bg-soft-success p-2 rounded mr-3">
                                <i class="fas fa-chart-pie text-success"></i>
                            </div>
                            <div>
                                <div class="text-dark font-weight-bold small">Monthly Executive KPI</div>
                                <div class="text-muted" style="font-size: 10px;">Targets: 2 Recipients</div>
                            </div>
                        </div>
                        <span class="badge badge-success rounded-pill">Active</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js" integrity="sha384-9MhbyIRcBVQiiC7FSd7T38oJNj2Zh+EfxS7/vjhBi4OOT78NlHSnzM31EZRWR1LZ" crossorigin="anonymous"></script>
<script>
    let engagementChart = null;

    // Initialize Chart
    function initEngagementChart(labels, consultations, contacts, applications) {
        const ctx = document.getElementById('engagementChart').getContext('2d');
        
        // Create gradients
        const gradient1 = ctx.createLinearGradient(0, 0, 0, 400);
        gradient1.addColorStop(0, 'rgba(47, 85, 151, 0.2)');
        gradient1.addColorStop(1, 'rgba(47, 85, 151, 0)');

        const gradient2 = ctx.createLinearGradient(0, 0, 0, 400);
        gradient2.addColorStop(0, 'rgba(8, 145, 178, 0.2)');
        gradient2.addColorStop(1, 'rgba(8, 145, 178, 0)');

        if (engagementChart) {
            engagementChart.destroy();
        }

        engagementChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Consultations',
                        data: consultations,
                        borderColor: '#2f5597',
                        backgroundColor: gradient1,
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#2f5597',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    },
                    {
                        label: 'Messages',
                        data: contacts,
                        borderColor: '#0891b2',
                        backgroundColor: gradient2,
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#0891b2',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: { size: 12, weight: '600' }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        titleFont: { size: 14 },
                        bodyFont: { size: 13 },
                        cornerRadius: 8,
                        displayColors: true
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#64748b', font: { size: 11 } }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [5, 5], color: '#e2e8f0' },
                        ticks: { color: '#64748b', font: { size: 11 }, stepSize: 5 }
                    }
                }
            }
        });
    }

    // Fetch Data
    async function updateChart(days, btnId) {
        // Toggle UI
        document.querySelectorAll('.btn-group button').forEach(b => {
            b.classList.remove('btn-primary', 'shadow-sm', 'rounded-pill');
            b.classList.add('btn-transparent', 'text-muted');
        });
        const activeBtn = document.getElementById(btnId);
        activeBtn.classList.remove('btn-transparent', 'text-muted');
        activeBtn.classList.add('btn-primary', 'shadow-sm', 'rounded-pill');

        try {
            const response = await fetch(`{{ route('admin.reports.chart-data') }}?days=${days}`);
            const data = await response.json();
            initEngagementChart(data.labels, data.consultations, data.contacts, data.applications);
        } catch (error) {
            console.error('Failed to update chart:', error);
        }
    }

    // Initial Load
    document.addEventListener('DOMContentLoaded', () => {
        const initialData = @json($chartData);
        initEngagementChart(initialData.labels, initialData.consultations, initialData.contacts, initialData.applications);

        // Event Listeners
        document.getElementById('timeRange7d').addEventListener('click', () => updateChart(7, 'timeRange7d'));
        document.getElementById('timeRange30d').addEventListener('click', () => updateChart(30, 'timeRange30d'));
        document.getElementById('timeRange90d').addEventListener('click', () => updateChart(90, 'timeRange90d'));
    });
</script>
@endpush
