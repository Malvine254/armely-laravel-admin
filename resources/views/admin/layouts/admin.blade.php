<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - Armely</title>
    
    <!-- Admin CSS from admin1 -->
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/style.css') }}">
    <link rel="icon" href="{{ asset('images/logo/logo1.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.3.0/mdb.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
    
    @stack('styles')
</head>
<body class="admin-body">

<!-- Top Navigation Bar -->
<nav class="navbar navbar-expand-lg admin-topbar py-3">
    <div class="container-fluid">
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-link text-white d-lg-none px-2" type="button" data-sidebar-toggle aria-label="Toggle sidebar">
                <i class="fas fa-bars"></i>
            </button>
            <a class="navbar-brand d-flex align-items-center gap-2 text-white" href="{{ route('admin.dashboard') }}">
                <img width="140" height="auto" src="{{ asset('images/logo/logo.svg') }}" alt="Armely logo">
            </a>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="text-white-50 small">Welcome, {{ auth('admin')->user()->name ?? 'Admin' }}</span>
            <div class="dropdown">
                <a data-mdb-dropdown-init class="dropdown-toggle d-flex align-items-center gap-2 text-white" href="#" id="navbarDropdownMenuAvatar" role="button" aria-expanded="false">
                    <img src="https://www.svgrepo.com/show/422421/account-avatar-multimedia.svg" class="rounded-circle" height="32" alt="User avatar" loading="lazy">
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownMenuAvatar">
                    <li><a class="dropdown-item" href="{{ route('admin.profile') }}">My profile</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.profile') }}#settings">Settings</a></li>
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.logout') }}" 
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<!-- Sidebar -->
<aside id="sidebarMenu" class="admin-sidebar">
    <div class="sidebar-brand">
        <img src="{{ asset('images/logo/logo.svg') }}" alt="Armely logo">
    </div>
    <ul class="admin-nav">
        <li>
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.tables') }}" class="nav-link {{ request()->routeIs('admin.tables*') ? 'active' : '' }}">
                <i class="fas fa-table"></i>
                <span>Manage User Page</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.admins') }}" class="nav-link {{ request()->routeIs('admin.admins*') ? 'active' : '' }}">
                <i class="fas fa-user-shield"></i>
                <span>Manage Admins</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.profile') }}" class="nav-link {{ request()->routeIs('admin.profile') ? 'active' : '' }}">
                <i class="fas fa-user"></i>
                <span>Profile & Settings</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.reports') }}" class="nav-link {{ request()->routeIs('admin.reports*') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i>
                <span>Generate Reports</span>
            </a>
        </li>
    </ul>
    <div class="sidebar-user mt-3">
        <strong>{{ auth('admin')->user()->name ?? 'Admin' }}</strong>
        <small>Administrator</small>
        <div class="mt-2">
            <a class="text-light small" href="{{ route('admin.logout') }}" 
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt me-2"></i>Logout
            </a>
        </div>
    </div>
</aside>

<form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
    @csrf
</form>

<!-- Main Content Area -->
<div class="content-area">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @yield('content')
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.4/dist/sweetalert2.all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.3.0/mdb.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.querySelector('[data-sidebar-toggle]');
    const sidebar = document.getElementById('sidebarMenu');
    if (toggle && sidebar) {
        toggle.addEventListener('click', function () {
            sidebar.classList.toggle('is-open');
        });
    }
});
</script>

@stack('scripts')

</body>
</html>
