@extends('admin.layouts.admin')

@section('page-title', 'Admin Users')
@section('title', 'Admin Users - Armely Admin')

@section('content')
<div class="page-title">
    <h1>Admin Users</h1>
    <p>Manage administrator accounts and permissions</p>
</div>

<!-- Stats -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <p class="text-muted mb-2">Total Admins</p>
                <h3 class="mb-0">{{ $stats['total'] ?? 0 }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <p class="text-muted mb-2">Active</p>
                <h3 class="mb-0" style="color: #27ae60;">{{ $stats['active'] ?? 0 }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <p class="text-muted mb-2">Super Admins</p>
                <h3 class="mb-0" style="color: #e74c3c;">{{ $stats['super_admins'] ?? 0 }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <p class="text-muted mb-2">Inactive</p>
                <h3 class="mb-0" style="color: #95a5a6;">{{ $stats['inactive'] ?? 0 }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Add Admin Button -->
<div class="card mb-4">
    <div class="card-body">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAdminModal">
            <i class="fas fa-user-plus"></i> Add New Admin
        </button>
    </div>
</div>

<!-- Admins Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Admin List</h5>
    </div>
    <div class="card-body p-0">
        @if($admins->count() > 0)
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($admins as $admin)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; margin-right: 10px;">
                                        {{ substr($admin->name, 0, 1) }}
                                    </div>
                                    {{ $admin->name }}
                                </div>
                            </td>
                            <td>{{ $admin->email }}</td>
                            <td>
                                <span class="badge {{ $admin->role === 'Super Admin' ? 'bg-danger' : 'bg-primary' }}">
                                    {{ $admin->role }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $admin->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ ucfirst($admin->status) }}
                                </span>
                            </td>
                            <td>{{ $admin->joined_date ? $admin->joined_date->format('M d, Y') : 'N/A' }}</td>
                            <td>
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editAdminModal" onclick="editAdmin({{ $admin }})">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                @if(auth('admin')->user()->id !== $admin->id)
                                    <form action="{{ route('admin.admins.delete', $admin->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="p-4 text-muted mb-0"><i class="fas fa-info-circle"></i> No admins found</p>
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
    document.getElementById('editAdminForm').action = `/admin/admins/${admin.id}`;
    document.getElementById('editName').value = admin.name;
    document.getElementById('editEmail').value = admin.email;
    document.getElementById('editRole').value = admin.role;
    document.getElementById('editStatus').value = admin.status;
}
</script>

@endsection
