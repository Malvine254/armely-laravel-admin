@extends('admin.layouts.admin')

@section('page-title', 'Company Content')
@section('title', 'Company Content - Armely Admin')

@push('styles')
<style>
    .content-card {
        border: 1px solid #e4e7ec;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(15, 27, 51, 0.05);
    }
    .content-card .card-header {
        background: #f8f9fa;
        border-bottom: 1px solid #e4e7ec;
        border-radius: 16px 16px 0 0;
    }
    .logo-preview {
        width: 48px;
        height: 48px;
        border-radius: 8px;
        object-fit: cover;
        border: 1px solid #e4e7ec;
    }
    .row-actions {
        display: flex;
        gap: 8px;
    }
    .section-title {
        font-weight: 700;
        color: #2f5597;
    }
    .features-list {
        margin: 0;
        padding-left: 18px;
    }
    .features-list li {
        font-size: 0.9rem;
        color: #475467;
    }
    .company-content-tabs .nav-link {
        color: #475467;
        font-weight: 600;
        border: none;
        border-bottom: 3px solid transparent;
        border-radius: 0;
        padding: 0.85rem 1rem;
    }
    .company-content-tabs .nav-link.active {
        color: #2f5597;
        background: transparent;
        border-bottom-color: #2f5597;
    }
    .tab-pane-content {
        padding-top: 1rem;
    }
</style>
@endpush

@section('content')
<div class="mb-4">
    <h2 class="fw-bold text-dark mb-1">Company Portfolio & Website Banners</h2>
    <p class="text-muted mb-0">Manage company page portfolio cards and website advert banners from the database.</p>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card content-card">
    <div class="card-header pt-2 pb-0">
        <ul class="nav nav-tabs company-content-tabs card-header-tabs" id="companyContentTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="portfolio-tab" data-bs-toggle="tab" data-bs-target="#portfolio-pane" type="button" role="tab" aria-controls="portfolio-pane" aria-selected="true">
                    Portfolio
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="banners-tab" data-bs-toggle="tab" data-bs-target="#banners-pane" type="button" role="tab" aria-controls="banners-pane" aria-selected="false">
                    Banners
                </button>
            </li>
        </ul>
    </div>
    <div class="card-body tab-pane-content">
        <div class="tab-content" id="companyContentTabsContent">
            <div class="tab-pane fade show active" id="portfolio-pane" role="tabpanel" aria-labelledby="portfolio-tab">
                <div class="row g-4">
                    <div class="col-12">
                        <div class="card content-card">
                            <div class="card-header py-3">
                                <h5 class="section-title mb-0">Add Portfolio Item</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('admin.company-content.portfolios.store') }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Title</label>
                                            <input type="text" name="title" class="form-control" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Category Label</label>
                                            <input type="text" name="category" class="form-control" placeholder="AI & Machine Learning">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Display Order</label>
                                            <input type="number" name="display_order" min="0" class="form-control" value="0">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Short Description</label>
                                            <textarea name="short_description" class="form-control" rows="3" required></textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Long Description</label>
                                            <textarea name="long_description" class="form-control" rows="3"></textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Features (one per line)</label>
                                            <textarea name="features" class="form-control" rows="4" placeholder="Feature one&#10;Feature two"></textarea>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">CTA Label</label>
                                            <input type="text" name="cta_label" class="form-control" placeholder="Learn More">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">CTA URL</label>
                                            <input type="text" name="cta_url" class="form-control" placeholder="/mela-ai">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Logo/Image</label>
                                            <input type="file" name="logo" class="form-control" accept="image/*">
                                        </div>
                                        <div class="col-md-4 d-flex align-items-end">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" role="switch" id="portfolio-active" name="is_active" value="1" checked>
                                                <label class="form-check-label" for="portfolio-active">Active</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4 d-flex align-items-end justify-content-end">
                                            <button type="submit" class="btn btn-primary">Save Portfolio Item</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card content-card">
                            <div class="card-header py-3">
                                <h5 class="section-title mb-0">Existing Portfolio Items</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Logo</th>
                                                <th>Title</th>
                                                <th>Category</th>
                                                <th>Order</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($portfolios as $portfolio)
                                                <tr>
                                                    <td>{{ $portfolio->id }}</td>
                                                    <td>
                                                        @if(!empty($portfolio->logo_path))
                                                            <img src="{{ asset('storage/' . $portfolio->logo_path) }}" class="logo-preview" alt="portfolio logo">
                                                        @else
                                                            <span class="text-muted">No image</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $portfolio->title }}</td>
                                                    <td>{{ $portfolio->category ?: '-' }}</td>
                                                    <td>{{ $portfolio->display_order }}</td>
                                                    <td>
                                                        @if($portfolio->is_active)
                                                            <span class="badge bg-success">Active</span>
                                                        @else
                                                            <span class="badge bg-secondary">Inactive</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="row-actions">
                                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#portfolio-edit-{{ $portfolio->id }}">Edit</button>
                                                            <form method="POST" action="{{ route('admin.company-content.portfolios.delete', $portfolio->id) }}" onsubmit="return confirm('Delete this portfolio item?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr class="collapse" id="portfolio-edit-{{ $portfolio->id }}">
                                                    <td colspan="7">
                                                        <form method="POST" action="{{ route('admin.company-content.portfolios.update', $portfolio->id) }}" enctype="multipart/form-data" class="p-3 bg-light border-top">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="row g-3">
                                                                <div class="col-md-6">
                                                                    <label class="form-label">Title</label>
                                                                    <input type="text" name="title" class="form-control" value="{{ $portfolio->title }}" required>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <label class="form-label">Category</label>
                                                                    <input type="text" name="category" class="form-control" value="{{ $portfolio->category }}">
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <label class="form-label">Display Order</label>
                                                                    <input type="number" name="display_order" class="form-control" min="0" value="{{ $portfolio->display_order }}">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label">Short Description</label>
                                                                    <textarea name="short_description" class="form-control" rows="3" required>{{ $portfolio->short_description }}</textarea>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label">Long Description</label>
                                                                    <textarea name="long_description" class="form-control" rows="3">{{ $portfolio->long_description }}</textarea>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label">Features (one per line)</label>
                                                                    <textarea name="features" class="form-control" rows="4">{{ is_string($portfolio->features) ? implode("\n", (json_decode($portfolio->features, true) ?: [])) : '' }}</textarea>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <label class="form-label">CTA Label</label>
                                                                    <input type="text" name="cta_label" class="form-control" value="{{ $portfolio->cta_label }}">
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <label class="form-label">CTA URL</label>
                                                                    <input type="text" name="cta_url" class="form-control" value="{{ $portfolio->cta_url }}">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label class="form-label">Replace Logo/Image</label>
                                                                    <input type="file" name="logo" class="form-control" accept="image/*">
                                                                </div>
                                                                <div class="col-md-4 d-flex align-items-end">
                                                                    <div class="form-check form-switch">
                                                                        <input class="form-check-input" type="checkbox" role="switch" id="portfolio-active-{{ $portfolio->id }}" name="is_active" value="1" {{ $portfolio->is_active ? 'checked' : '' }}>
                                                                        <label class="form-check-label" for="portfolio-active-{{ $portfolio->id }}">Active</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4 d-flex align-items-end justify-content-end">
                                                                    <button type="submit" class="btn btn-primary">Update Portfolio Item</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted py-4">No portfolio items found.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="banners-pane" role="tabpanel" aria-labelledby="banners-tab">
                <div class="row g-4">
                    <div class="col-12">
                        <div class="card content-card">
                            <div class="card-header py-3">
                                <h5 class="section-title mb-0">Add Website Advert Banner</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('admin.company-content.banners.store') }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <label class="form-label">Page</label>
                                            <select name="page" class="form-select" required>
                                                <option value="company">Company Page</option>
                                                <option value="home">Home Page</option>
                                                <option value="global">Global (All configured pages)</option>
                                            </select>
                                        </div>
                                        <div class="col-md-5">
                                            <label class="form-label">Headline</label>
                                            <input type="text" name="headline" class="form-control" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Display Order</label>
                                            <input type="number" name="display_order" min="0" class="form-control" value="0">
                                        </div>
                                        <div class="col-md-2 d-flex align-items-end">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" role="switch" id="banner-active" name="is_active" value="1" checked>
                                                <label class="form-check-label" for="banner-active">Active</label>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label">Message</label>
                                            <textarea name="message" class="form-control" rows="3"></textarea>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Button Label</label>
                                            <input type="text" name="button_label" class="form-control" placeholder="Learn More">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Button URL</label>
                                            <input type="text" name="button_url" class="form-control" placeholder="/contact">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Start Date/Time</label>
                                            <input type="datetime-local" name="starts_at" class="form-control">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">End Date/Time</label>
                                            <input type="datetime-local" name="ends_at" class="form-control">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Background Style (CSS gradient or color)</label>
                                            <input type="text" name="background_style" class="form-control" placeholder="linear-gradient(135deg, #2f5597, #1e3a6b)">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Banner Image (optional)</label>
                                            <input type="file" name="image" class="form-control" accept="image/*">
                                        </div>
                                        <div class="col-12 d-flex justify-content-end">
                                            <button type="submit" class="btn btn-primary">Save Banner</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card content-card">
                            <div class="card-header py-3">
                                <h5 class="section-title mb-0">Existing Website Advert Banners</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Page</th>
                                                <th>Headline</th>
                                                <th>Schedule</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($banners as $banner)
                                                <tr>
                                                    <td>{{ $banner->id }}</td>
                                                    <td><span class="badge bg-info text-dark text-uppercase">{{ $banner->page }}</span></td>
                                                    <td>{{ $banner->headline }}</td>
                                                    <td>
                                                        <small>
                                                            {{ $banner->starts_at ?: 'Now' }}<br>
                                                            to {{ $banner->ends_at ?: 'No end date' }}
                                                        </small>
                                                    </td>
                                                    <td>
                                                        @if($banner->is_active)
                                                            <span class="badge bg-success">Active</span>
                                                        @else
                                                            <span class="badge bg-secondary">Inactive</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="row-actions">
                                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#banner-edit-{{ $banner->id }}">Edit</button>
                                                            <form method="POST" action="{{ route('admin.company-content.banners.delete', $banner->id) }}" onsubmit="return confirm('Delete this banner?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr class="collapse" id="banner-edit-{{ $banner->id }}">
                                                    <td colspan="6">
                                                        <form method="POST" action="{{ route('admin.company-content.banners.update', $banner->id) }}" enctype="multipart/form-data" class="p-3 bg-light border-top">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="row g-3">
                                                                <div class="col-md-3">
                                                                    <label class="form-label">Page</label>
                                                                    <select name="page" class="form-select" required>
                                                                        <option value="company" {{ $banner->page === 'company' ? 'selected' : '' }}>Company Page</option>
                                                                        <option value="home" {{ $banner->page === 'home' ? 'selected' : '' }}>Home Page</option>
                                                                        <option value="global" {{ $banner->page === 'global' ? 'selected' : '' }}>Global</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-5">
                                                                    <label class="form-label">Headline</label>
                                                                    <input type="text" name="headline" class="form-control" value="{{ $banner->headline }}" required>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <label class="form-label">Display Order</label>
                                                                    <input type="number" name="display_order" class="form-control" min="0" value="{{ $banner->display_order }}">
                                                                </div>
                                                                <div class="col-md-2 d-flex align-items-end">
                                                                    <div class="form-check form-switch">
                                                                        <input class="form-check-input" type="checkbox" role="switch" id="banner-active-{{ $banner->id }}" name="is_active" value="1" {{ $banner->is_active ? 'checked' : '' }}>
                                                                        <label class="form-check-label" for="banner-active-{{ $banner->id }}">Active</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <label class="form-label">Message</label>
                                                                    <textarea name="message" class="form-control" rows="3">{{ $banner->message }}</textarea>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <label class="form-label">Button Label</label>
                                                                    <input type="text" name="button_label" class="form-control" value="{{ $banner->button_label }}">
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <label class="form-label">Button URL</label>
                                                                    <input type="text" name="button_url" class="form-control" value="{{ $banner->button_url }}">
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <label class="form-label">Start Date/Time</label>
                                                                    <input type="datetime-local" name="starts_at" class="form-control" value="{{ $banner->starts_at ? \Carbon\Carbon::parse($banner->starts_at)->format('Y-m-d\\TH:i') : '' }}">
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <label class="form-label">End Date/Time</label>
                                                                    <input type="datetime-local" name="ends_at" class="form-control" value="{{ $banner->ends_at ? \Carbon\Carbon::parse($banner->ends_at)->format('Y-m-d\\TH:i') : '' }}">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label">Background Style</label>
                                                                    <input type="text" name="background_style" class="form-control" value="{{ $banner->background_style }}">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label">Replace Banner Image</label>
                                                                    <input type="file" name="image" class="form-control" accept="image/*">
                                                                </div>
                                                                <div class="col-12 d-flex justify-content-end">
                                                                    <button type="submit" class="btn btn-primary">Update Banner</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted py-4">No banners found.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
