@extends('admin.layouts.admin')

@section('title', 'Analytics Dashboard')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h3 mb-0">
                <i class="fas fa-chart-line text-primary me-2"></i>Analytics Dashboard
            </h1>
            <p class="text-muted mt-2">Track user visits, IP addresses, page traffic, and visitor behavior</p>
        </div>
        <div class="col-md-6 text-end">
            <div class="btn-group me-2" role="group">
                <a href="{{ route('admin.analytics.export.csv', ['date_range' => $dateRange]) }}" class="btn btn-sm btn-outline-success">
                    <i class="fas fa-download me-1"></i>CSV
                </a>
                <a href="{{ route('admin.analytics.export.pdf', ['date_range' => $dateRange]) }}" class="btn btn-sm btn-outline-danger">
                    <i class="fas fa-file-pdf me-1"></i>PDF
                </a>
            </div>
            <select id="entityFilter" class="form-select form-select-sm d-inline-block me-2" style="width: auto;">
                <option value="">All Visitors</option>
                <option value="admin">Admin Only</option>
                <option value="user">Users Only</option>
                <option value="guest">Guests Only</option>
            </select>
            <select id="dateRangeFilter" class="form-select form-select-sm d-inline-block" style="width: auto;">
                <option value="7" {{ $dateRange == 7 ? 'selected' : '' }}>Last 7 Days</option>
                <option value="30" {{ $dateRange == 30 ? 'selected' : '' }}>Last 30 Days</option>
                <option value="90" {{ $dateRange == 90 ? 'selected' : '' }}>Last 90 Days</option>
                <option value="365" {{ $dateRange == 365 ? 'selected' : '' }}>Last Year</option>
            </select>
        </div>
    </div>

    <!-- Analytics Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-primary text-uppercase mb-1">
                        <small class="font-weight-bold">Total Visits</small>
                    </div>
                    <div class="h3 mb-0" data-metric="total_visits">{{ number_format($analytics['total_visits']) }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-success text-uppercase mb-1">
                        <small class="font-weight-bold">Unique Visitors</small>
                    </div>
                    <div class="h3 mb-0" data-metric="unique_visitors">{{ number_format($analytics['unique_visitors']) }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-info text-uppercase mb-1">
                        <small class="font-weight-bold">Unique IP Addresses</small>
                    </div>
                    <div class="h3 mb-0" data-metric="unique_ips">{{ number_format($analytics['unique_ips']) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-warning text-uppercase mb-1">
                        <small class="font-weight-bold">Admin Users</small>
                    </div>
                    <div class="h3 mb-0" data-metric="total_users">{{ number_format($analytics['total_users']) }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-danger text-uppercase mb-1">
                        <small class="font-weight-bold">Guest Visits</small>
                    </div>
                    <div class="h3 mb-0" data-metric="guest_visits">{{ number_format($analytics['guest_visits']) }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-secondary text-uppercase mb-1">
                        <small class="font-weight-bold">Admin Visits</small>
                    </div>
                    <div class="h3 mb-0" data-metric="admin_visits">{{ number_format($analytics['admin_visits']) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-chart-line me-2"></i>Visitor Timeline</h6>
                </div>
                <div class="card-body">
                    <canvas id="visitorChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="fas fa-pie-chart me-2"></i>Visitor Type Distribution</h6>
                </div>
                <div class="card-body">
                    <div style="max-height: 300px; display: flex; align-items: center;">
                        <canvas id="typeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Pages and Countries -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-file me-2"></i>Top 10 Pages</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0" data-table="top-pages">
                            <thead>
                                <tr>
                                    <th>Page</th>
                                    <th class="text-end">Visits</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topPages as $page)
                                    <tr>
                                        <td>
                                            <small><code>{{ substr($page->page_url, 0, 40) }}{{ strlen($page->page_url) > 40 ? '...' : '' }}</code></small>
                                        </td>
                                        <td class="text-end">
                                            <span class="badge bg-primary">{{ number_format($page->visits) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted py-3">No data available</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-globe me-2"></i>Top 10 Countries</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0" data-table="top-countries">
                            <thead>
                                <tr>
                                    <th>Country</th>
                                    <th class="text-end">Visits</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topCountries as $country)
                                    <tr>
                                        <td>
                                            <i class="flag flag-{{ strtolower(substr($country->country, 0, 2)) }} me-2"></i>
                                            {{ $country->country ?? 'Unknown' }}
                                        </td>
                                        <td class="text-end">
                                            <span class="badge bg-warning">{{ number_format($country->visits) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted py-3">No data available</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top IP Addresses -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0"><i class="fas fa-network-wired me-2"></i>Top 10 IP Addresses</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0" data-table="top-ips">
                            <thead>
                                <tr>
                                    <th>IP Address</th>
                                    <th>Country</th>
                                    <th class="text-end">Visits</th>
                                    <th class="text-end">Users</th>
                                    <th class="text-end">Pages</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topIPs as $ip)
                                    <tr>
                                        <td><code>{{ $ip->ip_address }}</code></td>
                                        <td>{{ $ip->country ?? 'Unknown' }}</td>
                                        <td class="text-end"><span class="badge bg-danger">{{ number_format($ip->visits) }}</span></td>
                                        <td class="text-center"><span class="badge bg-info">{{ $ip->unique_users ?? 0 }}</span></td>
                                        <td class="text-center"><span class="badge bg-secondary">{{ $ip->pages_visited ?? 0 }}</span></td>
                                        <td class="text-end">
                                            <button class="btn btn-sm btn-outline-info ip-details-btn" title="View details for {{ $ip->ip_address }}" data-ip="{{ $ip->ip_address }}" data-bs-toggle="tooltip">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-3">No data available</td>
                                    </tr>
                                @endforelse
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Global chart instances
    let visitorChart = null;
    let typeChart = null;

    // Initialize charts on page load
    function initializeCharts(visitorData, activityData) {
        // Destroy old charts if they exist
        if (visitorChart) visitorChart.destroy();
        if (typeChart) typeChart.destroy();

        // Visitor Timeline Chart
        const visitorCtx = document.getElementById('visitorChart').getContext('2d');
        visitorChart = new Chart(visitorCtx, {
            type: 'line',
            data: {
                labels: visitorData.map(d => d.date),
                datasets: [
                    {
                        label: 'Total Visits',
                        data: visitorData.map(d => d.visits),
                        borderColor: '#0066cc',
                        backgroundColor: 'rgba(0, 102, 204, 0.1)',
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Unique Visitors',
                        data: visitorData.map(d => d.unique_visitors),
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        fill: true,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        // Visitor Type Distribution Chart
        const typeCtx = document.getElementById('typeChart').getContext('2d');
        typeChart = new Chart(typeCtx, {
            type: 'doughnut',
            data: {
                labels: activityData.map(d => d.type.charAt(0).toUpperCase() + d.type.slice(1)),
                datasets: [{
                    data: activityData.map(d => d.count),
                    backgroundColor: ['#0066cc', '#28a745', '#dc3545', '#ffc107']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    // Initial page load - create charts
    const initialVisitorData = @json($visitorTimeline);
    const initialActivityData = @json($userActivity);
    initializeCharts(initialVisitorData, initialActivityData);

    // Event delegation for IP details button - works on all current and future buttons
    $(document).on('click', '.ip-details-btn', function() {
        const ip = $(this).data('ip');
        showIPDetails(ip);
    });

    // AJAX Date Range & Entity Type Filter
    $('#dateRangeFilter, #entityFilter').on('change', function() {
        const dateRange = $('#dateRangeFilter').val();
        const entityType = $('#entityFilter').val();
        const currentUrl = new URL(window.location.href);
        
        // Update URL without reloading
        currentUrl.searchParams.set('date_range', dateRange);
        if (entityType) {
            currentUrl.searchParams.set('entity_type', entityType);
        } else {
            currentUrl.searchParams.delete('entity_type');
        }
        window.history.replaceState({}, '', currentUrl);
        
        // Load analytics data via AJAX
        $.ajax({
            url: '{{ route("api.analytics.summary") }}',
            type: 'GET',
            data: { 
                date_range: dateRange,
                entity_type: entityType
            },
            dataType: 'json',
            success: function(data) {
                // Update metric cards
                updateAnalyticsCards(data);
                
                // Update all tables
                updateTopPages(data.top_pages);
                updateTopCountries(data.top_countries);
                updateTopIPs(data.top_ips);
                
                // Recreate charts with new data
                initializeCharts(data.visitor_timeline, data.user_activity);
                
                // Update export links
                updateExportLinks(dateRange, entityType);
                
                // Show success notification
                showNotification('Analytics updated successfully', 'success');
            },
            error: function(xhr) {
                console.error('Analytics error:', xhr);
                showNotification('Failed to load analytics data', 'danger');
            }
        });
    });

    function updateAnalyticsCards(data) {
        // Update Total Visits
        $('[data-metric="total_visits"]').text(formatNumber(data.total_visits));
        
        // Update Unique Visitors
        $('[data-metric="unique_visitors"]').text(formatNumber(data.unique_visitors));
        
        // Update Unique IPs
        $('[data-metric="unique_ips"]').text(formatNumber(data.unique_ips));
        
        // Update Total Users
        $('[data-metric="total_users"]').text(formatNumber(data.total_users));
        
        // Update Guest Visits
        $('[data-metric="guest_visits"]').text(formatNumber(data.guest_visits));
        
        // Update Admin Visits
        $('[data-metric="admin_visits"]').text(formatNumber(data.admin_visits));
    }

    function updateTopPages(pages) {
        const tbody = $('[data-table="top-pages"] tbody');
        tbody.empty();
        
        if (!pages || pages.length === 0) {
            tbody.html('<tr><td colspan="2" class="text-center text-muted py-3">No data available</td></tr>');
            return;
        }
        
        pages.forEach(function(page) {
            const url = page.page_url || 'Unknown';
            const row = `
                <tr>
                    <td><small><code>${url.substring(0, 40)}${url.length > 40 ? '...' : ''}</code></small></td>
                    <td class="text-end"><span class="badge bg-primary">${formatNumber(page.visits)}</span></td>
                </tr>
            `;
            tbody.append(row);
        });
    }

    function updateTopCountries(countries) {
        const tbody = $('[data-table="top-countries"] tbody');
        tbody.empty();
        
        if (!countries || countries.length === 0) {
            tbody.html('<tr><td colspan="2" class="text-center text-muted py-3">No data available</td></tr>');
            return;
        }
        
        countries.forEach(function(country) {
            const row = `
                <tr>
                    <td><i class="fas fa-globe me-2"></i>${country.country || 'Unknown'}</td>
                    <td class="text-end"><span class="badge bg-info">${formatNumber(country.visits)}</span></td>
                </tr>
            `;
            tbody.append(row);
        });
    }

    function updateTopIPs(ips) {
        const tbody = $('[data-table="top-ips"] tbody');
        tbody.empty();
        
        if (!ips || ips.length === 0) {
            tbody.html('<tr><td colspan="6" class="text-center text-muted py-3">No data available</td></tr>');
            return;
        }
        
        ips.forEach(function(ip) {
            const row = `
                <tr>
                    <td><code>${ip.ip_address || 'Unknown'}</code></td>
                    <td>${ip.country || 'Unknown'}</td>
                    <td class="text-end"><span class="badge bg-danger">${formatNumber(ip.visits)}</span></td>
                    <td class="text-center"><span class="badge bg-info">${ip.unique_users || 0}</span></td>
                    <td class="text-center"><span class="badge bg-secondary">${ip.pages_visited || 0}</span></td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-outline-info ip-details-btn" title="View details for ${ip.ip_address}" data-ip="${ip.ip_address}" data-bs-toggle="tooltip">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });
        
        // Initialize tooltips for new buttons
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function(tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    function updateExportLinks(dateRange, entityType = '') {
        const csvLink = '{{ route("admin.analytics.export.csv", ["date_range" => ""]) }}'.replace(/\/$/, '') + '?date_range=' + dateRange;
        const pdfLink = '{{ route("admin.analytics.export.pdf", ["date_range" => ""]) }}'.replace(/\/$/, '') + '?date_range=' + dateRange;
        
        let csvUrl = csvLink;
        let pdfUrl = pdfLink;
        
        if (entityType) {
            csvUrl += '&entity_type=' + entityType;
            pdfUrl += '&entity_type=' + entityType;
        }
        
        $('a[href*="export/csv"]').attr('href', csvUrl);
        $('a[href*="export/pdf"]').attr('href', pdfUrl);
    }

    function formatNumber(num) {
        if (num === null || num === undefined) return '0';
        return new Intl.NumberFormat('en-US').format(num);
    }

    function showNotification(message, type = 'info') {
        const alertClass = `alert-${type}`;
        const html = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                <i class="fas fa-info-circle me-2"></i>${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        // Insert at top of page
        $('.container-fluid').first().prepend(html);
        
        // Auto dismiss after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut(function() { $(this).remove(); });
        }, 5000);
    }

    function showIPDetails(ipAddress) {
        // Show detailed information about the IP in a modal
        Swal.fire({
            title: 'IP Address Details',
            html: `
                <div class="text-start" style="max-width: 100%;">
                    <div class="mb-3">
                        <strong>IP Address:</strong><br>
                        <code style="font-size: 16px; background: #f5f5f5; padding: 8px 12px; border-radius: 4px; display: inline-block; margin-top: 5px;">
                            ${ipAddress}
                        </code>
                    </div>
                    <div class="mb-3">
                        <strong>Status:</strong><br>
                        <span class="badge bg-success">Active</span>
                    </div>
                    <p class="text-muted small">
                        <i class="fas fa-info-circle me-2"></i>
                        You can expand this feature to show detailed logs, activity timeline, and threat analysis for this IP address.
                    </p>
                </div>
            `,
            icon: 'info',
            confirmButtonText: 'Close',
            confirmButtonColor: '#0066cc',
            width: '500px'
        });
    }
</script>
@endpush
@endsection
