@extends('admin.layouts.admin')

@section('page-title', 'Admin Users')
@section('title', 'Admin Users - Armely Admin')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" integrity="sha384-ok3J6xA9oQqai5C9ytYveFsBeKgoGk4T+NExsr6hoIKjZdv9SJcmx2mafwUWRNf9" crossorigin="anonymous">
<style>
    /* MODAL Z-INDEX OVERRIDE - Force modals to appear above header */
    .modal {
        z-index: 9999 !important;
    }
    .modal-backdrop {
        z-index: 9998 !important;
        background-color: rgba(0, 0, 0, 0.7);
    }
    .modal-dialog {
        z-index: 10000 !important;
    }

    /* MODERN MODAL STYLING */
    .modal-content {
        border: none;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        overflow: hidden;
    }

    .modal-header {
        background: linear-gradient(135deg, #2f5597 0%, #1e3a6b 100%);
        color: white;
        padding: 1.75rem 2rem;
        border-bottom: none;
    }

    .modal-header .modal-title {
        font-weight: 600;
        font-size: 1.5rem;
        color: white;
    }

    .modal-header .btn-close {
        filter: brightness(0) invert(1);
        opacity: 0.8;
    }

    .modal-body {
        padding: 2rem;
        background: #f8f9fa;
    }

    .modal-body form {
        background: white;
        padding: 1.5rem;
        border-radius: 16px;
    }

    .modal-footer {
        padding: 1.5rem 2rem;
        background: white;
        border-top: 1px solid #e9ecef;
    }

    .modal .form-label {
        font-weight: 600;
        color: #344054;
        margin-bottom: 0.5rem;
    }

    .modal .form-control,
    .modal .form-select {
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 0.75rem 1rem;
        transition: all 0.2s ease;
    }

    .modal .form-control:focus,
    .modal .form-select:focus {
        border-color: #2f5597;
        box-shadow: 0 0 0 4px rgba(47, 85, 151, 0.1);
    }

    .modal .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        border: none;
    }

    .modal .btn-primary {
        background: linear-gradient(135deg, #2f5597 0%, #1e3a6b 100%);
        box-shadow: 0 4px 12px rgba(47, 85, 151, 0.3);
    }

    .modal .btn-secondary {
        background: #e5e7eb;
        color: #6b7280;
    }

    .admin-stats-card {
        border: 1px solid #e5e7eb !important;
        border-radius: 12px;
        box-shadow: 0 8px 22px rgba(15, 23, 42, 0.06);
        transition: box-shadow 0.2s ease, border-color 0.2s ease;
        background: #fff;
        overflow: hidden;
    }
    .admin-stats-card:hover {
        border-color: #cbd5e1 !important;
        box-shadow: 0 14px 30px rgba(15, 23, 42, 0.1);
    }
    .admin-stats-card::before {
        content: '';
        display: block;
        height: 3px;
        background: #2f5597;
    }
    .admin-stats-card.border-success::before {
        background: #0891b2;
    }
    .admin-stats-card.border-danger::before {
        background: #d97706;
    }
    .admin-stats-card.border-secondary::before {
        background: #475569;
    }
    .stat-icon-box {
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
    .table-container-fixed {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 8px 22px rgba(15, 23, 42, 0.06);
        border: 1px solid #e5e7eb;
        overflow: hidden;
    }
    .table-responsive {
        margin: 0;
        padding: 0;
    }
    #adminsDataTable {
        width: 100% !important;
        margin: 0 !important;
        border-collapse: separate;
        border-spacing: 0;
    }
    #adminsDataTable thead th {
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.74rem;
        letter-spacing: 0.04em;
        padding: 0.9rem 1.25rem;
        border-bottom: 1px solid #e5e7eb;
    }
    #adminsDataTable tbody tr:hover {
        background-color: #f8fafc;
    }
    #adminsDataTable tbody td {
        padding: 0.95rem 1.25rem;
        vertical-align: middle;
        border-bottom: 1px solid #eef2f7;
        color: #334155;
    }
    #adminsDataTable tbody tr:last-child td {
        border-bottom: 0;
    }
    #adminsDataTable .badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        min-height: 30px;
        font-weight: 700;
        letter-spacing: 0;
    }

    /* Refined page styling overrides */
    .admins-page-title {
        margin-bottom: 1rem;
    }
    .admins-page-title h2 {
        font-size: 1.45rem;
        color: #1f3f80;
        margin-bottom: 0.35rem;
    }
    .admins-page-title p {
        font-size: 0.9rem;
        color: #667085;
        margin: 0;
    }

    .admins-actions .btn {
        border-radius: 10px;
        font-weight: 600;
        padding: 0.55rem 1rem;
    }

    .admins-stats .admin-stats-card {
        border-left-width: 1px !important;
    }
    .admins-stats .admin-stats-card:hover {
        transform: none;
    }
    .admins-stats .stat-icon-box {
        margin-bottom: 0.6rem;
    }
    .admins-stats h3 {
        font-size: 1.55rem;
        line-height: 1.15;
        color: #111827 !important;
    }
    .admins-stats p {
        color: #64748b !important;
        font-size: 0.74rem;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .admins-table-card {
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 8px 22px rgba(15, 23, 42, 0.06);
    }
    .admins-table-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #e5e7eb;
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    }
    .admins-table-header h5 {
        font-size: 1rem;
        color: #111827;
    }
    .admins-table-header .section-icon {
        width: 34px;
        height: 34px;
        border-radius: 9px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #eef4ff;
        color: #2f5597;
        border: 1px solid #dbe7ff;
    }

    .bg-soft-primary,
    .bg-soft-success,
    .bg-soft-danger,
    .bg-soft-secondary {
        background: #eef4ff !important;
        color: #2f5597 !important;
    }
    .admins-stats .text-success,
    .admins-stats .text-danger,
    .admins-stats .text-secondary {
        color: #111827 !important;
    }

    .avatar-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.85rem;
        letter-spacing: 0.3px;
    }
    .admin-email {
        color: #334155;
        font-size: 0.92rem;
    }
    .admin-id {
        color: #64748b;
        font-size: 0.78rem;
    }

    .pulse-indicator {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
        box-shadow: 0 0 0 3px rgba(47, 85, 151, 0.08);
    }
    .pulse-indicator.bg-success,
    .pulse-indicator.bg-secondary {
        background: #2f5597 !important;
    }

    .btn-white {
        background: #fff;
        border: 1px solid #e4e7ec;
    }
    .btn-white:hover {
        background: #f8fafc;
    }
    .admin-action-group {
        border: 1px solid #e5e7eb;
        background: #fff;
        box-shadow: 0 3px 10px rgba(15, 23, 42, 0.06) !important;
    }
    .admin-action-group .btn {
        width: 36px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 0 !important;
        border-radius: 0;
    }
    .admin-action-group .btn + form .btn,
    .admin-action-group form + .btn {
        border-left: 1px solid #e5e7eb !important;
    }
    .admin-action-group .btn:hover {
        background: #eef4ff;
    }
    .admin-action-group .text-danger {
        color: #b91c1c !important;
    }

    .dataTables_wrapper .dataTables_filter input,
    .dataTables_wrapper .dataTables_length select {
        border: 1px solid #d0d9e8;
        border-radius: 10px;
        min-height: 38px;
        background: #fff;
        color: #111827;
    }
    .dataTables_wrapper {
        padding: 1rem 1.25rem 1.25rem;
    }
    .dataTables_wrapper .row:first-child {
        margin: 0 0 0.9rem;
        padding: 0.75rem;
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        align-items: center;
    }
    .dataTables_wrapper .dataTables_length label,
    .dataTables_wrapper .dataTables_filter label {
        color: #64748b;
        font-size: 0.86rem;
        font-weight: 600;
    }
    .dataTables_wrapper .dataTables_filter input {
        min-width: 260px;
        padding: 0.48rem 0.75rem 0.48rem 2rem;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='11' cy='11' r='8'%3E%3C/circle%3E%3Cline x1='21' y1='21' x2='16.65' y2='16.65'%3E%3C/line%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: 0.65rem center;
        background-size: 15px;
    }
    .dataTables_wrapper .dataTables_filter input:focus,
    .dataTables_wrapper .dataTables_length select:focus {
        border-color: #2f5597;
        box-shadow: 0 0 0 4px rgba(47, 85, 151, 0.12);
        outline: none;
    }
    .dataTables_wrapper .dataTables_info {
        color: #64748b;
        font-size: 0.86rem;
        padding-top: 0.9rem;
    }
    .dataTables_wrapper .dataTables_paginate {
        padding-top: 0.75rem;
    }
    .dataTables_wrapper .page-link {
        border: 0;
        border-radius: 999px !important;
        color: #334155;
        margin: 0 0.12rem;
    }
    .dataTables_wrapper .page-item.active .page-link {
        background: #2f5597;
        color: #fff;
        box-shadow: 0 8px 16px rgba(47, 85, 151, 0.2);
    }
    @media (max-width: 768px) {
        .dataTables_wrapper .dataTables_filter input {
            min-width: 0;
            width: 100%;
        }
        .admins-table-header {
            gap: 0.75rem;
        }
    }
</style>
@endpush

@section('content')
<div class="admins-page-title">
    <h2 class="fw-bold">Admin Users</h2>
    <p>Manage administrator accounts and access.</p>
</div>

<!-- Stats -->
<div class="row g-3 mb-4 admins-stats">
    <div class="col-md-6 col-lg-3">
        <div class="card admin-stats-card border-start border-4 border-primary">
            <div class="card-body">
                <div class="stat-icon-box bg-soft-primary text-primary">
                    <i class="fas fa-users-cog"></i>
                </div>
                <p class="text-muted mb-1 fw-bold small uppercase">Total Admins</p>
                <h3 class="mb-0 fw-bold">{{ $stats['total'] ?? 0 }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card admin-stats-card border-start border-4 border-success">
            <div class="card-body">
                <div class="stat-icon-box bg-soft-success text-success">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <p class="text-muted mb-1 fw-bold small uppercase">Active</p>
                <h3 class="mb-0 fw-bold text-success">{{ $stats['active'] ?? 0 }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card admin-stats-card border-start border-4 border-danger">
            <div class="card-body">
                <div class="stat-icon-box bg-soft-danger text-danger">
                    <i class="fas fa-user-shield"></i>
                </div>
                <p class="text-muted mb-1 fw-bold small uppercase">Super Admins</p>
                <h3 class="mb-0 fw-bold text-danger">{{ $stats['super_admins'] ?? 0 }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card admin-stats-card border-start border-4 border-secondary">
            <div class="card-body">
                <div class="stat-icon-box bg-soft-secondary text-secondary">
                    <i class="fas fa-user-clock"></i>
                </div>
                <p class="text-muted mb-1 fw-bold small uppercase">Inactive</p>
                <h3 class="mb-0 fw-bold text-secondary">{{ $stats['inactive'] ?? 0 }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3 admins-actions">
    <h5 class="mb-0 fw-bold"><i class="fas fa-list-ul me-2 text-primary"></i>Administrators</h5>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAdminModal">
        <i class="fas fa-plus-circle me-2"></i>Add Admin
    </button>
</div>

<div class="table-container-fixed admins-table-card">
    <div class="admins-table-header d-flex justify-content-between align-items-center flex-wrap">
        <div class="d-flex align-items-center gap-2">
            <span class="section-icon"><i class="fas fa-users-gear"></i></span>
            <div>
                <h5 class="mb-0 fw-bold">Administrator Directory</h5>
                <div class="small text-muted">Search, review roles, and manage access.</div>
            </div>
        </div>
        <span class="badge bg-soft-primary text-primary rounded-pill px-3 py-2">{{ $admins->count() }} records</span>
    </div>
    <div class="card-body p-0">
        @if($admins->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="adminsDataTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th class="text-end px-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($admins as $admin)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-3" style="background: linear-gradient(135deg, #2f5597 0%, #4a6fb5 100%); width: 42px; height: 42px; color: #fff;">
                                        {{ strtoupper(substr($admin->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $admin->name }}</div>
                                        <div class="admin-id">ID: #{{ str_pad($admin->id, 4, '0', STR_PAD_LEFT) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="admin-email">{{ $admin->email }}</div>
                            </td>
                            <td>
                                @if($admin->role === 'Super Admin')
                                    <span class="badge bg-soft-danger text-danger border-0 px-3 py-2 rounded-pill">
                                        <i class="fas fa-crown me-1"></i> Super Admin
                                    </span>
                                @else
                                    <span class="badge bg-soft-primary text-primary border-0 px-3 py-2 rounded-pill">
                                        <i class="fas fa-user-lock me-1"></i> Admin
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="pulse-indicator me-2 bg-{{ $admin->status === 'active' ? 'success' : 'secondary' }}"></div>
                                    <span class="fw-bold text-{{ $admin->status === 'active' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($admin->status) }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="text-muted small">
                                    <i class="far fa-calendar-alt me-1"></i> {{ $admin->joined_date ? $admin->joined_date->format('d M, Y') : 'N/A' }}
                                </div>
                            </td>
                            <td class="text-end px-4">
                                <div class="btn-group rounded-pill overflow-hidden admin-action-group">
                                    <button class="btn btn-sm btn-white border-end" data-bs-toggle="modal" data-bs-target="#editAdminModal" onclick='editAdmin(@json($admin))' title="Edit">
                                        <i class="fas fa-cog text-primary"></i>
                                    </button>
                                    @if(auth('admin')->user()->id !== $admin->id)
                                        <form action="{{ route('admin.admins.delete', $admin->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-white" onclick="return confirm('Remove access for {{ $admin->name }}?')" title="Delete">
                                                <i class="fas fa-trash-alt text-danger"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-users-slash fa-3x text-muted mb-3 opacity-25"></i>
                <p class="text-muted">No admin users found.</p>
            </div>
        @endif
    </div>
</div>

<!-- Add Admin Modal -->
<div class="modal fade" id="addAdminModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Admin User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.admins.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" name="password_confirmation" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select class="form-select @error('role') is-invalid @enderror" name="role" required>
                            <option value="">Select Role</option>
                            <option value="Admin">Admin</option>
                            <option value="Super Admin">Super Admin</option>
                        </select>
                        @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Admin</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Admin Modal -->
<div class="modal fade" id="editAdminModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Admin User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editAdminForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" id="editName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" id="editEmail" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password (leave blank to keep current)</label>
                        <input type="password" class="form-control" id="editPassword" name="password">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="editPasswordConfirm" name="password_confirmation">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select class="form-select" id="editRole" name="role" required>
                            <option value="Admin">Admin</option>
                            <option value="Super Admin">Super Admin</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" id="editStatus" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Admin</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editAdmin(admin) {
    // If accidentally passed as a JSON string, parse it
    if (typeof admin === 'string') {
        try { admin = JSON.parse(admin); } catch (e) {}
    }
    const form = document.getElementById('editAdminForm');
    form.action = `/admin/admins/${admin.id}`;
    document.getElementById('editName').value = admin.name || '';
    document.getElementById('editEmail').value = admin.email || '';
    document.getElementById('editRole').value = admin.role || 'Admin';
    document.getElementById('editStatus').value = admin.status || 'active';
    // Clear password fields for safety
    document.getElementById('editPassword').value = '';
    document.getElementById('editPasswordConfirm').value = '';
}
</script>

    @endsection

    @push('scripts')
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js" integrity="sha384-cjmdOgDzOE22dUheI5E6Gzd3upfmReW8N1y/4jwKQE50KYcvFKZJA9JxWgQOzqwQ" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js" integrity="sha384-PgPBH0hy6DTJwu7pTf6bkRqPlf/+pjUBExpr/eIfzszlGYFlF9Wi9VTAJODPhgCO" crossorigin="anonymous"></script>
    <script>
    $(function() {
        if ($.fn.DataTable && !$.fn.DataTable.isDataTable('#adminsDataTable')) {
            $('#adminsDataTable').DataTable({
                dom: 'lfrtip',
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
                columnDefs: [{ orderable: false, targets: -1 }],
                responsive: true,
                autoWidth: false,
                language: {
                    search: '',
                    searchPlaceholder: 'Search admins...'
                }
            });
        }
    });
    </script>
    @endpush
