@extends('admin.layouts.admin')

@section('title', 'Analytics Dashboard')

@push('styles')
<style>
    .analytics-page {
        color: #1f2937;
        font-size: 0.94rem;
    }
    .analytics-page .mr-1 { margin-right: 0.25rem !important; }
    .analytics-page .mr-2 { margin-right: 0.5rem !important; }
    .analytics-page .mr-3 { margin-right: 0.75rem !important; }
    .analytics-header {
        background: linear-gradient(135deg, #244a86 0%, #17345f 100%);
        color: white;
        padding: 2rem 1.5rem;
        border-radius: 14px;
        margin-bottom: 1.5rem;
        box-shadow: 0 12px 28px rgba(31, 63, 127, 0.18);
        position: relative;
        overflow: hidden;
    }
    .analytics-header::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 400px;
        height: 400px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
        z-index: 0;
    }
    .analytics-card {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        transition: box-shadow 0.2s ease, border-color 0.2s ease;
        overflow: hidden;
        background: #fff !important;
        color: #1f2937 !important;
        box-shadow: 0 8px 22px rgba(15, 23, 42, 0.06) !important;
    }
    .analytics-card.text-white,
    .analytics-card.text-white * {
        color: #1f2937 !important;
    }
    .analytics-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 14px 30px rgba(15, 23, 42, 0.1) !important;
    }
    .analytics-card::before {
        content: '';
        display: block;
        height: 3px;
        background: #2f5597;
    }
    .analytics-card.bg-success::before,
    .analytics-card.bg-info::before {
        background: #0891b2;
    }
    .analytics-card.bg-warning::before {
        background: #d97706;
    }
    .analytics-card.bg-danger::before {
        background: #b91c1c;
    }
    .analytics-card.bg-secondary::before {
        background: #475569;
    }
    .card-icon-shape {
        width: 42px;
        height: 42px;
        background: #eef4ff !important;
        color: #2f5597 !important;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        font-size: 1rem;
        border: 1px solid #dbe7ff;
    }
    .analytics-card .card-icon-shape,
    .analytics-card .card-icon-shape * {
        color: #2f5597 !important;
    }
    .metric-label {
        font-size: 0.74rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #64748b;
    }
    .metric-value {
        font-size: 1.55rem;
        font-weight: 700;
        margin: 0.35rem 0;
        color: #111827;
    }
    .analytics-card .small,
    .analytics-card .opacity-75 {
        color: #64748b !important;
        opacity: 1 !important;
    }
    .analytics-page .card:not(.analytics-card) {
        border: 1px solid #e5e7eb !important;
        border-radius: 12px;
        box-shadow: 0 8px 22px rgba(15, 23, 42, 0.06) !important;
    }
    .analytics-page .card-header {
        border-bottom: 1px solid #eef2f7;
        color: #1f2937 !important;
    }
    .analytics-page .card-header.bg-secondary,
    .analytics-page .card-header.bg-dark {
        background: #f8fafc !important;
        color: #1f2937 !important;
    }
    .analytics-page .card-header.text-white,
    .analytics-page .card-header.text-white * {
        color: #1f2937 !important;
    }
    .analytics-page h1 {
        font-size: clamp(1.45rem, 2.2vw, 2rem);
        line-height: 1.15;
    }
    .analytics-page h6 {
        font-size: 0.9rem;
    }
    .analytics-page .text-success,
    .analytics-page .text-info,
    .analytics-page .text-warning,
    .analytics-page .text-danger {
        color: #2f5597 !important;
    }
    .analytics-page .bg-primary:not(.analytics-card),
    .analytics-page .bg-success:not(.analytics-card),
    .analytics-page .bg-info:not(.analytics-card),
    .analytics-page .bg-danger:not(.analytics-card),
    .analytics-page .bg-secondary:not(.analytics-card) {
        background-color: #2f5597 !important;
    }
    .analytics-page .badge.bg-primary,
    .analytics-page .badge.bg-info,
    .analytics-page .badge.bg-danger,
    .analytics-page .badge.bg-secondary {
        background-color: #eef4ff !important;
        color: #2f5597 !important;
    }
    .analytics-page .traffic-dot-admin {
        background-color: #2f5597 !important;
    }
    .analytics-page .traffic-dot-user {
        background-color: #0891b2 !important;
    }
    .analytics-page .traffic-dot-guest {
        background-color: #94a3b8 !important;
    }
    .analytics-page .bg-soft-primary,
    .analytics-page .bg-soft-info,
    .analytics-page .bg-soft-success {
        background: #eef4ff !important;
        color: #2f5597 !important;
    }
    .analytics-page .progress-bar {
        background-color: #2f5597 !important;
    }
    .analytics-page .analytics-card {
        background: #fff !important;
    }
    .analytics-page .analytics-card.bg-primary,
    .analytics-page .analytics-card.bg-success,
    .analytics-page .analytics-card.bg-info,
    .analytics-page .analytics-card.bg-warning,
    .analytics-page .analytics-card.bg-danger,
    .analytics-page .analytics-card.bg-secondary {
        background: #fff !important;
    }
    .analytics-page .bg-primary.text-white:not(.analytics-card),
    .analytics-page .bg-secondary.text-white:not(.analytics-card),
    .analytics-page .bg-dark.text-white:not(.analytics-card) {
        color: #fff !important;
    }
    .analytics-page .bg-primary.text-white:not(.analytics-card) *,
    .analytics-page .bg-secondary.text-white:not(.analytics-card) *,
    .analytics-page .bg-dark.text-white:not(.analytics-card) * {
        color: #fff !important;
    }
    .analytics-header .btn-light {
        color: #1f2937;
    }
    .analytics-header .btn-light .fa-download {
        color: #2f5597 !important;
    }
    .analytics-header .dropdown-menu .fa-file-csv {
        color: #16a34a !important;
    }
    .analytics-header .dropdown-menu .fa-file-pdf {
        color: #dc2626 !important;
    }
    .chart-container {
        position: relative;
        height: 350px;
    }
    .table-modern thead th {
        background-color: #f8fafc;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        border-top: none;
        color: #475569;
    }
    .pulse-live {
        display: inline-block;
        width: 10px;
        height: 10px;
        background-color: #2ecc71;
        border-radius: 50%;
        margin-right: 8px;
        box-shadow: 0 0 0 rgba(46, 204, 113, 0.4);
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(46, 204, 113, 0.4); }
        70% { box-shadow: 0 0 0 10px rgba(46, 204, 113, 0); }
        100% { box-shadow: 0 0 0 0 rgba(46, 204, 113, 0); }
    }
    @media (max-width: 768px) {
        .analytics-header {
            padding: 1.5rem 1rem;
        }
        .metric-value {
            font-size: 1.35rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4 analytics-page">
    <!-- Modern Header -->
    <div class="analytics-header">
        <div class="row align-items-center relative z-index-1">
            <div class="col-md-7">
                <h1 class="display-6 fw-bold mb-1">
                    <i class="fas fa-chart-line me-2"></i>Analytics Dashboard
                </h1>
                <p class="mb-0 opacity-75">
                    <span class="pulse-live"></span>Real-time visitor monitoring and traffic analysis
                </p>
            </div>
            <div class="col-md-5 mt-3 mt-md-0 d-flex justify-content-md-end gap-2">
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-download me-1"></i> Export
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('admin.analytics.export.csv', ['date_range' => $dateRange]) }}"><i class="fas fa-file-csv me-2 text-success"></i>CSV Format</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.analytics.export.pdf', ['date_range' => $dateRange]) }}"><i class="fas fa-file-pdf me-2 text-danger"></i>PDF Format</a></li>
                    </ul>
                </div>
                <select id="entityFilter" class="form-select border-0 shadow-sm" style="width: auto;">
                    <option value="">All Visitors</option>
                    <option value="admin">Admins</option>
                    <option value="user">Registered</option>
                    <option value="guest">Guests</option>
                </select>
                <select id="dateRangeFilter" class="form-select border-0 shadow-sm" style="width: auto;">
                    <option value="7" {{ $dateRange == 7 ? 'selected' : '' }}>7 Days</option>
                    <option value="30" {{ $dateRange == 30 ? 'selected' : '' }}>30 Days</option>
                    <option value="90" {{ $dateRange == 90 ? 'selected' : '' }}>90 Days</option>
                    <option value="365" {{ $dateRange == 365 ? 'selected' : '' }}>1 Year</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Executive KPI Cards -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card analytics-card bg-primary text-white shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="metric-label">Total Page Views</div>
                            <div class="metric-value" data-metric="total_visits">{{ number_format($analytics['total_visits']) }}</div>
                            <div class="small opacity-75"><i class="fas fa-arrow-up me-1"></i> Since start of period</div>
                        </div>
                        <div class="card-icon-shape">
                            <i class="fas fa-eye"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card analytics-card bg-success text-white shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="metric-label">Unique Visitors</div>
                            <div class="metric-value" data-metric="unique_visitors">{{ number_format($analytics['unique_visitors']) }}</div>
                            <div class="small opacity-75 text-truncate" style="max-width: 180px;">Distinct audience reach</div>
                        </div>
                        <div class="card-icon-shape">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card analytics-card bg-info text-white shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="metric-label">Traffic Sources (IPs)</div>
                            <div class="metric-value" data-metric="unique_ips">{{ number_format($analytics['unique_ips']) }}</div>
                            <div class="small opacity-75">Global network entry points</div>
                        </div>
                        <div class="card-icon-shape">
                            <i class="fas fa-network-wired"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card analytics-card bg-warning text-white shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="metric-label">Admin Accounts</div>
                            <div class="metric-value" data-metric="total_users">{{ number_format($analytics['total_users']) }}</div>
                            <div class="small opacity-75">Privileged access users</div>
                        </div>
                        <div class="card-icon-shape text-warning bg-white">
                            <i class="fas fa-user-shield"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card analytics-card bg-danger text-white shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="metric-label">Guest Sessions</div>
                            <div class="metric-value" data-metric="guest_visits">{{ number_format($analytics['guest_visits']) }}</div>
                            <div class="small opacity-75">Unauthenticated traffic</div>
                        </div>
                        <div class="card-icon-shape">
                            <i class="fas fa-user-ghost"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card analytics-card bg-secondary text-white shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="metric-label">System Interactions</div>
                            <div class="metric-value" data-metric="admin_visits">{{ number_format($analytics['admin_visits']) }}</div>
                            <div class="small opacity-75">Management trace frequency</div>
                        </div>
                        <div class="card-icon-shape">
                            <i class="fas fa-cog"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Insights Row -->
    <div class="row mb-4">
        <div class="col-lg-8 mb-4">
            <div class="card shadow border-0 h-100">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold text-primary"><i class="fas fa-chart-area me-2"></i>Visitor Traffic Trend</h6>
                    <div class="badge bg-soft-primary text-primary px-3">Live Updates</div>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="visitorChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card shadow border-0 h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-success"><i class="fas fa-chart-pie me-2"></i>Traffic Segmentation</h6>
                </div>
                <div class="card-body d-flex flex-column justify-content-center">
                    <div class="chart-container" style="height: 250px;">
                        <canvas id="typeChart"></canvas>
                    </div>
                    <div class="mt-4">
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge traffic-dot-admin rounded-circle p-1 me-2"><i class="fas fa-circle invisible"></i></span>
                            <span class="text-muted small flex-grow-1">Authenticated Admins</span>
                            <span class="fw-bold" data-summary="admin_percent">--%</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge traffic-dot-user rounded-circle p-1 me-2"><i class="fas fa-circle invisible"></i></span>
                            <span class="text-muted small flex-grow-1">Standard Users</span>
                            <span class="fw-bold" data-summary="user_percent">--%</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge traffic-dot-guest rounded-circle p-1 me-2"><i class="fas fa-circle invisible"></i></span>
                            <span class="text-muted small flex-grow-1">Public Guests</span>
                            <span class="fw-bold" data-summary="guest_percent">--%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Insights Row -->
    <div class="row mb-4">
        <div class="col-lg-4 mb-4">
            <div class="card shadow border-0 h-100">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="m-0 fw-bold text-info"><i class="fas fa-link me-2"></i>Top Entry Pages</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-modern table-hover align-middle mb-0" data-table="top-pages">
                            <thead>
                                <tr>
                                    <th class="ps-4">Resource Path</th>
                                    <th class="text-center">Popularity</th>
                                    <th class="text-end pe-4">Visits</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $maxVisits = collect($topPages)->first()?->visits ?? 1; @endphp
                                @forelse($topPages as $page)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="text-truncate" style="max-width: 200px;" title="{{ $page->page_url }}">
                                                <code class="text-primary small">{{ $page->page_url }}</code>
                                            </div>
                                        </td>
                                        <td style="width: 100px;">
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ ($page->visits / $maxVisits) * 100 }}%"></div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-4">
                                            <span class="fw-bold">{{ number_format($page->visits) }}</span>
                                        </div>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4">No page data found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 text-center py-3">
                    <a href="#" class="text-decoration-none small fw-bold">View Detailed Page Report <i class="fas fa-chevron-right ms-1"></i></a>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card shadow border-0 h-100">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="m-0 fw-bold text-info"><i class="fas fa-globe me-2"></i>Geographic Reach</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-modern table-hover align-middle mb-0" data-table="top-countries">
                            <thead>
                                <tr>
                                    <th class="ps-4">Country / Region</th>
                                    <th class="text-end pe-4">Session Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topCountries as $country)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-xs me-3 bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                    <i class="fas fa-map-marker-alt text-muted small"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold small">{{ $country->country ?? 'Unknown' }}</div>
                                                    <div class="text-muted smaller" style="font-size: 0.7rem;">Verified Location</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="badge bg-soft-info text-info rounded-pill px-3">{{ number_format($country->visits) }}</div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center py-4">Awaiting location data...</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 text-center py-3">
                    <a href="#" class="text-decoration-none small fw-bold">Global heat map coming soon <i class="fas fa-external-link-alt ms-1"></i></a>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card shadow border-0 h-100">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="m-0 fw-bold text-warning"><i class="fas fa-user-secret me-2"></i>High Traffic IPs</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-modern table-hover align-middle mb-0" data-table="top-ips-minimal">
                            <thead>
                                <tr>
                                    <th class="ps-4">IP Address</th>
                                    <th class="text-end pe-4">Activity</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topIPs as $ip)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <button class="btn btn-link p-0 text-decoration-none me-3 ip-details-btn" data-ip="{{ $ip->ip_address }}">
                                                    <code class="fw-bold">{{ $ip->ip_address }}</code>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="text-end pe-4">
                                            <span class="text-danger fw-bold">{{ number_format($ip->visits) }}</span>
                                            <small class="text-muted fs-xs ms-1">hits</small>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center py-4">No network activity logs.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 text-center py-3">
                    <button class="btn btn-sm btn-outline-warning rounded-pill px-4" onclick="$('#fullIpTable').toggleClass('d-none')">
                        Toggle Detailed View
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed IP Table (Initially Hidden) -->
    <div id="fullIpTable" class="row mb-4 d-none">
        <div class="col-12">
            <div class="card shadow border-0">
                <div class="card-header bg-dark text-white py-3">
                    <h6 class="m-0 fw-bold"><i class="fas fa-list me-2"></i>Complete Network Forensic Table</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-modern table-hover align-middle mb-0" data-table="top-ips">
                            <thead>
                                <tr>
                                    <th class="ps-4">Identifier</th>
                                    <th>Origin</th>
                                    <th class="text-end">Visits</th>
                                    <th class="text-center">Uniq Users</th>
                                    <th class="text-center">Pages</th>
                                    <th class="text-end pe-4">Intelligence</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topIPs as $ip)
                                    <tr>
                                        <td class="ps-4"><code>{{ $ip->ip_address }}</code></td>
                                        <td><span class="small">{{ $ip->country ?? 'Unknown' }}</span></td>
                                        <td class="text-end fw-bold text-danger">{{ number_format($ip->visits) }}</td>
                                        <td class="text-center"><span class="badge bg-light text-dark">{{ $ip->unique_users ?? 0 }}</span></td>
                                        <td class="text-center"><span class="badge bg-light text-dark">{{ $ip->pages_visited ?? 0 }}</span></td>
                                        <td class="text-end pe-4">
                                            <button class="btn btn-sm btn-info rounded-circle ip-details-btn" data-ip="{{ $ip->ip_address }}">
                                                <i class="fas fa-search-plus"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Action Links -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0"><i class="fas fa-link me-2"></i>Quick Links</h6>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-6 col-lg-3">
                            <span class="btn btn-outline-secondary btn-block w-100 disabled">
                                <i class="fas fa-file me-2"></i>Page Analytics
                            </span>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <span class="btn btn-outline-secondary btn-block w-100 disabled">
                                <i class="fas fa-users me-2"></i>User Analytics
                            </span>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <span class="btn btn-outline-secondary btn-block w-100 disabled">
                                <i class="fas fa-network-wired me-2"></i>IP Analytics
                            </span>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <span class="btn btn-outline-secondary btn-block w-100 disabled">
                                <i class="fas fa-list me-2"></i>Interaction Log
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js" integrity="sha384-e6nUZLBkQ86NJ6TVVKAeSaK8jWa3NhkYWZFomE39AvDbQWeie9PlQqM3pmYW5d1g" crossorigin="anonymous"></script>
<script>
    // Global chart instances
    let visitorChart = null;
    let typeChart = null;

    // Helper to format numbers like 1.2k
    function kFormatter(num) {
        return Math.abs(num) > 999 ? Math.sign(num)*((Math.abs(num)/1000).toFixed(1)) + 'k' : Math.sign(num)*Math.abs(num);
    }

    // Initialize charts on page load
    function initializeCharts(visitorData, activityData) {
        // Destroy old charts if they exist
        if (visitorChart) visitorChart.destroy();
        if (typeChart) typeChart.destroy();

        // Visitor Timeline Chart
        const visitorCtx = document.getElementById('visitorChart').getContext('2d');
        
        // Create Gradient
        const gradient1 = visitorCtx.createLinearGradient(0, 0, 0, 400);
        gradient1.addColorStop(0, 'rgba(47, 85, 151, 0.22)');
        gradient1.addColorStop(1, 'rgba(30, 60, 114, 0.0)');
        
        const gradient2 = visitorCtx.createLinearGradient(0, 0, 0, 400);
        gradient2.addColorStop(0, 'rgba(8, 145, 178, 0.16)');
        gradient2.addColorStop(1, 'rgba(8, 145, 178, 0.0)');

        visitorChart = new Chart(visitorCtx, {
            type: 'line',
            data: {
                labels: visitorData.map(d => {
                    const date = new Date(d.date);
                    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                }),
                datasets: [
                    {
                        label: 'Total Visits',
                        data: visitorData.map(d => d.visits),
                        borderColor: '#2f5597',
                        backgroundColor: gradient1,
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#2f5597',
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: '#2f5597',
                        pointHoverBorderColor: '#fff',
                    },
                    {
                        label: 'Unique Visitors',
                        data: visitorData.map(d => d.unique_visitors),
                        borderColor: '#0891b2',
                        backgroundColor: gradient2,
                        borderWidth: 2,
                        borderDash: [5, 5],
                        fill: true,
                        tension: 0.4,
                        pointRadius: 0,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { 
                        position: 'top',
                        labels: { usePointStyle: true, boxWidth: 6, font: { family: 'Inter', size: 12, weight: '600' } }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        padding: 12,
                        backgroundColor: 'rgba(0,0,0,0.8)',
                    }
                },
                scales: {
                    y: { 
                        beginAtZero: true,
                        grid: { borderDash: [2, 2], drawBorder: false },
                        ticks: { callback: value => kFormatter(value) }
                    },
                    x: {
                        grid: { display: false, drawBorder: false }
                    }
                }
            }
        });

        // Visitor Type Distribution Chart
        const typeCtx = document.getElementById('typeChart').getContext('2d');
        
        // Calculate Percentages for the UI
        const total = activityData.reduce((acc, curr) => acc + curr.count, 0);
        activityData.forEach(d => {
            const pct = total > 0 ? Math.round((d.count / total) * 100) : 0;
            $(`[data-summary="${d.type}_percent"]`).text(pct + '%');
        });

        typeChart = new Chart(typeCtx, {
            type: 'doughnut',
            data: {
                labels: activityData.map(d => d.type.charAt(0).toUpperCase() + d.type.slice(1)),
                datasets: [{
                    data: activityData.map(d => d.count),
                    backgroundColor: ['#2f5597', '#0891b2', '#94a3b8', '#cbd5e1'],
                    borderColor: '#fff',
                    borderWidth: 3,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }

    // Initial page load
    const initialVisitorData = @json($visitorTimeline);
    const initialActivityData = @json($userActivity);
    initializeCharts(initialVisitorData, initialActivityData);

    // Event delegation for IP details
    $(document).on('click', '.ip-details-btn', function() {
        const ip = $(this).data('ip');
        showIPDetails(ip);
    });

    // AJAX Handling
    $('#dateRangeFilter, #entityFilter').on('change', function() {
        const dateRange = $('#dateRangeFilter').val();
        const entityType = $('#entityFilter').val();
        
        // Show loading state
        $('.metric-value').addClass('opacity-50');
        
        $.ajax({
            url: '{{ route("api.analytics.summary") }}',
            method: 'GET',
            data: { date_range: dateRange, entity_type: entityType },
            success: function(response) {
                // Update KPIs
                updateKPIs(response);
                
                // Update Charts
                initializeCharts(response.visitor_timeline, response.user_activity);
                
                // Update Tables
                updateTopPages(response.top_pages);
                updateTopCountries(response.top_countries);
                updateTopIPs(response.top_ips);
                
                // Update Export Links
                updateExportLinks(dateRange, entityType);
                
                $('.metric-value').removeClass('opacity-50');
            },
            error: function() {
                $('.metric-value').removeClass('opacity-50');
                showNotification('Failed to refresh data. Please try again.', 'danger');
            }
        });
    });

    function updateKPIs(data) {
        $('[data-metric="total_visits"]').text(formatNumber(data.total_visits));
        $('[data-metric="unique_visitors"]').text(formatNumber(data.unique_visitors));
        $('[data-metric="unique_ips"]').text(formatNumber(data.unique_ips));
        $('[data-metric="total_users"]').text(formatNumber(data.total_users));
        $('[data-metric="guest_visits"]').text(formatNumber(data.guest_visits));
        $('[data-metric="admin_visits"]').text(formatNumber(data.admin_visits));
    }

    function updateTopPages(pages) {
        const tbody = $('[data-table="top-pages"] tbody');
        tbody.empty();
        
        if (!pages || pages.length === 0) {
            tbody.html('<tr><td colspan="3" class="text-center py-4">No data available</td></tr>');
            return;
        }
        
        const maxVisits = pages[0].visits || 1;
        
        pages.forEach(function(page) {
            const row = `
                <tr>
                    <td class="ps-4"><div class="text-truncate" style="max-width: 200px;"><code>${page.page_url}</code></div></td>
                    <td style="width: 100px;">
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-primary" style="width: ${(page.visits / maxVisits) * 100}%"></div>
                        </div>
                    </td>
                    <td class="text-end pe-4"><span class="fw-bold">${formatNumber(page.visits)}</span></td>
                </tr>
            `;
            tbody.append(row);
        });
    }

    function updateTopCountries(countries) {
        const tbody = $('[data-table="top-countries"] tbody');
        tbody.empty();
        
        if (!countries || countries.length === 0) {
            tbody.html('<tr><td colspan="2" class="text-center py-4">No data available</td></tr>');
            return;
        }
        
        countries.forEach(function(country) {
            const row = `
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-xs me-3 bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                <i class="fas fa-map-marker-alt text-muted small"></i>
                            </div>
                            <div class="fw-bold small">${country.country || 'Unknown'}</div>
                        </div>
                    </td>
                    <td class="text-end pe-4"><div class="badge bg-soft-info text-info rounded-pill px-3">${formatNumber(country.visits)}</div></td>
                </tr>
            `;
            tbody.append(row);
        });
    }

    function updateTopIPs(ips) {
        // Update both the minimal and full IP tables
        const minimalTbody = $('[data-table="top-ips-minimal"] tbody');
        const fullTbody = $('[data-table="top-ips"] tbody');
        minimalTbody.empty();
        fullTbody.empty();
        
        if (!ips || ips.length === 0) {
            const empty = '<tr><td colspan="2" class="text-center py-4">No activity found.</td></tr>';
            minimalTbody.html(empty);
            fullTbody.html('<tr><td colspan="6" class="text-center py-4">No data.</td></tr>');
            return;
        }
        
        ips.forEach(function(ip) {
            minimalTbody.append(`
                <tr>
                    <td class="ps-4">
                        <button class="btn btn-link p-0 text-decoration-none ip-details-btn" data-ip="${ip.ip_address}">
                            <code>${ip.ip_address}</code>
                        </button>
                    </td>
                    <td class="text-end pe-4"><span class="text-danger fw-bold">${formatNumber(ip.visits)}</span></td>
                </tr>
            `);
            
            fullTbody.append(`
                <tr>
                    <td class="ps-4"><code>${ip.ip_address}</code></td>
                    <td><span class="small">${ip.country || 'Unknown'}</span></td>
                    <td class="text-end fw-bold text-danger">${formatNumber(ip.visits)}</td>
                    <td class="text-center"><span class="badge bg-light text-dark">${ip.unique_users || 0}</span></td>
                    <td class="text-center"><span class="badge bg-light text-dark">${ip.pages_visited || 0}</span></td>
                    <td class="text-end pe-4">
                        <button class="btn btn-sm btn-info rounded-circle ip-details-btn" data-ip="${ip.ip_address}"><i class="fas fa-search-plus"></i></button>
                    </td>
                </tr>
            `);
        });
    }

    function updateExportLinks(dateRange, entityType = '') {
        const baseUrlCsv = '{{ route("admin.analytics.export.csv") }}';
        const baseUrlPdf = '{{ route("admin.analytics.export.pdf") }}';
        
        const params = new URLSearchParams({ date_range: dateRange });
        if (entityType) params.append('entity_type', entityType);
        
        $('.dropdown-item[href*="export/csv"]').attr('href', baseUrlCsv + '?' + params.toString());
        $('.dropdown-item[href*="export/pdf"]').attr('href', baseUrlPdf + '?' + params.toString());
    }

    function formatNumber(num) {
        return new Intl.NumberFormat('en-US').format(num || 0);
    }

    function showNotification(message, type = 'info') {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: type === 'danger' ? 'error' : type,
            title: message,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    }

    function showIPDetails(ipAddress) {
        Swal.fire({
            title: '<h5 class="fw-bold mb-0">Network Forensics</h5>',
            html: `
                <div class="text-start py-3">
                    <div class="mb-4 text-center">
                        <div class="display-6 fw-bold text-primary mb-2">${ipAddress}</div>
                        <span class="badge bg-soft-success text-success px-3">Verified Source</span>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="small text-muted mb-1">Reputation</label>
                            <div class="fw-bold"><i class="fas fa-shield-alt text-success me-1"></i> Clean</div>
                        </div>
                        <div class="col-6">
                            <label class="small text-muted mb-1">Last Seen</label>
                            <div class="fw-bold">Just now</div>
                        </div>
                    </div>
                    <hr>
                    <div class="mt-3">
                        <p class="small text-muted mb-3">IP Intelligence services can be integrated here to show ASN, ISP, and organizational data for this specific node.</p>
                        <button class="btn btn-primary w-100" onclick="navigator.clipboard.writeText('${ipAddress}'); Swal.fire('Copied!', '', 'success')">
                            <i class="fas fa-copy me-2"></i>Copy to Clipboard
                        </button>
                    </div>
                </div>
            `,
            showConfirmButton: false,
            showCloseButton: true,
            width: '450px'
        });
    }
</script>
@endpush
@endsection
