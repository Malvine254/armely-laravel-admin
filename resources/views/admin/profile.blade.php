@extends('admin.layouts.admin')

@section('page-title', 'Profile')
@section('title', 'Account Profile & Settings')

@push('styles')
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
    
    .modal .form-control:focus {
        border-color: #2f5597;
        box-shadow: 0 0 0 4px rgba(47, 85, 151, 0.1);
    }
    
    .modal textarea.form-control {
        min-height: 150px;
        resize: vertical;
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
    
    .profile-hero {
        background: linear-gradient(135deg, #2f5597 0%, #1e3c73 100%);
        border-radius: 20px;
        padding: 60px 30px;
        color: white;
        margin-bottom: 30px;
        position: relative;
        box-shadow: 0 10px 30px rgba(47, 85, 151, 0.15);
    }
    .profile-content-wrapper {
        position: relative;
    }
    .profile-sidebar-card {
        border-radius: 20px;
        border: 1px solid #e4e7ec;
        background: #fff;
    }
    .profile-avatar-container {
        position: relative;
        display: inline-block;
        margin-bottom: 15px;
    }
    .profile-avatar-img {
        width: 120px;
        height: 120px;
        border: 6px solid #fff;
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        object-fit: cover;
    }
    .nav-tabs-modern {
        border-bottom: 2px solid #f2f4f7;
    }
    .nav-tabs-modern .nav-item {
        margin-right: 30px;
    }
    .nav-tabs-modern .nav-link {
        border: none;
        color: #667085;
        font-weight: 600;
        padding: 15px 0;
        border-bottom: 3px solid transparent;
        transition: all 0.3s ease;
    }
    .nav-tabs-modern .nav-link:hover {
        color: #2f5597;
    }
    .nav-tabs-modern .nav-link.active {
        color: #2f5597 !important;
        background: none !important;
        border-bottom: 3px solid #2f5597 !important;
    }
    .profile-stat-box {
        background: #f9fafb;
        border-radius: 12px;
        padding: 1rem;
        text-align: center;
        border: 1px solid #f2f4f7;
    }
    .activity-timeline-item {
        position: relative;
        padding-left: 2rem;
        padding-bottom: 1.5rem;
        border-left: 2px solid #eef2ff;
    }
    .activity-timeline-item:last-child {
        border-left: none;
        padding-bottom: 0;
    }
    .timeline-dot {
        position: absolute;
        left: -9px;
        top: 0;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: #2f5597;
        border: 3px solid #fff;
        box-shadow: 0 0 0 3px #eef2ff;
    }
    .session-card {
        border-radius: 12px;
        border: 1px solid #e4e7ec;
        padding: 1rem;
        transition: all 0.2s;
    }
    .session-card:hover {
        border-color: #2f5597;
        background: #f5f8ff;
    }
    .sticky-sidebar {
        position: -webkit-sticky;
        position: sticky;
        top: 90px;
        z-index: 10;
    }
    .bg-soft-primary { background: #eef2ff !important; }
    .bg-soft-success { background: #ecfdf5 !important; }
    .bg-soft-info { background: #f0f9ff !important; }
    .bg-soft-warning { background: #fffbeb !important; }
    .text-primary { color: #2f5597 !important; }
</style>
@endpush

@section('content')
<div class="profile-hero mb-4 shadow-sm">
    <div class="row align-items-center px-lg-3">
        <div class="col-md-9">
            <h1 class="font-weight-bold text-white mb-2">Hello, {{ explode(' ', $admin->name)[0] }}!</h1>
            <p class="text-white-50 mb-0 font-weight-light" style="font-size: 1.1rem;">Welcome to your secure administrative portal. Oversee your profile and security parameters here.</p>
        </div>
        <div class="col-md-3 text-md-right d-none d-md-block">
            <div class="badge badge-light text-primary px-3 py-2 rounded-pill shadow-sm" style="font-size: 0.85rem;">
                <i class="fas fa-calendar-alt mr-2 text-primary"></i> {{ now()->format('D, d M Y') }}
            </div>
        </div>
    </div>
</div>

<div class="profile-content-wrapper">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-4 col-xl-3 mb-4">
            <div class="card profile-sidebar-card shadow-sm border-0 sticky-sidebar">
                <div class="card-body text-center p-4">
                    <div class="profile-avatar-container">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($admin->name) }}&background=2f5597&color=fff&bold=true&size=128" 
                             alt="{{ $admin->name }}" class="profile-avatar-img rounded-circle border-white shadow-sm" style="border-width: 5px !important; border-style: solid;">
                        <div class="position-absolute bg-success border border-white rounded-circle shadow-sm" style="width: 18px; height: 18px; bottom: 10px; right: 10px; border-width: 3px !important; border-style: solid;" title="Online Status"></div>
                    </div>
                    
                    <h4 class="mb-1 font-weight-bold text-dark">{{ $admin->name }}</h4>
                    <p class="text-primary font-weight-bold small mb-4">
                        <i class="fas fa-crown mr-1 text-warning"></i> {{ $admin->role ?? 'Administrator' }}
                    </p>

                    <div class="row mb-4">
                        <div class="col-6">
                            <div class="profile-stat-box">
                                <div class="font-weight-bold h5 mb-0">{{ count($activityHistory) }}</div>
                                <small class="text-muted small">Actions</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="profile-stat-box">
                                <div class="font-weight-bold h5 mb-0">{{ count(array_filter($loginHistory, fn($x) => $x['action'] === 'Login')) }}</div>
                                <small class="text-muted small">Logins</small>
                            </div>
                        </div>
                    </div>

                    <div class="text-left mb-4">
                        <div class="mb-3 d-flex align-items-center">
                            <div class="bg-soft-primary text-primary p-2 rounded-circle mr-3">
                                <i class="fas fa-envelope fa-fw"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block small">Email</small>
                                <span class="font-weight-bold text-dark text-break small">{{ $admin->email }}</span>
                            </div>
                        </div>
                        <div class="mb-3 d-flex align-items-center">
                            <div class="bg-soft-info text-info p-2 rounded-circle mr-3">
                                <i class="fas fa-phone fa-fw"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block small">Mobile</small>
                                <span class="font-weight-bold text-dark">{{ $admin->phone ?? 'Not set' }}</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="bg-soft-success text-success p-2 rounded-circle mr-3">
                                <i class="fas fa-calendar-check fa-fw"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block small">Member Since</small>
                                <span class="font-weight-bold text-dark">{{ optional($admin->joined_date)->format('M d, Y') ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary btn-rounded btn-block font-weight-bold">
                            <i class="fas fa-home mr-2"></i> Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-8 col-xl-9">
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-body p-4 p-md-5">
                    <!-- Tab Navigation -->
                    <ul class="nav nav-tabs nav-tabs-modern mb-5" id="profileTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="profile-tab" data-bs-toggle="pill" href="#profile" role="tab">
                                <i class="fas fa-id-card mr-2"></i>Profile
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="settings-tab" data-bs-toggle="pill" href="#settings" role="tab">
                                <i class="fas fa-fingerprint mr-2"></i>Security
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="activity-tab" data-bs-toggle="pill" href="#activity" role="tab">
                                <i class="fas fa-stream mr-2"></i>Activity
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="sessions-tab" data-bs-toggle="pill" href="#sessions" role="tab">
                                <i class="fas fa-history mr-2"></i>History
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content" id="profileTabContent">
                        <!-- Profile Information Tab -->
                        <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div>
                                    <h4 class="font-weight-bold text-dark mb-1">Account Identity</h4>
                                    <p class="text-muted mb-0">Update your primary information below</p>
                                </div>
                                <span class="badge badge-primary px-3 py-2 rounded-pill shadow-none">Status: Verified</span>
                            </div>
                            
                            <form method="POST" action="{{ route('admin.profile.update') }}">
                                @csrf
                                <div class="row mb-5">
                                    <div class="col-md-12 mb-4">
                                        <label class="form-label text-dark font-weight-bold">Full Professional Name</label>
                                        <div class="input-group input-group-lg border rounded overflow-hidden">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-white border-0"><i class="fas fa-user-edit text-muted"></i></span>
                                            </div>
                                            <input type="text" name="name" class="form-control border-0 bg-white" value="{{ old('name', $admin->name) }}" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label class="form-label text-dark font-weight-bold">Corporate Email</label>
                                        <div class="input-group input-group-lg border rounded overflow-hidden">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-light border-0"><i class="fas fa-envelope-shield text-muted"></i></span>
                                            </div>
                                            <input type="email" class="form-control border-0 bg-light" value="{{ $admin->email }}" disabled>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label class="form-label text-dark font-weight-bold">Contact Number</label>
                                        <div class="input-group input-group-lg border rounded overflow-hidden">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-white border-0"><i class="fas fa-mobile-alt text-muted"></i></span>
                                            </div>
                                            <input type="text" name="phone" class="form-control border-0 bg-white" value="{{ old('phone', $admin->phone) }}" placeholder="+1 (555) 000-0000">
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-light p-4 rounded mb-4 border-left border-warning border-4">
                                    <div class="d-flex">
                                        <i class="fas fa-key fa-2x text-warning mr-3"></i>
                                        <div class="flex-grow-1">
                                            <h6 class="font-weight-bold text-dark mb-1">Credential Security</h6>
                                            <p class="text-muted small mb-3">If you need to update your password, fill in the fields below. Ensure you use a strong, unique combination.</p>
                                            
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <input type="password" name="password" class="form-control" placeholder="New Secret Password" autocomplete="new-password">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm Secret Password" autocomplete="new-password">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-right mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm">
                                        <i class="fas fa-check-circle mr-2"></i> Apply Updates
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Security Tab -->
                        <div class="tab-pane fade" id="settings" role="tabpanel" aria-labelledby="settings-tab">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div>
                                    <h4 class="font-weight-bold text-dark mb-1">Security & Access</h4>
                                    <p class="text-muted mb-0">Management of authentication and protection</p>
                                </div>
                            </div>
                            
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="card border-0 bg-soft-info h-100 p-4 rounded-4">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="bg-info text-white p-3 rounded-circle mr-3 shadow-sm">
                                                <i class="fas fa-shield-alt fa-lg"></i>
                                            </div>
                                            <h5 class="font-weight-bold text-dark mb-0">Password Health</h5>
                                        </div>
                                        <p class="text-muted small">Your password security status is being monitored.</p>
                                        <ul class="list-unstyled small text-muted mb-0">
                                            <li class="mb-2"><i class="fas fa-check-circle text-success mr-2"></i> Min 8 characters</li>
                                            <li class="mb-2"><i class="fas fa-check-circle text-success mr-2"></i> Case sensitive</li>
                                            <li><i class="fas fa-check-circle text-success mr-2"></i> Symbols encouraged</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border-0 bg-soft-primary h-100 p-4 rounded-4">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="bg-primary text-white p-3 rounded-circle mr-3 shadow-sm">
                                                <i class="fas fa-user-lock fa-lg"></i>
                                            </div>
                                            <h5 class="font-weight-bold text-dark mb-0">Role & Access</h5>
                                        </div>
                                        <p class="text-muted small">You are currently logged in with <strong>{{ $admin->role ?? 'Primary Administrator' }}</strong> status.</p>
                                        <div class="p-2 bg-white rounded-3 border border-primary-light">
                                            <code class="text-primary small">PERMISSIONS: FULL_READ_WRITE</code>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Activity Log Tab -->
                        <div class="tab-pane fade" id="activity" role="tabpanel" aria-labelledby="activity-tab">
                            <h4 class="font-weight-bold text-dark mb-4">Recent Audit Trail</h4>
                            <div class="activity-timeline mt-2">
                                @forelse($activityHistory as $activity)
                                    <div class="activity-timeline-item">
                                        <div class="timeline-dot shadow-sm"></div>
                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                            <h6 class="font-weight-bold text-dark mb-0">
                                                @php
                                                    $action = is_array($activity) ? ($activity['type'] ?? 'Action') : ($activity->action ?? 'Action');
                                                    $description = is_array($activity) ? ($activity['description'] ?? '') : ($activity->description ?? '');
                                                    $timestamp = is_array($activity) ? ($activity['timestamp'] ?? now()) : ($activity->created_at ?? now());
                                                @endphp
                                                {{ ucfirst(str_replace('_', ' ', $action)) }}
                                            </h6>
                                            <span class="badge badge-light text-muted fw-normal">{{ \Carbon\Carbon::parse($timestamp)->format('H:i A') }}</span>
                                        </div>
                                        <p class="text-muted small mb-1">
                                            {{ \Illuminate\Support\Str::limit($description ?: 'Administrative update performed', 120) }}
                                        </p>
                                        <small class="text-muted-50 small"><i class="far fa-calendar-alt mr-1"></i> {{ \Carbon\Carbon::parse($timestamp)->format('M d, Y') }}</small>
                                    </div>
                                @empty
                                    <div class="text-center py-5">
                                        <i class="fas fa-history text-muted fa-3x opacity-25 mb-3"></i>
                                        <p class="text-muted">No recent activities found.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Session History Tab -->
                        <div class="tab-pane fade" id="sessions" role="tabpanel" aria-labelledby="sessions-tab">
                            <h4 class="font-weight-bold text-dark mb-4">Login Intelligence</h4>
                            <div class="row">
                                @forelse($loginHistory as $login)
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="session-card">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="bg-soft-primary p-2 rounded-circle mr-3">
                                                    <i class="fas fa-laptop text-primary"></i>
                                                </div>
                                                <div class="overflow-hidden">
                                                    <h6 class="font-weight-bold mb-0 text-dark">{{ is_array($login) ? $login['action'] : $login->action }}</h6>
                                                    <small class="text-muted text-truncate d-block">{{ is_array($login) ? ($login['entity_type'] ?? 'System') : ($login->entity_type ?? 'System') }}</small>
                                                </div>
                                            </div>
                                            <div class="badge badge-light text-dark mb-2 w-100 py-2 border shadow-none">
                                                {{ \Carbon\Carbon::parse(is_array($login) ? ($login['timestamp'] ?? now()) : ($login->created_at ?? now()))->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-center text-muted col-12 py-4">No login history recorded.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
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

<!-- Two-Factor Authentication Modal -->
<div class="modal fade" id="twoFactorModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-gradient border-0">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-mobile-alt mr-2 text-primary"></i>Set Up Two-Factor Authentication
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Enhanced Security:</strong> Two-factor authentication adds an extra layer of security to your account by requiring both your password and a code from your phone.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <h6 class="font-weight-bold mb-3">Step 1: Scan QR Code</h6>
                        <div class="card bg-light border-0 p-3 text-center mb-3">
                            <div class="qr-code-placeholder" style="background: #f8f9fa; width: 200px; height: 200px; margin: 0 auto; display: flex; align-items: center; justify-content: center; border: 2px dashed #2f5597; border-radius: 8px;">
                                <div class="text-center">
                                    <i class="fas fa-qrcode" style="font-size: 4rem; color: #2f5597;"></i>
                                    <p class="text-muted mt-2 small">QR Code</p>
                                </div>
                            </div>
                        </div>
                        <p class="text-muted small">Use Google Authenticator, Microsoft Authenticator, or Authy</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="font-weight-bold mb-3">Step 2: Enter Verification Code</h6>
                        <form>
                            <div class="mb-3">
                                <label class="form-label small fw-500">Verification Code</label>
                                <input type="text" class="form-control form-control-lg text-center" placeholder="000000" maxlength="6" style="letter-spacing: 0.5rem; font-size: 1.5rem;">
                            </div>
                            <p class="text-muted small">Enter the 6-digit code from your authenticator app</p>
                        </form>
                    </div>
                </div>

                <hr class="my-4">

                <h6 class="font-weight-bold mb-3">Backup Codes</h6>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <strong>Important:</strong> Save these backup codes in a secure location. Use them if you lose access to your authenticator app.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <div class="backup-codes bg-light p-3 rounded mb-3" style="font-family: monospace; font-size: 0.9rem; line-height: 1.8;">
                    <div>12AB-3456-7890-CD</div>
                    <div>EF12-3456-7890-AB</div>
                    <div>CD12-3456-7890-EF</div>
                    <div>34AB-5678-9012-CD</div>
                </div>
                <button class="btn btn-sm btn-outline-secondary" type="button">
                    <i class="fas fa-copy mr-1"></i>Copy Codes
                </button>
            </div>
            <div class="modal-footer border-0 bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">
                    <i class="fas fa-check mr-1"></i>Verify & Enable 2FA
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Simple tab state persistence
        const lastTab = localStorage.getItem('profileActiveTab');
        if (lastTab) {
            const tabBootstrap = new bootstrap.Tab(document.querySelector(`#${lastTab}`));
            tabBootstrap.show();
        }

        const tabLinks = document.querySelectorAll('button[data-bs-toggle="tab"]');
        tabLinks.forEach(link => {
            link.addEventListener('shown.bs.tab', function(e) {
                localStorage.setItem('profileActiveTab', e.target.id);
            });
        });
    });
</script>
@endpush

@push('styles')
<style>
/* Profile Page - Modern Styling (respects existing palette: primary #2f5597, accent #17a2b8) */

/* Cards & Shadows */
.shadow-soft { 
    box-shadow: 0 8px 24px rgba(0,0,0,0.08), 0 2px 6px rgba(0,0,0,0.04); 
    border: 1px solid rgba(0,0,0,0.04); 
}

.card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.card:hover {
    box-shadow: 0 12px 32px rgba(0,0,0,0.12);
    transform: translateY(-2px);
}

/* Profile Avatar */
.profile-avatar img { 
    box-shadow: 0 8px 24px rgba(47,85,151,0.25); 
    border: 4px solid #fff;
    transition: transform 0.3s ease;
}

.profile-avatar img:hover {
    transform: scale(1.05);
}

/* Profile Stats */
.profile-stats {
    background: linear-gradient(135deg, #f7f9fb 0%, #f0f3f7 100%);
    padding: 1rem;
    border-radius: 8px;
}

.stat-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: bold;
    color: #2f5597;
}

.stat-label {
    color: #666;
    font-size: 0.85rem;
}

/* Navigation Tabs */
.nav-tabs {
    border: none;
    background: linear-gradient(to right, #f7f9fb, #f0f3f7);
    padding: 0.75rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    gap: 0.5rem;
}

.nav-tabs .nav-link {
    color: #666;
    font-weight: 500;
    border: none;
    border-radius: 6px;
    padding: 0.75rem 1.25rem;
    transition: all 0.3s ease;
    position: relative;
}

.nav-tabs .nav-link:hover {
    color: #2f5597;
    background: rgba(47,85,151,0.05);
}

.nav-tabs .nav-link.active {
    background: linear-gradient(135deg, #2f5597 0%, #1e3a5f 100%);
    color: #fff;
    box-shadow: 0 4px 12px rgba(47,85,151,0.25);
}

.nav-tabs .nav-link.active::after {
    content: '';
    position: absolute;
    bottom: -8px;
    left: 50%;
    transform: translateX(-50%);
    width: 30px;
    height: 3px;
    background: #2f5597;
    border-radius: 2px;
}

/* Form Controls */
.form-control, .form-select {
    border: 1px solid #e0e3e8;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #2f5597;
    box-shadow: 0 0 0 3px rgba(47,85,151,0.1);
}

/* Input Groups with Icons */
.input-group-text {
    background: linear-gradient(135deg, #f7f9fb 0%, #f0f3f7 100%);
    border: 1px solid #e0e3e8;
    color: #2f5597;
}

/* Security Settings */
.security-settings .setting-item {
    transition: all 0.3s ease;
    border-left: 4px solid transparent !important;
}

.security-settings .setting-item:hover {
    border-left-color: #2f5597 !important;
    background: rgba(47,85,151,0.02) !important;
}

/* Activity Timeline */
.activity-timeline {
    position: relative;
    padding: 0.5rem 0;
}

.activity-item {
    display: flex;
    align-items: flex-start;
}

.activity-icon {
    min-width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #f7f9fb 0%, #f0f3f7 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
}

.activity-item h6 {
    color: #2f5597;
    font-size: 0.95rem;
}

/* Tables */
.table thead th {
    background: linear-gradient(135deg, #2f5597 0%, #1e3a5f 100%);
    color: #fff;
    border: none;
    font-weight: 600;
    padding: 1rem;
}

.table tbody tr {
    border-color: rgba(0,0,0,0.05);
    transition: all 0.2s ease;
}

.table tbody tr:hover {
    background: rgba(47,85,151,0.02);
}

.table tbody td {
    vertical-align: middle;
    padding: 1rem;
}

/* Badges */
.badge {
    padding: 0.5rem 0.75rem;
    font-weight: 500;
    border-radius: 6px;
}

.badge.bg-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
}

.badge.bg-primary {
    background: linear-gradient(135deg, #2f5597 0%, #17a2b8 100%) !important;
}

/* Buttons */
.btn-primary, .btn-outline-primary {
    border-radius: 6px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-primary {
    background: linear-gradient(135deg, #2f5597 0%, #1e3a5f 100%);
    border: none;
}

.btn-primary:hover {
    box-shadow: 0 6px 16px rgba(47,85,151,0.3);
    transform: translateY(-2px);
}

.btn-outline-primary {
    border: 2px solid #2f5597;
    color: #2f5597;
}

.btn-outline-primary:hover {
    background: linear-gradient(135deg, #2f5597 0%, #1e3a5f 100%);
    color: #fff;
    border-color: transparent;
}

/* Modal Styling */
.modal-header.bg-gradient {
    background: linear-gradient(135deg, #2f5597 0%, #1e3a5f 100%);
    color: #fff;
}

.modal-content {
    border-radius: 12px;
}

/* QR Code Placeholder */
.qr-code-placeholder {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

/* Page Title */
.page-title h1 {
    color: #2f5597;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.page-title p {
    color: #666;
    font-size: 1.05rem;
}

/* Alerts */
.alert {
    border: none;
    border-radius: 8px;
    border-left: 4px solid;
}

.alert-success {
    background: rgba(40, 167, 69, 0.1);
    border-left-color: #28a745;
}

.alert-danger {
    background: rgba(220, 53, 69, 0.1);
    border-left-color: #dc3545;
}

.alert-warning {
    background: rgba(255, 193, 7, 0.1);
    border-left-color: #ffc107;
}

.alert-info {
    background: rgba(23, 162, 184, 0.1);
    border-left-color: #17a2b8;
}

/* Sticky Profile Card */
.profile-card.sticky-top {
    margin-top: 0;
}

@media (max-width: 992px) {
    .profile-card.sticky-top {
        position: relative;
        top: 0 !important;
    }
}

/* Password Input Icons */
.password-toggle {
    cursor: pointer;
    color: #2f5597;
    transition: color 0.2s ease;
}

.password-toggle:hover {
    color: #1e3a5f;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .nav-tabs {
        flex-wrap: nowrap;
        overflow-x: auto;
    }

    .nav-tabs .nav-link {
        white-space: nowrap;
    }

    .stat-item {
        flex-direction: column;
        text-align: center;
    }
}
</style>
@endpush
