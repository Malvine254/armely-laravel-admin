@extends('admin.layouts.admin')

@section('title', 'Dashboard')

@push('styles')
<style>
.dashboard-header {
    background: linear-gradient(135deg, #2f5597 0%, #1e3a6b 100%);
    color: white;
    padding: 2rem 0;
    margin-bottom: 2rem;
    border-radius: 0 0 15px 15px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    border-left: 4px solid transparent;
    height: 100%;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.12);
}

.stat-card.primary { border-left-color: #2f5597; }
.stat-card.success { border-left-color: #28a745; }
.stat-card.info { border-left-color: #17a2b8; }
.stat-card.warning { border-left-color: #ffc107; }

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    margin-bottom: 1rem;
}

.stat-icon.primary { background: linear-gradient(135deg, #2f5597 0%, #4a6fb5 100%); color: white; }
.stat-icon.success { background: linear-gradient(135deg, #28a745 0%, #34ce57 100%); color: white; }
.stat-icon.info { background: linear-gradient(135deg, #17a2b8 0%, #20c9e0 100%); color: white; }
.stat-icon.warning { background: linear-gradient(135deg, #ffc107 0%, #ffcd39 100%); color: #333; }

.stat-value {
    font-size: 2rem;
    font-weight: bold;
    margin: 0.5rem 0;
    color: #2c3e50;
}

.stat-label {
    color: #6c757d;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.25rem;
}

.stat-change {
    font-size: 0.85rem;
    margin-top: 0.5rem;
}

.stat-change.positive { color: #28a745; }

.chart-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    height: 420px;
}

.chart-card h5 {
    color: #2f5597;
    font-weight: 600;
    margin-bottom: 1.5rem;
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 0.75rem;
}

.activity-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.activity-item {
    padding: 1rem;
    border-left: 3px solid #e9ecef;
    margin-bottom: 1rem;
    background: #f8f9fa;
    border-radius: 0 8px 8px 0;
    transition: all 0.2s ease;
}

.activity-item:hover {
    background: #e9ecef;
    border-left-color: #2f5597;
    transform: translateX(5px);
}

.activity-type {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.activity-type.consultation { background: #2f5597; color: white; }
.activity-type.contact { background: #17a2b8; color: white; }
.activity-type.job { background: #ffc107; color: #333; }
.activity-type.campaign { background: #28a745; color: white; }

.quick-action-btn {
    padding: 1rem;
    border-radius: 10px;
    border: 2px solid #e9ecef;
    background: white;
    transition: all 0.3s ease;
    text-decoration: none;
    display: block;
    text-align: center;
}

.quick-action-btn:hover {
    border-color: #2f5597;
    background: #f8f9fa;
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(47, 85, 151, 0.15);
}

.quick-action-btn i {
    font-size: 2rem;
    margin-bottom: 0.5rem;
    color: #2f5597;
}

.section-title {
    color: #2c3e50;
    font-weight: 600;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.section-title i {
    color: #2f5597;
}

.dashboard-tabs .nav-link {
    color: #2c3e50;
    font-weight: 600;
}

.dashboard-tabs .nav-link.active {
    background: #2f5597;
    color: white;
}

.content-tabs-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.content-tabs .nav-link {
    border: 1px solid #e9ecef;
    margin-right: 0.5rem;
    color: #2c3e50;
}

.content-tabs .nav-link.active {
    background: #2f5597;
    color: white;
    border-color: #2f5597;
}

.top-author-badge {
    background: #f0f4ff;
    color: #2f5597;
    padding: 0.5rem 0.75rem;
    border-radius: 8px;
    font-weight: 600;
    border: 1px solid #d8e2ff;
}

.mini-stat {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 1rem;
    border: 1px solid #e9ecef;
}

.mini-stat h6 {
    margin-bottom: 0.35rem;
    color: #2f5597;
    font-weight: 700;
}

.pill-badge {
    display: inline-block;
    padding: 0.35rem 0.6rem;
    border-radius: 999px;
    background: #eef2ff;
    color: #2f5597;
    font-weight: 600;
    font-size: 0.85rem;
}

.activity-list {
    max-height: 520px;
    overflow-y: auto;
    padding-right: 0.35rem;
}
</style>
@endpush

@section('content')
<!-- Dashboard Header -->
<div class="dashboard-header">
    <div class="container">
        <h1 class="mb-2"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</h1>
        <p class="mb-0">Welcome back, {{ $adminName }}! Here's your overview for today.</p>
        <small class="text-white-50"><i class="far fa-clock me-1"></i>{{ now()->format('l, F j, Y - g:i A') }}</small>
    </div>
</div>

<div class="container">
    <ul class="nav nav-tabs dashboard-tabs mb-3" id="dashboardTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="dash-overview-tab" data-bs-toggle="tab" data-bs-target="#dash-overview" type="button" role="tab" aria-controls="dash-overview" aria-selected="true">Overview</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="dash-content-tab" data-bs-toggle="tab" data-bs-target="#dash-content" type="button" role="tab" aria-controls="dash-content" aria-selected="false">Content</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="dash-activity-tab" data-bs-toggle="tab" data-bs-target="#dash-activity" type="button" role="tab" aria-controls="dash-activity" aria-selected="false">Activity</button>
        </li>
    </ul>

    <div class="tab-content" id="dashboardTabContent">
        <!-- Overview Tab -->
        <div class="tab-pane fade show active" id="dash-overview" role="tabpanel" aria-labelledby="dash-overview-tab">
            <!-- Stats Cards Row -->
            <div class="row g-3 mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="stat-card primary">
                        <div class="stat-icon primary">
                            <i class="fas fa-handshake"></i>
                        </div>
                        <div class="stat-label">Total Consultations</div>
                        <div class="stat-value">{{ number_format($stats['total_consultations']) }}</div>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up me-1"></i>
                            {{ $stats['consultations_this_week'] }} this week
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="stat-card success">
                        <div class="stat-icon success">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="stat-label">Contact Messages</div>
                        <div class="stat-value">{{ number_format($stats['total_contacts']) }}</div>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up me-1"></i>
                            {{ $stats['contacts_today'] }} today
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="stat-card warning">
                        <div class="stat-icon warning">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <div class="stat-label">Job Applications</div>
                        <div class="stat-value">{{ number_format($stats['total_job_apps']) }}</div>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up me-1"></i>
                            {{ $stats['job_apps_this_month'] }} this month
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="stat-card info">
                        <div class="stat-icon info">
                            <i class="fas fa-bullhorn"></i>
                        </div>
                        <div class="stat-label">Campaign Submissions</div>
                        <div class="stat-value">{{ number_format($stats['total_campaigns']) }}</div>
                        <div class="stat-change">
                            <i class="fas fa-chart-line me-1"></i>
                            Total entries
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row g-3 mb-4">
                <div class="col-lg-8">
                    <div class="chart-card">
                        <h5><i class="fas fa-chart-line me-2"></i>Activity Trends</h5>
                        <canvas id="lineChart"></canvas>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="chart-card">
                        <h5><i class="fas fa-chart-pie me-2"></i>Activity Distribution</h5>
                        <canvas id="pieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Tab -->
        <div class="tab-pane fade" id="dash-content" role="tabpanel" aria-labelledby="dash-content-tab">
            <!-- Content & Authors Mix -->
            <div class="row g-3 mb-4">
                <div class="col-lg-12">
                    <div class="chart-card" style="height: 360px;">
                        <h5><i class="fas fa-layer-group me-2"></i>Content & Authors Mix</h5>
                        <canvas id="contentChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Content Insights Tabs -->
            <div class="row g-3 mb-4">
                <div class="col-lg-12">
                    <div class="content-tabs-card">
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                            <div>
                                <h5 class="mb-1"><i class="fas fa-list-alt me-2"></i>Content Overview</h5>
                                <small class="text-muted">Compare blogs, videos, careers, and authors at a glance.</small>
                            </div>
                            <div class="top-author-badge">
                                <i class="fas fa-crown me-1"></i>
                                Top Author: {{ $topAuthorHighlight['name'] ?? 'N/A' }} ({{ $topAuthorHighlight['total'] ?? 0 }} blogs)
                            </div>
                        </div>

                        <ul class="nav nav-pills content-tabs" id="contentTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="tab-blogs" data-bs-toggle="pill" data-bs-target="#pane-blogs" type="button" role="tab" aria-controls="pane-blogs" aria-selected="true">Blogs</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="tab-authors" data-bs-toggle="pill" data-bs-target="#pane-authors" type="button" role="tab" aria-controls="pane-authors" aria-selected="false">Authors</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="tab-videos" data-bs-toggle="pill" data-bs-target="#pane-videos" type="button" role="tab" aria-controls="pane-videos" aria-selected="false">Videos</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="tab-careers" data-bs-toggle="pill" data-bs-target="#pane-careers" type="button" role="tab" aria-controls="pane-careers" aria-selected="false">Careers</button>
                            </li>
                        </ul>

                        <div class="tab-content mt-3" id="contentTabContent">
                            <!-- Blogs Tab -->
                            <div class="tab-pane fade show active" id="pane-blogs" role="tabpanel" aria-labelledby="tab-blogs">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="mini-stat mb-2">
                                            <h6>Total Blogs</h6>
                                            <div class="fs-3 fw-bold">{{ $stats['blogs'] }}</div>
                                            <div class="text-muted small">{{ $stats['unique_authors'] }} unique authors</div>
                                        </div>
                                        <div class="mini-stat">
                                            <h6>Top Author</h6>
                                            <div class="fw-bold">{{ $topAuthorHighlight['name'] ?? 'N/A' }}</div>
                                            <div class="text-muted small">{{ $topAuthorHighlight['total'] ?? 0 }} blogs published</div>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <strong>Recent Blogs</strong>
                                            <span class="pill-badge">Latest 5</span>
                                        </div>
                                        @if($recentBlogs->count())
                                            <div class="list-group list-group-flush">
                                                @foreach($recentBlogs as $blog)
                                                    @php
                                                        $title = $blog->title ?? $blog->blog_title ?? $blog->name ?? $blog->heading ?? ('Blog #' . ($blog->id ?? ''));
                                                        $author = $blog->author ?? $blog->blog_author ?? $blog->author_name ?? $blog->writer ?? $blog->written_by ?? null;
                                                        $publishedAt = $blog->date ?? $blog->blog_date ?? $blog->published_at ?? $blog->created_at ?? null;
                                                    @endphp
                                                    <div class="list-group-item px-0">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <div class="fw-bold text-dark">{{ $title }}</div>
                                                                <div class="text-muted small">
                                                                    <i class="far fa-user me-1"></i>{{ $author ?? 'Unknown author' }}
                                                                    <span class="ms-2"><i class="far fa-calendar-alt me-1"></i>{{ $publishedAt ?? 'N/A' }}</span>
                                                                </div>
                                                            </div>
                                                            <span class="badge bg-light text-dark">Blog</span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-muted">No blog entries found.</div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Authors Tab -->
                            <div class="tab-pane fade" id="pane-authors" role="tabpanel" aria-labelledby="tab-authors">
                                @if($topAuthors->count())
                                    <div class="list-group list-group-flush">
                                        @foreach($topAuthors as $index => $author)
                                            <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                                <div>
                                                    <span class="pill-badge me-2">#{{ $index + 1 }}</span>
                                                    <strong>{{ $author['name'] }}</strong>
                                                </div>
                                                <span class="text-muted">{{ $author['total'] }} blogs</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-muted">No author data available.</div>
                                @endif
                            </div>

                            <!-- Videos Tab -->
                            <div class="tab-pane fade" id="pane-videos" role="tabpanel" aria-labelledby="tab-videos">
                                <div class="mini-stat mb-3">
                                    <h6>Total Videos</h6>
                                    <div class="fs-3 fw-bold">{{ $stats['videos'] }}</div>
                                    <div class="text-muted small">Counts from videos/video table</div>
                                </div>
                                <div class="text-muted">Add video performance details once metrics are available.</div>
                            </div>

                            <!-- Careers Tab -->
                            <div class="tab-pane fade" id="pane-careers" role="tabpanel" aria-labelledby="tab-careers">
                                <div class="mini-stat mb-3">
                                    <h6>Open Roles</h6>
                                    <div class="fs-3 fw-bold">{{ $stats['careers'] }}</div>
                                    <div class="text-muted small">Active positions detected</div>
                                </div>
                                @if($activeCareers->count())
                                    <div class="list-group list-group-flush">
                                        @foreach($activeCareers as $career)
                                            @php
                                                $role = $career->title ?? $career->position ?? $career->name ?? ('Role #' . ($career->id ?? ''));
                                                $location = $career->location ?? $career->city ?? null;
                                            @endphp
                                            <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                                <div>
                                                    <div class="fw-bold text-dark">{{ $role }}</div>
                                                    @if($location)
                                                        <div class="text-muted small"><i class="fas fa-map-marker-alt me-1"></i>{{ $location }}</div>
                                                    @endif
                                                </div>
                                                <span class="badge bg-light text-dark">Active</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-muted">No active career entries found.</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Tab -->
        <div class="tab-pane fade" id="dash-activity" role="tabpanel" aria-labelledby="dash-activity-tab">
            <div class="row g-3 mb-4">
                <div class="col-lg-4">
                    <div class="activity-card">
                        <h5 class="section-title">
                            <i class="fas fa-bolt"></i>
                            Quick Actions
                        </h5>
                        <div class="row g-2">
                            <div class="col-6">
                                <a href="{{ route('admin.reports') }}" class="quick-action-btn">
                                    <i class="fas fa-chart-bar"></i>
                                    <div class="fw-bold">Reports</div>
                                    <small class="text-muted">View analytics</small>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('admin.admins') }}" class="quick-action-btn">
                                    <i class="fas fa-users-cog"></i>
                                    <div class="fw-bold">Admins</div>
                                    <small class="text-muted">Manage team</small>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('admin.tables') }}" class="quick-action-btn">
                                    <i class="fas fa-table"></i>
                                    <div class="fw-bold">Tables</div>
                                    <small class="text-muted">View data</small>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('admin.tables') }}" class="quick-action-btn">
                                    <i class="fas fa-user-tie"></i>
                                    <div class="fw-bold">Careers</div>
                                    <small class="text-muted">Manage jobs</small>
                                </a>
                            </div>
                        </div>

                        <hr class="my-3">

                        <h6 class="section-title mb-3">
                            <i class="fas fa-user-shield"></i>
                            Admin Overview
                        </h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total Admins</span>
                            <strong>{{ $stats['total_admins'] }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Active Admins</span>
                            <strong class="text-success">{{ $stats['active_admins'] }}</strong>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="activity-card">
                        <h5 class="section-title">
                            <i class="fas fa-history"></i>
                            Recent Activity
                        </h5>
                        @if(count($recentActivity) > 0)
                            <div class="activity-list">
                                @foreach($recentActivity as $activity)
                                    @php
                                        $type = strtolower($activity['type']);
                                        $typeClass = match($type) {
                                            'consultation' => 'consultation',
                                            'contact' => 'contact',
                                            'job application' => 'job',
                                            'campaign' => 'campaign',
                                            default => 'contact'
                                        };
                                        
                                        $icon = match($type) {
                                            'consultation' => 'fa-handshake',
                                            'contact' => 'fa-envelope',
                                            'job application' => 'fa-briefcase',
                                            'campaign' => 'fa-bullhorn',
                                            default => 'fa-circle'
                                        };
                                        
                                        $timeAgo = $activity['created_at'] ? $activity['created_at']->format('M d, g:i A') : 'N/A';
                                    @endphp
                                    <div class="activity-item">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <span class="activity-type {{ $typeClass }}">
                                                    <i class="fas {{ $icon }} me-1"></i>{{ $activity['type'] }}
                                                </span>
                                            </div>
                                            <small class="text-muted">
                                                <i class="far fa-clock me-1"></i>{{ $timeAgo }}
                                            </small>
                                        </div>
                                        <div class="fw-bold text-dark">{{ $activity['name'] }}</div>
                                        <div class="text-muted small">{{ $activity['email'] }}</div>
                                        <div class="text-secondary small mt-1">
                                            <i class="fas fa-info-circle me-1"></i>{{ \Str::limit($activity['detail'], 60) }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
                                <p>No recent activity</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    if (window.chartsInitialized) return;
    window.chartsInitialized = true;
    
    // Line Chart - Activity Trends
    const lineCtx = document.getElementById('lineChart');
    if (lineCtx) {
        new Chart(lineCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: {!! json_encode($monthlyData['labels']) !!},
                datasets: [
                    {
                        label: 'Consultations',
                        data: {!! json_encode($monthlyData['consultations']) !!},
                        borderColor: '#2f5597',
                        backgroundColor: 'rgba(47, 85, 151, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        pointBackgroundColor: '#2f5597',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    },
                    {
                        label: 'Contacts',
                        data: {!! json_encode($monthlyData['contacts']) !!},
                        borderColor: '#17a2b8',
                        backgroundColor: 'rgba(23, 162, 184, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        pointBackgroundColor: '#17a2b8',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    },
                    {
                        label: 'Job Applications',
                        data: {!! json_encode($monthlyData['job_applications']) !!},
                        borderColor: '#ffc107',
                        backgroundColor: 'rgba(255, 193, 7, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        pointBackgroundColor: '#ffc107',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 15,
                            font: { size: 12, weight: '600' }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 },
                        cornerRadius: 8
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            font: { size: 11 }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: { size: 11 }
                        }
                    }
                }
            }
        });
    }

    // Pie Chart - Activity Distribution
    const pieCtx = document.getElementById('pieChart');
    if (pieCtx) {
        new Chart(pieCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Consultations', 'Contacts', 'Job Applications', 'Campaigns'],
                datasets: [{
                    data: [{{ $stats['total_consultations'] }}, {{ $stats['total_contacts'] }}, {{ $stats['total_job_apps'] }}, {{ $stats['total_campaigns'] }}],
                    backgroundColor: [
                        '#2f5597',
                        '#17a2b8',
                        '#ffc107',
                        '#28a745'
                    ],
                    borderWidth: 3,
                    borderColor: '#fff',
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            font: { size: 12, weight: '600' }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 },
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    // Content & Authors Mix
    const contentCtx = document.getElementById('contentChart');
    if (contentCtx) {
        new Chart(contentCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['Blogs', 'Videos', 'Careers', 'Authors'],
                datasets: [{
                    data: [
                        {{ $stats['blogs'] ?? 0 }},
                        {{ $stats['videos'] ?? 0 }},
                        {{ $stats['careers'] ?? 0 }},
                        {{ $stats['unique_authors'] ?? 0 }}
                    ],
                    backgroundColor: [
                        'rgba(10, 113, 158, 0.85)',
                        'rgba(0, 173, 161, 0.85)',
                        'rgba(53, 162, 235, 0.85)',
                        'rgba(253, 126, 20, 0.85)'
                    ],
                    borderColor: [
                        'rgba(10, 113, 158, 1)',
                        'rgba(0, 173, 161, 1)',
                        'rgba(53, 162, 235, 1)',
                        'rgba(253, 126, 20, 1)'
                    ],
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1f2937',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        padding: 12,
                        cornerRadius: 6
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(15, 23, 42, 0.08)', drawBorder: false },
                        ticks: { stepSize: 1 }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }
})();
</script>
@endpush
