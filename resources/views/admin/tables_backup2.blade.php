@extends('admin.layouts.admin')

@section('page-title', 'Content Management')
@section('title', 'Content Management - Armely Admin')

@push('styles')
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
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Author</th>
                                    <th>Date</th>
                                    <th width="200">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="blogsTable">
                                @foreach($blogs as $blog)
                                <tr data-id="{{ $blog->id ?? $blog->blog_id }}">
                                    <td>{{ \Illuminate\Support\Str::limit(strip_tags($blog->title ?? $blog->blog_title ?? ''), 50) }}</td>
                                    <td>{{ $blog->author ?? 'Admin' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($blog->date ?? $blog->blog_date)->format('M d, Y') }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info view-blog" data-blog='@json($blog)' title="View">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning edit-blog" data-blog='@json($blog)' title="Edit">
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
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>URL</th>
                                    <th width="200">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="videosTable">
                                @foreach($videos as $video)
                                <tr data-id="{{ $video->id ?? $video->video_id }}">
                                    <td>{{ \Illuminate\Support\Str::limit($video->title ?? $video->video_title ?? '', 50) }}</td>
                                    <td><a href="{{ $video->url ?? $video->video_url ?? '#' }}" target="_blank">{{ \Illuminate\Support\Str::limit($video->url ?? $video->video_url ?? '', 40) }}</a></td>
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
                <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#careerModal">
                    <i class="fas fa-plus"></i> Add New Career
                </button>
                
                @if($careers->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
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
                <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#socialModal">
                    <i class="fas fa-plus"></i> Add New Impact Story
                </button>
                
                @if($socialImpact->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
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
                <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#storyModal">
                    <i class="fas fa-plus"></i> Add New Customer Story
                </button>
                
                @if($customerStories->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
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

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
<script>
$(document).ready(function() {
    // Initialize CKEditor
    let blogEditor;
    
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
        $('#blogModalTitle').text('Edit Blog');
        $('#blogId').val(blog.id || blog.blog_id);
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
        
        formData.append('_token', '{{ csrf_token() }}');
        if (isEdit) {
            formData.append('_method', 'PUT');
        }
        formData.append('title', $('#blogTitle').val());
        formData.append('author', $('#blogAuthor').val());
        formData.append('date', $('#blogDate').val());
        formData.append('body', blogEditor.getData());
        
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
                $('#blogModal').modal('hide');
                location.reload();
            },
            error: function(xhr) {
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
});
</script>
@endpush
