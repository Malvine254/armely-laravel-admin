@extends('admin.layouts.admin')

@section('title', 'Resources Management')
@section('page-title', 'Resources Management')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" integrity="sha384-ok3J6xA9oQqai5C9ytYveFsBeKgoGk4T+NExsr6hoIKjZdv9SJcmx2mafwUWRNf9" crossorigin="anonymous">
<style>
    .resources-page-title {
        margin-bottom: 1rem;
    }

    .resources-page-title h2 {
        font-size: 1.45rem;
        color: #1f3f80;
        margin-bottom: 0.35rem;
        font-weight: 700;
    }

    .resources-page-title p {
        font-size: 0.9rem;
        color: #667085;
        margin-bottom: 0;
    }

    .resources-stats .admin-stats-card {
        border: 1px solid #e5e7eb !important;
        border-radius: 14px;
        box-shadow: 0 8px 22px rgba(15, 23, 42, 0.06);
        transition: box-shadow 0.2s ease, border-color 0.2s ease, transform 0.2s ease;
        background: #fff;
        overflow: hidden;
    }

    .resources-stats .admin-stats-card:hover {
        border-color: #cbd5e1 !important;
        box-shadow: 0 14px 30px rgba(15, 23, 42, 0.1);
        transform: translateY(-1px);
    }

    .resources-stats .admin-stats-card::before {
        content: '';
        display: block;
        height: 3px;
        background: #2f5597;
    }

    .resources-stats .admin-stats-card.border-success::before {
        background: #0891b2;
    }

    .resources-stats .admin-stats-card.border-danger::before {
        background: #d97706;
    }

    .resources-stats .admin-stats-card.border-secondary::before {
        background: #475569;
    }

    .resources-stats .stat-icon-box {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 0.8rem;
        font-size: 1rem;
        border: 1px solid #dbe7ff;
        background: #eef4ff !important;
        color: #2f5597 !important;
    }

    .bg-soft-primary,
    .bg-soft-success,
    .bg-soft-danger,
    .bg-soft-secondary {
        background: #eef4ff !important;
        color: #2f5597 !important;
    }

    .resources-table-card {
        border-radius: 14px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 8px 22px rgba(15, 23, 42, 0.06);
        overflow: hidden;
        background: #fff;
    }

    .resources-table-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #e5e7eb;
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    }

    .resources-table-header h5 {
        font-size: 1rem;
        color: #111827;
    }

    .resources-table-header .section-icon {
        width: 34px;
        height: 34px;
        border-radius: 9px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #eef4ff;
        color: #2f5597;
        border: 1px solid #dbe7ff;
        flex: 0 0 auto;
    }

    .resources-table-card .table-responsive {
        margin: 0;
        padding: 0;
    }

    #resourcesTable {
        width: 100% !important;
        margin: 0 !important;
        border-collapse: separate;
        border-spacing: 0;
    }

    #resourcesTable thead th {
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.74rem;
        letter-spacing: 0.04em;
        padding: 0.9rem 1.15rem;
        border-bottom: 1px solid #e5e7eb;
        white-space: nowrap;
    }

    #resourcesTable tbody tr:hover {
        background-color: #f8fafc;
    }

    #resourcesTable tbody td {
        padding: 0.95rem 1.15rem;
        vertical-align: middle;
        border-bottom: 1px solid #eef2f7;
        color: #334155;
    }

    #resourcesTable tbody tr:last-child td {
        border-bottom: 0;
    }

    .resource-avatar {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
        color: #fff;
        font-size: 1rem;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2);
    }

    .resource-avatar-pdf { background: linear-gradient(135deg, #d73838 0%, #b62727 100%); }
    .resource-avatar-video { background: linear-gradient(135deg, #6a57d8 0%, #503fc0 100%); }
    .resource-avatar-image { background: linear-gradient(135deg, #ea9c2f 0%, #d57b11 100%); }
    .resource-avatar-checklist { background: linear-gradient(135deg, #3a80df 0%, #2a61b8 100%); }
    .resource-avatar-guide { background: linear-gradient(135deg, #2f77d7 0%, #215db0 100%); }
    .resource-avatar-default { background: linear-gradient(135deg, #526b92 0%, #3f5887 100%); }

    .resource-title-block .resource-title {
        color: #111827;
        font-weight: 700;
        line-height: 1.25;
        margin-bottom: 0.15rem;
    }

    .resource-title-block .resource-slug {
        color: #64748b;
        font-size: 0.78rem;
        word-break: break-word;
    }

    .resource-title-block .resource-engagement {
        margin-top: 0.25rem;
        color: #475569;
        font-size: 0.78rem;
        display: flex;
        align-items: center;
        gap: 0.7rem;
    }

    .resource-type-badge,
    .resource-category-badge,
    .resource-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        min-height: 30px;
        font-weight: 700;
        letter-spacing: 0;
        border-radius: 999px;
        padding: 0.35rem 0.75rem;
    }

    .resource-status-stack {
        display: flex;
        flex-wrap: wrap;
        gap: 0.35rem;
    }

    .resource-link-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        border-radius: 999px;
        border: 1px solid #dbe7ff;
        background: #eef4ff;
        color: #2f5597;
        padding: 0.45rem 0.7rem;
        text-decoration: none;
        font-weight: 700;
        font-size: 0.82rem;
        transition: background-color 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
    }

    .resource-link-chip:hover {
        background: #e1ecff;
        color: #1f4784;
        text-decoration: none;
        transform: translateY(-1px);
        box-shadow: 0 8px 14px rgba(47, 85, 151, 0.12);
    }

    .admin-action-group {
        border: 1px solid #e5e7eb;
        background: #fff;
        box-shadow: 0 3px 10px rgba(15, 23, 42, 0.06) !important;
        border-radius: 10px;
        overflow: hidden;
        display: inline-flex;
        align-items: stretch;
    }

    .admin-action-group .btn,
    .admin-action-group .action-link {
        width: 36px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 0 !important;
        border-radius: 0;
        text-decoration: none;
        color: #334155;
        background: #fff;
    }

    .admin-action-group .btn + form .btn,
    .admin-action-group form + .btn,
    .admin-action-group .action-link + .action-link,
    .admin-action-group .action-link + form .btn {
        border-left: 1px solid #e5e7eb !important;
    }

    .admin-action-group .btn:hover,
    .admin-action-group .action-link:hover {
        background: #eef4ff;
        color: #1f4784;
    }

    .admin-action-group .text-danger,
    .admin-action-group .action-link.text-danger {
        color: #b91c1c !important;
    }

    .resources-modal .modal-content {
        border: none;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.28);
        overflow: hidden;
    }

    .resources-modal.modal {
        z-index: 9999 !important;
    }

    .resources-modal + .modal-backdrop {
        z-index: 9998 !important;
    }

    .resources-modal .modal-header {
        background: linear-gradient(135deg, #2f5597 0%, #1e3a6b 100%);
        color: #fff;
        padding: 1.35rem 1.75rem;
        border-bottom: none;
    }

    .resources-modal .modal-title {
        color: #fff;
        font-weight: 700;
        font-size: 1.35rem;
    }

    .resources-modal .btn-close {
        filter: brightness(0) invert(1);
        opacity: 0.85;
    }

    .resources-modal .modal-body {
        padding: 1.5rem;
        background: #f8fafc;
    }

    .resources-modal .modal-footer {
        background: #fff;
        border-top: 1px solid #e9ecef;
        padding: 1rem 1.5rem;
    }

    .resources-modal .resource-form-panel {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 1.25rem;
    }

    .resources-modal .form-label,
    .resource-form-panel .form-label {
        font-weight: 600;
        color: #344054;
    }

    .resources-modal .form-control,
    .resources-modal .form-select {
        border: 1px solid #d6deea;
        border-radius: 12px;
        min-height: 44px;
        box-shadow: none;
    }

    .resources-modal .form-control:focus,
    .resources-modal .form-select:focus {
        border-color: #2f5597;
        box-shadow: 0 0 0 4px rgba(47, 85, 151, 0.12);
    }

    .resources-modal .btn-primary {
        background: linear-gradient(135deg, #2f5597 0%, #1e3a6b 100%);
        border: 0;
        box-shadow: 0 8px 18px rgba(47, 85, 151, 0.22);
    }

    .resources-modal .btn-outline-secondary {
        border-color: #d6deea;
        color: #344054;
        background: #fff;
    }

    .resources-modal .btn-outline-secondary:hover {
        background: #f8fafc;
    }

    .resources-modal .alert {
        border-radius: 14px;
    }

    @media (max-width: 768px) {
        .resources-page-title h2 {
            font-size: 1.2rem;
        }

        .resources-table-header {
            gap: 0.75rem;
        }

        .resource-link-chip {
            width: 100%;
            justify-content: center;
        }

        .admin-action-group {
            width: 100%;
            justify-content: space-between;
        }

        .admin-action-group .btn,
        .admin-action-group .action-link {
            flex: 1;
        }

        #resourcesTable tbody td,
        #resourcesTable thead th {
            padding-left: 0.85rem;
            padding-right: 0.85rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-3">
    <div class="resources-page-title d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h2>Resources Management</h2>
            <p>Manage public resource pages, files, and shareable links.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.resource-categories.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-tags me-2"></i>Manage Categories
            </a>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addResourceModal">
                <i class="fas fa-plus me-2"></i>Add Resource
            </button>
        </div>
    </div>

    <div class="row g-3 mb-4 resources-stats">
        <div class="col-md-6 col-lg-3">
            <div class="card admin-stats-card border-start border-4 border-primary">
                <div class="card-body">
                    <div class="stat-icon-box bg-soft-primary text-primary">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <p class="text-muted mb-1 fw-bold small text-uppercase">Total Resources</p>
                    <h3 class="mb-0 fw-bold">{{ $stats['total'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card admin-stats-card border-start border-4 border-success">
                <div class="card-body">
                    <div class="stat-icon-box bg-soft-success text-success">
                        <i class="fas fa-circle-check"></i>
                    </div>
                    <p class="text-muted mb-1 fw-bold small text-uppercase">Published</p>
                    <h3 class="mb-0 fw-bold text-success">{{ $stats['published'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card admin-stats-card border-start border-4 border-danger">
                <div class="card-body">
                    <div class="stat-icon-box bg-soft-danger text-danger">
                        <i class="fas fa-file-circle-plus"></i>
                    </div>
                    <p class="text-muted mb-1 fw-bold small text-uppercase">Drafts</p>
                    <h3 class="mb-0 fw-bold text-danger">{{ $stats['draft'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card admin-stats-card border-start border-4 border-secondary">
                <div class="card-body">
                    <div class="stat-icon-box bg-soft-secondary text-secondary">
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="text-muted mb-1 fw-bold small text-uppercase">Featured</p>
                    <h3 class="mb-0 fw-bold text-secondary">{{ $stats['featured'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="resources-table-card">
        <div class="resources-table-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <span class="section-icon"><i class="fas fa-folder-open"></i></span>
                <div>
                    <h5 class="mb-0 fw-bold">Resource Library</h5>
                    <div class="small text-muted">Search, share, edit, and manage public resources.</div>
                </div>
            </div>
            <span class="badge bg-soft-primary text-primary rounded-pill px-3 py-2">{{ $resources->total() }} records</span>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="resourcesTable">
                    <thead>
                        <tr>
                            <th style="width: 28%;">Resource</th>
                            <th>Type</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Engagement</th>
                            <th>Updated</th>
                            <th>Public Link</th>
                            <th class="text-end px-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($resources as $resource)
                            @php
                                $type = strtolower((string) $resource->resource_type);
                                $publicLink = $shareBaseUrl . (($type === 'pdf' || str_contains($type, 'white')) ? '/whitepapers/' : '/resources/') . $resource->slug;
                                $iconMap = [
                                    'pdf' => ['fas fa-file-pdf', 'resource-avatar-pdf', 'PDF'],
                                    'video' => ['fas fa-circle-play', 'resource-avatar-video', 'Video'],
                                    'image' => ['fas fa-image', 'resource-avatar-image', 'Image'],
                                    'checklist' => ['fas fa-list-check', 'resource-avatar-checklist', 'Checklist'],
                                    'guide' => ['fas fa-book-open', 'resource-avatar-guide', 'Guide'],
                                ];
                                $icon = $iconMap[$type] ?? ['fas fa-file-lines', 'resource-avatar-default', 'File'];
                                $resourceEditPayload = [
                                    'id' => $resource->id,
                                    'title' => $resource->title,
                                    'slug' => $resource->slug,
                                    'description' => $resource->description,
                                    'resource_type' => $resource->resource_type,
                                    'category_id' => $resource->category_id,
                                    'is_published' => (bool) $resource->is_published,
                                    'is_featured' => (bool) $resource->is_featured,
                                    'is_noindex' => (bool) $resource->is_noindex,
                                ];
                            @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="resource-avatar {{ $icon[1] }}">
                                            <i class="{{ $icon[0] }}"></i>
                                        </span>
                                        <div class="resource-title-block">
                                            <div class="resource-title">{{ $resource->title }}</div>
                                            <div class="resource-slug">/{{ $resource->slug }}</div>
                                            <div class="resource-engagement">
                                                <span><i class="fas fa-mouse-pointer" style="margin-right:6px;"></i>{{ (int) ($resource->click_count ?? 0) }} clicks</span>
                                                <span><i class="fas fa-download" style="margin-right:6px;"></i>{{ (int) ($resource->download_count ?? 0) }} downloads</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge resource-type-badge bg-soft-primary text-primary">
                                        <i class="{{ $icon[0] }}"></i>{{ ucfirst($resource->resource_type) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge resource-category-badge bg-light text-dark border border-1 border-light-subtle">
                                        {{ $resource->category ?: 'Uncategorized' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="resource-status-stack">
                                        @if($resource->is_published)
                                            <span class="badge resource-status-badge bg-success text-white">Published</span>
                                        @else
                                            <span class="badge resource-status-badge bg-secondary text-white">Draft</span>
                                        @endif
                                        @if($resource->is_featured)
                                            <span class="badge resource-status-badge bg-info text-dark">Featured</span>
                                        @endif
                                        @if($resource->is_noindex)
                                            <span class="badge resource-status-badge bg-warning text-dark">Noindex</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="small text-muted d-block">Clicks: <strong>{{ (int) ($resource->click_count ?? 0) }}</strong></span>
                                    <span class="small text-muted d-block">Downloads: <strong>{{ (int) ($resource->download_count ?? 0) }}</strong></span>
                                </td>
                                <td>{{ optional($resource->updated_at)->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ $publicLink }}" target="_blank" rel="noopener" class="resource-link-chip">
                                        <i class="fas fa-arrow-up-right-from-square"></i> Open
                                    </a>
                                </td>
                                <td class="text-end">
                                    <div class="admin-action-group">
                                        @if(!empty($resource->file_url) || !empty($resource->file_path))
                                            <a href="{{ route('admin.resources.download', ['resource' => $resource, 'mode' => 'inline']) }}" target="_blank" rel="noopener" class="action-link" title="Open file">
                                                <i class="fas fa-file-arrow-down"></i>
                                            </a>
                                        @endif
                                        <button type="button" class="action-link copy-link-btn" data-link="{{ $publicLink }}" title="Copy link">
                                            <i class="fas fa-link"></i>
                                        </button>
                                        <button type="button" class="action-link edit-resource-btn" title="Edit" data-resource='@json($resourceEditPayload)'>
                                            <i class="fas fa-pen"></i>
                                        </button>
                                        <button type="button" class="action-link text-danger delete-resource-btn" title="Delete" data-id="{{ $resource->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">No resources yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-3 px-md-4 py-3">
                {{ $resources->links() }}
            </div>
        </div>
    </div>
</div>

<div class="modal fade resources-modal" id="addResourceModal" tabindex="-1" aria-labelledby="addResourceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-1" id="addResourceModalLabel">Add Resource</h5>
                    <div class="small text-white-50">Create a new resource entry with files, metadata, and publishing controls.</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if($errors->any())
                    <div class="alert alert-danger mb-3">
                        <strong>Please review the form errors below.</strong>
                    </div>
                @endif
                <div class="resource-form-panel">
                    <form action="{{ $formAction }}" method="POST" enctype="multipart/form-data" id="resourceCreateForm">
                        @csrf
                        @if($formMethod !== 'POST')
                            @method($formMethod)
                        @endif
                        <input type="hidden" name="id" id="resourceModalId" value="">
                        <input type="hidden" id="resourceModalMode" value="create">

                        @include('admin.resources._fields', [
                            'resource' => $resourceForm,
                            'types' => $types,
                            'formKey' => 'resourceModal'
                        ])
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="resourceCreateForm" class="btn btn-primary px-4" id="saveResourceBtn">Save Resource</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalEl = document.getElementById('addResourceModal');
    const formEl = document.getElementById('resourceCreateForm');
    const saveBtn = document.getElementById('saveResourceBtn');
    const modeInput = document.getElementById('resourceModalMode');
    const idInput = document.getElementById('resourceModalId');
    const tokenInput = formEl ? formEl.querySelector('input[name="_token"]') : null;
    const csrfToken = tokenInput ? tokenInput.value : '';
    const resourceModal = (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal)
        ? bootstrap.Modal.getOrCreateInstance(modalEl)
        : null;

    function getDescriptionHtml() {
        if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances && CKEDITOR.instances.resourceModalDescription) {
            return CKEDITOR.instances.resourceModalDescription.getData();
        }

        const description = document.getElementById('resourceModalDescription');
        return description ? description.value : '';
    }

    function setDescriptionHtml(value) {
        if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances && CKEDITOR.instances.resourceModalDescription) {
            CKEDITOR.instances.resourceModalDescription.setData(value || '');
            return;
        }

        const description = document.getElementById('resourceModalDescription');
        if (description) {
            description.value = value || '';
        }
    }

    function setSavingState(isSaving) {
        if (!saveBtn) {
            return;
        }

        saveBtn.disabled = isSaving;
        saveBtn.textContent = isSaving ? 'Saving...' : (modeInput && modeInput.value === 'edit' ? 'Update Resource' : 'Save Resource');
    }

    function resetResourceModal() {
        if (!formEl) {
            return;
        }

        formEl.reset();
        if (idInput) {
            idInput.value = '';
        }
        if (modeInput) {
            modeInput.value = 'create';
        }

        const modalTitle = document.getElementById('addResourceModalLabel');
        if (modalTitle) {
            modalTitle.textContent = 'Add Resource';
        }

        setDescriptionHtml('');
        setSavingState(false);
    }

    function toBool(value) {
        return value === true || value === 1 || value === '1' || value === 'true';
    }

    function populateResourceModal(resource) {
        if (!formEl || !resource) {
            return;
        }

        if (idInput) {
            idInput.value = resource.id || '';
        }
        if (modeInput) {
            modeInput.value = 'edit';
        }

        const modalTitle = document.getElementById('addResourceModalLabel');
        if (modalTitle) {
            modalTitle.textContent = 'Edit Resource';
        }

        const setValue = function(selector, value) {
            const field = formEl.querySelector(selector);
            if (field) {
                field.value = value || '';
            }
        };

        setValue('#resourceModalTitle', resource.title);
        setValue('#resourceModalSlug', resource.slug);
        setValue('select[name="resource_type"]', resource.resource_type);
        setValue('select[name="category_id"]', resource.category_id ? String(resource.category_id) : '');

        const published = formEl.querySelector('#resourceModalPublished');
        const featured = formEl.querySelector('#resourceModalFeatured');
        const noindex = formEl.querySelector('#resourceModalNoindex');
        if (published) {
            published.checked = toBool(resource.is_published);
        }
        if (featured) {
            featured.checked = toBool(resource.is_featured);
        }
        if (noindex) {
            noindex.checked = toBool(resource.is_noindex);
        }

        const fileInput = formEl.querySelector('input[name="file"]');
        const thumbInput = formEl.querySelector('input[name="thumbnail"]');
        if (fileInput) {
            fileInput.value = '';
            fileInput.required = false;
        }
        if (thumbInput) {
            thumbInput.value = '';
        }

        setDescriptionHtml(resource.description || '');
        setSavingState(false);
    }

    async function refreshResourcesSection() {
        const response = await fetch(window.location.href, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
            cache: 'no-store',
        });

        if (!response.ok) {
            throw new Error('Failed to refresh resources list.');
        }

        const html = await response.text();
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');

        const currentStats = document.querySelector('.resources-stats');
        const newStats = doc.querySelector('.resources-stats');
        if (currentStats && newStats) {
            currentStats.replaceWith(newStats);
        }

        const currentTableCard = document.querySelector('.resources-table-card');
        const newTableCard = doc.querySelector('.resources-table-card');
        if (currentTableCard && newTableCard) {
            currentTableCard.replaceWith(newTableCard);
        }

        bindDynamicActions();
    }

    async function handleResourceDelete(resourceId) {
        if (!csrfToken) {
            alert('Missing CSRF token. Please refresh and try again.');
            return;
        }

        const formData = new FormData();
        formData.append('_token', csrfToken);
        formData.append('_method', 'DELETE');

        const response = await fetch('/admin/resources/' + resourceId, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: formData,
        });

        const payload = await response.json().catch(function () { return {}; });
        if (!response.ok) {
            throw new Error(payload.message || 'Failed to delete resource.');
        }

        await refreshResourcesSection();
        alert(payload.message || 'Resource deleted successfully.');
    }

    async function submitResourceForm(event) {
        event.preventDefault();
        if (!formEl) {
            return;
        }

        setSavingState(true);

        try {
            const mode = modeInput ? modeInput.value : 'create';
            const resourceId = idInput ? idInput.value : '';
            const isEdit = mode === 'edit' && resourceId !== '';
            const endpoint = isEdit ? '/admin/resources/' + resourceId : '/admin/resources';

            const formData = new FormData(formEl);
            formData.set('description', getDescriptionHtml());
            if (isEdit) {
                formData.append('_method', 'PUT');
            }

            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: formData,
            });

            const payload = await response.json().catch(function () { return {}; });
            if (!response.ok) {
                const firstValidation = payload.errors ? Object.values(payload.errors)[0] : null;
                const validationMessage = Array.isArray(firstValidation) ? firstValidation[0] : null;
                throw new Error(validationMessage || payload.message || 'Failed to save resource.');
            }

            if (resourceModal) {
                resourceModal.hide();
            }
            resetResourceModal();
            await refreshResourcesSection();
            alert(payload.message || 'Resource saved successfully.');
        } catch (error) {
            alert(error.message || 'Failed to save resource.');
        } finally {
            setSavingState(false);
        }
    }

    function bindCopyButtons() {
        document.querySelectorAll('.copy-link-btn').forEach(function (button) {
            if (button.dataset.bound === '1') {
                return;
            }
            button.dataset.bound = '1';

            button.addEventListener('click', async function () {
                const url = button.getAttribute('data-link') || '';
                if (!url) {
                    return;
                }

                try {
                    await navigator.clipboard.writeText(url);
                    button.innerHTML = '<i class="fas fa-check"></i>';
                    button.classList.add('text-success');
                } catch (error) {
                    const helper = document.createElement('textarea');
                    helper.value = url;
                    document.body.appendChild(helper);
                    helper.select();
                    document.execCommand('copy');
                    document.body.removeChild(helper);
                    button.innerHTML = '<i class="fas fa-check"></i>';
                    button.classList.add('text-success');
                }

                setTimeout(function () {
                    button.classList.remove('text-success');
                    button.innerHTML = '<i class="fas fa-link"></i>';
                }, 1500);
            });
        });
    }

    function bindDynamicActions() {
        bindCopyButtons();

        document.querySelectorAll('.edit-resource-btn').forEach(function (button) {
            if (button.dataset.bound === '1') {
                return;
            }
            button.dataset.bound = '1';

            button.addEventListener('click', function () {
                const raw = button.getAttribute('data-resource');
                if (!raw) {
                    return;
                }

                try {
                    const resource = JSON.parse(raw);
                    populateResourceModal(resource);
                    if (resourceModal) {
                        resourceModal.show();
                    }
                } catch (error) {
                    alert('Unable to load resource details for editing.');
                }
            });
        });

        document.querySelectorAll('.delete-resource-btn').forEach(function (button) {
            if (button.dataset.bound === '1') {
                return;
            }
            button.dataset.bound = '1';

            button.addEventListener('click', async function () {
                const resourceId = button.getAttribute('data-id');
                if (!resourceId) {
                    return;
                }

                if (!confirm('Delete this resource?')) {
                    return;
                }

                try {
                    await handleResourceDelete(resourceId);
                } catch (error) {
                    alert(error.message || 'Failed to delete resource.');
                }
            });
        });
    }

    if (formEl) {
        formEl.addEventListener('submit', submitResourceForm);
    }

    if (modalEl) {
        modalEl.addEventListener('hidden.bs.modal', function () {
            resetResourceModal();
        });
    }

    bindDynamicActions();

    @if($errors->any())
        if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            resourceModal.show();
        }
    @endif
});
</script>
@endpush
