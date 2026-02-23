@extends('admin.layouts.admin')

@section('page-title', 'Admin Users')
@section('title', 'Admin Users - Armely Admin')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<style>
    .admin-stats-card {
        border: none;
        border-radius: 16px;
        transition: all 0.3s ease;
        overflow: hidden;
        height: 100%;
    }
    .admin-stats-card .card-body {
        padding: 1.5rem;
    }
    .admin-stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.08);
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
        background: #f8f9fa;
        color: #475467;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e4e7ec;
    }
    #adminsDataTable tbody td {
        padding: 1rem 1.5rem;
        vertical-align: middle;
        border-bottom: 1px solid #f2f4f7;
    }
</style>
@endpush

@section('content')
<div class="mb-4">
    <h2 class="fw-bold text-dark mb-1">Admin Management</h2>
    <p class="text-muted">Monitor and manage access levels for your team members</p>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
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
                <p class="text-muted mb-1 fw-bold small uppercase">Active Sessions</p>
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

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0 fw-bold"><i class="fas fa-list-ul me-2 text-primary"></i>System Administrators</h5>
    <button class="btn btn-primary px-4 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#addAdminModal">
        <i class="fas fa-plus-circle me-2"></i>Provision New Admin
    </button>
</div>

<!-- Admins Table -->
<div class="table-container-fixed">
    <div class="card-body p-0">
        @if($admins->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="adminsDataTable">
                    <thead>
                        <tr>
                            <th>Member</th>
                            <th>Email Identity</th>
                            <th>Privilege</th>
                            <th>Status</th>
                            <th>Joined On</th>
                            <th class="text-end px-4">Management</th>
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
                                        <i class="fas fa-user-lock me-1"></i> Standard Admin
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
                                    <button class="btn btn-sm btn-white border-end" data-bs-toggle="modal" data-bs-target="#editAdminModal" onclick='editAdmin(@json($admin))' title="Settings">
                                        <i class="fas fa-cog text-primary"></i>
                                    </button>
                                    @if(auth('admin')->user()->id !== $admin->id)
                                        <form action="{{ route('admin.admins.delete', $admin->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-white" onclick="return confirm('Immediately revoke access for {{ $admin->name }}?')" title="Delete Account">
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
                <p class="text-muted">No administrative accounts found in the system</p>
            </div>
        @endif
    </div>
</div>

<!-- Add Admin Modal -->
<div class="modal fade" id="addAdminModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Admin</h5>
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
                <h5 class="modal-title">Edit Admin</h5>
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
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
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
