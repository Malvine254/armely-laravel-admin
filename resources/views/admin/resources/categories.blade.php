@extends('admin.layouts.admin')

@section('title', 'Resource Categories')
@section('page-title', 'Resource Categories')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" integrity="sha384-ok3J6xA9oQqai5C9ytYveFsBeKgoGk4T+NExsr6hoIKjZdv9SJcmx2mafwUWRNf9" crossorigin="anonymous">
<style>
    .resource-categories-title {
        margin-bottom: 1rem;
    }
    .resource-categories-title h2 {
        font-size: 1.45rem;
        color: #1f3f80;
        margin-bottom: 0.35rem;
        font-weight: 700;
    }
    .resource-categories-title p {
        font-size: 0.9rem;
        color: #667085;
        margin-bottom: 0;
    }
    .bg-soft-primary {
        background: #eef4ff !important;
        color: #2f5597 !important;
    }
    .resource-categories-table-card {
        border-radius: 14px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 8px 22px rgba(15, 23, 42, 0.06);
        overflow: hidden;
        background: #fff;
    }
    .resource-categories-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #e5e7eb;
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    }
    .resource-categories-header .section-icon {
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
    .resource-categories-header h5 {
        font-size: 1rem;
        color: #111827;
    }
    #resourceCategoriesTable {
        width: 100% !important;
        margin: 0 !important;
        border-collapse: separate;
        border-spacing: 0;
    }
    #resourceCategoriesTable thead th {
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
    #resourceCategoriesTable tbody td {
        padding: 0.95rem 1.15rem;
        vertical-align: middle;
        border-bottom: 1px solid #eef2f7;
        color: #334155;
    }
    #resourceCategoriesTable tbody tr:hover {
        background: #f8fafc;
    }
    .resource-category-name {
        color: #111827;
        font-weight: 700;
    }
    .resource-category-meta {
        color: #64748b;
        font-size: 0.8rem;
    }
    .resources-modal .modal-content {
        border: none;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.28);
        overflow: hidden;
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
    .resources-modal .form-label {
        font-weight: 600;
        color: #344054;
    }
    .resources-modal .form-control {
        border: 1px solid #d6deea;
        border-radius: 12px;
        min-height: 44px;
        box-shadow: none;
    }
    .resources-modal .form-control:focus {
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
</style>
@endpush

@section('content')
<div class="container-fluid py-3">
    <div class="resource-categories-title d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h2>Resource Categories</h2>
            <p>Manage the dropdown categories used by the resource repository forms.</p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            <i class="fas fa-plus me-2"></i>Add Category
        </button>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted mb-1 fw-bold small text-uppercase">Total Categories</p>
                    <h3 class="mb-0 fw-bold">{{ $stats['total'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted mb-1 fw-bold small text-uppercase">Active</p>
                    <h3 class="mb-0 fw-bold text-success">{{ $stats['active'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted mb-1 fw-bold small text-uppercase">Inactive</p>
                    <h3 class="mb-0 fw-bold text-secondary">{{ $stats['inactive'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted mb-1 fw-bold small text-uppercase">Service-Seeded</p>
                    <h3 class="mb-0 fw-bold text-primary">{{ $stats['service'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="resource-categories-table-card">
        <div class="resource-categories-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <span class="section-icon"><i class="fas fa-tags"></i></span>
                <div>
                    <h5 class="mb-0 fw-bold">Category Library</h5>
                    <div class="small text-muted">Service-backed defaults plus manually added categories.</div>
                </div>
            </div>
            <span class="badge bg-soft-primary text-primary rounded-pill px-3 py-2">{{ $categories->count() }} records</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="resourceCategoriesTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Source</th>
                            <th>Resources</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td>
                                    <div class="resource-category-name">{{ $category->name }}</div>
                                    <div class="resource-category-meta">/{{ $category->slug }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-soft-primary text-primary rounded-pill px-3 py-2">{{ ucfirst($category->source ?: 'manual') }}</span>
                                </td>
                                <td>{{ $category->resources_count ?? 0 }}</td>
                                <td>
                                    @if($category->is_active)
                                        <span class="badge bg-success text-white rounded-pill px-3 py-2">Active</span>
                                    @else
                                        <span class="badge bg-secondary text-white rounded-pill px-3 py-2">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ route('admin.resource-categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Delete this category?');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">No categories yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade resources-modal" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-1" id="addCategoryModalLabel">Add Category</h5>
                    <div class="small text-white-50">Create a new dropdown category for resource forms.</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
                @if($errors->any())
                    <div class="alert alert-danger mb-3">
                        <strong>Please review the category form errors below.</strong>
                    </div>
                @endif
            <form action="{{ route('admin.resource-categories.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="resource-form-panel">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Category Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="e.g. Copilot Readiness" required>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-semibold">Source</label>
                            <input type="text" name="source" class="form-control" value="{{ old('source') }}" placeholder="manual or service">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>
    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        @if($errors->any())
            const modalEl = document.getElementById('addCategoryModal');
            if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
            }
        @endif
    });
    </script>
    @endpush
@endsection
