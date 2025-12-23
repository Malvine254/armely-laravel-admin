@extends('admin.layouts.admin')

@section('page-title', 'Content Management')
@section('title', 'Content Management - Armely Admin')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<style>
    .content-preview {
        max-height: 100px;
        overflow: hidden;
    }
    .modal-lg {
        max-width: 900px;
    }
    .table td {
        vertical-align: middle;
    }
    /* Icon-only action button layout */
    .action-btns{display:flex;gap:.5rem;align-items:center}
    .action-btns .btn{min-width:36px;padding:6px;display:inline-flex;align-items:center;justify-content:center}
    @media(max-width:576px){.action-btns{flex-direction:row;flex-wrap:wrap}.action-btns .btn{min-width:40px}}
    
    /* DataTables custom styling */
    .dataTables_wrapper {
        display: flex;
        flex-direction: column;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0.25rem 0.5rem;
        margin: 0 2px;
    }
    .dataTables_wrapper .dataTables_length select {
        padding: 0.25rem 2rem 0.25rem 0.5rem;
    }
    .dataTables_wrapper .dataTables_filter input {
        margin-left: 0.5rem;
    }
    
    /* Uniform table styling */
    .table-responsive {
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
    }
    
    .dataTables_wrapper .dataTables_paginate {
        padding-top: 1rem;
        border-top: 1px solid #dee2e6;
    }
</style>
@endpush

@section('content')
<div class="page-title">
    <h1>Content Management</h1>
    <p>Manage all website content in one place</p>
</div>

<!-- Nav Tabs -->
<div class="card mb-4">
    <div class="card-header border-bottom">
        <ul class="nav nav-tabs card-header-tabs" role="tablist">
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
                <a class="nav-link" id="jobs-tab" data-bs-toggle="tab" href="#jobs" role="tab">
                    <i class="fas fa-briefcase"></i> Job Applications
                </a>
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
                                <tr><td colspan="4" class="text-center"><span class="spinner-border spinner-border-sm"></span></td></tr>
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
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="applicationsTable">
                                    <tr><td colspan="7" class="text-center"><span class="spinner-border spinner-border-sm"></span></td></tr>
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
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="shortlistedTable">
                                    <tr><td colspan="6" class="text-center"><span class="spinner-border spinner-border-sm"></span></td></tr>
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
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="hiredTable">
                                    <tr><td colspan="6" class="text-center"><span class="spinner-border spinner-border-sm"></span></td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="blogModalTitle">Add New Blog</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="blogForm">
                    <input type="hidden" id="blogId" name="id">
                    <div class="mb-3">
                        <label class="form-label">Title *</label>
                        <input type="text" class="form-control" id="blogTitle" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Author *</label>
                        <input type="text" class="form-control" id="blogAuthor" name="author" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date *</label>
                        <input type="date" class="form-control" id="blogDate" name="date" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Content</label>
                        <textarea class="form-control" id="blogBody" name="body" rows="10"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Image</label>
                        <input type="file" class="form-control" id="blogImage" name="image" accept="image/*">
                        <small class="text-muted">Current image will be preserved if no new image is uploaded</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveBlogBtn">Save Blog</button>
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
    <div class="modal-dialog modal-lg">
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
    <div class="modal-dialog modal-lg">
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
    <div class="modal-dialog modal-lg">
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
    <div class="modal-dialog modal-lg">
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
    <div class="modal-dialog modal-lg">
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

@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
<script>
$(document).ready(function() {
    // Initialize CKEditor
    let blogEditor;
    
    // ==================== TABLE RELOAD FUNCTIONS ====================
    
    // Reload blogs table
    function reloadBlogsTable() {
        $.ajax({
            url: '/admin/tables/blogs/list?limit=50',
            type: 'GET',
            dataType: 'json',
            timeout: 15000,
            success: function(response) {
                const tbody = $('#blogsTable');
                tbody.empty();

                // small helpers to validate & escape
                function safeText(s){ return $('<div/>').text(s || '').html(); }
                function isValidBlog(b){
                    if (!b) return false;
                    const id = b.blog_id || b.id;
                    // must have an id and at least a title or author
                    if (!id) return false;
                    if (!b.title && !b.blog_title && !b.author) return false;
                    return true;
                }

                // Expected: { success: true, data: [...] }
                if (response && Array.isArray(response.data)) {
                    if (response.data.length === 0) {
                        tbody.html('<tr><td colspan="4" class="text-center text-muted">No blogs found.</td></tr>');
                        return;
                    }

                    let validCount = 0;
                    response.data.forEach(function(blog, idx) {
                        try {
                            if (!isValidBlog(blog)) {
                                console.warn('Skipping invalid blog at index', idx, blog);
                                return; // skip malformed
                            }

                            const blogId = blog.blog_id || blog.id;
                            const title = safeText(blog.title || blog.blog_title || '');
                            const author = safeText(blog.author || '');
                            const date = safeText(blog.date || blog.blog_date || '');

                            const btns = `
                                <div class="action-btns">
                                    <button class="btn btn-sm btn-info view-blog" data-blog='${JSON.stringify(blog).replace(/'/g, "&apos;")}' title="View">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-warning edit-blog" data-blog='${JSON.stringify(blog).replace(/'/g, "&apos;")}' title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-blog" data-id="${blogId}" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>`;

                            const row = `<tr data-id="${blogId}">
                                <td>${title}</td>
                                <td>${author}</td>
                                <td>${date}</td>
                                <td>${btns}</td>
                            </tr>`;
                            tbody.append(row);
                            validCount++;
                        } catch (e) {
                            console.warn('Error rendering blog at index', idx, e, blog);
                        }
                    });

                    if (validCount === 0) {
                        tbody.html('<tr><td colspan="4" class="text-center text-muted">No valid blogs to display.</td></tr>');
                    }
                } else {
                    // Unexpected response (maybe HTML login page on deployed site)
                    console.warn('Unexpected blogs list response:', response);
                    tbody.html('<tr><td colspan="4" class="text-center text-danger">Unable to load blogs (unexpected response).</td></tr>');
                }
            },
            error: function(xhr, status, err) {
                console.error('Error reloading blogs table:', status, err, xhr);
                const tbody = $('#blogsTable');
                let msg = 'Error loading blogs';
                if (xhr && xhr.status) msg += ': ' + xhr.status + ' ' + (xhr.statusText || '');
                tbody.html(`<tr><td colspan="4" class="text-center text-danger">${msg}. Check console/network.</td></tr>`);
            }
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
    
    // Blog View
    $(document).on('click', '.view-blog', function() {
        const blog = $(this).data('blog');
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
        
        // Initialize CKEditor if not already
        if (!blogEditor) {
            blogEditor = CKEDITOR.replace('blogBody');
        } else {
            blogEditor.setData('');
        }
    });

    // Blog Edit
    $(document).on('click', '.edit-blog', function() {
        const blog = $(this).data('blog');
        
        console.log('Full blog object:', blog);
        console.log('blog.id:', blog.id);
        console.log('blog.blog_id:', blog.blog_id);
        
        $('#blogModalTitle').text('Edit Blog');
        
        // Use blog_id if it exists, otherwise use id
        const blogId = blog.blog_id || blog.id;
        console.log('Using blog ID:', blogId);
        
        $('#blogId').val(blogId);
        $('#blogTitle').val(blog.title || blog.blog_title || '');
        $('#blogAuthor').val(blog.author || '');
        $('#blogDate').val(blog.date || blog.blog_date || '');
        
        // Initialize CKEditor if not already
        if (!blogEditor) {
            blogEditor = CKEDITOR.replace('blogBody');
        }
        blogEditor.setData(blog.body || blog.content || '');
        
        $('#blogModal').modal('show');
    });

    // Save Blog (Add/Edit)
    $('#saveBlogBtn').click(function() {
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
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log('Save successful:', response);
                $('#blogModal').modal('hide');
                reloadBlogsTable();
                alert('Blog saved successfully!');
            },
            error: function(xhr) {
                console.error('Save error:', xhr.responseJSON);
                alert('Error saving blog: ' + (xhr.responseJSON?.message || 'Unknown error'));
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
                $('#videoModal').modal('hide');
                resetVideoForm();
                reloadVideosTable();
                alert('Video saved successfully!');
            },
            error: function(xhr) {
                alert('Error saving video: ' + (xhr.responseJSON?.message || 'Unknown error'));
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
                $('#careerModal').modal('hide');
                reloadCareersTable();
                alert('Career saved successfully!');
            },
            error: function(xhr) {
                alert('Error saving career: ' + (xhr.responseJSON?.message || 'Unknown error'));
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
                $('#socialModal').modal('hide');
                reloadSocialTable();
                alert('Social impact story saved successfully!');
            },
            error: function(xhr) {
                alert('Error saving social impact story: ' + (xhr.responseJSON?.message || 'Unknown error'));
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
                $('#storyModal').modal('hide');
                reloadStoriesTable();
                alert('Customer story saved successfully!');
            },
            error: function(xhr) {
                alert('Error saving customer story: ' + (xhr.responseJSON?.message || 'Unknown error'));
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
                $('#eventModal').modal('hide');
                reloadEventsTable();
                alert('Event saved successfully!');
            },
            error: function(xhr) {
                alert('Error saving event: ' + (xhr.responseJSON?.message || 'Unknown error'));
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
    
    // Team Save
    $('#saveTeamBtn').click(function() {
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
                $('#teamModal').modal('hide');
                reloadTeamTable();
                alert('Team member saved successfully!');
            },
            error: function(xhr) {
                alert('Error saving team member: ' + (xhr.responseJSON?.message || 'Unknown error'));
            }
        });
    });

    // Job Applications Handlers
    function loadApplications() {
        $.ajax({
            url: '/admin/career/list-applications',
            type: 'GET',
            success: function(response) {
                let html = '';
                response.forEach(function(app, index) {
                    html += `<tr data-id="${app.id}" data-status="${app.status}">
                        <td>${index + 1}</td>
                        <td>${app.name}</td>
                        <td>${app.email}</td>
                        <td>${app.position}</td>
                        <td>${app.city || ''}</td>
                        <td>${new Date(app.application_date).toLocaleDateString()}</td>
                        <td>
                            <button class="btn btn-sm btn-success shortlist-btn" data-id="${app.id}" title="Shortlist">
                                <i class="fas fa-check"></i>
                            </button>
                            <button class="btn btn-sm btn-danger reject-btn" data-id="${app.id}" title="Reject">
                                <i class="fas fa-times"></i>
                            </button>
                        </td>
                    </tr>`;
                });
                $('#applicationsTable').html(html || '<tr><td colspan="7" class="text-center text-muted">No pending applications</td></tr>');
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
                    html += `<tr data-id="${app.id}">
                        <td>${index + 1}</td>
                        <td>${app.name}</td>
                        <td>${app.email}</td>
                        <td>${app.position}</td>
                        <td>${new Date(app.application_date).toLocaleDateString()}</td>
                        <td>
                            <button class="btn btn-sm btn-primary hire-btn" data-id="${app.id}" title="Hire">
                                <i class="fas fa-user-check"></i>
                            </button>
                            <button class="btn btn-sm btn-danger reject-btn" data-id="${app.id}" title="Reject">
                                <i class="fas fa-times"></i>
                            </button>
                        </td>
                    </tr>`;
                });
                $('#shortlistedTable').html(html || '<tr><td colspan="6" class="text-center text-muted">No shortlisted candidates</td></tr>');
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
                    html += `<tr data-id="${app.id}">
                        <td>${index + 1}</td>
                        <td>${app.name}</td>
                        <td>${app.email}</td>
                        <td>${app.position}</td>
                        <td>${new Date(app.application_date).toLocaleDateString()}</td>
                        <td>
                            <button class="btn btn-sm btn-danger delete-hire-btn" data-id="${app.id}" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>`;
                });
                $('#hiredTable').html(html || '<tr><td colspan="6" class="text-center text-muted">No hired employees</td></tr>');
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
    // Initial load for the active tab (Blogs)
    reloadBlogsTable();

    // Initialize DataTables after data loads
    function initializeDataTables() {
        const tables = [
            { id: '#blogsDataTable', pageLength: 10 },
            { id: '#videosDataTable', pageLength: 10 },
            { id: '#careersDataTable', pageLength: 10 },
            { id: '#socialDataTable', pageLength: 10 },
            { id: '#storiesDataTable', pageLength: 10 },
            { id: '#eventsDataTable', pageLength: 10 },
            { id: '#teamDataTable', pageLength: 10 }
            // Skip job application tables - they load via AJAX
        ];

        tables.forEach(function(table) {
            try {
                // Destroy existing instance
                if ($.fn.DataTable.isDataTable(table.id)) {
                    $(table.id).DataTable().destroy();
                }
                
                // Check if table exists and has data
                if ($(table.id).length && $(table.id).find('tbody tr:not(:empty)').length > 0) {
                    $(table.id).DataTable({
                        pageLength: table.pageLength,
                        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                        order: [[0, 'asc']],
                        language: {
                            search: "_INPUT_",
                            searchPlaceholder: "Search...",
                            lengthMenu: "Show _MENU_ entries",
                            info: "Showing _START_ to _END_ of _TOTAL_ entries",
                            infoEmpty: "No entries available",
                            infoFiltered: "(filtered from _MAX_ total entries)",
                            zeroRecords: "No matching records found",
                            emptyTable: "No data available in table"
                        },
                        columnDefs: [
                            { orderable: false, targets: -1 } // Disable sorting on last column
                        ],
                        responsive: true,
                        autoWidth: false,
                        dom: 'lBfrtip',
                        buttons: []
                    });
                }
            } catch(e) {
                console.log('Error initializing DataTable for ' + table.id, e);
            }
        });
    }

    // Wrap the original reloadBlogsTable to call DataTables init
    const originalReloadBlogsTable = window.reloadBlogsTable;
    window.reloadBlogsTable = function() {
        if (typeof originalReloadBlogsTable === 'function') {
            originalReloadBlogsTable();
            setTimeout(function() {
                initializeDataTables();
            }, 300);
        }
    };

    // Initialize DataTables when switching to static content tabs (not job applications)
    $('#blogs-tab, #videos-tab, #careers-tab, #social-tab, #stories-tab, #events-tab, #team-tab').on('click', function() {
        setTimeout(function() {
            initializeDataTables();
        }, 100);
    });
});
</script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
@endpush
