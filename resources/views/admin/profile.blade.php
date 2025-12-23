@extends('admin.layouts.admin')

@section('page-title', 'Profile')
@section('title', 'Account Profile & Settings')

@section('content')
<div class="page-title mb-4">
    <h1>My Account</h1>
    <p>Manage your profile, security settings, and view your activity history.</p>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <strong>Success!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <strong>Error!</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row">
    <!-- Sidebar -->
    <div class="col-lg-3 mb-4">
        <div class="card profile-card shadow-sm border-0 sticky-top" style="top: 80px;">
            <div class="card-body text-center">
                <div class="profile-avatar-wrapper mb-3">
                    <div class="profile-avatar mx-auto mb-3">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($admin->name) }}&background=2f5597&color=fff&bold=true&size=128" 
                             alt="{{ $admin->name }}" class="rounded-circle img-fluid" width="100" height="100">
                    </div>
                    <h5 class="mb-1 fw-bold">{{ $admin->name }}</h5>
                    <p class="text-muted mb-3">
                        <span class="badge bg-primary">{{ $admin->role ?? 'Administrator' }}</span>
                    </p>
                </div>

                <div class="profile-stats mb-4">
                    <div class="stat-item pb-2">
                        <h3 class="mb-0">{{ count($activityHistory) }}</h3>
                        <small class="text-muted">Activities Logged</small>
                    </div>
                    <hr>
                    <div class="stat-item pb-2">
                        <h3 class="mb-0">{{ count(array_filter($loginHistory, fn($x) => $x['action'] === 'Login')) }}</h3>
                        <small class="text-muted">Total Logins</small>
                    </div>
                    <hr>
                    <div class="stat-item">
                        <h3 class="mb-0">{{ count($pageVisits) }}</h3>
                        <small class="text-muted">Pages Visited</small>
                    </div>
                </div>

                <div class="profile-meta text-start mb-3">
                    <div class="meta-item mb-2">
                        <small class="text-muted d-block">Email Address</small>
                        <span class="d-block text-break">{{ $admin->email }}</span>
                    </div>
                    @if($admin->phone)
                        <div class="meta-item mb-2">
                            <small class="text-muted d-block">Phone Number</small>
                            <span class="d-block">{{ $admin->phone }}</span>
                        </div>
                    @endif
                    <div class="meta-item">
                        <small class="text-muted d-block">Joined Date</small>
                        <span class="d-block">{{ optional($admin->joined_date)->format('M d, Y') ?? 'N/A' }}</span>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <a href="{{ route('admin.reports') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-chart-line me-1"></i> View Reports
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="col-lg-9">
        <!-- Tab Navigation -->
        <ul class="nav nav-tabs nav-tabs-modern mb-4" id="profileTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button">
                    <i class="fas fa-user me-2"></i>Profile Information
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="settings-tab" data-bs-toggle="tab" data-bs-target="#settings" type="button">
                    <i class="fas fa-shield-alt me-2"></i>Security Settings
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="activity-tab" data-bs-toggle="tab" data-bs-target="#activity" type="button">
                    <i class="fas fa-history me-2"></i>Activity Log
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="sessions-tab" data-bs-toggle="tab" data-bs-target="#sessions" type="button">
                    <i class="fas fa-sign-in-alt me-2"></i>Session History
                </button>
            </li>
        </ul>

        <div class="tab-content" id="profileTabContent">
        <div class="tab-content" id="profileTabContent">
            <!-- Profile Information Tab -->
            <div class="tab-pane fade show active" id="profile" role="tabpanel">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h6 class="card-title fw-bold mb-4">
                            <i class="fas fa-edit text-primary me-2"></i>Edit Profile Information
                        </h6>
                        
                        <form method="POST" action="{{ route('admin.profile.update') }}" class="needs-validation" novalidate>
                            @csrf
                            
                            <div class="row g-3 mb-4">
                                <div class="col-md-12">
                                    <label class="form-label fw-500">Full Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                               value="{{ old('name', $admin->name) }}" required>
                                    </div>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-500">Email Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        <input type="email" class="form-control" value="{{ $admin->email }}" disabled>
                                    </div>
                                    <small class="text-muted d-block mt-1">Email is managed by administrator</small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-500">Phone Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                                               value="{{ old('phone', $admin->phone) }}" placeholder="+1 (555) 000-0000">
                                    </div>
                                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <hr class="my-4">

                            <h6 class="fw-bold mb-3">Change Password</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-500">New Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                                               minlength="8" autocomplete="new-password" placeholder="Leave blank to keep current">
                                    </div>
                                    <small class="text-muted d-block mt-1">Minimum 8 characters</small>
                                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-500">Confirm Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        <input type="password" name="password_confirmation" class="form-control" 
                                               minlength="8" autocomplete="new-password">
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                                <small class="text-muted">Last updated: {{ optional($admin->updated_at)->diffForHumans() ?? 'Never' }}</small>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Security Settings Tab -->
            <div class="tab-pane fade" id="settings" role="tabpanel">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h6 class="card-title fw-bold mb-4">
                            <i class="fas fa-shield-alt text-success me-2"></i>Security & Privacy Settings
                        </h6>

                        <div class="security-settings">
                            <!-- Password Security -->
                            <div class="setting-item card bg-light border-0 mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="fw-bold mb-1">
                                                <i class="fas fa-key text-warning me-2"></i>Password Security
                                            </h6>
                                            <p class="text-muted mb-0 small">Ensure you have a strong, unique password</p>
                                        </div>
                                        <span class="badge bg-success">Active</span>
                                    </div>
                                    <div class="progress mt-2" style="height: 4px;">
                                        <div class="progress-bar bg-success" style="width: 100%;"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Activity Logging -->
                            <div class="setting-item card bg-light border-0 mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="fw-bold mb-1">
                                                <i class="fas fa-history text-info me-2"></i>Activity Logging
                                            </h6>
                                            <p class="text-muted mb-0 small">Record all administrative actions for auditing</p>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="activityLog" checked disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Two-Factor Authentication -->
                            <div class="setting-item card bg-light border-0 mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="fw-bold mb-1">
                                                <i class="fas fa-mobile-alt text-primary me-2"></i>Two-Factor Authentication (2FA)
                                            </h6>
                                            <p class="text-muted mb-0 small">Add an extra layer of security to your account</p>
                                        </div>
                                        <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#twoFactorModal">
                                            Set Up
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Login Alerts -->
                            <div class="setting-item card bg-light border-0 mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="fw-bold mb-1">
                                                <i class="fas fa-bell text-warning me-2"></i>Login Notifications
                                            </h6>
                                            <p class="text-muted mb-0 small">Get notified of unusual login activity</p>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="loginAlerts" checked>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Session Management -->
                            <div class="setting-item card bg-light border-0">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="fw-bold mb-1">
                                                <i class="fas fa-sign-out-alt text-danger me-2"></i>Session Management
                                            </h6>
                                            <p class="text-muted mb-0 small">Sign out of all other sessions immediately</p>
                                        </div>
                                        <button class="btn btn-sm btn-outline-danger" type="button">
                                            Sign Out All
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activity Log Tab -->
            <div class="tab-pane fade" id="activity" role="tabpanel">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h6 class="card-title fw-bold mb-4">
                            <i class="fas fa-history text-primary me-2"></i>Recent Activity
                        </h6>

                        @if(count($activityHistory) > 0)
                            <div class="activity-timeline">
                                @foreach($activityHistory as $activity)
                                    <div class="activity-item mb-3 pb-3 border-bottom">
                                        <div class="d-flex">
                                            <div class="activity-icon me-3">
                                                @if($activity['type'] === 'page_visit')
                                                    <i class="fas fa-globe text-info"></i>
                                                @elseif($activity['type'] === 'login')
                                                    <i class="fas fa-sign-in-alt text-success"></i>
                                                @elseif($activity['type'] === 'logout')
                                                    <i class="fas fa-sign-out-alt text-warning"></i>
                                                @else
                                                    <i class="fas fa-cog text-secondary"></i>
                                                @endif
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 fw-500">{{ ucfirst(str_replace('_', ' ', $activity['type'])) }}</h6>
                                                <p class="text-muted small mb-1">{{ $activity['description'] ?? $activity['entity_type'] }}</p>
                                                <small class="text-muted">
                                                    <i class="fas fa-clock me-1"></i>
                                                    {{ \Carbon\Carbon::parse($activity['timestamp'])->diffForHumans() }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-inbox text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-3">No activity recorded yet</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Session History Tab -->
            <div class="tab-pane fade" id="sessions" role="tabpanel">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h6 class="card-title fw-bold mb-4">
                            <i class="fas fa-sign-in-alt text-success me-2"></i>Login & Session History
                        </h6>

                        @if(count($loginHistory) > 0)
                            <div class="table-responsive">
                                <table class="table align-middle table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Action</th>
                                            <th>Type</th>
                                            <th>Time</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($loginHistory as $session)
                                            <tr>
                                                <td>
                                                    <span class="fw-500">
                                                        @if($session['action'] === 'Login')
                                                            <i class="fas fa-check-circle text-success me-2"></i>
                                                        @else
                                                            <i class="fas fa-times-circle text-warning me-2"></i>
                                                        @endif
                                                        {{ $session['action'] }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-dark">{{ $session['entity_type'] }}</span>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        {{ \Carbon\Carbon::parse($session['timestamp'])->format('M d, Y H:i') }}
                                                    </small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success">Active</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-inbox text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-3">No login history available</p>
                            </div>
                        @endif

                        @if(count($pageVisits) > 0)
                            <hr class="my-4">
                            <h6 class="fw-bold mb-3">Recently Visited Pages</h6>
                            <div class="pages-visited">
                                @foreach($pageVisits as $visit)
                                    <div class="visit-item d-flex justify-content-between align-items-center p-2 border-bottom small">
                                        <span>
                                            <i class="fas fa-arrow-right text-muted me-2"></i>
                                            {{ $visit['page'] ?? 'Admin Dashboard' }}
                                        </span>
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($visit['timestamp'])->diffForHumans() }}
                                        </small>
                                    </div>
                                @endforeach
                            </div>
                        @endif
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
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-mobile-alt me-2 text-primary"></i>Set Up Two-Factor Authentication
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Enhanced Security:</strong> Two-factor authentication adds an extra layer of security to your account by requiring both your password and a code from your phone.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3">Step 1: Scan QR Code</h6>
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
                        <h6 class="fw-bold mb-3">Step 2: Enter Verification Code</h6>
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

                <h6 class="fw-bold mb-3">Backup Codes</h6>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
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
                    <i class="fas fa-copy me-1"></i>Copy Codes
                </button>
            </div>
            <div class="modal-footer border-0 bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">
                    <i class="fas fa-check me-1"></i>Verify & Enable 2FA
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

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
