@extends('admin.layouts.admin')

@section('page-title', 'Content Management')
@section('title', 'Content Management - Armely Admin')

@php
    $currentAdminName = auth('admin')->user()->name ?? '';
@endphp

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" integrity="sha384-ok3J6xA9oQqai5C9ytYveFsBeKgoGk4T+NExsr6hoIKjZdv9SJcmx2mafwUWRNf9" crossorigin="anonymous">
<style>
    .page-title,
    .card,
    .table,
    .modal,
    .dataTables_wrapper {
        font-family: 'Inter', sans-serif;
    }

    .content-area {
        height: auto;
        min-height: calc(100vh - 72px);
        overflow: visible;
        display: block;
    }

    .page-title {
        flex: 0 0 auto;
        margin-bottom: 0.85rem;
    }

    .page-title h1 {
        font-size: clamp(1.35rem, 2vw, 1.75rem);
        line-height: 1.15;
        letter-spacing: 0;
        color: #1f3f80;
        margin-bottom: 0.3rem;
        font-weight: 700;
    }

    .page-title p {
        font-size: 0.9rem;
        color: #667085;
        margin-bottom: 0;
    }

    .content-preview {
        max-height: 100px;
        overflow: hidden;
    }

    .table {
        width: 100% !important;
        table-layout: fixed;
        font-size: 0.88rem;
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table thead th {
        background: #eef2f7;
        color: #334e77;
        border-bottom: 1px solid #d8e1ee;
        font-size: 0.79rem;
        font-weight: 600;
        letter-spacing: 0.35px;
        text-transform: uppercase;
        padding-top: 0.78rem;
        padding-bottom: 0.78rem;
    }

    .table th,
    .table td {
        vertical-align: middle;
        white-space: normal !important;
        word-break: break-word;
        overflow-wrap: anywhere;
        border-color: #e4eaf3;
        padding-top: 0.86rem;
        padding-bottom: 0.86rem;
    }

    .table td:last-child,
    .table th:last-child {
        white-space: nowrap !important;
        word-break: normal;
        overflow-wrap: normal;
    }
    
    /* Clean modal styling */
    .modal-header {
        background: #2F5597 !important;
        color: #fff !important;
        border-bottom: none;
        padding: 1rem 1.5rem;
    }
    
    .modal-header .modal-title {
        color: #fff !important;
        font-weight: 600;
    }
    
    .modal-header .btn-close {
        filter: brightness(0) invert(1);
        opacity: 0.8;
    }
    
    .modal-header .btn-close:hover {
        opacity: 1;
    }
    
    .modal-body {
        background: #fff;
        padding: 1.5rem;
    }
    
    .modal-body .form-label {
        color: #2F5597;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    
    .modal-body .form-control {
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        padding: 0.625rem 0.875rem;
    }
    
    .modal-body .form-control:focus {
        border-color: #2F5597;
        box-shadow: 0 0 0 0.2rem rgba(47, 85, 151, 0.15);
    }
    
    .modal-footer {
        background: #fff;
        border-top: none;
        padding: 1rem 1.5rem;
        gap: 0.5rem;
    }
    
    .modal-footer .btn-secondary {
        background: #6c757d;
        border: none;
    }
    
    .modal-footer .btn-primary {
        background: #2F5597;
        border: none;
    }
    
    .modal-footer .btn-primary:hover {
        background: #1e3a6b;
    }
    
    /* Form Helper Text */
    .modal .text-muted {
        font-size: 0.85rem;
        color: #6b7280;
        margin-top: 0.25rem;
    }
    
    /* Input File Styling */
    .modal input[type="file"] {
        padding: 0.5rem;
        cursor: pointer;
    }
    
    /* MB-3 Spacing Override */
    .modal-body .mb-3 {
        margin-bottom: 1.25rem !important;
    }
    
    /* Remove form label icons - clean look */
    .modal-body .form-label i {
        display: none;
    }
    
    /* Icon-only action button layout */
    .action-btns{display:flex;gap:.5rem;align-items:center}
    .action-btns .btn{min-width:36px;padding:6px;display:inline-flex;align-items:center;justify-content:center}
    .cv-btns{display:inline-flex;gap:.35rem;align-items:center;flex-wrap:wrap}
    .cv-btns .btn{min-width:34px;padding:5px;display:inline-flex;align-items:center;justify-content:center}
    .cv-btns .btn i{font-size:.9rem}
    @media(max-width:576px){.action-btns{flex-direction:row;flex-wrap:wrap}.action-btns .btn{min-width:40px}}
    
    /* DataTables custom styling */
    .dataTables_wrapper {
        display: flex;
        flex-direction: column;
        width: 100%;
        max-width: 100%;
        overflow-x: hidden;
    }

    .dataTables_wrapper .row {
        width: 100%;
        margin-left: 0;
        margin-right: 0;
    }

    .content-management-card .dataTables_wrapper .dataTables_filter,
    .content-management-card .dataTables_wrapper .dataTables_length,
    .content-management-card .dataTables_wrapper .dataTables_info,
    .content-management-card .dataTables_wrapper .dataTables_paginate {
        font-size: 0.82rem;
        color: #5f6c80;
    }

    .content-management-tabs {
        align-items: center;
        gap: 0.2rem;
    }

    .content-management-tabs .nav-link {
        white-space: nowrap;
    }

    .content-management-tabs .more-tabs-item .dropdown-menu {
        border: 1px solid #d8e1ee;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.12);
        border-radius: 8px;
        padding: 0.4rem;
        min-width: 220px;
    }

    .content-management-tabs .more-tabs-item .dropdown-item {
        border-radius: 6px;
        color: #334e77;
        font-size: 0.9rem;
        font-weight: 600;
        padding: 0.62rem 0.75rem;
    }

    .content-management-tabs .more-tabs-item .dropdown-item i {
        width: 18px;
        margin-right: 0.45rem;
        color: #2f5597;
    }

    .content-management-tabs .more-tabs-item .dropdown-item.active,
    .content-management-tabs .more-tabs-item .dropdown-item:active {
        background: #2f5597;
        color: #fff;
    }

    .content-management-tabs .more-tabs-item .dropdown-item.active i,
    .content-management-tabs .more-tabs-item .dropdown-item:active i {
        color: #fff;
    }

    /* Top toolbar matches Job Applications feel: clean and compact */
    .content-management-card .dt-toolbar {
        margin: 0 0 0.75rem 0 !important;
        padding: 0.25rem 0;
        border: 0;
        border-radius: 0;
        background: transparent;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: nowrap;
        gap: 0.6rem;
    }

    .content-management-card .dt-toolbar .dt-length,
    .content-management-card .dt-toolbar .dt-search {
        display: flex;
        align-items: center;
        min-height: 42px;
        flex: 0 0 auto;
    }

    .content-management-card .dt-toolbar .dt-search {
        justify-content: flex-start;
    }

    .content-management-card .dt-toolbar .dt-length {
        justify-content: flex-end;
        margin-left: auto;
    }

    .content-management-card .dt-toolbar .dataTables_filter,
    .content-management-card .dt-toolbar .dataTables_length {
        margin: 0 !important;
    }

    .content-management-card .dt-toolbar .dataTables_length label,
    .content-management-card .dt-toolbar .dataTables_filter label {
        width: auto;
        margin: 0;
        position: relative;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #5b6678;
        font-weight: 500;
        font-size: 0.86rem;
        white-space: nowrap;
    }

    .content-management-card .dt-toolbar .dataTables_filter label {
        justify-content: flex-start;
    }

    .content-management-card .dt-toolbar .dataTables_filter label::before {
        content: "\f002";
        font-family: "Font Awesome 6 Free";
        font-weight: 900;
        color: #233a59;
        position: absolute;
        left: 0.8rem;
        pointer-events: none;
        font-size: 0.95rem;
    }

    .content-management-card .dt-toolbar .dataTables_length select {
        height: 42px;
        border: 1px solid #cfd9e8;
        border-radius: 0.5rem;
        background: #fff;
        min-width: 105px;
        color: #233a59;
        padding-left: 0.65rem;
    }

    .content-management-card .dt-toolbar .dataTables_filter input {
        height: 42px;
        border: 1px solid #cfd9e8;
        border-radius: 0.5rem;
        background: #fff;
        width: min(100%, 560px);
        min-width: 260px;
        padding-left: 2.3rem;
        margin-left: 0;
    }

    @media (max-width: 992px) {
        .content-management-card .dt-toolbar {
            flex-wrap: wrap;
            justify-content: flex-start;
        }

        .content-management-card .dt-toolbar .dt-length {
            margin-left: 0;
        }
    }

    .content-management-card .dt-toolbar .dataTables_filter input:focus,
    .content-management-card .dt-toolbar .dataTables_length select:focus {
        border-color: #9fb4d6;
        box-shadow: 0 0 0 0.2rem rgba(47, 85, 151, 0.12);
        outline: none;
    }
    
    .content-management-card .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0.32rem 0.62rem;
        margin: 0 1px;
        border-radius: 0.4rem !important;
    }

    .content-management-card .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .content-management-card .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: #2f5597 !important;
        color: #fff !important;
        border: 1px solid #2f5597 !important;
    }
    
    /* Uniform table styling */
    .table-responsive {
        border: 1px solid #e5ebf3;
        border-radius: 0.55rem;
        width: 100%;
        max-width: 100%;
        overflow-x: auto !important;
        overflow-y: visible !important;
        background: #fff;
        max-height: none;
        box-shadow: 0 2px 8px rgba(31, 63, 128, 0.05);
    }

    .card-body {
        width: 100%;
        max-width: 100%;
        overflow-x: hidden;
    }

    .card.mb-4 {
        display: block;
        height: auto;
        min-height: 0;
        overflow: visible;
    }

    .card.mb-4 > .tab-content {
        min-height: 0;
        overflow: visible;
    }

    .card.mb-4 > .tab-content > .tab-pane {
        height: auto;
    }

    .card.mb-4 > .tab-content > .tab-pane > .card-body {
        height: auto;
        display: block;
        min-height: 0;
    }

    .card.mb-4 > .tab-content > .tab-pane > .card-body > .btn.btn-primary.mb-3 {
        align-self: flex-start;
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        width: auto !important;
        max-width: 100%;
    }

    .card.mb-4 > .tab-content > .tab-pane > .card-body > .table-responsive,
    .card.mb-4 > .tab-content > .tab-pane > .card-body > .row > .col-12 > .table-responsive {
        min-height: 0;
    }

    .dataTables_wrapper {
        height: auto;
    }

    .dataTables_wrapper .dataTables_scrollBody {
        max-height: none !important;
        overflow-y: visible !important;
        overflow-x: auto !important;
        border-bottom: 1px solid #edf1f6;
    }

    .content-management-card .dataTables_wrapper .dataTables_paginate,
    .content-management-card .dataTables_wrapper .dataTables_info {
        position: static;
        background: #fff;
        z-index: 2;
    }
    
    .content-management-card .dataTables_wrapper .dataTables_paginate {
        margin-top: auto;
        padding-top: 1rem;
        border-top: 1px solid #dee2e6;
    }

    .content-management-card .dt-footer {
        margin: 0 !important;
        padding-top: 0.65rem;
        border-top: 1px solid #e8edf5;
        align-items: center;
        background: #fff;
    }

    .content-management-card .dt-footer .dataTables_info {
        padding: 0;
    }

    .content-management-card .dt-footer .dataTables_paginate {
        padding: 0;
        border-top: none;
    }

    @media (max-width: 768px) {
        .content-area {
            min-height: calc(100vh - 64px);
        }

        .card.mb-4 {
            min-height: 0;
        }

        .table {
            font-size: 0.8rem;
        }

        .table thead th {
            font-size: 0.72rem;
        }

        .dataTables_wrapper .dataTables_filter input {
            width: 100%;
            margin-left: 0;
            margin-top: 0.35rem;
            min-width: 0;
        }

        .content-management-card .dt-toolbar {
            gap: 0.45rem;
        }

        .content-management-card .dt-toolbar .dataTables_length label,
        .content-management-card .dt-toolbar .dataTables_filter label {
            justify-content: flex-start;
        }

        .content-management-card .dt-toolbar .dt-search {
            justify-content: flex-start;
        }

        .action-btns {
            flex-wrap: wrap;
        }
    }
</style>
@endpush

@section('content')
<div class="page-title">
    <h1>Content Management</h1>
    <p>Manage all website content in one place</p>
</div>

<!-- Nav Tabs -->
<div class="card mb-4 content-management-card">
    <div class="card-header border-bottom">
        <ul class="nav nav-tabs card-header-tabs content-management-tabs" id="contentManagementTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="blogs-tab" data-bs-toggle="tab" href="#blogs" role="tab">
                    <i class="fas fa-blog"></i> Blogs
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="videos-tab" data-bs-toggle="tab" href="#videos" role="tab">
                    <i class="fas fa-video"></i> Videos
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="careers-tab" data-bs-toggle="tab" href="#careers" role="tab">
                    <i class="fas fa-briefcase"></i> Careers
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="social-tab" data-bs-toggle="tab" href="#social" role="tab">
                    <i class="fas fa-heart"></i> Social Impact
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="stories-tab" data-bs-toggle="tab" href="#stories" role="tab">
                    <i class="fas fa-user-tie"></i> Customer Stories
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="case-studies-tab" data-bs-toggle="tab" href="#case-studies" role="tab">
                    <i class="fas fa-file-alt"></i> Case Studies
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="events-tab" data-bs-toggle="tab" href="#events" role="tab">
                    <i class="fas fa-calendar-alt"></i> Events
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="team-tab" data-bs-toggle="tab" href="#team" role="tab">
                    <i class="fas fa-users"></i> Team
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="newsletter-tab" data-bs-toggle="tab" href="#newsletter" role="tab">
                    <i class="fas fa-envelope-open-text"></i> Newsletter
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="jobs-tab" data-bs-toggle="tab" href="#jobs" role="tab">
                    <i class="fas fa-briefcase"></i> Job Applications
                </a>
            </li>
            <li class="nav-item dropdown more-tabs-item d-none" id="contentTabsMoreItem">
                <a class="nav-link dropdown-toggle" href="#" id="contentTabsMoreToggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-ellipsis-h"></i> More
                </a>
                <ul class="dropdown-menu dropdown-menu-end" id="contentTabsMoreMenu" aria-labelledby="contentTabsMoreToggle"></ul>
            </li>
        </ul>
    </div>
    
    <div class="tab-content">
        <!-- Blogs Tab -->
        <div class="tab-pane fade show active" id="blogs" role="tabpanel">
            <div class="card-body">
                <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#blogModal">
                    <i class="fas fa-plus"></i> Add New Blog
                </button>
                
                    <div class="table-responsive">
                        <table class="table table-hover" id="blogsDataTable">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Author</th>
                                    <th>Date</th>
                                    <th width="200">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="blogsTable">
                                <tr>
                                    <td class="text-center"><span class="spinner-border spinner-border-sm"></span></td>
                                    <td>Loading blogs...</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
            </div>
        </div>
        
        <!-- Videos Tab -->
        <div class="tab-pane fade" id="videos" role="tabpanel">
            <div class="card-body">
                <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#videoModal" onclick="resetVideoForm()">
                    <i class="fas fa-plus"></i> Add New Video
                </button>
                
                @if($videos->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover" id="videosDataTable">
                            <thead>
                                <tr>
                                    <th width="60%">Video URL</th>
                                    <th width="200">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="videosTable">
                                @foreach($videos as $video)
                                <tr data-id="{{ $video->id ?? $video->video_id }}">
                                    <td>
                                        @php
                                            $videoContent = $video->url ?? $video->video_url ?? $video->iframe ?? '';
                                            // Show preview link instead of actual video
                                            if (stripos($videoContent, '<iframe') !== false) {
                                                // Extract URL from iframe if possible
                                                preg_match('/src=["\']([^"\']+)["\']/', $videoContent, $matches);
                                                $url = $matches[1] ?? $videoContent;
                                                echo '<a href="' . e($url) . '" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-play-circle me-2"></i>Preview Video</a>';
                                            } else {
                                                echo '<a href="' . e($videoContent) . '" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-play-circle me-2"></i>Preview Video</a>';
                                            }
                                        @endphp
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-warning edit-video" data-video='@json($video)' title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger delete-video" data-id="{{ $video->id ?? $video->video_id }}" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted"><i class="fas fa-info-circle"></i> No videos yet. Add your first video!</p>
                @endif
            </div>
        </div>
        
        <!-- Careers Tab -->
        <div class="tab-pane fade" id="careers" role="tabpanel">
            <div class="card-body">
                <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#careerModal" onclick="resetCareerForm()">
                    <i class="fas fa-plus"></i> Add New Career
                </button>
                
                @if($careers->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover" id="careersDataTable">
                            <thead>
                                <tr>
                                    <th>Job Title</th>
                                    <th>Location</th>
                                    <th>Type</th>
                                    <th width="200">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="careersTable">
                                @foreach($careers as $career)
                                <tr data-id="{{ $career->id }}">
                                    <td>{{ $career->job_title ?? $career->title ?? 'N/A' }}</td>
                                    <td>{{ $career->job_location ?? $career->location ?? 'N/A' }}</td>
                                    <td><span class="badge bg-info">{{ $career->job_type ?? $career->type ?? 'N/A' }}</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-info view-career" data-career='@json($career)' title="View">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning edit-career" data-career='@json($career)' title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger delete-career" data-id="{{ $career->id }}" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted"><i class="fas fa-info-circle"></i> No careers yet. Post a job!</p>
                @endif
            </div>
        </div>
        
        <!-- Social Impact Tab -->
        <div class="tab-pane fade" id="social" role="tabpanel">
            <div class="card-body">
                <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#socialModal" onclick="resetSocialForm()">
                    <i class="fas fa-plus"></i> Add New Impact Story
                </button>
                
                @if($socialImpact->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover" id="socialDataTable">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Published</th>
                                    <th width="200">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="socialTable">
                                @foreach($socialImpact as $impact)
                                <tr data-id="{{ $impact->id }}">
                                    <td>{{ \Illuminate\Support\Str::limit($impact->title ?? '', 50) }}</td>
                                    <td>{{ $impact->category ?? $impact->impact_area ?? 'N/A' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($impact->posted_date ?? $impact->published_date ?? now())->format('M d, Y') }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info view-social" data-social='@json($impact)' title="View">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning edit-social" data-social='@json($impact)' title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger delete-social" data-id="{{ $impact->id }}" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted"><i class="fas fa-info-circle"></i> No impact stories yet. Share your impact!</p>
                @endif
            </div>
        </div>
        
        <!-- Customer Stories Tab -->
        <div class="tab-pane fade" id="stories" role="tabpanel">
            <div class="card-body">
                <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#storyModal" onclick="resetStoryForm()">
                    <i class="fas fa-plus"></i> Add New Customer Story
                </button>
                
                @if($customerStories->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover" id="storiesDataTable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Position</th>
                                    <th width="200">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="storiesTable">
                                @foreach($customerStories as $story)
                                <tr data-id="{{ $story->id }}">
                                    <td>{{ $story->name ?? 'N/A' }}</td>
                                    <td>{{ $story->position ?? 'N/A' }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info view-story" data-story='@json($story)' title="View">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning edit-story" data-story='@json($story)' title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger delete-story" data-id="{{ $story->id }}" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted"><i class="fas fa-info-circle"></i> No customer stories yet. Add one!</p>
                @endif
            </div>
        </div>

        <!-- Case Studies Tab -->
        <div class="tab-pane fade" id="case-studies" role="tabpanel">
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#caseStudyModal" onclick="resetCaseStudyForm()">
                        <i class="fas fa-plus"></i> Add New Resource
                    </button>
                    <a class="btn btn-outline-primary" href="{{ route('admin.case-study-categories.index') }}">
                        <i class="fas fa-tags"></i> Manage Categories
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover" id="caseStudiesDataTable">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Industry Type</th>
                                <th>Outcome Tag</th>
                                <th>Title</th>
                                <th>Image</th>
                                <th>PDF</th>
                                <th>Summary</th>
                                <th width="200">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="caseStudiesTable">
                            @forelse(($caseStudies ?? collect()) as $caseStudy)
                            <tr data-id="{{ $caseStudy->id }}">
                                @php($resourceType = $caseStudy->resource_type ?? 'case_study')
                                @php($isWhitePaper = $resourceType === 'white_paper')
                                <td>
                                    <span class="badge {{ $isWhitePaper ? 'bg-secondary' : 'bg-primary' }}">
                                        {{ $isWhitePaper ? 'White Paper' : 'Case Study' }}
                                    </span>
                                </td>
                                <td>{{ $isWhitePaper ? 'N/A' : ($caseStudy->category ?? 'N/A') }}</td>
                                <td>{{ $isWhitePaper ? 'N/A' : ($caseStudy->outcome_tag ?? 'N/A') }}</td>
                                <td>{{ $caseStudy->title ?? ($caseStudy->category ?? 'N/A') }}</td>
                                <td>
                                    @if(!empty($caseStudy->listing_image))
                                        @php($imageBase = $isWhitePaper ? asset('images/white-papers') : asset('images/case-study'))
                                        <img src="{{ $imageBase . '/' . $caseStudy->listing_image }}" alt="{{ $caseStudy->title ?? $caseStudy->category ?? 'Resource' }}" style="width: 48px; height: 36px; object-fit: cover; border-radius: 6px;">
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($caseStudy->pdf_url))
                                        @php($pdfFolder = $isWhitePaper ? 'white_paper_docs' : 'case_docs')
                                        @php($casePdfUrl = str_starts_with($caseStudy->pdf_url, 'http') ? $caseStudy->pdf_url : url($pdfFolder . '/' . $caseStudy->pdf_url))
                                        <a href="{{ $casePdfUrl }}" target="_blank" class="btn btn-sm btn-outline-primary" title="Preview PDF"><i class="fas fa-file-pdf"></i></a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>{{ \Illuminate\Support\Str::limit(strip_tags($caseStudy->body ?? ''), 90) }}</td>
                                <td>
                                    <div class="action-btns">
                                        <button class="btn btn-sm btn-info view-case-study" data-case-study='@json($caseStudy)' title="View">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning edit-case-study" data-case-study='@json($caseStudy)' title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger delete-case-study" data-id="{{ $caseStudy->id }}" data-type="{{ $resourceType }}" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="text-center text-muted">No resources yet. Add one!</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Events Tab -->
        <div class="tab-pane fade" id="events" role="tabpanel">
            <div class="card-body">
                <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#eventModal" onclick="resetEventForm()">
                    <i class="fas fa-plus"></i> Add New Event
                </button>
                
                @if($events->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover" id="eventsDataTable">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Date</th>
                                    <th>URL</th>
                                    <th width="200">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="eventsTable">
                                @foreach($events as $event)
                                <tr data-id="{{ $event->id }}">
                                    <td>{{ \Illuminate\Support\Str::limit(strip_tags($event->title ?? ''), 50) }}</td>
                                    <td>{{ $event->start_date ?? 'N/A' }}</td>
                                    <td>
                                        @if($event->url)
                                            <a href="{{ $event->url }}" target="_blank" class="text-primary">
                                                <i class="fas fa-external-link-alt"></i> Link
                                            </a>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info view-event" data-event='@json($event)' title="View">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning edit-event" data-event='@json($event)' title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger delete-event" data-id="{{ $event->id }}" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted"><i class="fas fa-info-circle"></i> No events yet. Add one!</p>
                @endif
            </div>
        </div>

        <!-- Team Tab -->
        <div class="tab-pane fade" id="team" role="tabpanel">
            <div class="card-body">
                <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#teamModal" onclick="resetTeamForm()">
                    <i class="fas fa-plus"></i> Add New Team Member
                </button>
                
                @if($team->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover" id="teamDataTable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Title</th>
                                    <th>Image</th>
                                    <th width="200">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="teamTable">
                                @foreach($team as $member)
                                <tr data-id="{{ $member->id }}">
                                    <td>{{ $member->team_name ?? 'N/A' }}</td>
                                    <td>{{ $member->team_title ?? 'N/A' }}</td>
                                    <td>
                                        @if($member->team_image)
                                            <img src="{{ asset('images/team/' . $member->team_image) }}" alt="{{ $member->team_name }}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%;">
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info view-team" data-team='@json($member)' title="View">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning edit-team" data-team='@json($member)' title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger delete-team" data-id="{{ $member->id }}" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted"><i class="fas fa-info-circle"></i> No team members yet. Add one!</p>
                @endif
            </div>
        </div>

        <!-- Newsletter Tab -->
        <div class="tab-pane fade" id="newsletter" role="tabpanel">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <div>
                        <h5 class="mb-1">Newsletter Subscribers</h5>
                        <p class="text-muted mb-0">Manage footer signups and email subscription status.</p>
                    </div>
                    <span class="badge bg-primary">{{ ($newsletterSubscribers ?? collect())->count() }} shown</span>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover" id="newsletterDataTable">
                        <thead>
                            <tr>
                                <th>Email</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Subscribed</th>
                                <th>Last Notified</th>
                                <th width="220">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="newsletterTable">
                            @forelse(($newsletterSubscribers ?? collect()) as $subscriber)
                            <tr data-id="{{ $subscriber->id }}">
                                <td>{{ $subscriber->email }}</td>
                                <td>{{ $subscriber->name ?: 'N/A' }}</td>
                                <td>
                                    <span class="badge {{ ($subscriber->status ?? 'active') === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ ucfirst($subscriber->status ?? 'active') }}
                                    </span>
                                </td>
                                <td>{{ $subscriber->subscribed_at ? \Carbon\Carbon::parse($subscriber->subscribed_at)->format('M d, Y g:i A') : 'N/A' }}</td>
                                <td>{{ $subscriber->last_notified_at ? \Carbon\Carbon::parse($subscriber->last_notified_at)->format('M d, Y g:i A') : 'Never' }}</td>
                                <td>
                                    <div class="action-btns">
                                        @if(($subscriber->status ?? 'active') === 'active')
                                            <button class="btn btn-sm btn-warning unsubscribe-newsletter" data-id="{{ $subscriber->id }}" title="Unsubscribe">
                                                <i class="fas fa-user-slash"></i>
                                            </button>
                                        @else
                                            <button class="btn btn-sm btn-success resubscribe-newsletter" data-id="{{ $subscriber->id }}" title="Resubscribe">
                                                <i class="fas fa-user-check"></i>
                                            </button>
                                        @endif
                                        <button class="btn btn-sm btn-danger delete-newsletter" data-id="{{ $subscriber->id }}" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center text-muted">No newsletter subscribers yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Job Applications Tab -->
        <div class="tab-pane fade" id="jobs" role="tabpanel">
            <div class="card-body">
                <ul class="nav nav-tabs mb-3" id="jobsTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="applications-tab" data-bs-toggle="tab" data-bs-target="#applications" type="button" role="tab">Pending</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="shortlisted-tab" data-bs-toggle="tab" data-bs-target="#shortlisted" type="button" role="tab">Shortlisted</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="hired-tab" data-bs-toggle="tab" data-bs-target="#hired" type="button" role="tab">Hired</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="rejected-tab" data-bs-toggle="tab" data-bs-target="#rejected" type="button" role="tab">Rejected</button>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- Pending Applications -->
                    <div class="tab-pane fade show active" id="applications" role="tabpanel">
                        <div class="row mb-3 align-items-center">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" class="form-control" id="searchApplications" placeholder="Search applications...">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <select class="form-select" id="filterLocation">
                                    <option value="">All Locations</option>
                                </select>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover" id="applicationsDataTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Position</th>
                                        <th>Location</th>
                                        <th>Date</th>
                                        <th>CV</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="applicationsTable">
                                    <tr><td colspan="8" class="text-center"><span class="spinner-border spinner-border-sm"></span></td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Shortlisted -->
                    <div class="tab-pane fade" id="shortlisted" role="tabpanel">
                        <div class="row mb-3 align-items-center">
                            <div class="col-md-12">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" class="form-control" id="searchShortlisted" placeholder="Search shortlisted...">
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover" id="shortlistedDataTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Position</th>
                                        <th>Date Applied</th>
                                        <th>CV</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="shortlistedTable">
                                    <tr><td colspan="7" class="text-center"><span class="spinner-border spinner-border-sm"></span></td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Hired -->
                    <div class="tab-pane fade" id="hired" role="tabpanel">
                        <div class="row mb-3 align-items-center">
                            <div class="col-md-12">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" class="form-control" id="searchHired" placeholder="Search hired...">
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover" id="hiredDataTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Position</th>
                                        <th>Date Applied</th>
                                        <th>CV</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="hiredTable">
                                    <tr><td colspan="7" class="text-center"><span class="spinner-border spinner-border-sm"></span></td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Rejected -->
                    <div class="tab-pane fade" id="rejected" role="tabpanel">
                        <div class="row mb-3 align-items-center">
                            <div class="col-md-12">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" class="form-control" id="searchRejected" placeholder="Search rejected...">
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover" id="rejectedDataTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Position</th>
                                        <th>Date Applied</th>
                                        <th>CV</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="rejectedTable">
                                    <tr><td colspan="7" class="text-center"><span class="spinner-border spinner-border-sm"></span></td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('modals')
<!-- MODALS -->

<!-- Blog View Modal -->
<div class="modal fade" id="viewBlogModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">View Blog</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h4 id="viewBlogTitle"></h4>
                <p class="text-muted">
                    <small>By <span id="viewBlogAuthor"></span> on <span id="viewBlogDate"></span></small>
                </p>
                <hr>
                <div id="viewBlogContent"></div>
            </div>
        </div>
    </div>
</div>

<!-- Blog Edit/Add Modal -->
<div class="modal fade" id="blogModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="blogModalTitle">Add New Blog</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="blogForm" method="POST" action="{{ route('admin.tables.blogs.store') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="blogId" name="id">
                    
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label for="blogTitle" class="form-label">Title *</label>
                            <input type="text" class="form-control" id="blogTitle" name="title" required placeholder="Enter blog title">
                        </div>
                        
                        <div class="col-md-4">
                            <label for="blogDate" class="form-label">Date *</label>
                            <input type="date" class="form-control" id="blogDate" name="date" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="blogAuthor" class="form-label">Author *</label>
                            <select class="form-select" id="blogAuthor" name="author" required data-default-author="{{ $currentAdminName }}">
                                @foreach(($adminAuthors ?? collect()) as $authorName)
                                    <option value="{{ $authorName }}" {{ $authorName === $currentAdminName ? 'selected' : '' }}>{{ $authorName }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="blogImage" class="form-label">Featured Image</label>
                            <input type="file" class="form-control" id="blogImage" name="image" accept="image/*">
                            <small class="text-muted">Use a JPG, PNG, or WEBP image at least 900x500px. Current image will be preserved if no new image is uploaded.</small>
                        </div>
                        
                        <div class="col-12">
                            <label for="blogBody" class="form-label">Content</label>
                            <textarea class="form-control" id="blogBody" name="body" rows="15"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancel
                </button>
                <button type="button" class="btn btn-primary" id="saveBlogBtn">
                    <i class="fas fa-save me-2"></i>Save Blog
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Case Study View Modal -->
<div class="modal fade" id="viewCaseStudyModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">View Case Study</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h4 id="viewCaseStudyCategory"></h4>
                <p class="text-muted mb-3">
                    <a id="viewCaseStudyPdf" href="#" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary d-none">
                        <i class="fas fa-file-pdf me-1"></i> Preview PDF
                    </a>
                </p>
                <div id="viewCaseStudyImageWrap" class="mb-3 d-none">
                    <img id="viewCaseStudyImage" src="" alt="" class="img-fluid rounded border" style="max-height: 220px; object-fit: cover;">
                </div>
                <hr>
                <div id="viewCaseStudyBody"></div>
            </div>
        </div>
    </div>
</div>

<!-- Case Study Edit/Add Modal -->
<div class="modal fade" id="caseStudyModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="caseStudyModalTitle">Add New Case Study</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="caseStudyForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="caseStudyId" name="id">
                    <input type="hidden" id="caseStudyMode" value="create">
                    <input type="hidden" id="caseStudySourceType" value="case_study">

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label d-block">Resource Type *</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="resource_type" id="resourceTypeCaseStudy" value="case_study" checked>
                                    <label class="form-check-label" for="resourceTypeCaseStudy">Case Study</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="resource_type" id="resourceTypeWhitePaper" value="white_paper">
                                    <label class="form-check-label" for="resourceTypeWhitePaper">White Paper</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6" id="caseStudyCategoryGroup">
                            <label for="caseStudyCategory" class="form-label">Industry Type *</label>
                            <div class="d-flex align-items-center gap-2">
                                <select class="form-select" id="caseStudyCategory" name="category" required>
                                    <option value="" selected disabled>Select a category</option>
                                    @foreach(($caseStudyCategories ?? collect()) as $category)
                                        <option value="{{ $category }}">{{ $category }}</option>
                                    @endforeach
                                </select>
                                <a href="{{ route('admin.case-study-categories.index') }}" class="btn btn-outline-secondary btn-sm" title="Manage categories">
                                    <i class="fas fa-pen"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6" id="caseStudyOutcomeTagGroup">
                            <label for="caseStudyOutcomeTag" class="form-label" id="caseStudyOutcomeTagLabel">Outcome Tag</label>
                            <input type="text" class="form-control" id="caseStudyOutcomeTag" name="outcome_tag" list="caseStudyOutcomeTagOptions" placeholder="e.g. Faster dispatch visibility" maxlength="255">
                            <small class="text-muted" id="caseStudyOutcomeTagHelp">Use one tag per card. This appears as the single highlight chip on the case-study card.</small>
                            <datalist id="caseStudyOutcomeTagOptions">
                                <option value="Faster dispatch visibility"></option>
                                <option value="Reduced manual status updates"></option>
                                <option value="Improved delivery performance tracking"></option>
                            </datalist>
                        </div>
                        <div class="col-md-6">
                            <label for="caseStudyTitle" class="form-label" id="caseStudyTitleLabel">Case Study Title *</label>
                            <input type="text" class="form-control" id="caseStudyTitle" name="title" required placeholder="e.g. SmartWay Transportation">
                        </div>
                        <div class="col-md-6 d-none" id="caseStudyExistingImageGroup">
                            <label for="caseStudyExistingImage" class="form-label" id="caseStudyExistingImageLabel">Existing White Paper Image Filename or URL</label>
                            <input type="text" class="form-control" id="caseStudyExistingImage" name="existing_image" placeholder="white-paper.jpg or https://...">
                            <small class="text-muted" id="caseStudyExistingImageHelp">Upload a new image below to replace this value.</small>
                        </div>
                        <div class="col-md-6">
                            <label for="caseStudyPdfUrl" class="form-label">Existing PDF Filename or URL</label>
                            <input type="text" class="form-control" id="caseStudyPdfUrl" name="pdf_url" placeholder="case-study.pdf or https://...">
                            <small class="text-muted" id="caseStudyPdfHelp">Upload a new PDF below to replace this value.</small>
                        </div>
                        <div class="col-md-6">
                            <label for="caseStudyImage" class="form-label" id="caseStudyImageLabel">Listing Image</label>
                            <input type="file" class="form-control" id="caseStudyImage" name="listing_image" accept="image/*">
                            <small class="text-muted" id="caseStudyImageHelp">Saved to public/images/case-study.</small>
                        </div>
                        <div class="col-md-6">
                            <label for="caseStudyPdf" class="form-label">PDF</label>
                            <input type="file" class="form-control" id="caseStudyPdf" name="pdf" accept="application/pdf,.pdf">
                            <small class="text-muted" id="caseStudyPdfPathHelp">Saved to public/case_docs.</small>
                        </div>
                        <div class="col-12">
                            <label for="caseStudyBody" class="form-label">Summary / Body</label>
                            <textarea class="form-control" id="caseStudyBody" name="body" rows="12"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancel
                </button>
                <button type="button" class="btn btn-primary" id="saveCaseStudyBtn">
                    <span class="btn-label">
                        <i class="fas fa-save me-2"></i>Save Case Study
                    </span>
                    <span class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Career View Modal -->
<div class="modal fade" id="viewCareerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">View Career</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h4 id="viewCareerTitle"></h4>
                <p class="text-muted">
                    <small><span id="viewCareerLocation"></span> • <span id="viewCareerType"></span></small>
                </p>
                <hr>
                <div id="viewCareerContent"></div>
            </div>
        </div>
    </div>
</div>

<!-- Social Impact View Modal -->
<div class="modal fade" id="viewSocialModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">View Social Impact Story</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h4 id="viewSocialTitle"></h4>
                <p class="text-muted">
                    <small>Category: <span id="viewSocialCategory"></span> • Posted: <span id="viewSocialDate"></span></small>
                </p>
                <hr>
                <div id="viewSocialContent"></div>
            </div>
        </div>
    </div>
</div>

<!-- Customer Story View Modal -->
<div class="modal fade" id="viewStoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">View Customer Story</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h4 id="viewStoryName"></h4>
                <p class="text-muted">
                    <small><span id="viewStoryPosition"></span></small>
                </p>
                <hr>
                <div id="viewStoryContent"></div>
            </div>
        </div>
    </div>
</div>

<!-- Video Edit/Add Modal -->
<div class="modal fade" id="videoModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="videoModalTitle">Add New Video</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="videoForm">
                    <input type="hidden" id="videoId" name="id">
                    <div class="mb-3">
                        <label class="form-label">Video URL or Embed Code *</label>
                        <textarea class="form-control" id="videoUrl" name="url" rows="4" required placeholder="Paste YouTube URL or iframe embed code"></textarea>
                        <small class="text-muted">Paste the video URL or complete iframe code from YouTube/Vimeo</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveVideoBtn">Save Video</button>
            </div>
        </div>
    </div>
</div>

<!-- Career Edit/Add Modal -->
<div class="modal fade" id="careerModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="careerModalTitle">Add New Career</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="careerForm">
                    <input type="hidden" id="careerId" name="id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Job Title *</label>
                            <input type="text" class="form-control" id="careerTitle" name="job_title" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Location *</label>
                            <input type="text" class="form-control" id="careerLocation" name="job_location" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Job Type *</label>
                            <select class="form-control" id="careerType" name="job_type" required>
                                <option value="">Select Type</option>
                                <option value="Full-time">Full-time</option>
                                <option value="Part-time">Part-time</option>
                                <option value="Contract">Contract</option>
                                <option value="Internship">Internship</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Deadline</label>
                            <input type="date" class="form-control" id="careerDeadline" name="job_deadline">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Job Description *</label>
                        <textarea class="form-control" id="careerBody" name="job_description" rows="10"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveCareerBtn">Save Career</button>
            </div>
        </div>
    </div>
</div>

<!-- Social Impact Edit/Add Modal -->
<div class="modal fade" id="socialModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="socialModalTitle">Add New Social Impact Story</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="socialForm">
                    <input type="hidden" id="socialId" name="id">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Title *</label>
                            <input type="text" class="form-control" id="socialTitle" name="title" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Category *</label>
                            <input type="text" class="form-control" id="socialCategory" name="category" required placeholder="e.g., Education, Health">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Posted Date *</label>
                        <input type="date" class="form-control" id="socialDate" name="posted_date" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Content *</label>
                        <textarea class="form-control" id="socialBody" name="body" rows="10"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Author Name</label>
                        <input type="text" class="form-control" id="socialAuthor" name="author_name" placeholder="John Doe">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Image</label>
                        <input type="file" class="form-control" id="socialImage" name="image" accept="image/*">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveSocialBtn">Save Story</button>
            </div>
        </div>
    </div>
</div>

<!-- Customer Story Edit/Add Modal -->
<div class="modal fade" id="storyModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="storyModalTitle">Add New Customer Story</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="storyForm">
                    <input type="hidden" id="storyId" name="id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Name *</label>
                            <input type="text" class="form-control" id="storyName" name="name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Position *</label>
                            <input type="text" class="form-control" id="storyPosition" name="position" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Story Content *</label>
                        <textarea class="form-control" id="storyBody" name="body_content" rows="10"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Profile Image</label>
                        <input type="file" class="form-control" id="storyImage" name="profile" accept="image/*">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveStoryBtn">Save Story</button>
            </div>
        </div>
    </div>
</div>

<!-- Event Edit/Add Modal -->
<div class="modal fade" id="eventModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalTitle">Add New Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="eventForm">
                    <input type="hidden" id="eventId" name="id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Event Title *</label>
                            <input type="text" class="form-control" id="eventTitle" name="title" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Event Date *</label>
                            <input type="text" class="form-control" id="eventDate" name="start_date" required placeholder="DD/MM/YYYY">
                            <small class="text-muted">Format: DD/MM/YYYY (e.g., 25/12/2025)</small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Event URL</label>
                            <input type="url" class="form-control" id="eventUrl" name="url" placeholder="https://...">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Recorded URL</label>
                            <input type="url" class="form-control" id="eventRecordedUrl" name="recorded_url" placeholder="https://...">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Event Description *</label>
                        <textarea class="form-control" id="eventBody" name="body" rows="10"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveEventBtn">Save Event</button>
            </div>
        </div>
    </div>
</div>

<!-- Event View Modal -->
<div class="modal fade" id="viewEventModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">View Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h4 id="viewEventTitle"></h4>
                <p class="text-muted">
                    <small><i class="fas fa-calendar"></i> <span id="viewEventDate"></span></small>
                </p>
                <div class="mb-3">
                    <strong>Event URL:</strong> <a href="#" id="viewEventUrl" target="_blank" class="text-primary"></a>
                </div>
                <div class="mb-3" id="viewEventRecordedUrlContainer" style="display: none;">
                    <strong>Recorded URL:</strong> <a href="#" id="viewEventRecordedUrl" target="_blank" class="text-primary"></a>
                </div>
                <hr>
                <div id="viewEventContent"></div>
            </div>
        </div>
    </div>
</div>

<!-- Team Edit/Add Modal -->
<div class="modal fade" id="teamModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="teamModalTitle">Add New Team Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="teamForm">
                    <input type="hidden" id="teamId" name="id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Name *</label>
                            <input type="text" class="form-control" id="teamName" name="team_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Title *</label>
                            <input type="text" class="form-control" id="teamTitle" name="team_title" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">LinkedIn URL</label>
                            <input type="url" class="form-control" id="teamLinkedin" name="linkedin" placeholder="https://linkedin.com/in/...">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Facebook URL</label>
                            <input type="url" class="form-control" id="teamFacebook" name="facebook" placeholder="https://facebook.com/...">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Instagram URL</label>
                            <input type="url" class="form-control" id="teamInstagram" name="instagram" placeholder="https://instagram.com/...">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">X (Twitter) URL</label>
                            <input type="url" class="form-control" id="teamX" name="x" placeholder="https://x.com/...">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bio *</label>
                        <textarea class="form-control" id="teamBody" name="team_body" rows="6" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Profile Image</label>
                        <input type="file" class="form-control" id="teamImage" name="team_image" accept="image/*">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveTeamBtn">Save Team Member</button>
            </div>
        </div>
    </div>
</div>

<!-- Team View Modal -->
<div class="modal fade" id="viewTeamModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">View Team Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <img id="viewTeamImage" src="" alt="Team Member" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                </div>
                <h4 class="text-center" id="viewTeamName"></h4>
                <p class="text-center text-muted">
                    <small id="viewTeamTitle"></small>
                </p>
                <hr>
                <div id="viewTeamBio"></div>
                <hr>
                <div class="text-center" id="viewTeamSocials">
                    <!-- Social links will be added dynamically -->
                </div>
            </div>
        </div>
    </div>
</div>
@endpush

@endsection

@push('scripts')
<!-- DataTables (jQuery is already loaded from layout) -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js" integrity="sha384-cjmdOgDzOE22dUheI5E6Gzd3upfmReW8N1y/4jwKQE50KYcvFKZJA9JxWgQOzqwQ" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js" integrity="sha384-PgPBH0hy6DTJwu7pTf6bkRqPlf/+pjUBExpr/eIfzszlGYFlF9Wi9VTAJODPhgCO" crossorigin="anonymous"></script>
<!-- CKEditor -->
<script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
<script>
$(document).ready(function() {
        const $contentTabs = $('#contentManagementTabs');
        const $contentTabsMoreItem = $('#contentTabsMoreItem');
        const $contentTabsMoreMenu = $('#contentTabsMoreMenu');
        const contentTabItems = $contentTabs.children('.nav-item').not($contentTabsMoreItem).toArray();

        function getContentTabsVisibleLimit() {
            const width = window.innerWidth || document.documentElement.clientWidth;

            if (width >= 1200) return 8;
            if (width >= 992) return 6;
            if (width >= 768) return 5;
            if (width >= 576) return 4;
            if (width >= 420) return 3;

            return 2;
        }

        function updateContentTabsMoreActive() {
            const hasHiddenActive = $contentTabsMoreMenu.find('.dropdown-item.active').length > 0;
            $('#contentTabsMoreToggle').toggleClass('active', hasHiddenActive);
        }

        function syncContentTabActiveState(target) {
            if (!target) return;

            $contentTabs.find('[data-bs-toggle="tab"]').removeClass('active');
            $contentTabs.find('[data-bs-toggle="tab"][href="' + target + '"]').addClass('active');
            updateContentTabsMoreActive();
        }

        function rebuildContentTabsMore() {
            const visibleLimit = Math.min(getContentTabsVisibleLimit(), contentTabItems.length);
            $contentTabsMoreMenu.empty();

            let hiddenCount = 0;
            contentTabItems.forEach(function(item, index) {
                const $item = $(item);
                const $link = $item.find('[data-bs-toggle="tab"]').first();

                if (index < visibleLimit) {
                    $item.removeClass('d-none');
                    return;
                }

                $item.addClass('d-none');
                hiddenCount += 1;

                const $dropdownLink = $link.clone(false)
                    .removeClass('nav-link')
                    .addClass('dropdown-item')
                    .attr('id', ($link.attr('id') || 'content-tab') + '-more')
                    .attr('role', 'menuitem');

                $('<li>').append($dropdownLink).appendTo($contentTabsMoreMenu);
            });

            $contentTabsMoreItem.toggleClass('d-none', hiddenCount === 0);
            const activeTarget = $contentTabs.find('.nav-link.active, .dropdown-item.active').first().attr('href') || '#blogs';
            syncContentTabActiveState(activeTarget);
        }

        let contentTabsResizeTimer;
        $(window).on('resize', function() {
            clearTimeout(contentTabsResizeTimer);
            contentTabsResizeTimer = setTimeout(rebuildContentTabsMore, 120);
        });

        $(document).on('shown.bs.tab', '#contentManagementTabs [data-bs-toggle="tab"]', function(event) {
            const target = $(event.target).attr('href');
            syncContentTabActiveState(target);

            if (target === '#blogs') {
                reloadBlogsTable();
            } else if (target === '#jobs') {
                loadApplications();
                loadShortlisted();
                loadHired();
                loadRejected();
            } else {
                setTimeout(function() {
                    initializeDataTables();
                }, 100);
            }
        });

        rebuildContentTabsMore();

        function setModalStatus($button, type, message) {
            const $footer = $button.closest('.modal-content').find('.modal-footer').first();
            if (!$footer.length) return;

            let $status = $footer.find('.ajax-status-message');
            if (!$status.length) {
                $status = $('<div class="ajax-status-message small me-auto" role="status" aria-live="polite"></div>');
                $footer.prepend($status);
            }

            $status
                .removeClass('text-muted text-success text-danger')
                .addClass(type === 'success' ? 'text-success' : (type === 'error' ? 'text-danger' : 'text-muted'))
                .html(message || '');
        }

        function setButtonSaving($button, isSaving, savingText) {
            if (!$button.length) return;

            if (isSaving) {
                if (!$button.data('original-html')) {
                    $button.data('original-html', $button.html());
                }
                $button.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>' +
                    (savingText || 'Saving...')
                );
                setModalStatus($button, 'muted', '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>' + (savingText || 'Saving...'));
                return;
            }

            $button.prop('disabled', false).html($button.data('original-html') || $button.html());
        }

        function markAjaxSaveSuccess($button, message) {
            setModalStatus($button, 'success', '<i class="fas fa-check-circle me-1"></i>' + (message || 'Saved successfully.'));
        }

        function markAjaxSaveError($button, message) {
            setModalStatus($button, 'error', '<i class="fas fa-exclamation-circle me-1"></i>' + (message || 'Something went wrong.'));
        }

        $('.modal').on('hidden.bs.modal', function() {
            $(this).find('.ajax-status-message').remove();
        });
        
        // Initialize CKEditor when needed
        let blogEditor;
        
        function loadRejected() {
            $.ajax({
                url: '/admin/career/list-rejected',
                type: 'GET',
                success: function(response) {
                    let html = '';
                    response.forEach(function(app, index) {
                        const cvPreview = buildCvPreview(app);
                        const appliedDate = app.application_date ? new Date(app.application_date).toLocaleDateString() : 'N/A';
                        html += `<tr data-id="${app.id}">
                            <td>${index + 1}</td>
                            <td>${app.name}</td>
                            <td>${app.email}</td>
                            <td>${app.position}</td>
                            <td>${appliedDate}</td>
                            <td>${cvPreview}</td>
                            <td>
                                <div class="action-btns">
                                    <button class="btn btn-sm btn-success shortlist-btn" data-id="${app.id}" title="Shortlist">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger delete-cv-btn" data-id="${app.id}" title="Delete Application">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>`;
                    });
                    $('#rejectedTable').html(html || '<tr><td colspan="7" class="text-center text-muted">No rejected applications</td></tr>');
                }
            });
        }
    
    // ==================== TABLE RELOAD FUNCTIONS ====================
    
    // Reload blogs table
    function reloadBlogsTable() {
        if ($.fn.DataTable.isDataTable('#blogsDataTable')) {
            $('#blogsDataTable').DataTable().ajax.reload();
        } else {
            initBlogsDataTable();
        }
    }

    // In-memory cache for blog row data (keyed by blog_id/id)
    var blogDataCache = {};

    function initBlogsDataTable() {
        if ($.fn.DataTable.isDataTable('#blogsDataTable')) {
            return;
        }

        $('#blogsDataTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/admin/tables/blogs/list',
                type: 'GET',
                dataSrc: function(json) {
                    // Cache each blog row so we don't need data-blog attributes
                    if (json.data) {
                        json.data.forEach(function(row) {
                            var key = row.blog_id || row.id;
                            if (key) blogDataCache[key] = row;
                        });
                    }
                    return json.data;
                }
            },
            columns: [
                { data: null, render: function(data) { return data.title || data.blog_title || 'N/A'; } },
                { data: 'author', defaultContent: 'N/A' },
                { data: null, render: function(data) { return data.date || data.blog_date || 'N/A'; } },
                { 
                    data: null, 
                    orderable: false,
                    render: function(data) {
                        const blogId = data.blog_id || data.id;
                        return `
                        <div class="action-btns">
                            <button class="btn btn-sm btn-info view-blog" data-blog-id="${blogId}" title="View">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-warning edit-blog" data-blog-id="${blogId}" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger delete-blog" data-id="${blogId}" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>`;
                    } 
                }
            ],
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            order: [[2, 'desc']],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search blogs...",
                processing: '<span class="spinner-border spinner-border-sm"></span> Loading...',
                paginate: {
                    next: 'Read more',
                    previous: 'Previous'
                }
            },
                  dom: "<'dt-toolbar'<'dt-search'f><'dt-length'l>>" +
                 "t" +
                      "<'dt-footer row'<'col-md-5 col-12'i><'col-md-7 col-12'p>>",
            columnDefs: [
                { orderable: false, targets: 3 }
            ],
            responsive: true,
            autoWidth: false
        });
    }

    // Reload videos table
    function reloadVideosTable() {
        $.ajax({
            url: '/admin/tables/videos/list',
            type: 'GET',
                success: function(response) {
                const tbody = $('#videosTable');
                tbody.empty();

                if (response.data && Array.isArray(response.data)) {
                    response.data.forEach(function(video) {
                        let videoPreview = video.url || '';
                        if (videoPreview.includes('<iframe')) {
                            videoPreview = `<div style="max-width: 400px;">${videoPreview}</div>`;
                        } else {
                            videoPreview = `<a href="${videoPreview}" target="_blank">${videoPreview.substring(0, 60)}</a>`;
                        }
                        const btns = `
                            <div class="action-btns">
                                <button class="btn btn-sm btn-warning edit-video" data-video='${JSON.stringify(video).replace(/'/g, "&apos;")}' title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger delete-video" data-id="${video.id}" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>`;
                        const row = `<tr data-id="${video.id}">
                            <td>${videoPreview}</td>
                            <td>${btns}</td>
                        </tr>`;
                        tbody.append(row);
                    });
                }
            },
            error: function(xhr) {
                console.error('Error reloading videos table:', xhr);
            }
        });
    }
    
    // Reload careers table
    function reloadCareersTable() {
        $.ajax({
            url: '/admin/tables/careers/list',
            type: 'GET',
                success: function(response) {
                const tbody = $('#careersTable');
                tbody.empty();

                if (response.data && Array.isArray(response.data)) {
                    response.data.forEach(function(career) {
                        const btns = `
                            <div class="action-btns">
                                <button class="btn btn-sm btn-info view-career" data-career='${JSON.stringify(career).replace(/'/g, "&apos;")}' title="View">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-warning edit-career" data-career='${JSON.stringify(career).replace(/'/g, "&apos;")}' title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger delete-career" data-id="${career.id}" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>`;
                        const row = `<tr data-id="${career.id}">
                            <td>${career.job_title || career.title || ''}</td>
                            <td>${career.job_location || career.location || ''}</td>
                            <td>${career.job_type || career.type || ''}</td>
                            <td>${career.job_deadline || career.deadline || ''}</td>
                            <td>${btns}</td>
                        </tr>`;
                        tbody.append(row);
                    });
                }
            },
            error: function(xhr) {
                console.error('Error reloading careers table:', xhr);
            }
        });
    }
    
    // Reload social impact table
    function reloadSocialTable() {
        $.ajax({
            url: '/admin/tables/social-impact/list',
            type: 'GET',
                success: function(response) {
                const tbody = $('#socialTable');
                tbody.empty();

                if (response.data && Array.isArray(response.data)) {
                    response.data.forEach(function(social) {
                        const btns = `
                            <div class="action-btns">
                                <button class="btn btn-sm btn-info view-social" data-social='${JSON.stringify(social).replace(/'/g, "&apos;")}' title="View">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-warning edit-social" data-social='${JSON.stringify(social).replace(/'/g, "&apos;")}' title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger delete-social" data-id="${social.id}" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>`;
                        const row = `<tr data-id="${social.id}">
                            <td>${social.title || ''}</td>
                            <td>${social.category || social.impact_area || ''}</td>
                            <td>${social.posted_date || social.published_date || ''}</td>
                            <td>${social.author_name || 'Admin'}</td>
                            <td>${btns}</td>
                        </tr>`;
                        tbody.append(row);
                    });
                }
            },
            error: function(xhr) {
                console.error('Error reloading social impact table:', xhr);
            }
        });
    }
    
    // Reload customer stories table
    function reloadStoriesTable() {
        $.ajax({
            url: '/admin/tables/customer-stories/list',
            type: 'GET',
                success: function(response) {
                const tbody = $('#storiesTable');
                tbody.empty();

                if (response.data && Array.isArray(response.data)) {
                    response.data.forEach(function(story) {
                        const btns = `
                            <div class="action-btns">
                                <button class="btn btn-sm btn-info view-story" data-story='${JSON.stringify(story).replace(/'/g, "&apos;")}' title="View">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-warning edit-story" data-story='${JSON.stringify(story).replace(/'/g, "&apos;")}' title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger delete-story" data-id="${story.id}" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>`;
                        const row = `<tr data-id="${story.id}">
                            <td>${story.name || ''}</td>
                            <td>${story.position || ''}</td>
                            <td>${btns}</td>
                        </tr>`;
                        tbody.append(row);
                    });
                }
            },
            error: function(xhr) {
                console.error('Error reloading stories table:', xhr);
            }
        });
    }

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function stripTags(value) {
        return String(value ?? '').replace(/<[^>]*>/g, '');
    }

    function caseStudyPdfUrl(pdfValue, item = {}) {
        const value = String(pdfValue || '').trim();
        if (!value) return '';
        if (/^https?:\/\//i.test(value)) return value;
        const basePath = item.resource_type === 'white_paper' ? `{{ url('white_paper_docs') }}` : `{{ url('case_docs') }}`;
        return `${basePath}/${encodeURIComponent(value)}`;
    }

    function caseStudyImageUrl(imageValue, item = {}) {
        const value = String(imageValue || '').trim();
        if (!value) return '';
        if (/^https?:\/\//i.test(value)) return value;
        const basePath = item.resource_type === 'white_paper' ? `{{ asset('images/white-papers') }}` : `{{ asset('images/case-study') }}`;
        return `${basePath}/${encodeURIComponent(value)}`;
    }

    function reloadCaseStudiesTable() {
        $.ajax({
            url: '/admin/tables/case-studies/list',
            type: 'GET',
            success: function(response) {
                const tbody = $('#caseStudiesTable');
                tbody.empty();

                if (response.data && Array.isArray(response.data) && response.data.length > 0) {
                    response.data.forEach(function(item) {
                        const isWhitePaper = item.resource_type === 'white_paper';
                        const typeBadge = isWhitePaper
                            ? '<span class="badge bg-secondary">White Paper</span>'
                            : '<span class="badge bg-primary">Case Study</span>';
                        const imageHtml = item.listing_image
                            ? `<img src="${caseStudyImageUrl(item.listing_image, item)}" alt="${escapeHtml(item.title || item.category || 'Resource')}" style="width:48px;height:36px;object-fit:cover;border-radius:6px;">`
                            : '<span class="text-muted">N/A</span>';
                        const pdfUrl = caseStudyPdfUrl(item.pdf_url, item);
                        const pdfHtml = pdfUrl
                            ? `<a href="${pdfUrl}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary" title="Preview PDF"><i class="fas fa-file-pdf"></i></a>`
                            : '<span class="text-muted">N/A</span>';
                        const btns = `
                            <div class="action-btns">
                                <button class="btn btn-sm btn-info view-case-study" data-case-study='${JSON.stringify(item).replace(/'/g, "&apos;")}' title="View"><i class="fas fa-eye"></i></button>
                                <button class="btn btn-sm btn-warning edit-case-study" data-case-study='${JSON.stringify(item).replace(/'/g, "&apos;")}' title="Edit"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-sm btn-danger delete-case-study" data-id="${item.id}" data-type="${item.resource_type || 'case_study'}" title="Delete"><i class="fas fa-trash"></i></button>
                            </div>`;
                        const summary = stripTags(item.body || '').slice(0, 90);
                        tbody.append(`<tr data-id="${item.id}">
                            <td>${typeBadge}</td>
                            <td>${isWhitePaper ? 'N/A' : escapeHtml(item.category || 'N/A')}</td>
                            <td>${isWhitePaper ? 'N/A' : escapeHtml(item.outcome_tag || 'N/A')}</td>
                            <td>${escapeHtml(item.title || item.category || 'N/A')}</td>
                            <td>${imageHtml}</td>
                            <td>${pdfHtml}</td>
                            <td>${escapeHtml(summary)}${summary.length >= 90 ? '...' : ''}</td>
                            <td>${btns}</td>
                        </tr>`);
                    });
                } else {
                    tbody.html('<tr><td colspan="7" class="text-center text-muted">No resources yet. Add one!</td></tr>');
                }
            },
            error: function(xhr) {
                console.error('Error reloading case studies table:', xhr);
            }
        });
    }
    
    // Reload events table
    function reloadEventsTable() {
        $.ajax({
            url: '/admin/tables/events/list',
            type: 'GET',
            success: function(response) {
                const tbody = $('#eventsTable');
                tbody.empty();
                
                if (response.data && Array.isArray(response.data)) {
                    response.data.forEach(function(event) {
                        const urlLink = event.url ? `<a href="${event.url}" target="_blank" class="text-primary"><i class="fas fa-external-link-alt"></i> Link</a>` : 'N/A';
                        const row = `<tr data-id="${event.id}">
                            <td>${event.title || ''}</td>
                            <td>${event.start_date || 'N/A'}</td>
                            <td>${urlLink}</td>
                            <td>
                                <button class="btn btn-sm btn-info view-event" data-event='${JSON.stringify(event).replace(/'/g, "&apos;")}' title="View"><i class="fas fa-eye"></i></button>
                                <button class="btn btn-sm btn-warning edit-event" data-event='${JSON.stringify(event).replace(/'/g, "&apos;")}' title="Edit"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-sm btn-danger delete-event" data-id="${event.id}" title="Delete"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>`;
                        tbody.append(row);
                    });
                }
            },
            error: function(xhr) {
                console.error('Error reloading events table:', xhr);
            }
        });
    }
    
    // Reload team table
    function reloadTeamTable() {
        $.ajax({
            url: '/admin/tables/team/list',
            type: 'GET',
            success: function(response) {
                const tbody = $('#teamTable');
                tbody.empty();
                
                if (response.data && Array.isArray(response.data)) {
                    response.data.forEach(function(member) {
                        const imageHtml = member.team_image ? 
                            `<img src="{{ asset('images/team/') }}/${member.team_image}" alt="${member.team_name || ''}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%;">` : 
                            'N/A';
                        const row = `<tr data-id="${member.id}">
                            <td>${member.team_name || 'N/A'}</td>
                            <td>${member.team_title || 'N/A'}</td>
                            <td>${imageHtml}</td>
                            <td>
                                <button class="btn btn-sm btn-info view-team" data-team='${JSON.stringify(member).replace(/'/g, "&apos;")}' title="View"><i class="fas fa-eye"></i></button>
                                <button class="btn btn-sm btn-warning edit-team" data-team='${JSON.stringify(member).replace(/'/g, "&apos;")}' title="Edit"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-sm btn-danger delete-team" data-id="${member.id}" title="Delete"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>`;
                        tbody.append(row);
                    });
                }
            },
            error: function(xhr) {
                console.error('Error reloading team table:', xhr);
            }
        });
    }
    
    // ==================== END TABLE RELOAD FUNCTIONS ====================
    
    function getBlogDataFromButton($button) {
        var blogId = $button.attr('data-blog-id');
        if (blogId && blogDataCache[blogId]) {
            return blogDataCache[blogId];
        }
        return {};
    }

    // Blog View
    $(document).on('click', '.view-blog', function() {
        const blog = getBlogDataFromButton($(this));
        $('#viewBlogTitle').text(blog.title || blog.blog_title || '');
        $('#viewBlogAuthor').text(blog.author || 'Admin');
        $('#viewBlogDate').text(blog.date || blog.blog_date || '');
        $('#viewBlogContent').html(blog.body || blog.content || '');
        $('#viewBlogModal').modal('show');
    });

    // Blog Add - Open Modal
    $('[data-bs-target="#blogModal"]').click(function() {
        $('#blogModalTitle').text('Add New Blog');
        $('#blogForm')[0].reset();
        $('#blogId').val('');

        const defaultAuthor = $('#blogAuthor').data('default-author');
        if (defaultAuthor) {
            $('#blogAuthor').val(defaultAuthor);
        }
        
        // Initialize CKEditor if not already
        if (!blogEditor) {
            blogEditor = CKEDITOR.replace('blogBody');
        } else {
            blogEditor.setData('');
        }
    });

    // Blog Edit
    $(document).on('click', '.edit-blog', function() {
        const blog = getBlogDataFromButton($(this));
        if (!blog || !Object.keys(blog).length) {
            alert('Could not load blog data. Please refresh the page and try again.');
            return;
        }
        
        $('#blogModalTitle').text('Edit Blog');
        
        const blogId = blog.blog_id || blog.id || '';
        
        $('#blogId').val(blogId);
        $('#blogTitle').val(blog.title || blog.blog_title || '');
        const editAuthor = blog.author || '';
        if (editAuthor && $('#blogAuthor option[value="' + editAuthor.replace(/"/g, '\\"') + '"]').length === 0) {
            $('#blogAuthor').append(new Option(editAuthor, editAuthor));
        }
        $('#blogAuthor').val(editAuthor);
        $('#blogDate').val(blog.date || blog.blog_date || '');
        
        // Initialize CKEditor if not already
        if (!blogEditor) {
            blogEditor = CKEDITOR.replace('blogBody');
        }
        
        const content = blog.body || blog.content || blog.description || blog.blog_body || '';
        blogEditor.setData(content);
        
        $('#blogModal').modal('show');
    });

    // Ensure Enter key submits through the same AJAX flow.
    $('#blogForm').on('submit', function(e) {
        e.preventDefault();
        $('#saveBlogBtn').trigger('click');
    });

    // Save Blog (Add/Edit)
    $('#saveBlogBtn').on('click', function(e) {
        e.preventDefault();
        const $saveBtn = $(this);
        if ($saveBtn.prop('disabled')) {
            return;
        }
        setButtonSaving($saveBtn, true, 'Saving blog...');

        const formData = new FormData();
        const id = $('#blogId').val();
        const isEdit = id !== '';
        
        const title = $('#blogTitle').val();
        const author = $('#blogAuthor').val();
        const date = $('#blogDate').val();
        const body = blogEditor ? blogEditor.getData() : '';
        
        formData.append('_token', '{{ csrf_token() }}');
        if (isEdit) {
            formData.append('_method', 'PUT');
            formData.append('id', id);
        }
        formData.append('title', title);
        formData.append('author', author);
        formData.append('date', date);
        formData.append('body', body);
        
        const imageFile = $('#blogImage')[0].files[0];
        if (imageFile) {
            formData.append('image', imageFile);
        }

        $.ajax({
            url: isEdit ? `/admin/tables/blogs/${id}` : '/admin/tables/blogs',
            method: 'POST',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log('Save successful:', response);
                markAjaxSaveSuccess($saveBtn, 'Blog saved successfully.');
                $('#blogModal').modal('hide');
                reloadBlogsTable();
                alert('Blog saved successfully!');
            },
            error: function(xhr) {
                console.error('Save error:', xhr.responseJSON);
                const message = xhr.responseJSON?.message || 'Unknown error';
                markAjaxSaveError($saveBtn, 'Error saving blog: ' + message);
                alert('Error saving blog: ' + message);
            },
            complete: function() {
                setButtonSaving($saveBtn, false);
            }
        });
    });

    // Delete Blog
    $(document).on('click', '.delete-blog', function() {
        if (!confirm('Are you sure you want to delete this blog?')) return;
        
        const id = $(this).data('id');
        const $row = $(this).closest('tr');
        
        $.ajax({
            url: `/admin/tables/blogs/${id}`,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                $row.fadeOut(300, function() {
                    $(this).remove();
                });
            },
            error: function(xhr) {
                alert('Error deleting blog: ' + (xhr.responseJSON?.message || 'Unknown error'));
            }
        });
    });

    // Career View
    $(document).on('click', '.view-career', function() {
        const career = $(this).data('career');
        $('#viewCareerTitle').text(career.job_title || career.title || '');
        $('#viewCareerLocation').text(career.job_location || career.location || '');
        $('#viewCareerType').text(career.job_type || career.type || '');
        $('#viewCareerContent').html(career.job_description || career.description || '');
        $('#viewCareerModal').modal('show');
    });

    // Delete Career
    $(document).on('click', '.delete-career', function() {
        if (!confirm('Are you sure you want to delete this career?')) return;
        
        const id = $(this).data('id');
        const $row = $(this).closest('tr');
        
        $.ajax({
            url: `/admin/tables/careers/${id}`,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                $row.fadeOut(300, function() {
                    $(this).remove();
                });
            },
            error: function(xhr) {
                alert('Error deleting career: ' + (xhr.responseJSON?.message || 'Unknown error'));
            }
        });
    });

    // Social Impact View
    $(document).on('click', '.view-social', function() {
        const social = $(this).data('social');
        $('#viewSocialTitle').text(social.title || '');
        $('#viewSocialCategory').text(social.category || social.impact_area || '');
        $('#viewSocialDate').text(social.posted_date || social.published_date || '');
        $('#viewSocialContent').html(social.body || social.content || '');
        $('#viewSocialModal').modal('show');
    });

    // Delete Social Impact
    $(document).on('click', '.delete-social', function() {
        if (!confirm('Are you sure you want to delete this social impact story?')) return;
        
        const id = $(this).data('id');
        const $row = $(this).closest('tr');
        
        $.ajax({
            url: `/admin/tables/social-impact/${id}`,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                $row.fadeOut(300, function() {
                    $(this).remove();
                });
            },
            error: function(xhr) {
                alert('Error deleting social impact story: ' + (xhr.responseJSON?.message || 'Unknown error'));
            }
        });
    });

    // Customer Story View
    $(document).on('click', '.view-story', function() {
        const story = $(this).data('story');
        $('#viewStoryName').text(story.name || '');
        $('#viewStoryPosition').text(story.position || '');
        $('#viewStoryContent').html(story.body_content || story.content || '');
        $('#viewStoryModal').modal('show');
    });

    // Delete Customer Story
    $(document).on('click', '.delete-story', function() {
        if (!confirm('Are you sure you want to delete this customer story?')) return;
        
        const id = $(this).data('id');
        const $row = $(this).closest('tr');
        
        $.ajax({
            url: `/admin/tables/customer-stories/${id}`,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                $row.fadeOut(300, function() {
                    $(this).remove();
                });
            },
            error: function(xhr) {
                alert('Error deleting customer story: ' + (xhr.responseJSON?.message || 'Unknown error'));
            }
        });
    });

    // Delete Video
    $(document).on('click', '.delete-video', function() {
        if (!confirm('Are you sure you want to delete this video?')) return;
        
        const id = $(this).data('id');
        const $row = $(this).closest('tr');
        
        $.ajax({
            url: `/admin/tables/videos/${id}`,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                $row.fadeOut(300, function() {
                    $(this).remove();
                });
            },
            error: function(xhr) {
                alert('Error deleting video: ' + (xhr.responseJSON?.message || 'Unknown error'));
            }
        });
    });

    // === VIDEO HANDLERS ===
    // Reset video form
    window.resetVideoForm = function() {
        $('#videoModalTitle').text('Add New Video');
        $('#videoForm')[0].reset();
        $('#videoId').val('');
    };

    // Edit Video
    $(document).on('click', '.edit-video', function() {
        const video = $(this).data('video');
        $('#videoModalTitle').text('Edit Video');
        $('#videoId').val(video.id || video.video_id);
        $('#videoUrl').val(video.url || video.video_url || video.iframe || video.embed || '');
        $('#videoModal').modal('show');
    });

    // Save Video
    $('#saveVideoBtn').click(function() {
        const $saveBtn = $(this);
        if ($saveBtn.prop('disabled')) {
            return;
        }
        setButtonSaving($saveBtn, true, 'Saving video...');

        const id = $('#videoId').val();
        const isEdit = id !== '';
        const url = $('#videoUrl').val();
        const data = {
            _token: '{{ csrf_token() }}',
            url: url
        };
        
        if (isEdit) {
            data._method = 'PUT';
            data.id = id;
        }

        $.ajax({
            url: isEdit ? `/admin/tables/videos/${id}` : '/admin/tables/videos',
            type: 'POST',
            data: data,
            success: function(response) {
                markAjaxSaveSuccess($saveBtn, 'Video saved successfully.');
                $('#videoModal').modal('hide');
                resetVideoForm();
                reloadVideosTable();
                alert('Video saved successfully!');
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Unknown error';
                markAjaxSaveError($saveBtn, 'Error saving video: ' + message);
                alert('Error saving video: ' + message);
            },
            complete: function() {
                setButtonSaving($saveBtn, false);
            }
        });
    });

    // === CAREER HANDLERS ===
    let careerEditor;

    // Reset career form
    window.resetCareerForm = function() {
        $('#careerModalTitle').text('Add New Career');
        $('#careerForm')[0].reset();
        $('#careerId').val('');
        if (careerEditor) {
            careerEditor.setData('');
        }
    };

    // Edit Career
    $(document).on('click', '.edit-career', function() {
        const career = $(this).data('career');
        $('#careerModalTitle').text('Edit Career');
        $('#careerId').val(career.id);
        $('#careerTitle').val(career.job_title || career.title || '');
        $('#careerLocation').val(career.job_location || career.location || '');
        $('#careerType').val(career.job_type || career.type || '');
        $('#careerDeadline').val(career.job_deadline || career.deadline || '');
        
        // Initialize CKEditor if not already
        if (!careerEditor) {
            careerEditor = CKEDITOR.replace('careerBody');
        }
        careerEditor.setData(career.job_description || career.description || '');
        
        $('#careerModal').modal('show');
    });

    // Open Career Modal (for Add)
    $('[data-bs-target="#careerModal"]').click(function() {
        if (!careerEditor) {
            careerEditor = CKEDITOR.replace('careerBody');
        }
    });

    // Save Career
    $('#saveCareerBtn').click(function() {
        const $saveBtn = $(this);
        if ($saveBtn.prop('disabled')) {
            return;
        }
        setButtonSaving($saveBtn, true, 'Saving career...');

        const id = $('#careerId').val();
        const isEdit = id !== '';
        const data = {
            _token: '{{ csrf_token() }}',
            job_title: $('#careerTitle').val(),
            job_location: $('#careerLocation').val(),
            job_type: $('#careerType').val(),
            job_deadline: $('#careerDeadline').val(),
            job_description: careerEditor ? careerEditor.getData() : ''
        };
        
        if (isEdit) {
            data._method = 'PUT';
            data.id = id;
        }

        $.ajax({
            url: isEdit ? `/admin/tables/careers/${id}` : '/admin/tables/careers',
            type: 'POST',
            data: data,
            success: function(response) {
                markAjaxSaveSuccess($saveBtn, 'Career saved successfully.');
                $('#careerModal').modal('hide');
                reloadCareersTable();
                alert('Career saved successfully!');
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Unknown error';
                markAjaxSaveError($saveBtn, 'Error saving career: ' + message);
                alert('Error saving career: ' + message);
            },
            complete: function() {
                setButtonSaving($saveBtn, false);
            }
        });
    });

    // === SOCIAL IMPACT HANDLERS ===
    let socialEditor;

    // Reset social form
    window.resetSocialForm = function() {
        $('#socialModalTitle').text('Add New Social Impact Story');
        $('#socialForm')[0].reset();
        $('#socialId').val('');
        if (socialEditor) {
            socialEditor.setData('');
        }
    };

    // Edit Social Impact
    $(document).on('click', '.edit-social', function() {
        const social = $(this).data('social');
        $('#socialModalTitle').text('Edit Social Impact Story');
        $('#socialId').val(social.id);
        $('#socialTitle').val(social.title || '');
        $('#socialCategory').val(social.category || social.impact_area || '');
        $('#socialDate').val(social.posted_date || social.published_date || '');
        $('#socialAuthor').val(social.author_name || '');
        
        // Initialize CKEditor if not already
        if (!socialEditor) {
            socialEditor = CKEDITOR.replace('socialBody');
        }
        socialEditor.setData(social.body || social.content || '');
        
        $('#socialModal').modal('show');
    });

    // Open Social Modal (for Add)
    $('[data-bs-target="#socialModal"]').click(function() {
        if (!socialEditor) {
            socialEditor = CKEDITOR.replace('socialBody');
        }
    });

    // Save Social Impact
    $('#saveSocialBtn').click(function() {
        const $saveBtn = $(this);
        if ($saveBtn.prop('disabled')) {
            return;
        }
        setButtonSaving($saveBtn, true, 'Saving story...');

        const formData = new FormData();
        const id = $('#socialId').val();
        const isEdit = id !== '';
        
        formData.append('_token', '{{ csrf_token() }}');
        if (isEdit) {
            formData.append('_method', 'PUT');
            formData.append('id', id);
        }
        formData.append('title', $('#socialTitle').val());
        formData.append('category', $('#socialCategory').val());
        formData.append('posted_date', $('#socialDate').val());
        formData.append('author_name', $('#socialAuthor').val());
        formData.append('body', socialEditor ? socialEditor.getData() : '');
        
        const imageFile = $('#socialImage')[0].files[0];
        if (imageFile) {
            formData.append('image', imageFile);
        }

        $.ajax({
            url: isEdit ? `/admin/tables/social-impact/${id}` : '/admin/tables/social-impact',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                markAjaxSaveSuccess($saveBtn, 'Social impact story saved successfully.');
                $('#socialModal').modal('hide');
                reloadSocialTable();
                alert('Social impact story saved successfully!');
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Unknown error';
                markAjaxSaveError($saveBtn, 'Error saving social impact story: ' + message);
                alert('Error saving social impact story: ' + message);
            },
            complete: function() {
                setButtonSaving($saveBtn, false);
            }
        });
    });

    // === CUSTOMER STORY HANDLERS ===
    let storyEditor;

    // Reset story form
    window.resetStoryForm = function() {
        $('#storyModalTitle').text('Add New Customer Story');
        $('#storyForm')[0].reset();
        $('#storyId').val('');
        if (storyEditor) {
            storyEditor.setData('');
        }
    };

    // Edit Customer Story
    $(document).on('click', '.edit-story', function() {
        const story = $(this).data('story');
        $('#storyModalTitle').text('Edit Customer Story');
        $('#storyId').val(story.id);
        $('#storyName').val(story.name || '');
        $('#storyPosition').val(story.position || '');
        
        // Initialize CKEditor if not already
        if (!storyEditor) {
            storyEditor = CKEDITOR.replace('storyBody');
        }
        storyEditor.setData(story.body_content || story.content || '');
        
        $('#storyModal').modal('show');
    });

    // Open Story Modal (for Add)
    $('[data-bs-target="#storyModal"]').click(function() {
        if (!storyEditor) {
            storyEditor = CKEDITOR.replace('storyBody');
        }
    });

    // Save Customer Story
    $('#saveStoryBtn').click(function() {
        const $saveBtn = $(this);
        if ($saveBtn.prop('disabled')) {
            return;
        }
        setButtonSaving($saveBtn, true, 'Saving customer story...');

        const formData = new FormData();
        const id = $('#storyId').val();
        const isEdit = id !== '';
        
        formData.append('_token', '{{ csrf_token() }}');
        if (isEdit) {
            formData.append('_method', 'PUT');
            formData.append('id', id);
        }
        formData.append('name', $('#storyName').val());
        formData.append('position', $('#storyPosition').val());
        formData.append('body_content', storyEditor ? storyEditor.getData() : '');
        
        const imageFile = $('#storyImage')[0].files[0];
        if (imageFile) {
            formData.append('profile', imageFile);
        }

        $.ajax({
            url: isEdit ? `/admin/tables/customer-stories/${id}` : '/admin/tables/customer-stories',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                markAjaxSaveSuccess($saveBtn, 'Customer story saved successfully.');
                $('#storyModal').modal('hide');
                reloadStoriesTable();
                alert('Customer story saved successfully!');
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Unknown error';
                markAjaxSaveError($saveBtn, 'Error saving customer story: ' + message);
                alert('Error saving customer story: ' + message);
            },
            complete: function() {
                setButtonSaving($saveBtn, false);
            }
        });
    });

    // === CASE STUDY HANDLERS ===
    let caseStudyEditor;

    function getSelectedResourceType() {
        return $('input[name="resource_type"]:checked').val() || 'case_study';
    }

    function applyCaseStudyResourceTypeUI() {
        const resourceType = getSelectedResourceType();
        const isWhitePaper = resourceType === 'white_paper';
        const isEditMode = $('#caseStudyMode').val() === 'edit';

        $('#caseStudyCategoryGroup').toggleClass('d-none', isWhitePaper);
        $('#caseStudyOutcomeTagGroup').toggleClass('d-none', isWhitePaper);
        $('#caseStudyExistingImageGroup').toggleClass('d-none', !isWhitePaper);
        $('#caseStudyCategory').prop('required', !isWhitePaper);
        $('#caseStudyTitleLabel').text(isWhitePaper ? 'White Paper Title *' : 'Case Study Title *');
        $('#caseStudyImageLabel').text(isWhitePaper ? 'White Paper Image' : 'Listing Image');
        $('#caseStudyImageHelp').text(isWhitePaper ? 'Saved to public/images/white-papers.' : 'Saved to public/images/case-study.');
        $('#caseStudyExistingImageLabel').text(isWhitePaper ? 'Existing White Paper Image Filename or URL' : 'Existing White Paper Image Filename or URL');
        $('#caseStudyExistingImageHelp').text(isWhitePaper ? 'Upload a new image below to replace this value.' : '');
        $('#caseStudyPdfPathHelp').text(isWhitePaper ? 'Saved to public/white_paper_docs.' : 'Saved to public/case_docs.');

        if (!isEditMode) {
            $('#caseStudyModalTitle').text(isWhitePaper ? 'Add New White Paper' : 'Add New Case Study');
            $('#saveCaseStudyBtn .btn-label').html(isWhitePaper
                ? '<i class="fas fa-save me-2"></i>Save White Paper'
                : '<i class="fas fa-save me-2"></i>Save Case Study');
        } else {
            $('#saveCaseStudyBtn .btn-label').html(isWhitePaper
                ? '<i class="fas fa-save me-2"></i>Update White Paper'
                : '<i class="fas fa-save me-2"></i>Update Case Study');
        }
    }

    window.resetCaseStudyForm = function() {
        $('#caseStudyMode').val('create');
        $('#caseStudySourceType').val('case_study');
        $('#caseStudyModalTitle').text('Add New Case Study');
        $('#caseStudyForm')[0].reset();
        $('#caseStudyId').val('');
        $('#caseStudyCategory').val('');
        $('#caseStudyOutcomeTag').val('');
        $('#resourceTypeCaseStudy').prop('checked', true);
        applyCaseStudyResourceTypeUI();
        if (caseStudyEditor) {
            caseStudyEditor.setData('');
        }
    };

    $('input[name="resource_type"]').on('change', applyCaseStudyResourceTypeUI);

    $('[data-bs-target="#caseStudyModal"]').click(function() {
        if (!caseStudyEditor) {
            caseStudyEditor = CKEDITOR.replace('caseStudyBody');
        }
    });

    $(document).on('click', '.view-case-study', function() {
        const item = $(this).data('case-study');
        const isWhitePaper = item.resource_type === 'white_paper';
        const typeLabel = isWhitePaper ? 'White Paper' : 'Case Study';
        const headerText = item.title || item.category || typeLabel;
        $('#viewCaseStudyCategory').text(`${typeLabel}: ${headerText}`);
        $('#viewCaseStudyBody').html(item.body || '');

        const imageUrl = caseStudyImageUrl(item.listing_image, item);
        if (imageUrl) {
            $('#viewCaseStudyImage').attr('src', imageUrl).attr('alt', item.title || item.category || typeLabel);
            $('#viewCaseStudyImageWrap').removeClass('d-none');
        } else {
            $('#viewCaseStudyImageWrap').addClass('d-none');
        }

        const pdfUrl = caseStudyPdfUrl(item.pdf_url, item);
        if (pdfUrl) {
            $('#viewCaseStudyPdf').attr('href', pdfUrl).removeClass('d-none');
        } else {
            $('#viewCaseStudyPdf').addClass('d-none');
        }

        $('#viewCaseStudyModal').modal('show');
    });

    $(document).on('click', '.edit-case-study', function() {
        const item = $(this).data('case-study');
        const resourceType = item.resource_type || 'case_study';
        const isWhitePaper = resourceType === 'white_paper';
        $('#caseStudyMode').val('edit');
        $('#caseStudySourceType').val(resourceType);
        $('#caseStudyModalTitle').text(isWhitePaper ? 'Edit White Paper' : 'Edit Case Study');
        $('#caseStudyId').val(item.id || '');

        if (!isWhitePaper && item.category) {
            const categoryValue = String(item.category);
            if ($('#caseStudyCategory option').filter(function() { return $(this).val() === categoryValue; }).length === 0) {
                $('#caseStudyCategory').append(new Option(categoryValue, categoryValue));
            }
        }

        $('#caseStudyCategory').val(item.category || '');
        $('#caseStudyOutcomeTag').val(item.outcome_tag || item.outcome_tags || '');
        $('#caseStudyTitle').val(item.title || '');
        $('#caseStudyExistingImage').val(item.listing_image || item.images || item.image || '');
        $('#caseStudyPdfUrl').val(item.pdf_url || '');
        if (isWhitePaper) {
            $('#resourceTypeWhitePaper').prop('checked', true);
        } else {
            $('#resourceTypeCaseStudy').prop('checked', true);
        }
        applyCaseStudyResourceTypeUI();

        if (!caseStudyEditor) {
            caseStudyEditor = CKEDITOR.replace('caseStudyBody');
        }
        caseStudyEditor.setData(item.body || '');

        $('#caseStudyModal').modal('show');
    });

    $('#saveCaseStudyBtn').click(function() {
        const $saveBtn = $('#saveCaseStudyBtn');
        if ($saveBtn.prop('disabled')) {
            return;
        }

        const setCaseStudySavingState = function(isSaving) {
            $saveBtn.prop('disabled', isSaving);
            $saveBtn.find('.btn-label').toggleClass('d-none', isSaving);
            $saveBtn.find('.spinner-border').toggleClass('d-none', !isSaving);
        };

        setCaseStudySavingState(true);
        setModalStatus($saveBtn, 'muted', '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Saving resource...');

        const id = $('#caseStudyId').val();
        const resourceType = getSelectedResourceType();
        const isWhitePaper = resourceType === 'white_paper';
        const isEdit = id !== '';
        const formData = new FormData();

        formData.append('_token', '{{ csrf_token() }}');
        if (isEdit) {
            formData.append('_method', 'PUT');
            formData.append('id', id);
        }
        formData.append('title', $('#caseStudyTitle').val());
        formData.append('resource_type', resourceType);
        formData.append('source_resource_type', $('#caseStudySourceType').val() || resourceType);
        formData.append('pdf_url', $('#caseStudyPdfUrl').val());
        formData.append('body', caseStudyEditor ? caseStudyEditor.getData() : $('#caseStudyBody').val());

        if (!isWhitePaper) {
            formData.append('category', $('#caseStudyCategory').val());
            formData.append('outcome_tag', $('#caseStudyOutcomeTag').val());
        }

        const imageFile = $('#caseStudyImage')[0].files[0];
        if (imageFile) {
            formData.append(isWhitePaper ? 'white_paper_image' : 'listing_image', imageFile);
        } else if (isWhitePaper && $('#caseStudyExistingImage').val().trim() !== '') {
            formData.append('existing_image', $('#caseStudyExistingImage').val().trim());
        }

        const pdfFile = $('#caseStudyPdf')[0].files[0];
        if (pdfFile) {
            formData.append('pdf', pdfFile);
        }

        const endpointBase = isWhitePaper ? '/admin/tables/white-papers' : '/admin/tables/case-studies';
        const resourceLabel = isWhitePaper ? 'white paper' : 'case study';

        $.ajax({
            url: isEdit ? `${endpointBase}/${id}` : endpointBase,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                markAjaxSaveSuccess($saveBtn, response.message || `${resourceLabel.charAt(0).toUpperCase()}${resourceLabel.slice(1)} saved successfully!`);
                $('#caseStudyModal').modal('hide');
                reloadCaseStudiesTable();
                alert(response.message || `${resourceLabel.charAt(0).toUpperCase()}${resourceLabel.slice(1)} saved successfully!`);
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Unknown error';
                markAjaxSaveError($saveBtn, `Error saving ${resourceLabel}: ` + message);
                alert(`Error saving ${resourceLabel}: ` + message);
            },
            complete: function() {
                setCaseStudySavingState(false);
            }
        });
    });

    $(document).on('click', '.delete-case-study', function() {
        const id = $(this).data('id');
        const resourceType = $(this).data('type') || 'case_study';
        const isWhitePaper = resourceType === 'white_paper';
        const endpointBase = isWhitePaper ? '/admin/tables/white-papers' : '/admin/tables/case-studies';
        const resourceLabel = isWhitePaper ? 'white paper' : 'case study';

        if (!confirm(`Are you sure you want to delete this ${resourceLabel}?`)) {
            return;
        }

        $.ajax({
            url: `${endpointBase}/${id}`,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function() {
                reloadCaseStudiesTable();
                alert(`${resourceLabel.charAt(0).toUpperCase()}${resourceLabel.slice(1)} deleted successfully!`);
            },
            error: function(xhr) {
                alert(`Error deleting ${resourceLabel}: ` + (xhr.responseJSON?.message || 'Unknown error'));
            }
        });
    });
    
    // ==================== EVENT HANDLERS ====================
    
    // Event View
    $(document).on('click', '.view-event', function() {
        const event = $(this).data('event');
        
        $('#viewEventTitle').text(event.title || 'Untitled Event');
        $('#viewEventDate').text(event.start_date || 'N/A');
        $('#viewEventUrl').attr('href', event.url || '#').text(event.url || 'N/A');
        
        if (event.recorded_url) {
            $('#viewEventRecordedUrlContainer').show();
            $('#viewEventRecordedUrl').attr('href', event.recorded_url).text(event.recorded_url);
        } else {
            $('#viewEventRecordedUrlContainer').hide();
        }
        
        $('#viewEventContent').html(event.body || '');
        $('#viewEventModal').modal('show');
    });
    
    // Event Edit
    $(document).on('click', '.edit-event', function() {
        const event = $(this).data('event');
        
        $('#eventModalTitle').text('Edit Event');
        $('#eventId').val(event.id);
        $('#eventTitle').val(event.title || '');
        $('#eventDate').val(event.start_date || '');
        $('#eventUrl').val(event.url || '');
        $('#eventRecordedUrl').val(event.recorded_url || '');
        
        if (eventEditor) {
            eventEditor.setData(event.body || '');
        } else {
            $('#eventBody').val(event.body || '');
        }
        
        $('#eventModal').modal('show');
    });
    
    // Event Reset Form
    window.resetEventForm = function() {
        $('#eventModalTitle').text('Add New Event');
        $('#eventForm')[0].reset();
        $('#eventId').val('');
        
        if (eventEditor) {
            eventEditor.setData('');
        }
    };
    
    // Event Delete
    $(document).on('click', '.delete-event', function() {
        const id = $(this).data('id');
        
        if (!confirm('Are you sure you want to delete this event?')) {
            return;
        }
        
        $.ajax({
            url: `/admin/tables/events/${id}`,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                reloadEventsTable();
                alert('Event deleted successfully!');
            },
            error: function(xhr) {
                alert('Error deleting event: ' + (xhr.responseJSON?.message || 'Unknown error'));
            }
        });
    });
    
    // Initialize Event Editor
    let eventEditor;
    $('#eventModal').on('shown.bs.modal', function() {
        if (!eventEditor) {
            CKEDITOR.replace('eventBody');
            eventEditor = CKEDITOR.instances.eventBody;
        }
    });
    
    // Event Save
    $('#saveEventBtn').click(function() {
        const $saveBtn = $(this);
        if ($saveBtn.prop('disabled')) {
            return;
        }
        setButtonSaving($saveBtn, true, 'Saving event...');

        const id = $('#eventId').val();
        const isEdit = id !== '';
        
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        if (isEdit) {
            formData.append('id', id);
        }
        formData.append('title', $('#eventTitle').val());
        formData.append('start_date', $('#eventDate').val());
        formData.append('url', $('#eventUrl').val());
        formData.append('recorded_url', $('#eventRecordedUrl').val());
        formData.append('body', eventEditor ? eventEditor.getData() : $('#eventBody').val());
        
        $.ajax({
            url: '/admin/tables/events',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                markAjaxSaveSuccess($saveBtn, 'Event saved successfully.');
                $('#eventModal').modal('hide');
                reloadEventsTable();
                alert('Event saved successfully!');
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Unknown error';
                markAjaxSaveError($saveBtn, 'Error saving event: ' + message);
                alert('Error saving event: ' + message);
            },
            complete: function() {
                setButtonSaving($saveBtn, false);
            }
        });
    });
    
    // ==================== TEAM HANDLERS ====================
    
    // Team View
    $(document).on('click', '.view-team', function() {
        const member = $(this).data('team');
        
        $('#viewTeamName').text(member.team_name || 'Unknown');
        $('#viewTeamTitle').text(member.team_title || '');
        $('#viewTeamBio').html(member.team_body || '');
        
        if (member.team_image) {
            $('#viewTeamImage').attr('src', '{{ asset("images/team/") }}/' + member.team_image);
        } else {
            $('#viewTeamImage').attr('src', '{{ asset("images/default-avatar.png") }}');
        }
        
        // Build social links
        let socialsHtml = '<div class="btn-group" role="group">';
        if (member.linkedin) {
            socialsHtml += `<a href="${member.linkedin}" target="_blank" class="btn btn-outline-primary btn-sm"><i class="fab fa-linkedin"></i> LinkedIn</a>`;
        }
        if (member.facebook) {
            socialsHtml += `<a href="${member.facebook}" target="_blank" class="btn btn-outline-primary btn-sm"><i class="fab fa-facebook"></i> Facebook</a>`;
        }
        if (member.instagram) {
            socialsHtml += `<a href="${member.instagram}" target="_blank" class="btn btn-outline-primary btn-sm"><i class="fab fa-instagram"></i> Instagram</a>`;
        }
        if (member.x) {
            socialsHtml += `<a href="${member.x}" target="_blank" class="btn btn-outline-primary btn-sm"><i class="fab fa-x-twitter"></i> X</a>`;
        }
        socialsHtml += '</div>';
        $('#viewTeamSocials').html(socialsHtml);
        
        $('#viewTeamModal').modal('show');
    });
    
    // Team Edit
    $(document).on('click', '.edit-team', function() {
        const member = $(this).data('team');
        
        $('#teamModalTitle').text('Edit Team Member');
        $('#teamId').val(member.id);
        $('#teamName').val(member.team_name || '');
        $('#teamTitle').val(member.team_title || '');
        $('#teamBody').val(member.team_body || '');
        $('#teamLinkedin').val(member.linkedin || '');
        $('#teamFacebook').val(member.facebook || '');
        $('#teamInstagram').val(member.instagram || '');
        $('#teamX').val(member.x || '');
        
        $('#teamModal').modal('show');
    });
    
    // Team Reset Form
    window.resetTeamForm = function() {
        $('#teamModalTitle').text('Add New Team Member');
        $('#teamForm')[0].reset();
        $('#teamId').val('');
    };
    
    // Team Delete
    $(document).on('click', '.delete-team', function() {
        const id = $(this).data('id');
        
        if (!confirm('Are you sure you want to delete this team member?')) {
            return;
        }
        
        $.ajax({
            url: `/admin/tables/team/${id}`,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                reloadTeamTable();
                alert('Team member deleted successfully!');
            },
            error: function(xhr) {
                alert('Error deleting team member: ' + (xhr.responseJSON?.message || 'Unknown error'));
            }
        });
    });

    // ==================== NEWSLETTER HANDLERS ====================

    $(document).on('click', '.unsubscribe-newsletter', function() {
        const id = $(this).data('id');

        if (!confirm('Unsubscribe this user from newsletter emails?')) {
            return;
        }

        $.ajax({
            url: `/admin/tables/newsletter/${id}/unsubscribe`,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function() {
                location.reload();
            },
            error: function(xhr) {
                alert('Error unsubscribing user: ' + (xhr.responseJSON?.message || 'Unknown error'));
            }
        });
    });

    $(document).on('click', '.resubscribe-newsletter', function() {
        const id = $(this).data('id');

        $.ajax({
            url: `/admin/tables/newsletter/${id}/resubscribe`,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function() {
                location.reload();
            },
            error: function(xhr) {
                alert('Error resubscribing user: ' + (xhr.responseJSON?.message || 'Unknown error'));
            }
        });
    });

    $(document).on('click', '.delete-newsletter', function() {
        const id = $(this).data('id');

        if (!confirm('Delete this newsletter subscriber? This cannot be undone.')) {
            return;
        }

        $.ajax({
            url: `/admin/tables/newsletter/${id}`,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function() {
                location.reload();
            },
            error: function(xhr) {
                alert('Error deleting subscriber: ' + (xhr.responseJSON?.message || 'Unknown error'));
            }
        });
    });
    
    // Team Save
    $('#saveTeamBtn').click(function() {
        const $saveBtn = $(this);
        if ($saveBtn.prop('disabled')) {
            return;
        }
        setButtonSaving($saveBtn, true, 'Saving team member...');

        const id = $('#teamId').val();
        const isEdit = id !== '';
        
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        if (isEdit) {
            formData.append('id', id);
        }
        formData.append('team_name', $('#teamName').val());
        formData.append('team_title', $('#teamTitle').val());
        formData.append('team_body', $('#teamBody').val());
        formData.append('linkedin', $('#teamLinkedin').val());
        formData.append('facebook', $('#teamFacebook').val());
        formData.append('instagram', $('#teamInstagram').val());
        formData.append('x', $('#teamX').val());
        
        const imageFile = $('#teamImage')[0].files[0];
        if (imageFile) {
            formData.append('team_image', imageFile);
        }
        
        $.ajax({
            url: '/admin/tables/team',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                markAjaxSaveSuccess($saveBtn, 'Team member saved successfully.');
                $('#teamModal').modal('hide');
                reloadTeamTable();
                alert('Team member saved successfully!');
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Unknown error';
                markAjaxSaveError($saveBtn, 'Error saving team member: ' + message);
                alert('Error saving team member: ' + message);
            },
            complete: function() {
                setButtonSaving($saveBtn, false);
            }
        });
    });

    // Job Applications Handlers
    const cvDownloadBase = "{{ url('/admin/career/cv') }}";
    const cvStorageBase = "{{ asset('storage') }}";
    const cvDeleteBase = "{{ url('/admin/career/cv') }}";
    function buildCvPreview(app) {
        const cvValue = app.cv_path || app.cv || app.resume || '';

        if (!cvValue) {
            return '<span class="text-muted">N/A</span>';
        }

        const downloadUrl = `${cvDownloadBase}/${app.id}`;

        if (/^https?:\/\//i.test(cvValue)) {
            return `<div class="cv-btns">` +
                   `<a href="${cvValue}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary" title="View CV"><i class="fas fa-file-lines"></i></a>` +
                   `<a href="${downloadUrl}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary" title="Download CV"><i class="fas fa-download"></i></a>` +
                   `</div>`;
        }

        // Normalize stored value to a direct public URL
        let cleaned = cvValue.replace(/^storage\//i, '').replace(/^public\//i, '');
        if (!cleaned.startsWith('cv_uploads/')) {
            cleaned = `cv_uploads/${cleaned}`;
        }

        const storageUrl = `${cvStorageBase}/${cleaned}`;

         return `<div class="cv-btns">` +
             `<a href="${storageUrl}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary" title="View CV"><i class="fas fa-file-lines"></i></a>` +
             `<a href="${downloadUrl}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary" title="Download CV"><i class="fas fa-download"></i></a>` +
             `</div>`;
    }

    function loadApplications() {
        $.ajax({
            url: '/admin/career/list-applications',
            type: 'GET',
            success: function(response) {
                let html = '';
                response.forEach(function(app, index) {
                    const cvPreview = buildCvPreview(app);
                    const appliedDate = app.application_date ? new Date(app.application_date).toLocaleDateString() : 'N/A';

                    html += `<tr data-id="${app.id}" data-status="${app.status}">
                        <td>${index + 1}</td>
                        <td>${app.name}</td>
                        <td>${app.email}</td>
                        <td>${app.position}</td>
                        <td>${app.city || ''}</td>
                        <td>${appliedDate}</td>
                        <td>${cvPreview}</td>
                        <td>
                            <div class="action-btns">
                                <button class="btn btn-sm btn-success shortlist-btn" data-id="${app.id}" title="Shortlist">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button class="btn btn-sm btn-danger reject-btn" data-id="${app.id}" title="Reject">
                                    <i class="fas fa-times"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger delete-cv-btn" data-id="${app.id}" title="Delete Application">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>`;
                });
                $('#applicationsTable').html(html || '<tr><td colspan="8" class="text-center text-muted">No pending applications</td></tr>');
                populateLocationFilter(response);
            }
        });
    }

    function loadShortlisted() {
        $.ajax({
            url: '/admin/career/list-shortlisted',
            type: 'GET',
            success: function(response) {
                let html = '';
                response.forEach(function(app, index) {
                    const cvPreview = buildCvPreview(app);
                    const appliedDate = app.application_date ? new Date(app.application_date).toLocaleDateString() : 'N/A';

                    html += `<tr data-id="${app.id}">
                        <td>${index + 1}</td>
                        <td>${app.name}</td>
                        <td>${app.email}</td>
                        <td>${app.position}</td>
                        <td>${appliedDate}</td>
                        <td>${cvPreview}</td>
                        <td>
                            <div class="action-btns">
                                <button class="btn btn-sm btn-primary hire-btn" data-id="${app.id}" title="Hire">
                                    <i class="fas fa-user-check"></i>
                                </button>
                                <button class="btn btn-sm btn-danger reject-btn" data-id="${app.id}" title="Reject">
                                    <i class="fas fa-times"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger delete-cv-btn" data-id="${app.id}" title="Delete Application">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>`;
                });
                $('#shortlistedTable').html(html || '<tr><td colspan="7" class="text-center text-muted">No shortlisted candidates</td></tr>');
            }
        });
    }

    function loadHired() {
        $.ajax({
            url: '/admin/career/list-employees',
            type: 'GET',
            success: function(response) {
                let html = '';
                response.forEach(function(app, index) {
                    const cvPreview = buildCvPreview(app);
                    const appliedDate = app.application_date ? new Date(app.application_date).toLocaleDateString() : 'N/A';

                    html += `<tr data-id="${app.id}">
                        <td>${index + 1}</td>
                        <td>${app.name}</td>
                        <td>${app.email}</td>
                        <td>${app.position}</td>
                        <td>${appliedDate}</td>
                        <td>${cvPreview}</td>
                        <td>
                            <div class="action-btns">
                                <button class="btn btn-sm btn-outline-danger delete-cv-btn" data-id="${app.id}" title="Delete Application">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <button class="btn btn-sm btn-danger delete-hire-btn" data-id="${app.id}" title="Remove From Hired">
                                    <i class="fas fa-user-minus"></i>
                                </button>
                            </div>
                        </td>
                    </tr>`;
                });
                $('#hiredTable').html(html || '<tr><td colspan="7" class="text-center text-muted">No hired employees</td></tr>');
            }
        });
    }

    function populateLocationFilter(applications) {
        let locations = [...new Set(applications.map(a => a.city).filter(c => c))];
        let html = '<option value="">All Locations</option>';
        locations.forEach(loc => {
            html += `<option value="${loc}">${loc}</option>`;
        });
        $('#filterLocation').html(html);
    }

    // Load applications when tab is shown
    $(document).on('show.bs.tab', '#jobs-tab', function() {
        loadApplications();
        loadShortlisted();
        loadHired();
        loadRejected();
    });

    // Also load rejected in real time when switching directly to the rejected tab
    $(document).on('shown.bs.tab', 'button[data-bs-target="#rejected"]', function() {
        loadRejected();
    });

    // Search Functionality
    $(document).on('keyup', '#searchApplications', function() {
        let searchTerm = $(this).val().toLowerCase();
        $('#applicationsTable tr').each(function() {
            let row = $(this);
            let text = row.text().toLowerCase();
            row.toggle(text.includes(searchTerm));
        });
    });

    $(document).on('keyup', '#searchShortlisted', function() {
        let searchTerm = $(this).val().toLowerCase();
        $('#shortlistedTable tr').each(function() {
            let row = $(this);
            let text = row.text().toLowerCase();
            row.toggle(text.includes(searchTerm));
        });
    });

    $(document).on('keyup', '#searchHired', function() {
        let searchTerm = $(this).val().toLowerCase();
        $('#hiredTable tr').each(function() {
            let row = $(this);
            let text = row.text().toLowerCase();
            row.toggle(text.includes(searchTerm));
        });
    });

    $(document).on('keyup', '#searchRejected', function() {
        let searchTerm = $(this).val().toLowerCase();
        $('#rejectedTable tr').each(function() {
            let row = $(this);
            let text = row.text().toLowerCase();
            row.toggle(text.includes(searchTerm));
        });
    });

    // Location Filter
    $(document).on('change', '#filterLocation', function() {
        let filterValue = $(this).val().toLowerCase();
        if (!filterValue) {
            $('#applicationsTable tr').show();
        } else {
            $('#applicationsTable tr').each(function() {
                let row = $(this);
                let location = row.find('td:eq(4)').text().toLowerCase();
                row.toggle(location.includes(filterValue));
            });
        }
    });

    // Shortlist Handler
    $(document).on('click', '.shortlist-btn', function() {
        let appId = $(this).data('id');
        $.ajax({
            url: '/admin/career/shortlist/' + appId,
            type: 'POST',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function() {
                alert('Candidate shortlisted successfully!');
                loadApplications();
                loadShortlisted();
            },
            error: function(xhr) {
                alert('Error: ' + (xhr.responseJSON?.message || 'Failed to shortlist'));
            }
        });
    });

    // Hire Handler
    $(document).on('click', '.hire-btn', function() {
        let appId = $(this).data('id');
        $.ajax({
            url: '/admin/career/hire/' + appId,
            type: 'POST',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function() {
                alert('Candidate hired successfully!');
                loadShortlisted();
                loadHired();
            },
            error: function(xhr) {
                alert('Error: ' + (xhr.responseJSON?.message || 'Failed to hire'));
            }
        });
    });

    // Reject Handler
    $(document).on('click', '.reject-btn', function() {
        let appId = $(this).data('id');
        $.ajax({
            url: '/admin/career/reject/' + appId,
            type: 'POST',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function() {
                alert('Candidate rejected.');
                loadApplications();
                loadShortlisted();
            },
            error: function(xhr) {
                alert('Error: ' + (xhr.responseJSON?.message || 'Failed to reject'));
            }
        });
    });

    // Delete Hired Handler
    $(document).on('click', '.delete-hire-btn', function() {
        if (confirm('Remove this employee from hired list?')) {
            let appId = $(this).data('id');
            $.ajax({
                url: '/admin/career/delete-application/' + appId,
                type: 'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function() {
                    alert('Employee removed.');
                    loadHired();
                },
                error: function(xhr) {
                    alert('Error: ' + (xhr.responseJSON?.message || 'Failed to delete'));
                }
            });
        }
    });

    // Delete CV Handler
    $(document).on('click', '.delete-cv-btn', function() {
        if (!confirm('Delete this CV and application? This action cannot be undone.')) {
            return;
        }

        let appId = $(this).data('id');
        const $row = $(this).closest('tr');

        $.ajax({
            url: `${cvDeleteBase}/${appId}`,
            type: 'DELETE',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function(response) {
                $row.fadeOut(300, function() {
                    $(this).remove();
                });
                alert('Application and CV deleted.');
            },
            error: function(xhr) {
                alert('Error: ' + (xhr.responseJSON?.message || 'Failed to delete'));
            }
        });
    });
    // Initial load for the active tab (Blogs)
    reloadBlogsTable();

    // Initialize DataTables after data loads
    function initializeDataTables() {
        const tables = [
            { id: '#videosDataTable', pageLength: 10 },
            { id: '#careersDataTable', pageLength: 10 },
            { id: '#socialDataTable', pageLength: 10 },
            { id: '#storiesDataTable', pageLength: 10 },
            { id: '#caseStudiesDataTable', pageLength: 10 },
            { id: '#eventsDataTable', pageLength: 10 },
            { id: '#teamDataTable', pageLength: 10 },
            { id: '#newsletterDataTable', pageLength: 10 }
            // Skip blogsDataTable - initialized separately for Server-Side
            // Skip job application tables - they load via AJAX
        ];

        tables.forEach(function(table) {
            try {
                // Destroy existing instance
                if ($.fn.DataTable.isDataTable(table.id)) {
                    $(table.id).DataTable().destroy();
                }
                
                // Check if table exists and has data
                const $target = $(table.id);
                if ($target.length === 0) return;

                const $tbody = $target.find('tbody');
                const $rows = $tbody.find('tr').filter(function() {
                    return $(this).text().trim() !== '' || $(this).find('td').length > 0;
                });

                if ($rows.length > 0) {
                    // Validate column count vs header to prevent DT warning (TN18)
                    const headerCols = $target.find('thead tr:first th').length;
                    const bodyCols = $rows.first().find('td').length;
                    const hasColspan = $rows.first().find('td[colspan]').length > 0;

                    if (headerCols > 0 && bodyCols > 0 && (headerCols !== bodyCols || hasColspan)) {
                        console.warn('Skipping DataTable init for ' + table.id + ' due to column mismatch or colspan.');
                        return;
                    }

                    $target.DataTable({
                        pageLength: table.pageLength,
                        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                        order: [[0, 'asc']],
                        language: {
                            search: "_INPUT_",
                            searchPlaceholder: "Search...",
                            lengthMenu: "Show _MENU_ entries",
                            info: "Showing _START_ to _END_ of _TOTAL_ entries",
                            infoEmpty: "No entries available",
                            infoFiltered: "(filtered from _MAX_ total entries)",
                            zeroRecords: "No matching records found",
                            emptyTable: "No data available in table",
                            paginate: {
                                next: 'Read more',
                                previous: 'Previous'
                            }
                        },
                        columnDefs: [
                            { orderable: false, targets: -1 } // Disable sorting on last column
                        ],
                        responsive: true,
                        autoWidth: false,
                        dom: "<'dt-toolbar'<'dt-search'f><'dt-length'l>>" +
                             "t" +
                             "<'dt-footer row'<'col-md-5 col-12'i><'col-md-7 col-12'p>>",
                        buttons: []
                    });
                }
            } catch(e) {
                console.log('Error initializing DataTable for ' + table.id, e);
            }
        });
    }

    // Initialize DataTables when switching to static content tabs
    $('#blogs-tab').on('click', function() {
        reloadBlogsTable();
    });

    $('#videos-tab, #careers-tab, #social-tab, #stories-tab, #case-studies-tab, #events-tab, #team-tab, #newsletter-tab').on('click', function() {
        setTimeout(function() {
            initializeDataTables();
        }, 100);
    });
});
</script>
@endpush
