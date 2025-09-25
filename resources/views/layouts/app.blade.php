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

                    <!-- Reports -->
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
                        </a>
                    </li>

                    <!-- Users (Admin Only) -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}"
                            href="{{ route('users.index') }}">
                            <i class="fas fa-users"></i> Pengguna
                        </a>
                    </li>

                    <!-- Units (Admin Only) -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('units.*') ? 'active' : '' }}"
                            href="{{ route('units.index') }}">
                            <i class="fas fa-building"></i> Unit Kerja
                        </a>
                    </li>

                    <!-- Admin Tools -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="adminToolsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-cogs"></i> Tools Admin
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="adminToolsDropdown">
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
                            <button type="submit" class="logout-btn" onclick="return confirmLogout(event)">
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
                            <button type="submit" class="logout-btn" onclick="return confirmLogout(event)">
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

        @yield('content')
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Logout Fix Script -->
    <script>
    function confirmLogout(event) {
        if (confirm('Yakin ingin logout?')) {
            // Refresh CSRF token sebelum submit
            event.preventDefault();
            
            fetch('/refresh-csrf')
                .then(response => response.json())
                .then(data => {
                    if (data.csrf_token) {
                        // Update CSRF token di form
                        const csrfInput = event.target.form.querySelector('input[name="_token"]');
                        if (csrfInput) {
                            csrfInput.value = data.csrf_token;
                        }
                    }
                    // Submit form setelah token diupdate
                    event.target.form.submit();
                })
                .catch(error => {
                    // Jika refresh gagal, submit tetap dilakukan
                    console.log('CSRF refresh failed, proceeding with logout');
                    event.target.form.submit();
                });
            return false;
        }
        return false;
    }

    // Auto-refresh CSRF token setiap 10 menit untuk mencegah expiry
    setInterval(function() {
        fetch('/refresh-csrf')
            .then(response => response.json())
            .then(data => {
                if (data.csrf_token) {
                    document.querySelector('meta[name="csrf-token"]').setAttribute('content', data.csrf_token);
                    // Update all CSRF forms
                    document.querySelectorAll('input[name="_token"]').forEach(input => {
                        input.value = data.csrf_token;
                    });
                }
            })
            .catch(error => console.log('Background CSRF refresh failed'));
    }, 600000); // 10 menit
    </script>

    @yield('scripts')
</body>
</html>