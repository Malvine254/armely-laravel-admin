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
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        background: linear-gradient(135deg, #ffffff, #f8f9fa);
    }
    .admin-stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
    }
    .stat-icon-box {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
        font-size: 1.25rem;
    }
    .table-container-fixed {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(15, 27, 51, 0.05);
        border: 1px solid #e4e7ec;
        overflow: hidden;
    }
    .table-responsive {
        margin: 0;
        padding: 0;
    }
    #adminsDataTable {
        width: 100% !important;
        margin: 0 !important;
    }
    #adminsDataTable thead th {
        background: linear-gradient(135deg, #e3f2fd, #bbdefb);
        color: #0d47a1;
        font-weight: bold;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        padding: 1rem 1.5rem;
        border-bottom: 2px solid #90caf9;
    }
    #adminsDataTable tbody tr:hover {
        background-color: #e3f2fd;
    }
    #adminsDataTable tbody td {
        padding: 1rem 1.5rem;
        vertical-align: middle;
        border-bottom: 1px solid #e0e0e0;
        color: #37474f;
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
        border: 1px solid #edf1f7;
        border-left-width: 3px;
        box-shadow: 0 2px 8px rgba(31, 63, 128, 0.06);
    }
    .admins-stats .admin-stats-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(31, 63, 128, 0.1);
    }
    .admins-stats .stat-icon-box {
        margin-bottom: 0.6rem;
    }
    .admins-stats h3 {
        font-size: 1.4rem;
        line-height: 1.2;
    }

    .admins-table-card {
        border-radius: 12px;
        border: 1px solid #e4e7ec;
        box-shadow: 0 2px 8px rgba(31, 63, 128, 0.06);
    }

    .bg-soft-primary { background: #eef2ff !important; }
    .bg-soft-success { background: #ecfdf3 !important; }
    .bg-soft-danger { background: #fef3f2 !important; }
    .bg-soft-secondary { background: #f2f4f7 !important; }

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

    .pulse-indicator {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
    }

    .btn-white {
        background: #fff;
        border: 1px solid #e4e7ec;
    }
    .btn-white:hover {
        background: #f8fafc;
    }

    .dataTables_wrapper .dataTables_filter input,
    .dataTables_wrapper .dataTables_length select {
        border: 1px solid #d0d9e8;
        border-radius: 8px;
        min-height: 36px;
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

<!-- Admins Table -->
<div class="table-container-fixed admins-table-card">
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
                                        <div class="small text-muted">ID: #{{ str_pad($admin->id, 4, '0', STR_PAD_LEFT) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-dark">{{ $admin->email }}</div>
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
                                <div class="btn-group shadow-sm rounded-pill overflow-hidden">
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
