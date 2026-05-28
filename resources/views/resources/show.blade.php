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
    border-color: #2f5597;
    color: #2f5597 !important;
    font-weight: 700;
}
.resource-btn-secondary:hover,
.resource-btn-secondary:focus,
.resource-btn-secondary:active,
.resource-btn-secondary:visited {
    background: #e9f1ff;
    border-color: #2f5597;
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
@media (max-width: 992px) {
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
        @endphp

        <div class="resource-panel">
            <div class="resource-meta-list">
                <span class="meta-chip">{{ ucfirst($resource->resource_type) }}</span>
                @if($resource->category)
                    <span class="meta-chip">{{ $resource->category }}</span>
                @endif
                <span class="meta-chip">Added {{ optional($resource->created_at)->format('M d, Y') }}</span>
            </div>

            <p class="folder-location">📁 Folder Location: {{ $resource->category ?: 'General' }} / {{ ucfirst($resource->resource_type) }}</p>

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
                    <a href="{{ $resource->file_url }}" target="_blank" rel="noopener" class="btn resource-btn-primary">
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
                                <source src="{{ $resource->file_url }}">
                                Your browser does not support the video element.
                            </video>
                        @elseif($isImageFile)
                            <img src="{{ $resource->file_url }}" alt="{{ $resource->title }}">
                        @elseif($isPdfFile)
                            <iframe src="{{ $resource->file_url }}#view=FitH" title="{{ $resource->title }}"></iframe>
                        @else
                            <iframe src="{{ $resource->file_url }}" title="{{ $resource->title }}"></iframe>
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
    </div>
</section>
@endsection
