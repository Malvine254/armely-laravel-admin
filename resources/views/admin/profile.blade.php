@extends('admin.layouts.admin')

@section('page-title', 'Profile')
@section('title', 'Profile & Settings')

@section('content')
<div class="page-title">
    <h1>Profile</h1>
    <p>Manage your account details, security preferences, and recent sign-in activity.</p>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<ul class="nav nav-tabs" id="profileTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab">Your Profile</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="settings-tab" data-bs-toggle="tab" data-bs-target="#settings" type="button" role="tab">Account Settings</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab">Account History</button>
    </li>
</ul>

<div class="tab-content mt-3">
    <!-- Profile Tab -->
    <div class="tab-pane fade show active" id="profile" role="tabpanel">
        <div class="row">
                <div class="col-lg-4 mb-3">
                    <div class="card h-100 shadow-soft">
                        <div class="card-body text-center">
                            <div class="profile-avatar mb-3 mx-auto">
                                <img src="https://bootdey.com/img/Content/avatar/avatar1.png" alt="avatar" class="rounded-circle" width="96" height="96">
                            </div>
                            <h5 class="mb-1">{{ $admin->name }}</h5>
                            <p class="text-muted mb-1">{{ $admin->role ?? 'Admin' }}</p>
                            <small class="text-muted">Joined: {{ optional($admin->joined_date)->format('M d, Y') }}</small>
                            <div class="mt-3 text-start small text-muted">
                                <div><strong>Email:</strong> {{ $admin->email }}</div>
                                @if($admin->phone)
                                    <div><strong>Phone:</strong> {{ $admin->phone }}</div>
                                @endif
                            </div>
                            <div class="mt-3 d-grid gap-2">
                                <a class="btn btn-outline-primary" href="{{ route('admin.reports') }}">
                                    <i class="fas fa-chart-line me-1"></i> View Reports
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <div class="col-lg-8">
                    <div class="card shadow-soft">
                    <div class="card-body">
                        <h5 class="mb-3">Profile Settings</h5>
                        <form method="POST" action="{{ route('admin.profile.update') }}">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name', $admin->name) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" class="form-control" value="{{ $admin->email }}" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone Number</label>
                                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $admin->phone) }}" placeholder="Optional">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">New Password</label>
                                    <input type="password" name="password" class="form-control" minlength="8" autocomplete="off">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Confirm Password</label>
                                    <input type="password" name="password_confirmation" class="form-control" minlength="8" autocomplete="off">
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <div class="text-muted small">Leave password blank to keep the current password. Email is managed by an administrator.</div>
                                <button type="submit" class="btn btn-primary">Update Profile</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Settings Tab -->
    <div class="tab-pane fade" id="settings" role="tabpanel">
        <div class="card shadow-soft">
            <div class="card-body">
                <h5 class="mb-3">Account Settings</h5>
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>Activity Logs</strong>
                            <p class="text-muted mb-0">Record key admin actions for auditing.</p>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" checked>
                        </div>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>Two-Factor Authentication</strong>
                            <p class="text-muted mb-0">Add a second step at sign-in for stronger security.</p>
                        </div>
                        <button class="btn btn-sm btn-outline-primary" type="button">Manage</button>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>PIN for Sensitive Actions</strong>
                            <p class="text-muted mb-0">Require a short PIN before deleting data or changing roles.</p>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- History Tab -->
    <div class="tab-pane fade" id="history" role="tabpanel">
        <div class="card shadow-soft">
            <div class="card-body">
                <h5 class="mb-3">Account History</h5>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Device</th>
                                <th>Location</th>
                                <th>IP</th>
                                <th>Time</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($loginHistory as $entry)
                                <tr>
                                    <td>{{ $entry['device'] }}</td>
                                    <td>{{ $entry['location'] }}</td>
                                    <td>{{ $entry['ip'] }}</td>
                                    <td>{{ $entry['time'] }}</td>
                                    <td><a href="#" class="text-muted" title="Remove"><i class="fas fa-times"></i></a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Admin Modal -->
<div class="modal fade" id="addAdminModal" tabindex="-1" aria-labelledby="addAdminModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addAdminModalLabel">New Admin</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="{{ route('admin.admins.store') }}">
        @csrf
        <div class="modal-body">
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" minlength="8" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control" minlength="8" required>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select" required>
                        <option value="Admin">Admin</option>
                        <option value="Super Admin">Super Admin</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Create Admin</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
/* Profile page polish respecting existing palette (primary #2f5597, accents #17a2b8, warning #ffc107) */
.shadow-soft { box-shadow: 0 8px 24px rgba(0,0,0,0.08), 0 2px 6px rgba(0,0,0,0.04); border: 1px solid rgba(0,0,0,0.04); }
.profile-avatar img { box-shadow: 0 6px 18px rgba(47,85,151,0.25); }
.nav-tabs .nav-link { color: #2f5597; font-weight: 500; }
.nav-tabs .nav-link.active { background: #2f5597; color: #fff; border-color: #2f5597; }
.list-group-item { border: none; border-bottom: 1px solid rgba(0,0,0,0.05); }
.list-group-item:last-child { border-bottom: none; }
.table thead th { background: #f7f9fb; color: #2f5597; }
.btn-outline-primary { border-color: #2f5597; color: #2f5597; }
.btn-outline-primary:hover { background: #2f5597; color: #fff; }
</style>
@endpush
