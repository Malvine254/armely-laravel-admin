@extends('admin.layouts.admin')

@section('title', $pageTitle)

@push('styles')
<style>
    .resource-admin-shell {
        max-width: 1180px;
        margin: 0 auto;
    }
    .resource-form-card {
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        box-shadow: 0 16px 40px rgba(15, 23, 42, 0.08);
        overflow: hidden;
    }
    .resource-form-card .card-header {
        background: linear-gradient(135deg, #2f5597 0%, #1e3a6b 100%);
        color: #fff;
        padding: 1.15rem 1.5rem;
        border-bottom: none;
    }
    .resource-form-card .card-header h2 {
        font-size: 1.35rem;
        margin-bottom: 0.25rem;
        color: #fff;
    }
    .resource-form-card .card-header p {
        margin-bottom: 0;
        color: rgba(255, 255, 255, 0.88);
    }
    .resource-form-card .card-body {
        background: #f8fafc;
        padding: 1.5rem;
    }
    .resource-form-card .resource-form-panel {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 1.35rem;
    }
    .resource-form-card .form-label {
        font-weight: 600;
        color: #344054;
    }
    .resource-form-card .form-control,
    .resource-form-card .form-select {
        border: 1px solid #d6deea;
        border-radius: 12px;
        min-height: 44px;
        box-shadow: none;
    }
    .resource-form-card .form-control:focus,
    .resource-form-card .form-select:focus {
        border-color: #2f5597;
        box-shadow: 0 0 0 4px rgba(47, 85, 151, 0.12);
    }
    .resource-form-card .btn-primary {
        background: linear-gradient(135deg, #2f5597 0%, #1e3a6b 100%);
        border: 0;
        box-shadow: 0 8px 18px rgba(47, 85, 151, 0.22);
    }
    .resource-form-card .btn-outline-secondary {
        border-color: #d6deea;
        color: #344054;
        background: #fff;
    }
    .resource-form-card .btn-outline-secondary:hover {
        background: #f8fafc;
    }
    @media (max-width: 768px) {
        .resource-form-card .card-body,
        .resource-form-card .resource-form-panel {
            padding: 1rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-3 resource-admin-shell">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h2 class="mb-1">{{ $pageTitle }}</h2>
            <p class="text-muted mb-0">Add or update public resources, links, and supporting files.</p>
        </div>
        <a href="{{ route('admin.resources.index') }}" class="btn btn-outline-secondary">Back</a>
    </div>

    <div class="card resource-form-card">
        <div class="card-header">
            <h2 class="mb-0">{{ $pageTitle }}</h2>
            <p>Use this form to publish a new resource or update an existing one.</p>
        </div>
        <div class="card-body">
            <div class="resource-form-panel">
                <form action="{{ $formAction }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if($formMethod !== 'POST')
                        @method($formMethod)
                    @endif

                    @include('admin.resources._fields', [
                        'resource' => $resource,
                        'types' => $types,
                        'formKey' => 'resourceForm'
                    ])

                    <div class="d-flex flex-wrap gap-2 mt-4 justify-content-end">
                        <a href="{{ route('admin.resources.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4">Save Resource</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
