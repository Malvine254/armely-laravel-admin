@extends('admin.layouts.admin')

@section('page-title', 'Content Management')
@section('title', 'Content Management - Armely Admin')

@push('styles')
<style>
    .content-preview {
        max-height: 100px;
        overflow: hidden;
        position: relative;
    }
    .content-preview::after {
        content: '...';
        position: absolute;
        bottom: 0;
        right: 0;
        background: white;
        padding: 0 5px;
    }
    .modal-lg {
        max-width: 900px;
    }
    .table td {
        vertical-align: middle;
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
        </ul>
    </div>
    
    <div class="tab-content">
        <!-- Blogs Tab -->
        <div class="tab-pane fade show active" id="blogs" role="tabpanel">
            <div class="card-body">
                <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#blogModal">
                    <i class="fas fa-plus"></i> Add New Blog
                </button>
                
                @if($blogs->count() > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Author</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($blogs as $blog)
                                <tr data-id="{{ $blog->id ?? $blog->blog_id }}">
                                    <td>{{ \Illuminate\Support\Str::limit(strip_tags($blog->title ?? $blog->blog_title ?? ''), 50) }}</td>
                                    <td>{{ $blog->author ?? 'Admin' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($blog->date ?? $blog->blog_date)->format('M d, Y') }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info view-blog" data-id="{{ $blog->id ?? $blog->blog_id }}" title="View">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning edit-blog" data-id="{{ $blog->id ?? $blog->blog_id }}" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger delete-blog" data-id="{{ $blog->id ?? $blog->blog_id }}" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted"><i class="fas fa-info-circle"></i> No blogs yet. Create your first blog!</p>
                @endif
            </div>
        </div>
        
        <!-- Videos Tab -->
        <div class="tab-pane fade" id="videos" role="tabpanel">
            <div class="card-body">
                <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#videoModal">
                    <i class="fas fa-plus"></i> Add New Video
                </button>
                
                @if($videos->count() > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>URL</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($videos as $video)
                                <tr>
                                    <td>{{ \Illuminate\Support\Str::limit($video->title ?? $video->video_title ?? '', 40) }}</td>
                                    <td><a href="{{ $video->url ?? $video->video_url ?? '#' }}" target="_blank" class="text-truncate">{{ $video->url ?? $video->video_url ?? '#' }}</a></td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editVideoModal" onclick='editVideo(@json($video))'>
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('admin.tables.videos.delete', $video->id ?? $video->video_id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
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
                <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#careerModal">
                    <i class="fas fa-plus"></i> Add New Career
                </button>
                
                @if($careers->count() > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Job Title</th>
                                    <th>Department</th>
                                    <th>Location</th>
                                    <th>Type</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($careers as $career)
                                <tr>
                                    <td>{{ $career->job_title ?? $career->title ?? 'N/A' }}</td>
                                    <td>{{ $career->department ?? 'N/A' }}</td>
                                    <td>{{ $career->job_location ?? $career->location ?? 'N/A' }}</td>
                                    <td><span class="badge bg-info">{{ $career->job_type ?? $career->type ?? 'N/A' }}</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editCareerModal" onclick='editCareer(@json($career))'>
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('admin.tables.careers.delete', $career->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
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
                <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#socialModal">
                    <i class="fas fa-plus"></i> Add New Impact Story
                </button>
                
                @if($socialImpact->count() > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Area</th>
                                    <th>Published</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($socialImpact as $impact)
                                <tr>
                                    <td>{{ \Illuminate\Support\Str::limit($impact->title ?? '', 40) }}</td>
                                    <td>{{ $impact->category ?? $impact->impact_area ?? 'N/A' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($impact->posted_date ?? $impact->published_date ?? now())->format('M d, Y') }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editSocialModal" onclick='editSocialImpact(@json($impact))'>
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('admin.tables.social-impact.delete', $impact->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
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
                <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#storyModal">
                    <i class="fas fa-plus"></i> Add New Customer Story
                </button>
                
                @if($customerStories->count() > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Company</th>
                                    <th>Industry</th>
                                    <th>Story Title</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customerStories as $story)
                                <tr>
                                    <td>{{ $story->name ?? $story->company_name ?? 'N/A' }}</td>
                                    <td>{{ $story->industry ?? 'N/A' }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($story->body_content ?? $story->story_title ?? '', 40) }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editStoryModal" onclick='editCustomerStory(@json($story))'>
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('admin.tables.customer-stories.delete', $story->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
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
    </div>
</div>

<!-- Modals for each content type would go here -->
<!-- For brevity, showing just structure - each modal follows same pattern -->

<script>
function editBlog(blog) {
    // Populate edit form with blog data
    console.log(blog);
}

function editVideo(video) {
    console.log(video);
}

function editCareer(career) {
    console.log(career);
}

function editSocialImpact(impact) {
    console.log(impact);
}

function editCustomerStory(story) {
    console.log(story);
}
</script>

@endsection
