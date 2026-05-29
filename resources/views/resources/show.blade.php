@extends('layouts.public')

@section('title', $resource->title . ' | Resources | Armely')
@section('meta_description', $resource->description ?: 'Practical guides, checklists, and insights from Armely.')
@section('robots', $resource->is_noindex ? 'noindex,nofollow' : 'index,follow')

@push('styles')
<style>
.resource-detail-hero {
    background: linear-gradient(145deg, #0f274f 0%, #1f4886 55%, #2f5597 100%);
    color: #fff;
    padding: 108px 0 76px;
    position: relative;
    overflow: hidden;
}
.resource-detail-hero::after {
    content: '';
    position: absolute;
    width: 420px;
    height: 420px;
    right: -120px;
    top: -150px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.2) 0%, rgba(255, 255, 255, 0) 70%);
}
.resource-detail-title {
    font-size: 2.6rem;
    line-height: 1.15;
    font-weight: 900;
    margin-bottom: 16px;
    color: #fff;
}
.resource-detail-description {
    max-width: 860px;
    color: #ffffff !important;
    line-height: 1.7;
    font-size: 1.04rem;
    margin-bottom: 0;
}
.resource-detail-meta {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-top: 18px;
    background: rgba(255, 255, 255, 0.14);
    border: 1px solid rgba(255, 255, 255, 0.24);
    color: #f5f9ff;
    padding: 8px 12px;
    border-radius: 999px;
    font-size: 0.79rem;
    font-weight: 700;
    letter-spacing: 0.03em;
    text-transform: uppercase;
}
.resource-detail-main {
    background: linear-gradient(180deg, #f4f8ff 0%, #f8fbff 48%, #ffffff 100%);
    padding: 54px 0 84px;
}
.resource-panel {
    border: 1px solid #dce7fb;
    border-radius: 14px;
    background: #fff;
    box-shadow: 0 12px 26px rgba(17, 44, 86, 0.08);
    padding: 24px;
}
.resource-split {
    display: grid;
    grid-template-columns: minmax(0, 1.4fr) minmax(320px, 0.8fr);
    gap: 22px;
    align-items: start;
}
.resource-meta-list {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 18px;
}
.meta-chip {
    background: #edf3ff;
    border: 1px solid #d3e0f8;
    color: #24457f;
    border-radius: 999px;
    padding: 6px 12px;
    font-size: 0.78rem;
    font-weight: 800;
}
.meta-chip-click {
    background: #eef4ff;
    border-color: #cfdffa;
    color: #274a82;
}
.meta-chip-download {
    background: #edf9f2;
    border-color: #c6e9d4;
    color: #1f6b47;
}
.resource-preview {
    border-radius: 12px;
    border: 1px solid #d9e4fa;
    background: linear-gradient(145deg, #f6f9ff 0%, #eaf1ff 100%);
    overflow: hidden;
    margin-bottom: 18px;
}
.resource-preview img {
    width: 100%;
    max-height: 340px;
    object-fit: contain;
    background: #fff;
}
.resource-preview video {
    width: 100%;
    max-height: 460px;
    background: #000;
    display: block;
}
.resource-file-tile {
    display: grid;
    grid-template-columns: auto minmax(0, 1fr);
    gap: 14px;
    align-items: center;
    padding: 18px 20px;
}
.resource-file-icon {
    width: 58px;
    height: 58px;
    border-radius: 14px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.7rem;
    color: #fff;
}
.icon-pdf { background: linear-gradient(145deg, #d73838, #b62727); }
.icon-video { background: linear-gradient(145deg, #6a57d8, #503fc0); }
.icon-image { background: linear-gradient(145deg, #ea9c2f, #d57b11); }
.icon-checklist { background: linear-gradient(145deg, #3a80df, #2a61b8); }
.icon-guide { background: linear-gradient(145deg, #2f77d7, #215db0); }
.icon-default { background: linear-gradient(145deg, #5270a2, #3f5887); }
.resource-file-label {
    margin: 0;
    font-size: 1.08rem;
    color: #153869;
    font-weight: 800;
}
.resource-file-name {
    margin: 5px 0 0;
    color: #4b5f83;
    font-size: 0.92rem;
}
.folder-location {
    margin: 0 0 14px;
    border-radius: 9px;
    background: #eff4ff;
    color: #1f467f;
    border: 1px solid #d5e2f9;
    padding: 8px 12px;
    font-size: 0.8rem;
    font-weight: 700;
}
.folder-location a {
    color: #1f467f;
    text-decoration: underline;
    text-underline-offset: 2px;
    font-weight: 800;
}
.folder-location a:hover,
.folder-location a:focus {
    color: #173766;
}
.resource-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 8px;
}
.resource-btn-primary {
    background: #2f5597;
    border-color: #2f5597;
    color: #fff !important;
    font-weight: 700;
}
.resource-btn-primary:hover,
.resource-btn-primary:focus,
.resource-btn-primary:active,
.resource-btn-primary:visited {
    background: #244a86;
    border-color: #244a86;
    color: #fff !important;
}
.resource-btn-secondary {
    background: #e9f1ff !important;
    border-color: #c6d8f8 !important;
    color: #1f4583 !important;
    font-weight: 700;
}
.resource-btn-secondary:hover,
.resource-btn-secondary:focus,
.resource-btn-secondary:active,
.resource-btn-secondary:visited {
    background: #dce9ff !important;
    border-color: #b3caf2 !important;
    color: #1f4583 !important;
}
.resource-embed-wrap {
    margin-top: 16px;
    border: 1px solid #d9e4fa;
    border-radius: 12px;
    background: #fff;
    overflow: hidden;
}
.resource-embed-head {
    padding: 10px 12px;
    border-bottom: 1px solid #e2ebfb;
    background: #f4f8ff;
    color: #1f457f;
    font-size: 0.82rem;
    font-weight: 800;
    letter-spacing: 0.03em;
    text-transform: uppercase;
}
.resource-embed-body {
    min-height: 520px;
    background: #fff;
}
.resource-embed-body iframe,
.resource-embed-body img,
.resource-embed-body video {
    width: 100%;
    display: block;
    border: 0;
}
.resource-embed-body iframe {
    min-height: 520px;
}
.resource-embed-body img {
    max-height: 680px;
    object-fit: contain;
    background: #fff;
}
.resource-embed-note {
    margin: 0;
    padding: 16px;
    color: #4a6188;
    font-size: 0.92rem;
}
.related-wrap {
    margin-top: 28px;
}
.related-title {
    color: #16386b;
    font-size: 1.1rem;
    font-weight: 900;
    margin: 0 0 12px;
}
.related-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 12px;
}
.related-card {
    border: 1px solid #d7e4fb;
    border-radius: 12px;
    background: #fff;
    text-decoration: none;
    color: #173868;
    padding: 12px;
    transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
}
.related-card:hover {
    text-decoration: none;
    color: #12345f;
    transform: translateY(-1px);
    border-color: #bdd3f8;
    box-shadow: 0 10px 20px rgba(18, 46, 87, 0.1);
}
.related-card .meta {
    font-size: 0.74rem;
    font-weight: 700;
    color: #4b6288;
    margin-bottom: 4px;
}
.related-card .name {
    margin: 0;
    font-size: 0.92rem;
    font-weight: 800;
    line-height: 1.35;
}
.resource-request-panel {
    border: 1px solid #dce7fb;
    border-radius: 14px;
    background: linear-gradient(180deg, #ffffff 0%, #f7faff 100%);
    box-shadow: 0 12px 26px rgba(17, 44, 86, 0.08);
    padding: 22px;
    position: sticky;
    top: 92px;
}
.resource-request-kicker {
    color: #2f5597;
    font-size: 0.78rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    margin-bottom: 8px;
}
.resource-request-title {
    color: #16386b;
    font-size: 1.22rem;
    font-weight: 900;
    margin: 0 0 8px;
}
.resource-request-copy {
    color: #4b6187;
    line-height: 1.65;
    font-size: 0.94rem;
    margin-bottom: 16px;
}
.resource-request-form .form-group {
    margin-bottom: 14px;
}
.resource-request-form label {
    display: block;
    color: #27497f;
    font-size: 0.86rem;
    font-weight: 700;
    margin-bottom: 7px;
}
.resource-request-form .form-control,
.resource-request-form textarea {
    border: 1px solid #cbd9f4;
    border-radius: 12px;
    background: #f7faff;
    color: #193763;
    padding: 12px 14px;
    min-height: 48px;
    box-shadow: none;
}
.resource-request-form textarea {
    min-height: 112px;
    resize: vertical;
}
.resource-request-form .form-control:focus,
.resource-request-form textarea:focus {
    border-color: #2f5597;
    background: #fff;
    box-shadow: 0 0 0 4px rgba(47, 85, 151, 0.12);
}
.resource-request-note {
    margin: 10px 0 0;
    color: #5b6f91;
    font-size: 0.86rem;
    line-height: 1.55;
}
@media (max-width: 992px) {
    .resource-split {
        grid-template-columns: 1fr;
    }
    .resource-request-panel {
        position: static;
    }
    .related-grid {
        grid-template-columns: 1fr;
    }
    .resource-embed-body,
    .resource-embed-body iframe {
        min-height: 380px;
    }
}
@media (max-width: 767px) {
    .resource-detail-hero {
        padding: 88px 0 56px;
    }
    .resource-detail-title {
        font-size: 2.05rem;
    }
    .resource-panel {
        padding: 18px;
    }
    .resource-file-tile {
        padding: 16px;
    }
}
</style>
@endpush

@section('content')
<section class="resource-detail-hero">
    <div class="container">
        <h1 class="resource-detail-title">{{ $resource->title }}</h1>
        <p class="resource-detail-description">{{ $resource->description ?: 'Explore this resource for practical implementation guidance from Armely.' }}</p>
    </div>
</section>

<section class="resource-detail-main">
    <div class="container">
        @php
            $type = strtolower((string) $resource->resource_type);
            $typeIconMap = [
                'pdf' => ['fa-file-pdf-o', 'icon-pdf', 'PDF Document'],
                'video' => ['fa-play-circle', 'icon-video', 'Video Resource'],
                'image' => ['fa-file-image-o', 'icon-image', 'Image Asset'],
                'checklist' => ['fa-check-square-o', 'icon-checklist', 'Checklist'],
                'guide' => ['fa-book', 'icon-guide', 'Guide'],
            ];
            $iconConfig = $typeIconMap[$type] ?? ['fa-file-text-o', 'icon-default', 'Document'];
            $isImageFile = str_starts_with((string) ($resource->file_url ?? ''), 'data:image') || preg_match('/\.(png|jpe?g|webp|gif)$/i', (string) ($resource->file_url ?? ''));
            $isPdfFile = preg_match('/\.pdf($|\?)/i', (string) ($resource->file_url ?? ''));
            $folderCategory = trim((string) ($resource->category ?? ''));
            $folderCategoryLabel = $folderCategory !== '' ? $folderCategory : 'General';
            $folderCategoryUrl = $folderCategory !== ''
                ? route('resources.index', ['category' => $folderCategory])
                : route('resources.index');
            $folderTypeUrl = route('resources.index', array_filter([
                'category' => $folderCategory !== '' ? $folderCategory : null,
                'type' => $resource->resource_type,
            ]));
            $resourceInlineUrl = route('resources.download', ['slug' => $resource->slug, 'mode' => 'inline']);
        @endphp

        <div class="resource-split">
            <div class="resource-panel">
                @if(session('resource_request_status'))
                    <div class="alert alert-success">{{ session('resource_request_status') }}</div>
                @endif

                @if($errors->has('resource_request'))
                    <div class="alert alert-danger">{{ $errors->first('resource_request') }}</div>
                @endif

                <div class="resource-meta-list">
                    <span class="meta-chip">{{ ucfirst($resource->resource_type) }}</span>
                    @if($resource->category)
                        <span class="meta-chip">{{ $resource->category }}</span>
                    @endif
                    <span class="meta-chip">Added {{ optional($resource->created_at)->format('M d, Y') }}</span>
                    <span class="meta-chip meta-chip-click"><i class="fa fa-mouse-pointer" aria-hidden="true" style="margin-right:6px;"></i>{{ (int) ($resource->click_count ?? 0) }} clicks</span>
                    <span class="meta-chip meta-chip-download"><i class="fa fa-download" aria-hidden="true" style="margin-right:6px;"></i>{{ (int) ($resource->download_count ?? 0) }} downloads</span>
                </div>

                <p class="folder-location">📁 Folder Location: <a href="{{ $folderCategoryUrl }}">{{ $folderCategoryLabel }}</a> / <a href="{{ $folderTypeUrl }}">{{ ucfirst($resource->resource_type) }}</a></p>

                <div class="resource-preview">
                    <div class="resource-file-tile">
                        <span class="resource-file-icon {{ $iconConfig[1] }}">
                            <i class="fa {{ $iconConfig[0] }}" aria-hidden="true"></i>
                        </span>
                        <div>
                            <p class="resource-file-label">{{ $iconConfig[2] }}</p>
                            <p class="resource-file-name">{{ $resource->file_name ?: ($resource->slug . '.asset') }}</p>
                        </div>
                    </div>
                </div>

                <div class="resource-actions">
                    @if($resource->file_url)
                        <a href="{{ route('resources.download', $resource->slug) }}" class="btn resource-btn-primary">
                            {{ $resource->resource_type === 'video' ? 'Open Full Video' : 'Download / Open File' }}
                        </a>
                    @endif
                    <a href="{{ route('resources.index') }}" class="btn resource-btn-secondary">Back to repository</a>
                </div>

                @if($resource->file_url)
                    <div class="resource-embed-wrap">
                        <div class="resource-embed-head">Embedded Content Preview</div>
                        <div class="resource-embed-body">
                            @if($resource->isVideo())
                                <video controls preload="metadata" playsinline>
                                    <source src="{{ $resourceInlineUrl }}">
                                    Your browser does not support the video element.
                                </video>
                            @elseif($isImageFile)
                                <img src="{{ $resourceInlineUrl }}" alt="{{ $resource->title }}">
                            @elseif($isPdfFile)
                                <iframe src="{{ $resourceInlineUrl }}#view=FitH" title="{{ $resource->title }}"></iframe>
                            @else
                                <iframe src="{{ $resourceInlineUrl }}" title="{{ $resource->title }}"></iframe>
                                <p class="resource-embed-note">If this file cannot be previewed here due to browser or source restrictions, use the Open File button above.</p>
                            @endif
                        </div>
                    </div>
                @endif

                @if(($relatedResources ?? collect())->isNotEmpty())
                    <div class="related-wrap">
                        <h3 class="related-title">Recommended Next Resources</h3>
                        <div class="related-grid">
                            @foreach($relatedResources as $related)
                                <a href="{{ route('resources.show', $related->slug) }}" class="related-card">
                                    <div class="meta">{{ ucfirst($related->resource_type) }} @if($related->category) · {{ $related->category }} @endif</div>
                                    <p class="name">{{ $related->title }}</p>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <aside id="resource-request-form" class="resource-request-panel">
                <div class="resource-request-kicker">Request By Email</div>
                <h2 class="resource-request-title">Get this resource in your inbox</h2>
                <p class="resource-request-copy">Fill in your details and Armely will send this resource link to your email so you can review or forward it later.</p>
                <div id="resourceRequestAjaxAlert" class="alert d-none" role="alert"></div>

                <form id="resourceRequestForm" method="POST" action="{{ route('resources.request', $resource->slug) }}" class="resource-request-form">
                    @csrf

                    <div class="form-group">
                        <label for="resourceRequestName">Name</label>
                        <input id="resourceRequestName" type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        @error('name')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="resourceRequestEmail">Work Email</label>
                        <input id="resourceRequestEmail" type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                        @error('email')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="resourceRequestOrganization">Company</label>
                        <input id="resourceRequestOrganization" type="text" name="organization" class="form-control" value="{{ old('organization') }}">
                        @error('organization')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="resourceRequestRole">Job Title</label>
                        <input id="resourceRequestRole" type="text" name="job_title" class="form-control" value="{{ old('job_title') }}">
                        @error('job_title')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="resourceRequestMessage">What are you interested in?</label>
                        <textarea id="resourceRequestMessage" name="message">{{ old('message') }}</textarea>
                        @error('message')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>

                    <button type="submit" class="btn resource-btn-primary w-100">Email Me This Resource</button>
                    <p class="resource-request-note">The submitted details are recorded so the team can follow up with related content if needed.</p>
                </form>
            </aside>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('resourceRequestForm');
    if (!form) {
        return;
    }

    var submitBtn = form.querySelector('button[type="submit"]');
    var alertBox = document.getElementById('resourceRequestAjaxAlert');
    var originalBtnText = submitBtn ? submitBtn.textContent : 'Email Me This Resource';

    var showAlert = function (message, type) {
        if (!alertBox) {
            return;
        }

        alertBox.textContent = message;
        alertBox.classList.remove('d-none', 'alert-success', 'alert-danger');
        alertBox.classList.add(type === 'success' ? 'alert-success' : 'alert-danger');
    };

    var setSubmitting = function (isSubmitting) {
        if (!submitBtn) {
            return;
        }

        submitBtn.disabled = isSubmitting;
        submitBtn.textContent = isSubmitting ? 'Sending...' : originalBtnText;
    };

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        setSubmitting(true);

        var formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: formData
        })
            .then(function (response) {
                return response.json().catch(function () {
                    return {};
                }).then(function (json) {
                    return { ok: response.ok, status: response.status, json: json };
                });
            })
            .then(function (result) {
                if (!result.ok || !(result.json && result.json.success)) {
                    if (result.status === 422 && result.json && result.json.errors) {
                        var firstField = Object.keys(result.json.errors)[0];
                        var firstError = firstField && result.json.errors[firstField] && result.json.errors[firstField][0]
                            ? result.json.errors[firstField][0]
                            : 'Please check the form and try again.';
                        showAlert(firstError, 'error');
                        return;
                    }

                    showAlert((result.json && result.json.message) || 'We could not send your request right now. Please try again.', 'error');
                    return;
                }

                showAlert(result.json.message || 'Resource link sent successfully.', 'success');
                form.reset();
            })
            .catch(function () {
                showAlert('Network issue. Please try again.', 'error');
            })
            .finally(function () {
                setSubmitting(false);
            });
    });
});
</script>
@endpush
