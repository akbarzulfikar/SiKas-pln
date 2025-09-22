<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Informasi Kas PLN UP3 Langsa')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --pln-blue: #003d7a;
            --pln-blue-dark: #002952;
            --pln-yellow: #ffd700;
        }

        body {
            background-color: #f8f9fa;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar-custom {
            background: linear-gradient(135deg, var(--pln-blue), var(--pln-blue-dark));
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 15px 0;
        }

        .navbar-brand {
            font-weight: bold;
            font-size: 1.4rem;
            color: white !important;
        }

        .navbar-brand i {
            margin-right: 10px;
            color: var(--pln-yellow);
        }

        .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            padding: 8px 15px !important;
            margin: 0 2px;
            border-radius: 6px;
            transition: all 0.3s ease;
            position: relative;
        }

        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            background-color: rgba(255, 255, 255, 0.15);
            color: white !important;
            transform: translateY(-1px);
        }

        .navbar-user {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.9rem;
        }

        .user-info {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            line-height: 1.2;
        }

        .user-name {
            font-weight: 600;
            color: white;
        }

        .user-unit {
            font-size: 0.8rem;
            opacity: 0.8;
        }

        .user-role {
            font-size: 0.7rem;
            background: rgba(255, 255, 255, 0.2);
            padding: 2px 6px;
            border-radius: 10px;
            margin-top: 2px;
        }

        .logout-btn {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            border-radius: 6px;
            padding: 6px 12px;
            font-size: 0.8rem;
            transition: all 0.3s ease;
            text-decoration: none;
            margin-left: 15px;
        }

        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border-color: rgba(255, 255, 255, 0.5);
            transform: translateY(-1px);
        }

        .main-content {
            min-height: calc(100vh - 160px);
            padding: 20px 0;
        }

        .alert {
            border-radius: 10px;
            border: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .admin-badge {
            background: #dc3545;
            color: white;
            font-size: 0.6rem;
            padding: 2px 5px;
            border-radius: 8px;
            margin-left: 5px;
            font-weight: 600;
        }

        .dropdown-menu {
            border-radius: 8px;
            border: none;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            margin-top: 5px;
        }

        .dropdown-item {
            padding: 8px 16px;
            transition: all 0.2s ease;
            color: #333;
        }

        .dropdown-item:hover {
            background-color: #f8f9fa;
            transform: translateX(5px);
            color: var(--pln-blue);
        }

        .dropdown-item.active {
            background-color: var(--pln-blue) !important;
            color: white !important;
        }

        .dropdown-item i {
            margin-right: 8px;
            width: 16px;
        }

        .nav-item.dropdown .nav-link.dropdown-toggle::after {
            margin-left: 8px;
        }

        footer {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-top: 1px solid #dee2e6;
            padding: 20px 0;
            margin-top: 40px;
        }

        /* Mobile Responsive */
        @media (max-width: 991px) {
            .navbar-user {
                margin-top: 15px;
                padding-top: 15px;
                border-top: 1px solid rgba(255, 255, 255, 0.2);
            }

            .user-info {
                align-items: flex-start;
            }

            .logout-btn {
                margin-left: 0;
                margin-top: 10px;
                display: inline-block;
            }
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('dashboard.index') }}">
                <i class="fas fa-bolt"></i> PLN UP3 Langsa - Sistem Kas
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon" style="background-image: url('data:image/svg+xml,%3csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 30 30\'%3e%3cpath stroke=\'rgba%28255, 255, 255, 0.75%29\' stroke-linecap=\'round\' stroke-miterlimit=\'10\' stroke-width=\'2\' d=\'M4 7h22M4 15h22M4 23h22\'/%3e%3c/svg%3e');"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <!-- Dashboard -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard.*') ? 'active' : '' }}"
                            href="{{ route('dashboard.index') }}">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>

                    <!-- Transactions -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}"
                            href="{{ route('transactions.index') }}">
                            <i class="fas fa-exchange-alt"></i> Transaksi
                        </a>
                    </li>

                    <!-- Reports - FIXED -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}"
                            href="{{ route('reports.index') }}">
                            <i class="fas fa-chart-bar"></i> Laporan Transaksi
                        </a>
                    </li>

                    @if(Auth::user()->role === 'admin')
                    <!-- Categories (Admin Only) -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}"
                            href="{{ route('categories.index') }}">
                            <i class="fas fa-tags"></i> Kategori
                            <span class="admin-badge">Admin</span>
                        </a>
                    </li>

                    <!-- Admin Dropdown Menu -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('users.*') || request()->routeIs('units.*') ? 'active' : '' }}" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-cog"></i> Admin
                            <span class="admin-badge">Only</span>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="adminDropdown">
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                                    <i class="fas fa-users"></i> Manajemen User
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('units.*') ? 'active' : '' }}" href="{{ route('units.index') }}">
                                    <i class="fas fa-building"></i> Manajemen Unit
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item" href="#" onclick="alert('Fitur ini akan segera hadir!')">
                                    <i class="fas fa-cog"></i> Pengaturan Sistem
                                    <small class="text-muted d-block">Coming Soon</small>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#" onclick="alert('Fitur ini akan segera hadir!')">
                                    <i class="fas fa-database"></i> Backup Data
                                    <small class="text-muted d-block">Coming Soon</small>
                                </a>
                            </li>
                        </ul>
                    </li>
                    @endif
                </ul>

                <!-- User Info -->
                <div class="navbar-user d-none d-lg-flex align-items-center">
                    <div class="user-info me-3">
                        <div class="user-name">{{ Auth::user()->name }}</div>
                        <div class="user-unit">{{ Auth::user()->unit->unit_name }}</div>
                        <div class="user-role">{{ Auth::user()->role_display_name }}</div>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="me-3">
                            <i class="fas fa-calendar"></i> {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
                        </span>
                        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="logout-btn" onclick="return confirm('Yakin ingin logout?')">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Mobile User Info -->
                <div class="navbar-user d-lg-none">
                    <div class="user-info">
                        <div class="user-name">{{ Auth::user()->name }}</div>
                        <div class="user-unit">{{ Auth::user()->unit->unit_name }}</div>
                        <div class="user-role">{{ Auth::user()->role_display_name }}</div>
                        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="logout-btn" onclick="return confirm('Yakin ingin logout?')">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        @if(session('success'))
        <div class="container-fluid">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="container-fluid">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
        @endif

        @if(session('warning'))
        <div class="container-fluid">
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
        @endif

        @if(session('info'))
        <div class="container-fluid">
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="fas fa-info-circle"></i> {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
        @endif

        @if($errors->any())
        <div class="container-fluid">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
        @endif

        @yield('content')
    </div>

    <!-- Footer -->
    <footer class="text-center">
        <div class="container">
            <div class="row">
                <div class="col-md-6 text-md-start text-center">
                    <p class="mb-0 text-muted">
                        <i class="fas fa-bolt text-primary"></i>
                        <strong>Sistem Informasi Kas PLN UP3 Langsa</strong>
                    </p>
                    <small class="text-muted">
                        Version 1.0 &copy; {{ date('Y') }} - Built with Laravel {{ app()->version() }}
                    </small>
                </div>
                <div class="col-md-6 text-md-end text-center">
                    @auth
                    <p class="mb-0 text-muted">
                        <i class="fas fa-building"></i> {{ Auth::user()->unit->unit_name }}
                        <br>
                        <small>Login sebagai: <strong>{{ Auth::user()->name }}</strong> ({{ Auth::user()->role_display_name }})</small>
                    </p>
                    @endauth
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    if (alert.querySelector('.btn-close')) {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    }
                });
            }, 5000);
        });

        // Add loading state to forms
        document.querySelectorAll('form').forEach(function(form) {
            form.addEventListener('submit', function() {
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn && !submitBtn.disabled) {
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
                    submitBtn.disabled = true;

                    // Re-enable after 10 seconds to prevent permanent disable
                    setTimeout(function() {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }, 10000);
                }
            });
        });

        // Confirm delete actions
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-delete') ||
                e.target.closest('.btn-delete') ||
                (e.target.getAttribute('onclick') && e.target.getAttribute('onclick').includes('confirmDelete'))) {
                if (!confirm('Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan!')) {
                    e.preventDefault();
                    return false;
                }
            }
        });

        // Highlight active dropdown items
        document.addEventListener('DOMContentLoaded', function() {
            const currentPath = window.location.pathname;
            const dropdownItems = document.querySelectorAll('.dropdown-item');

            dropdownItems.forEach(function(item) {
                const href = item.getAttribute('href');
                if (href && currentPath.startsWith(href) && href !== '#') {
                    item.classList.add('active');
                }
            });
        });

        // Show notification for coming soon features
        function showComingSoon(feature) {
            alert('Fitur ' + feature + ' akan segera hadir! 🚀');
        }
    </script>

    @yield('scripts')
</body>

</html>