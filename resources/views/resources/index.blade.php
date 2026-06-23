@extends('layouts.public')

@section('title', 'Resources | Armely')
@section('meta_description', 'Practical guides, checklists, and insights from Armely.')
@section('canonical_url', route('resources.index'))

@push('styles')
<style>
.resources-hero {
    background: linear-gradient(140deg, #0a224c 0%, #1a427f 55%, #2f5597 100%);
    color: #fff;
    padding: 126px 0 90px;
    position: relative;
    overflow: hidden;
}
.resources-hero::after {
    content: '';
    position: absolute;
    width: 460px;
    height: 460px;
    right: -120px;
    top: -170px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.24) 0%, rgba(255, 255, 255, 0) 70%);
}
.resources-title {
    font-size: 3rem;
    font-weight: 900;
    margin-bottom: 14px;
    line-height: 1.1;
    color: #fff;
}
.resources-subtitle {
    font-size: 1.1rem;
    color: #ffffff !important;
    max-width: 840px;
    margin-bottom: 26px;
}
.resources-availability {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(255, 255, 255, 0.14);
    border: 1px solid rgba(255, 255, 255, 0.28);
    color: #ffffff !important;
    padding: 8px 12px;
    border-radius: 999px;
    font-size: 0.79rem;
    font-weight: 700;
    letter-spacing: 0.03em;
    text-transform: uppercase;
}
.resources-stats {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 12px;
    max-width: 640px;
    margin-top: 18px;
}
.resources-stat {
    border: 1px solid rgba(255, 255, 255, 0.28);
    background: rgba(255, 255, 255, 0.14);
    border-radius: 12px;
    padding: 12px 14px;
}
.resources-stat strong {
    display: block;
    color: #fff;
    font-weight: 800;
    font-size: 1.15rem;
}
.resources-stat span {
    color: rgba(245, 250, 255, 0.95);
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}
.resources-section {
    padding: 56px 0 88px;
    background: linear-gradient(180deg, #edf4ff 0%, #f8fbff 42%, #ffffff 100%);
    position: relative;
    overflow-x: clip;
}
.resources-section::before,
.resources-section::after {
    content: '';
    position: absolute;
    border-radius: 50%;
    pointer-events: none;
}
.resources-section::before {
    width: 300px;
    height: 300px;
    right: -120px;
    top: 110px;
    background: radial-gradient(circle, rgba(47, 85, 151, 0.1), rgba(47, 85, 151, 0));
}
.resources-section::after {
    width: 260px;
    height: 260px;
    left: -110px;
    bottom: 80px;
    background: radial-gradient(circle, rgba(32, 77, 142, 0.08), rgba(32, 77, 142, 0));
}
.resources-section .container {
    position: relative;
    z-index: 1;
    max-width: min(1320px, calc(100vw - 120px));
    padding-left: 24px;
    padding-right: 24px;
}
.featured-wrap {
    margin-bottom: 20px;
    border: 1px solid #cfe1ff;
    border-radius: 16px;
    background: #fff;
    box-shadow: 0 14px 32px rgba(14, 39, 79, 0.1);
    padding: 14px;
}
.featured-title {
    font-size: 0.92rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #1b4b8f;
    font-weight: 800;
    margin: 0 0 10px;
}
.featured-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 10px;
}
.featured-card {
    border: 1px solid #d9e6ff;
    border-radius: 12px;
    background: linear-gradient(145deg, #f5f9ff, #ffffff);
    padding: 12px;
    text-decoration: none;
    color: #173766;
    transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
}
.featured-card:hover {
    text-decoration: none;
    color: #13335e;
    transform: translateY(-2px);
    border-color: #bdd3f8;
    box-shadow: 0 10px 22px rgba(20, 47, 89, 0.12);
}
.featured-card .meta {
    color: #4d6287;
    font-size: 0.75rem;
    font-weight: 700;
    margin-bottom: 4px;
}
.featured-card .name {
    margin: 0;
    font-size: 0.95rem;
    font-weight: 800;
    line-height: 1.3;
}
.repo-shell {
    border-radius: 18px;
    border: 1px solid #cfe0ff;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(2px);
    box-shadow: 0 24px 48px rgba(19, 44, 88, 0.12);
    overflow: visible;
    max-width: 100%;
}
.repo-shell.is-loading {
    opacity: 0.62;
    pointer-events: none;
    transition: opacity 0.15s ease;
}
.repo-topbar {
    background: linear-gradient(135deg, #0e2a56 0%, #1d4688 58%, #2f5597 100%);
    color: #fff !important;
    padding: 20px 22px;
    border-top-left-radius: 18px;
    border-top-right-radius: 18px;
}
.repo-title {
    font-size: 0.98rem;
    font-weight: 800;
    margin: 0;
    letter-spacing: 0.02em;
    color: #fff !important;
}
.repo-subtitle {
    margin: 0;
    color: rgba(255, 255, 255, 0.96) !important;
    font-size: 0.78rem;
    font-weight: 600;
}
.repo-meta {
    margin-top: 8px;
    color: #ffffff !important;
    font-size: 0.77rem;
    font-weight: 700;
    letter-spacing: 0.03em;
    text-transform: uppercase;
}
.repo-filter {
    --filter-control-height: 44px;
    padding: 14px 16px;
    border-top: 1px solid rgba(255, 255, 255, 0.2);
    border-bottom: 1px solid #dde8fc;
    background: linear-gradient(180deg, #f9fbff 0%, #f4f8ff 100%);
}
.filter-grid {
    display: grid;
    grid-template-columns: minmax(0, 1.8fr) repeat(3, minmax(0, 1fr)) auto;
    column-gap: 16px;
    row-gap: 8px;
    align-items: stretch;
    width: 100%;
}
.filter-cell {
    display: flex;
    align-items: stretch;
    min-width: 0;
}
.filter-cell > * {
    width: 100%;
}
.filter-cell-search {
    min-width: 0;
}
.filter-cell-category {
    padding-right: 10px;
}
.filter-cell-type {
    padding-left: 10px;
}
.filter-clear-btn {
    min-width: 90px;
    height: var(--filter-control-height);
    min-height: var(--filter-control-height);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    background: #edf3ff;
    border-color: #c8daf8;
    color: #29528f;
    font-weight: 700;
}
.filter-clear-btn:hover,
.filter-clear-btn:focus {
    background: #e1ecff;
    border-color: #b8cff5;
    color: #1f4784;
}
.filter-clear-btn,
.filter-clear-btn:visited,
.filter-clear-btn:hover,
.filter-clear-btn:focus,
.filter-clear-btn:active {
    color: #29528f !important;
}
.filter-input-wrap,
.filter-select-wrap {
    position: relative;
    display: flex;
    align-items: center;
    height: var(--filter-control-height);
}
.filter-input-wrap i,
.filter-select-wrap i {
    position: absolute;
    top: 0;
    bottom: 0;
    left: 11px;
    transform: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #607da9;
    font-size: 0.9rem;
    line-height: 1;
    width: 16px;
    text-align: center;
    pointer-events: none;
    z-index: 2;
}
.repo-filter .form-control,
.repo-filter .form-select {
    box-sizing: border-box;
    border-color: #c8daf8;
    border-radius: 10px;
    height: var(--filter-control-height);
    min-height: var(--filter-control-height);
    line-height: 1.2;
    padding-left: 36px;
    padding-top: 0;
    padding-bottom: 0;
    font-weight: 600;
}
.repo-filter .form-select {
    appearance: none;
    padding-right: 2.2rem;
    background-position: right 0.8rem center;
}
.repo-body {
    display: grid;
    grid-template-columns: 290px minmax(0, 1fr);
    min-height: 520px;
    max-width: 100%;
}
.folder-pane {
    border-right: 1px solid #e7eefc;
    background: linear-gradient(180deg, #fbfdff 0%, #f5f9ff 100%);
    padding: 16px;
    position: sticky;
    top: 90px;
    height: fit-content;
    box-shadow: inset -1px 0 0 rgba(207, 223, 245, 0.35);
}
.folder-pane h3 {
    margin: 0;
    font-size: 0.88rem;
    font-weight: 900;
    color: #193e76;
    text-transform: uppercase;
    letter-spacing: 0.08em;
}
.folder-divider {
    margin: 10px 0 12px;
    border: 0;
    border-top: 1px solid #e2eafe;
}
.folder-list {
    display: grid;
    gap: 8px;
}
.folder-tree {
    display: grid;
    gap: 8px;
}
.folder-children {
    display: grid;
    gap: 7px;
    padding-left: 14px;
    margin-top: 6px;
    border-left: 2px solid #dbe7fb;
}
.folder-item {
    border: 1px solid #d7e5ff;
    border-radius: 12px;
    background: #fff;
    padding: 10px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    color: #1f427d;
    text-decoration: none;
    font-weight: 700;
    transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease, border-color 0.2s ease;
}
.folder-toggle {
    border: 0;
    background: transparent;
    color: inherit;
    padding: 0;
    margin-left: 8px;
    width: 18px;
    height: 18px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.folder-toggle i {
    margin-right: 0;
    color: inherit;
    font-size: 0.84rem;
    transition: transform 0.2s ease;
}
.folder-tree.is-collapsed .folder-toggle i {
    transform: rotate(-90deg);
}
.folder-tree.is-collapsed .folder-children {
    display: none;
}
.folder-item-label {
    display: flex;
    align-items: center;
    min-width: 0;
    flex: 1;
}
.folder-item:hover {
    background: #eef4ff;
    color: #123465;
    text-decoration: none;
    transform: translateY(-1px);
    border-color: #bfd5fb;
    box-shadow: 0 10px 20px rgba(20, 47, 89, 0.1);
}
.folder-item.active {
    background: linear-gradient(135deg, #1f4f96, #2f5597);
    color: #fff;
    border-color: #244d8f;
    box-shadow: 0 12px 24px rgba(20, 47, 89, 0.22);
}
.folder-item i {
    color: #f4b544;
    margin-right: 8px;
}
.folder-item.active i {
    color: #ffe3a6;
}
.folder-name {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    display: block;
    max-width: 100%;
}
.folder-count {
    border-radius: 999px;
    font-size: 0.72rem;
    font-weight: 800;
    background: #edf4ff;
    color: #35598f;
    padding: 3px 8px;
}
.folder-item.active .folder-count {
    background: rgba(255, 255, 255, 0.2);
    color: #fff;
}
.repo-files {
    padding: 12px 14px 16px;
    min-width: 0;
}
.file-row {
    border: 1px solid #dce7fb;
    border-radius: 13px;
    background: #fff;
    padding: 14px;
    margin-bottom: 12px;
    display: grid;
    grid-template-columns: 74px minmax(0, 1fr) auto;
    gap: 14px;
    align-items: start;
    transition: transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease;
    min-width: 0;
}
.file-row:hover {
    transform: translateY(-2px);
    border-color: #b7cdf6;
    box-shadow: 0 14px 28px rgba(20, 47, 89, 0.13);
}
.file-thumb {
    width: 74px;
    height: 74px;
    border-radius: 12px;
    border: 1px solid #d8e5fb;
    background: linear-gradient(145deg, #f6f9ff, #ebf2ff);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    position: relative;
    box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.45), 0 8px 14px rgba(29, 62, 112, 0.08);
}
.file-thumb img,
.file-thumb video {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.file-thumb i {
    font-size: 1.6rem;
}
.thumb-placeholder {
    width: 100%;
    height: 100%;
    display: grid;
    grid-template-rows: 1fr auto;
    align-items: center;
    justify-items: center;
    padding: 8px 6px;
    text-align: center;
}
.thumb-placeholder i {
    font-size: 1.55rem;
}
.thumb-placeholder .thumb-ext {
    font-size: 0.62rem;
    font-weight: 800;
    letter-spacing: 0.05em;
    color: #315489;
    background: rgba(255, 255, 255, 0.86);
    border: 1px solid rgba(182, 203, 238, 0.95);
    border-radius: 999px;
    padding: 2px 8px;
}
.thumb-pdf {
    background: linear-gradient(150deg, #fff6f6, #ffe6e6);
}
.thumb-video {
    background: linear-gradient(150deg, #eef1ff, #dfe6ff);
}
.thumb-image {
    background: linear-gradient(150deg, #fff8ee, #ffe9cc);
}
.thumb-default {
    background: linear-gradient(150deg, #eff4ff, #e3ecff);
}
.thumb-play {
    position: absolute;
    right: 6px;
    bottom: 6px;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: rgba(20, 47, 89, 0.82);
    color: #fff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.7rem;
}
.file-main {
    min-width: 0;
}
.file-name-line {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 8px;
}
.file-icon {
    font-size: 1rem;
}
.file-icon-pdf { color: #d73737; }
.file-icon-video { color: #6b57d9; }
.file-icon-image { color: #e28c23; }
.file-icon-checklist { color: #2f77d7; }
.file-icon-guide { color: #2a6bc6; }
.file-icon-default { color: #4f6691; }
.file-title {
    color: #143665;
    font-size: 1.08rem;
    line-height: 1.3;
    margin: 0;
    font-weight: 800;
    overflow-wrap: anywhere;
}
.file-meta {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 4px;
}
.file-type,
.file-date {
    font-size: 0.71rem;
    border-radius: 999px;
    padding: 5px 10px;
    font-weight: 800;
    letter-spacing: 0.02em;
}
.file-type {
    background: #ebf3ff;
    color: #1f4784;
    border: 1px solid #d4e1f7;
}
.file-date {
    background: #f5f8ff;
    color: #4d6489;
    border: 1px solid #e0e8f8;
}
.file-engagement {
    background: #f0f5ff;
    color: #2a4f89;
    border: 1px solid #d7e3fb;
}
.file-engagement-click {
    background: #eef4ff;
    color: #274a82;
    border: 1px solid #cfdffa;
}
.file-engagement-download {
    background: #edf9f2;
    color: #1f6b47;
    border: 1px solid #c6e9d4;
}
.file-description {
    color: #4a5d7e;
    line-height: 1.65;
    font-size: 0.92rem;
    margin: 10px 0 0;
    max-width: 100%;
    overflow-wrap: anywhere;
}
.file-description p {
    margin: 0 0 0.65rem;
}
.file-description p:last-child {
    margin-bottom: 0;
}
.file-description img,
.file-description video,
.file-description iframe {
    max-width: 100%;
    height: auto;
}
.file-description table {
    display: block;
    max-width: 100%;
    overflow-x: auto;
}
.folder-location {
    margin-top: 12px;
    border-radius: 9px;
    background: #eff4ff;
    color: #1f467f;
    border: 1px solid #d5e2f9;
    padding: 6px 10px;
    font-size: 0.77rem;
    font-weight: 700;
    max-width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
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
.file-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}
.file-actions .dropdown {
    position: relative;
}
.file-actions .btn {
    white-space: nowrap;
    transition: transform 0.18s ease, box-shadow 0.18s ease, background-color 0.18s ease, border-color 0.18s ease;
}
.file-actions .btn:hover {
    transform: translateY(-1px);
}
.file-actions .dropdown-toggle::after {
    display: none;
}
.file-actions .dropdown-menu {
    display: none;
    position: absolute;
    top: calc(100% + 6px);
    right: 0;
    left: auto;
    z-index: 1080;
}
.file-actions .dropdown-menu.show {
    display: block;
}
.empty-state {
    border: 1px dashed #c8d8f3;
    border-radius: 14px;
    padding: 36px;
    text-align: center;
    background: #fff;
    color: #4f5f7b;
}
.preview-modal-body {
    min-height: 360px;
    background: #f3f7ff;
    border-radius: 10px;
    padding: 10px;
}
.preview-modal-body iframe,
.preview-modal-body video,
.preview-modal-body img {
    width: 100%;
    min-height: 320px;
    border: 0;
    border-radius: 10px;
    background: #fff;
}
@media (max-width: 1200px) {
    .featured-grid {
        grid-template-columns: 1fr;
    }
    .filter-grid {
        grid-template-columns: minmax(0, 1.6fr) repeat(3, minmax(0, 1fr));
    }
    .filter-cell:last-child {
        grid-column: 1 / -1;
    }
}
@media (max-width: 992px) {
    .resources-stats {
        max-width: none;
    }
    .repo-body {
        grid-template-columns: 1fr;
    }
    .folder-pane {
        position: static;
        border-right: 0;
        border-bottom: 1px solid #e0e9fb;
    }
    .filter-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px;
    }
    .filter-cell-category,
    .filter-cell-type {
        padding-left: 0;
        padding-right: 0;
    }
    .filter-cell-search {
        grid-column: 1 / -1;
    }
    .file-row {
        grid-template-columns: 74px minmax(0, 1fr);
        padding: 12px;
    }
    .file-actions {
        grid-column: 1 / -1;
        justify-content: flex-end;
    }
}
@media (max-width: 767px) {
    .resources-hero {
        padding: 92px 0 58px;
    }
    .resources-title {
        font-size: 2.1rem;
    }
    .resources-stats {
        grid-template-columns: 1fr;
    }
    .file-row {
        grid-template-columns: 1fr;
    }
    .file-thumb {
        width: 64px;
        height: 64px;
    }
    .file-actions {
        width: 100%;
        justify-content: stretch;
    }
    .file-actions .btn {
        flex: 1;
    }
    .filter-grid {
        grid-template-columns: 1fr;
    }
    .filter-clear-btn {
        width: 100%;
    }
    .repo-topbar {
        padding: 16px;
    }
    .repo-filter {
        padding: 12px;
    }
    .repo-shell {
        overflow: hidden;
    }
    .resources-section .container {
        max-width: calc(100vw - 36px);
        padding-left: 12px;
        padding-right: 12px;
    }
}
</style>
@endpush

@section('content')
<section class="resources-section">
    <div class="container">
        @if(($featuredResources ?? collect())->isNotEmpty())
            <div class="featured-wrap">
                <p class="featured-title">Featured Resources</p>
                <div class="featured-grid">
                    @foreach($featuredResources as $featured)
                        @php
                            $featuredType = strtolower((string) $featured->resource_type);
                            $featuredRouteName = $featuredType === 'pdf' || str_contains($featuredType, 'white') ? 'whitepapers.show' : 'resources.show';
                        @endphp
                        <a href="{{ route($featuredRouteName, $featured->slug) }}" class="featured-card">
                            <div class="meta">{{ ucfirst($featured->resource_type) }} @if($featured->category) · {{ $featured->category }} @endif</div>
                            <p class="name">{{ $featured->title }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="repo-shell">
            <div class="repo-topbar d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <p class="repo-title">Armely Resource Repository</p>
                    <p class="repo-subtitle">Organized by folder hierarchy, content type, and publishing status</p>
                    <p class="repo-meta">{{ $resources->total() }} matching resources</p>
                </div>
            </div>

            <form id="resourceFilterForm" class="repo-filter" method="GET" action="{{ route('resources.index') }}">
                <div class="filter-grid">
                    <div class="filter-cell filter-cell-search">
                        <div class="filter-input-wrap">
                            <i class="fa fa-search" aria-hidden="true"></i>
                            <input id="resourceSearchInput" type="text" name="q" class="form-control" placeholder="Search resources" value="{{ $search }}">
                        </div>
                    </div>
                    <div class="filter-cell filter-cell-category">
                        <div class="filter-select-wrap">
                            <i class="fa fa-folder-open" aria-hidden="true"></i>
                            <select id="resourceCategorySelect" name="category" class="form-select no-nice-select">
                                <option value="">All folders</option>
                                @foreach($categories as $availableCategory)
                                    <option value="{{ $availableCategory }}" {{ $selectedCategory === $availableCategory ? 'selected' : '' }}>
                                        {{ $availableCategory }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="filter-cell filter-cell-type">
                        <div class="filter-select-wrap">
                            <i class="fa fa-file-text-o" aria-hidden="true"></i>
                            <select id="resourceTypeSelect" name="type" class="form-select no-nice-select">
                                <option value="">All types</option>
                                @foreach($types as $availableType)
                                    <option value="{{ $availableType }}" {{ $selectedType === $availableType ? 'selected' : '' }}>
                                        {{ ucfirst($availableType) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="filter-cell">
                        <div class="filter-select-wrap">
                            <i class="fa fa-sort" aria-hidden="true"></i>
                            <select id="resourceSortSelect" name="sort" class="form-select no-nice-select">
                                <option value="newest" {{ $selectedSort === 'newest' ? 'selected' : '' }}>Newest</option>
                                <option value="updated" {{ $selectedSort === 'updated' ? 'selected' : '' }}>Recently updated</option>
                                <option value="alphabetical" {{ $selectedSort === 'alphabetical' ? 'selected' : '' }}>Alphabetical</option>
                                <option value="featured" {{ $selectedSort === 'featured' ? 'selected' : '' }}>Featured first</option>
                            </select>
                        </div>
                    </div>
                    <div class="filter-cell">
                        <a class="btn filter-clear-btn" href="{{ route('resources.index') }}">Clear</a>
                    </div>
                </div>
            </form>

            @php
                $folderCounts = collect($categoryCounts ?? []);
                $allResourcesCount = (int) $folderCounts->sum();
            @endphp

            <div class="repo-body">
                <aside class="folder-pane">
                    <h3>Folders</h3>
                    <hr class="folder-divider">
                    <div class="folder-list">
                        <div id="resourceFolderTree" class="folder-tree {{ $selectedCategory === '' ? '' : '' }}">
                            <a class="folder-item {{ $selectedCategory === '' ? 'active' : '' }}" href="{{ route('resources.index', array_filter(['q' => $search, 'type' => $selectedType, 'sort' => $selectedSort])) }}">
                                <span class="folder-item-label"><i class="fa fa-folder-open" aria-hidden="true"></i><span class="folder-name">All Resources</span></span>
                                <span class="d-inline-flex align-items-center gap-2">
                                    <span class="folder-count">{{ $allResourcesCount }}</span>
                                    <button type="button" class="folder-toggle" aria-label="Toggle folder hierarchy" aria-expanded="true">
                                        <i class="fa fa-chevron-down" aria-hidden="true"></i>
                                    </button>
                                </span>
                            </a>
                            <div class="folder-children">
                                @foreach($folderCounts as $folder => $count)
                                    <a class="folder-item {{ $selectedCategory === $folder ? 'active' : '' }}" href="{{ route('resources.index', array_filter(['q' => $search, 'type' => $selectedType, 'sort' => $selectedSort, 'category' => $folder])) }}">
                                        <span class="folder-item-label"><i class="fa fa-folder" aria-hidden="true"></i><span class="folder-name">{{ $folder }}</span></span>
                                        <span class="folder-count">{{ $count }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </aside>

                <div class="repo-files">
                    @if($resources->isEmpty())
                        <div class="empty-state">
                            <h4>No resources found</h4>
                            <p class="mb-0">Try adjusting filters, folder, or search terms.</p>
                        </div>
                    @else
                        @foreach($resources as $resource)
                                @php
                                    $type = strtolower((string) $resource->resource_type);
                                    $detailRouteName = $type === 'pdf' || str_contains($type, 'white') ? 'whitepapers.show' : 'resources.show';
                                    $downloadRouteName = $type === 'pdf' || str_contains($type, 'white') ? 'whitepapers.download' : 'resources.download';
                                    $iconMap = [
                                        'pdf' => ['fa-file-pdf-o', 'file-icon-pdf', 'PDF Document'],
                                        'video' => ['fa-play-circle', 'file-icon-video', 'Video Resource'],
                                        'image' => ['fa-file-image-o', 'file-icon-image', 'Image Asset'],
                                        'checklist' => ['fa-check-square-o', 'file-icon-checklist', 'Checklist'],
                                        'guide' => ['fa-book', 'file-icon-guide', 'Guide'],
                                    ];
                                    $iconConfig = $iconMap[$type] ?? ['fa-file-text-o', 'file-icon-default', 'Document'];
                                    $fileUrl = (string) ($resource->file_url ?? '');
                                    $isImagePreview = str_starts_with($fileUrl, 'data:image') || preg_match('/\.(png|jpe?g|webp|gif)$/i', $fileUrl);
                                    $isVideoPreview = $type === 'video';
                                    $requiresRequestForFullContent = $type === 'pdf';
                                    $thumbImage = $resource->thumbnail_url ?: ($isImagePreview ? $fileUrl : null);
                                    $folderCategory = trim((string) ($resource->category ?? ''));
                                    $folderCategoryLabel = $folderCategory !== '' ? $folderCategory : 'General';
                                    $folderTypeLabel = ucfirst((string) $resource->resource_type);
                                    $folderCategoryUrl = $folderCategory !== ''
                                        ? route('resources.index', array_filter([
                                            'category' => $folderCategory,
                                            'sort' => $selectedSort,
                                        ]))
                                        : route('resources.index', array_filter([
                                            'sort' => $selectedSort,
                                        ]));
                                    $folderTypeUrl = route('resources.index', array_filter([
                                        'category' => $folderCategory !== '' ? $folderCategory : null,
                                        'type' => $resource->resource_type,
                                        'sort' => $selectedSort,
                                    ]));
                                @endphp
                            <article class="file-row">
                                <div class="file-thumb">
                                    @if($thumbImage)
                                        <img src="{{ $thumbImage }}" alt="{{ $resource->title }}">
                                        @if($isVideoPreview)
                                            <span class="thumb-play"><i class="fa fa-play" aria-hidden="true"></i></span>
                                        @endif
                                    @elseif($type === 'pdf')
                                        <div class="thumb-placeholder thumb-pdf">
                                            <i class="fa {{ $iconConfig[0] }} {{ $iconConfig[1] }}" aria-hidden="true"></i>
                                            <span class="thumb-ext">PDF</span>
                                        </div>
                                    @elseif($type === 'video')
                                        <div class="thumb-placeholder thumb-video">
                                            <i class="fa {{ $iconConfig[0] }} {{ $iconConfig[1] }}" aria-hidden="true"></i>
                                            <span class="thumb-ext">VIDEO</span>
                                        </div>
                                    @elseif($type === 'image')
                                        <div class="thumb-placeholder thumb-image">
                                            <i class="fa {{ $iconConfig[0] }} {{ $iconConfig[1] }}" aria-hidden="true"></i>
                                            <span class="thumb-ext">IMAGE</span>
                                        </div>
                                    @else
                                        <div class="thumb-placeholder thumb-default">
                                            <i class="fa {{ $iconConfig[0] }} {{ $iconConfig[1] }}" aria-hidden="true"></i>
                                            <span class="thumb-ext">FILE</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="file-main">
                                    <div class="file-name-line">
                                        <i class="fa {{ $iconConfig[0] }} {{ $iconConfig[1] }} file-icon" aria-hidden="true"></i>
                                        <h2 class="file-title">{{ $resource->title }}</h2>
                                    </div>
                                    <div class="file-meta">
                                        <span class="file-type">{{ $iconConfig[2] }}</span>
                                        <span class="file-date">Added {{ optional($resource->created_at)->format('M d, Y') }}</span>
                                        <span class="file-date file-engagement file-engagement-click"><i class="fa fa-mouse-pointer" aria-hidden="true" style="margin-right:6px;"></i>{{ (int) ($resource->click_count ?? 0) }} clicks</span>
                                        <span class="file-date file-engagement file-engagement-download"><i class="fa fa-download" aria-hidden="true" style="margin-right:6px;"></i>{{ (int) ($resource->download_count ?? 0) }} downloads</span>
                                    </div>
                                    <div class="file-description">
                                        @php
                                            $descriptionHtml = trim((string) ($resource->description ?? ''));
                                        @endphp
                                        @if($descriptionHtml !== '')
                                            {!! html_entity_decode($descriptionHtml, ENT_QUOTES | ENT_HTML5, 'UTF-8') !!}
                                        @else
                                            <p>Practical implementation guidance from Armely.</p>
                                        @endif
                                    </div>
                                    <div class="folder-location">📁 Folder Location: <a href="{{ $folderCategoryUrl }}">{{ $folderCategoryLabel }}</a> / <a href="{{ $folderTypeUrl }}">{{ $folderTypeLabel }}</a></div>
                                </div>

                                <div class="file-actions">
                                    <a class="btn default-background text-light" href="{{ route($detailRouteName, $resource->slug) }}">View Details</a>
                                    <div class="dropdown">
                                        <button class="btn btn-outline-primary dropdown-toggle resource-actions-toggle" type="button" aria-expanded="false" aria-label="Resource actions">
                                            <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-right">
                                            <li><a class="dropdown-item preview-resource-btn" href="#" data-title="{{ $resource->title }}" data-type="{{ $resource->resource_type }}" data-url="{{ route($downloadRouteName, ['slug' => $resource->slug, 'mode' => 'inline']) }}">Preview</a></li>
                                            <li><a class="dropdown-item copy-link-btn" href="#" data-url="{{ route($detailRouteName, $resource->slug) }}">Copy Link</a></li>
                                            <li><a class="dropdown-item" href="{{ route($detailRouteName, $resource->slug) }}">Open Resource</a></li>
                                            @if($resource->file_url)
                                                @if($requiresRequestForFullContent)
                                                    <li><a class="dropdown-item" href="{{ route($detailRouteName, $resource->slug) }}#resource-request-form">Request Full Contents</a></li>
                                                @else
                                                    <li><a class="dropdown-item" href="{{ route($downloadRouteName, $resource->slug) }}">Download</a></li>
                                                @endif
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </article>
                        @endforeach

                        <div class="mt-3">
                            {{ $resources->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="resourcePreviewModal" tabindex="-1" aria-labelledby="resourcePreviewLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header">
                <h5 class="modal-title" id="resourcePreviewLabel">Resource Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body preview-modal-body" id="resourcePreviewBody"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let searchDebounce;
    let isLoading = false;

    const previewModalEl = document.getElementById('resourcePreviewModal');
    const previewBody = document.getElementById('resourcePreviewBody');
    const previewTitle = document.getElementById('resourcePreviewLabel');
    const previewModal = previewModalEl ? new bootstrap.Modal(previewModalEl) : null;

    const getShell = function () {
        return document.querySelector('.repo-shell');
    };

    const setLoading = function (loading) {
        const shell = getShell();
        if (!shell) {
            return;
        }
        shell.classList.toggle('is-loading', loading);
    };

    const syncFiltersFromUrl = function () {
        const params = new URLSearchParams(window.location.search);
        const search = document.getElementById('resourceSearchInput');
        const category = document.getElementById('resourceCategorySelect');
        const type = document.getElementById('resourceTypeSelect');
        const sort = document.getElementById('resourceSortSelect');

        if (search) search.value = params.get('q') || '';
        if (category) category.value = params.get('category') || '';
        if (type) type.value = params.get('type') || '';
        if (sort) sort.value = params.get('sort') || 'newest';
    };

    const loadResources = async function (url, pushState) {
        const shell = getShell();
        if (!shell || isLoading) {
            return;
        }

        isLoading = true;
        setLoading(true);

        try {
            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                window.location.href = url;
                return;
            }

            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const nextShell = doc.querySelector('.repo-shell');

            if (!nextShell) {
                window.location.href = url;
                return;
            }

            shell.innerHTML = nextShell.innerHTML;

            if (pushState) {
                history.pushState({ resources: true }, '', url);
            }

            syncFiltersFromUrl();
        } catch (error) {
            window.location.href = url;
        } finally {
            isLoading = false;
            setLoading(false);
        }
    };

    const loadFromCurrentFilters = function () {
        const form = document.getElementById('resourceFilterForm');
        if (!form) {
            return;
        }

        const action = form.getAttribute('action') || window.location.pathname;
        const params = new URLSearchParams(new FormData(form));
        const url = params.toString() ? action + '?' + params.toString() : action;
        loadResources(url, true);
    };

    syncFiltersFromUrl();

    document.addEventListener('submit', function (event) {
        const form = event.target.closest('#resourceFilterForm');
        if (!form) {
            return;
        }
        event.preventDefault();
        loadFromCurrentFilters();
    });

    document.addEventListener('input', function (event) {
        const search = event.target.closest('#resourceSearchInput');
        if (!search) {
            return;
        }
        window.clearTimeout(searchDebounce);
        searchDebounce = window.setTimeout(loadFromCurrentFilters, 320);
    });

    document.addEventListener('change', function (event) {
        const control = event.target.closest('#resourceCategorySelect, #resourceTypeSelect, #resourceSortSelect');
        if (!control) {
            return;
        }
        loadFromCurrentFilters();
    });

    document.addEventListener('click', function (event) {
        const link = event.target.closest('a');
        if (!link) {
            return;
        }

        const shouldHandleInPlace = link.classList.contains('filter-clear-btn') || link.closest('.folder-pane') || link.closest('.pagination');
        if (!shouldHandleInPlace) {
            return;
        }

        const href = link.getAttribute('href');
        if (!href || href.startsWith('#') || link.target === '_blank') {
            return;
        }

        event.preventDefault();
        loadResources(href, true);
    });

    window.addEventListener('popstate', function () {
        loadResources(window.location.href, false);
    });

    document.addEventListener('click', function (event) {
        const button = event.target.closest('.preview-resource-btn');
        if (!button) {
            return;
        }

        event.preventDefault();
        if (!previewModal || !previewBody || !previewTitle) {
            return;
        }

        const title = button.getAttribute('data-title') || 'Resource Preview';
        const typeValue = (button.getAttribute('data-type') || '').toLowerCase();
        const url = button.getAttribute('data-url') || '';

        previewTitle.textContent = title;

        if (!url) {
            previewBody.innerHTML = '<p class="text-muted mb-0">Preview unavailable for this resource.</p>';
            previewModal.show();
            return;
        }

        if (typeValue === 'video') {
            previewBody.innerHTML = '<video controls preload="metadata" playsinline><source src="' + url + '">Your browser does not support the video element.</video>';
        } else if (typeValue === 'image') {
            previewBody.innerHTML = '<img src="' + url + '" alt="' + title.replace(/"/g, '&quot;') + '">';
        } else if (typeValue === 'pdf') {
            previewBody.innerHTML = '<iframe src="' + url + '#view=FitH"></iframe>';
        } else {
            previewBody.innerHTML = '<iframe src="' + url + '"></iframe>';
        }

        previewModal.show();
    });

    document.addEventListener('click', async function (event) {
        const button = event.target.closest('.copy-link-btn');
        if (!button) {
            return;
        }

        event.preventDefault();
        const url = button.getAttribute('data-url') || '';
        if (!url) {
            return;
        }

        try {
            await navigator.clipboard.writeText(url);
            button.textContent = 'Copied';
        } catch (error) {
            const helper = document.createElement('textarea');
            helper.value = url;
            document.body.appendChild(helper);
            helper.select();
            document.execCommand('copy');
            document.body.removeChild(helper);
            button.textContent = 'Copied';
        }

        window.setTimeout(function () {
            button.textContent = 'Copy Link';
        }, 1200);
    });

    // Custom action menu toggle for consistent behavior across Bootstrap versions.
    const closeActionMenus = function () {
        document.querySelectorAll('.file-actions .dropdown.show').forEach(function (dropdown) {
            dropdown.classList.remove('show');
        });
        document.querySelectorAll('.file-actions .dropdown-menu.show').forEach(function (menu) {
            menu.classList.remove('show');
        });
        document.querySelectorAll('.resource-actions-toggle').forEach(function (toggle) {
            toggle.setAttribute('aria-expanded', 'false');
        });
    };

    document.addEventListener('click', function (event) {
        const folderToggle = event.target.closest('.folder-toggle');
        if (folderToggle) {
            event.preventDefault();
            event.stopPropagation();

            const folderTree = folderToggle.closest('.folder-tree');
            if (folderTree) {
                folderTree.classList.toggle('is-collapsed');
                const expanded = !folderTree.classList.contains('is-collapsed');
                folderToggle.setAttribute('aria-expanded', expanded ? 'true' : 'false');
            }
            return;
        }

        const toggle = event.target.closest('.resource-actions-toggle');
        if (!toggle) {
            if (!event.target.closest('.file-actions .dropdown')) {
                closeActionMenus();
            }
            return;
        }

        event.preventDefault();
        event.stopPropagation();

        const dropdown = toggle.closest('.dropdown');
        const menu = dropdown ? dropdown.querySelector('.dropdown-menu') : null;
        if (!dropdown || !menu) {
            return;
        }

        const shouldOpen = !menu.classList.contains('show');
        closeActionMenus();

        if (shouldOpen) {
            dropdown.classList.add('show');
            menu.classList.add('show');
            toggle.setAttribute('aria-expanded', 'true');
        }
    });

    document.addEventListener('click', function (event) {
        if (event.target.closest('.file-actions .dropdown-menu .dropdown-item')) {
            closeActionMenus();
        }
    });
});
</script>
@endpush
