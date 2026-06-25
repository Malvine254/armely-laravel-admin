@extends('admin.layouts.admin')

@section('title', 'Case Study Technologies')
@section('page-title', 'Case Study Technologies')

@push('styles')
<style>
    .case-categories-title {
        margin-bottom: 1rem;
    }
    .case-categories-title h2 {
        font-size: 1.45rem;
        color: #1f3f80;
        margin-bottom: 0.35rem;
        font-weight: 700;
    }
    .case-categories-title p {
        font-size: 0.9rem;
        color: #667085;
        margin-bottom: 0;
    }
    .case-categories-table-card {
        border-radius: 14px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 8px 22px rgba(15, 23, 42, 0.06);
        overflow: hidden;
        background: #fff;
    }
    .case-categories-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #e5e7eb;
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    }
    .case-categories-header .section-icon {
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
    #caseTechnologiesTable {
        width: 100% !important;
        margin: 0 !important;
        border-collapse: separate;
        border-spacing: 0;
    }
    #caseTechnologiesTable thead th {
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
    #caseTechnologiesTable tbody td {
        padding: 0.95rem 1.15rem;
        vertical-align: middle;
        border-bottom: 1px solid #eef2f7;
        color: #334155;
    }
    #caseTechnologiesTable tbody tr:hover {
        background: #f8fafc;
    }
    .case-category-name {
        color: #111827;
        font-weight: 700;
    }
    .case-category-meta {
        color: #64748b;
        font-size: 0.8rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-3">
    <div class="case-categories-title d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h2>Case Study Technologies</h2>
            <p>Manage the technology options used in case study admin forms and site filters.</p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTechnologyModal">
            <i class="fas fa-plus me-2"></i>Add Technology
        </button>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted mb-1 fw-bold small text-uppercase">Total Technologies</p>
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
                    <p class="text-muted mb-1 fw-bold small text-uppercase">Default</p>
                    <h3 class="mb-0 fw-bold text-primary">{{ $stats['default'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="case-categories-table-card">
        <div class="case-categories-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <span class="section-icon"><i class="fas fa-microchip"></i></span>
                <div>
                    <h5 class="mb-0 fw-bold">Technology Library</h5>
                    <div class="small text-muted">Default platforms plus manually added technologies.</div>
                </div>
            </div>
            <span class="badge bg-light text-primary rounded-pill px-3 py-2">{{ $technologies->count() }} records</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="caseTechnologiesTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Source</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($technologies as $technology)
                            <tr>
                                <td>
                                    <div class="case-category-name">{{ $technology->name }}</div>
                                    <div class="case-category-meta">/{{ $technology->slug }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-primary rounded-pill px-3 py-2">{{ ucfirst($technology->source ?: 'manual') }}</span>
                                </td>
                                <td>
                                    @if($technology->is_active)
                                        <span class="badge bg-success text-white rounded-pill px-3 py-2">Active</span>
                                    @else
                                        <span class="badge bg-secondary text-white rounded-pill px-3 py-2">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ route('admin.case-study-technologies.destroy', $technology) }}" method="POST" onsubmit="return confirm('Delete this technology?');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-5">No technologies yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addTechnologyModal" tabindex="-1" aria-labelledby="addTechnologyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTechnologyModalLabel">Add Technology</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            @if($errors->any())
                <div class="alert alert-danger m-3 mb-0">
                    <strong>Please review the technology form errors below.</strong>
                </div>
            @endif
            <form action="{{ route('admin.case-study-technologies.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Technology Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="e.g. Snowflake" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold">Source</label>
                        <input type="text" name="source" class="form-control" value="{{ old('source') }}" placeholder="manual">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Save Technology</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    @if($errors->any())
        const modalEl = document.getElementById('addTechnologyModal');
        if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
        }
    @endif
});
</script>
@endpush
@endsection
