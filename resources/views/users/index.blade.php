@extends('layouts.app')

@section('title', 'Kelola User - Sistem Kas PLN')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <div class="page-icon me-3">
                <i class="fas fa-users"></i>
            </div>
            <h1 class="page-title mb-0">Kelola User</h1>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('users.export') }}" class="btn btn-outline-success">
                <i class="fas fa-file-csv"></i> Export CSV
            </a>
            <a href="{{ route('users.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah User
            </a>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="filter-card">
                <div class="filter-header">
                    <h5><i class="fas fa-filter"></i> Filter User</h5>
                </div>
                <div class="filter-body">
                    <form method="GET" action="{{ route('users.index') }}" id="filterForm">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">🔍 Pencarian</label>
                                <input type="text" class="form-control" name="search" 
                                       value="{{ $search }}" 
                                       placeholder="Nama, username, email, NIP...">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Role</label>
                                <select class="form-select" name="role">
                                    <option value="">👥 Semua Role</option>
                                    <option value="admin" {{ $role == 'admin' ? 'selected' : '' }}>
                                        🔐 Administrator
                                    </option>
                                    <option value="user" {{ $role == 'user' ? 'selected' : '' }}>
                                        👤 User
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Unit</label>
                                <select class="form-select" name="unit_id">
                                    <option value="">🏢 Semua Unit</option>
                                    @foreach($units as $unitOption)
                                    <option value="{{ $unitOption->unit_id }}" {{ $unit == $unitOption->unit_id ? 'selected' : '' }}>
                                        {{ $unitOption->unit_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="">✅ Semua Status</option>
                                    <option value="1" {{ $status === '1' ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ $status === '0' ? 'selected' : '' }}>Tidak Aktif</option>
                                </select>
                            </div>

                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-undo"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card total-card">
                <div class="stats-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $stats['total'] }}</h3>
                    <p>Total User</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card active-card">
                <div class="stats-icon">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $stats['active'] }}</h3>
                    <p>User Aktif</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card admin-card">
                <div class="stats-icon">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $stats['admin'] }}</h3>
                    <p>Administrator</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card user-card">
                <div class="stats-icon">
                    <i class="fas fa-user"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $stats['regular'] }}</h3>
                    <p>Regular User</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="row">
        <div class="col-12">
            <div class="table-card">
                <div class="table-header">
                    <h5><i class="fas fa-list"></i> Daftar User</h5>
                    <div class="table-info">
                        <span class="badge bg-primary">{{ $users->total() }} Total</span>
                    </div>
                </div>
                <div class="table-body">
                    @if($users->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Username & Email</th>
                                    <th>Role</th>
                                    <th>Unit</th>
                                    <th>NIP & Jabatan</th>
                                    <th>Status</th>
                                    <th>Last Login</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                <tr class="{{ !$user->is_active ? 'table-warning' : '' }}">
                                    <td>
                                        <div class="user-info">
                                            <div class="user-avatar">
                                                <div class="avatar-circle">
                                                    {{ $user->initials }}
                                                </div>
                                            </div>
                                            <div class="user-details">
                                                <div class="user-name">{{ $user->name }}</div>
                                                <small class="user-id text-muted">ID: {{ $user->user_id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="contact-info">
                                            <div class="username">
                                                <i class="fas fa-user"></i> {{ $user->username }}
                                            </div>
                                            <div class="email">
                                                <i class="fas fa-envelope"></i> {{ $user->email }}
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $user->role_badge_color }}">
                                            <i class="fas fa-{{ $user->role === 'admin' ? 'shield-alt' : 'user' }}"></i>
                                            {{ $user->role_display_name }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $user->unit->badge_color }}">
                                            <i class="fas fa-building"></i>
                                            {{ $user->unit->unit_name }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="job-info">
                                            @if($user->nip)
                                            <div class="nip">
                                                <i class="fas fa-id-card"></i> {{ $user->nip }}
                                            </div>
                                            @endif
                                            @if($user->position)
                                            <div class="position">
                                                <i class="fas fa-briefcase"></i> {{ $user->position }}
                                            </div>
                                            @endif
                                            @if(!$user->nip && !$user->position)
                                            <span class="text-muted">-</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $user->is_active ? 'success' : 'warning' }}">
                                            <i class="fas fa-{{ $user->is_active ? 'check-circle' : 'pause-circle' }}"></i>
                                            {{ $user->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($user->last_login)
                                        <div class="last-login">
                                            <div class="login-date">{{ $user->last_login->format('d/m/Y') }}</div>
                                            <small class="login-time text-muted">{{ $user->last_login->format('H:i') }}</small>
                                        </div>
                                        @else
                                        <span class="text-muted">Belum pernah login</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('users.show', $user->user_id) }}" 
                                               class="btn btn-outline-info" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('users.edit', $user->user_id) }}" 
                                               class="btn btn-outline-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('users.change-password', $user->user_id) }}" 
                                               class="btn btn-outline-secondary" title="Ganti Password">
                                                <i class="fas fa-key"></i>
                                            </a>
                                            @if($user->user_id !== Auth::user()->user_id)
                                            <button class="btn btn-outline-danger" 
                                                    onclick="confirmDelete('{{ $user->user_id }}', '{{ $user->name }}')"
                                                    title="Hapus User">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="pagination-info">
                            <small class="text-muted">
                                Menampilkan {{ $users->firstItem() }} - {{ $users->lastItem() }} 
                                dari {{ $users->total() }} user
                            </small>
                        </div>
                        <div class="pagination-links">
                            {{ $users->links() }}
                        </div>
                    </div>
                    @else
                    <div class="empty-state">
                        <i class="fas fa-users fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">Belum Ada User</h5>
                        <p class="text-muted">
                            @if(request()->hasAny(['search', 'role', 'unit_id', 'status']))
                            Tidak ada user yang sesuai dengan filter yang dipilih.<br>
                            <a href="{{ route('users.index') }}">Reset filter</a> atau ubah kriteria pencarian.
                            @else
                            Belum ada user yang terdaftar.<br>
                            <a href="{{ route('users.create') }}" class="btn btn-primary mt-2">
                                <i class="fas fa-plus"></i> Tambah User Pertama
                            </a>
                            @endif
                        </p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Form -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<style>
    .page-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, var(--pln-blue), var(--pln-blue-dark));
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
    }

    .page-title {
        font-size: 2rem;
        font-weight: 600;
        color: #333;
    }

    .filter-card,
    .table-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border: none;
        margin-bottom: 20px;
    }

    .filter-header,
    .table-header {
        background: linear-gradient(135deg, var(--pln-blue), var(--pln-blue-dark));
        color: white;
        padding: 15px 20px;
        border-radius: 12px 12px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .filter-body,
    .table-body {
        padding: 25px;
    }

    .stats-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        gap: 15px;
        border-left: 4px solid;
        transition: transform 0.2s ease;
    }

    .stats-card:hover {
        transform: translateY(-2px);
    }

    .total-card {
        border-left-color: var(--pln-blue);
    }

    .active-card {
        border-left-color: #28a745;
    }

    .admin-card {
        border-left-color: #dc3545;
    }

    .user-card {
        border-left-color: #17a2b8;
    }

    .stats-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: white;
    }

    .total-card .stats-icon {
        background: linear-gradient(135deg, var(--pln-blue), var(--pln-blue-dark));
    }

    .active-card .stats-icon {
        background: linear-gradient(135deg, #28a745, #34ce57);
    }

    .admin-card .stats-icon {
        background: linear-gradient(135deg, #dc3545, #e85d75);
    }

    .user-card .stats-icon {
        background: linear-gradient(135deg, #17a2b8, #20c4d6);
    }

    .stats-content h3 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 700;
        color: #333;
    }

    .stats-content p {
        margin: 0;
        color: #666;
        font-size: 0.9rem;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .user-avatar .avatar-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--pln-blue), var(--pln-blue-dark));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 0.9rem;
    }

    .user-name {
        font-weight: 600;
        color: #333;
    }

    .contact-info {
        line-height: 1.4;
    }

    .contact-info .username,
    .contact-info .email {
        display: flex;
        align-items: center;
        gap: 5px;
        margin-bottom: 2px;
        font-size: 0.9rem;
    }

    .contact-info .username {
        font-weight: 600;
        color: #495057;
    }

    .contact-info .email {
        color: #6c757d;
    }

    .job-info {
        line-height: 1.4;
    }

    .job-info .nip,
    .job-info .position {
        display: flex;
        align-items: center;
        gap: 5px;
        margin-bottom: 2px;
        font-size: 0.85rem;
        color: #6c757d;
    }

    .last-login {
        text-align: center;
    }

    .login-date {
        font-weight: 600;
        color: #495057;
        font-size: 0.9rem;
    }

    .table th {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        color: #495057;
        font-size: 0.9rem;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .table-warning {
        --bs-table-bg: rgba(255, 193, 7, 0.1);
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #666;
    }

    .pagination-info {
        flex: 1;
    }

    .pagination-links .pagination {
        margin: 0;
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .stats-card {
            margin-bottom: 15px;
        }
        
        .table th,
        .table td {
            font-size: 0.8rem;
            padding: 8px;
        }
        
        .btn-group {
            flex-direction: column;
            width: 100%;
        }
        
        .user-info {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

<script>
function confirmDelete(userId, userName) {
    if (confirm(`Apakah Anda yakin ingin menghapus user "${userName}"?\n\nTindakan ini tidak dapat dibatalkan!`)) {
        const form = document.getElementById('deleteForm');
        form.action = `/users/${userId}`;
        form.submit();
    }
}
</script>
@endsection