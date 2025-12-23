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

    <style>
    /* Modern, consistent pagination styles across admin */
    /* DataTables pagination */
    .dataTables_wrapper .dataTables_paginate {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 6px;
        padding: 0.75rem 0;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        border: none !important;
        border-radius: 999px !important;
        background: #f2f4f7 !important;
        color: #344054 !important;
        padding: 0.4rem 0.75rem !important;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #e4e7ec !important;
        color: #1f2937 !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: linear-gradient(135deg, #6366f1, #8b5cf6) !important;
        color: #fff !important;
        box-shadow: 0 8px 16px rgba(99, 102, 241, 0.25);
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
        opacity: 0.45;
        cursor: not-allowed;
        background: #f9fafb !important;
        color: #98a2b3 !important;
    }

    /* DataTables top controls toolbar */
    .dataTables_wrapper .row:first-child {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 0.75rem;
        padding: 0.5rem 0.75rem;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
    }
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        float: none;
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 0;
    }
    .dataTables_wrapper .dataTables_length { flex: 0 0 auto; }
    .dataTables_wrapper .dataTables_filter { flex: 1 1 auto; justify-content: flex-end; }
    .dataTables_wrapper .dataTables_length label,
    .dataTables_wrapper .dataTables_filter label {
        margin-bottom: 0;
        color: #6b7280;
        font-weight: 500;
        font-size: 0.925rem;
    }
    .dataTables_wrapper .dataTables_length select,
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 0.45rem 0.65rem;
        background: #fff;
        color: #111827;
        transition: box-shadow 0.2s ease, border-color 0.2s ease, background 0.2s ease;
    }
    .dataTables_wrapper .dataTables_length select {
        min-width: 84px;
    }
    .dataTables_wrapper .dataTables_filter input {
        min-width: 280px;
        max-width: 420px;
        width: 100%;
        padding-left: 2rem;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23344054' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='11' cy='11' r='8'%3E%3C/circle%3E%3Cline x1='21' y1='21' x2='16.65' y2='16.65'%3E%3C/line%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: 0.6rem center;
        background-size: 16px 16px;
    }
    .dataTables_wrapper .dataTables_filter input:focus,
    .dataTables_wrapper .dataTables_length select:focus {
        outline: none;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.2);
        border-color: #6366f1;
        background: #fff;
    }
    /* Stack nicely on small screens */
    @media (max-width: 576px) {
        .dataTables_wrapper .row:first-child {
            flex-wrap: wrap;
        }
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            justify-content: center;
            text-align: center;
            width: 100%;
            gap: 8px;
        }
        .dataTables_wrapper .dataTables_filter input {
            max-width: 100%;
        }
    }
    .dataTables_wrapper .dataTables_info {
        color: #6b7280;
        padding: 0.5rem 0;
        text-align: center;
        float: none !important;
        width: 100%;
    }

    /* Make the bottom info + pagination row stack nicely and center */
    .dataTables_wrapper .row:last-child {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    .dataTables_wrapper .row:last-child > div {
        flex: 0 0 100%;
        max-width: 100%;
    }
    .dataTables_wrapper .dataTables_paginate {
        justify-content: center;
    }

    /* On wider screens, align info left and pagination right */
    @media (min-width: 992px) {
        .dataTables_wrapper .row:last-child {
            justify-content: space-between;
        }
        .dataTables_wrapper .row:last-child > div {
            flex: 0 0 auto;
            max-width: none;
        }
        .dataTables_wrapper .dataTables_info {
            text-align: left;
            width: auto;
        }
        .dataTables_wrapper .dataTables_paginate {
            justify-content: flex-end;
        }
    }

    /* Bootstrap/Laravel paginator */
    .pagination {
        justify-content: center;
        gap: 6px;
        padding: 0.75rem 0;
    }
    .page-item .page-link {
        border: none;
        border-radius: 999px;
        background: #f2f4f7;
        color: #344054;
        padding: 0.4rem 0.75rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    .page-item .page-link:hover {
        background: #e4e7ec;
        color: #1f2937;
    }
    .page-item.active .page-link {
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: #fff;
        box-shadow: 0 8px 16px rgba(99, 102, 241, 0.25);
    }
    .page-item.disabled .page-link {
        opacity: 0.45;
        cursor: not-allowed;
        background: #f9fafb;
        color: #98a2b3;
    }
    .page-link:focus {
        outline: none;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.2);
    }
    </style>
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
                <a class="dropdown-toggle d-flex align-items-center gap-2 text-white" href="#" id="navbarDropdownMenuAvatar" role="button" aria-expanded="false" data-bs-toggle="dropdown">
                    <img src="https://www.svgrepo.com/show/422421/account-avatar-multimedia.svg" class="rounded-circle" height="32" alt="User avatar" loading="lazy">
                </a>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-right" aria-labelledby="navbarDropdownMenuAvatar">
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.profile') }}">
                            <i class="fas fa-user-circle me-2 text-primary"></i>
                            My profile
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.profile') }}#settings">
                            <i class="fas fa-gear me-2 text-primary"></i>
                            Settings
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.logout.get') }}" 
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-right-from-bracket me-2 text-danger"></i>
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
                <a class="text-light small" href="{{ route('admin.logout.get') }}" 
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

    // Bootstrap handles dropdowns via data-bs-toggle; no custom handler needed
});
</script>

@stack('scripts')

</body>
</html>
