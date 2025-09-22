@extends('layouts.app')

@section('title', 'Detail User - ' . $user->name)

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">
                        <i class="fas fa-user-circle text-primary"></i> 
                        Detail User
                    </h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Manajemen User</a></li>
                            <li class="breadcrumb-item active">Detail User</li>
                        </ol>
                    </nav>
                </div>
                <div class="btn-group">
                    <a href="{{ route('users.edit', $user->user_id) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit User
                    </a>
                    <a href="{{ route('users.change-password', $user->user_id) }}" class="btn btn-warning">
                        <i class="fas fa-key"></i> Ganti Password
                    </a>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- User Profile Card -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-user"></i> Profil User
                    </h5>
                </div>
                <div class="card-body text-center">
                    <!-- Avatar -->
                    <div class="mb-3">
                        <div class="avatar-large mx-auto">
                            {{ $user->initials }}
                        </div>
                    </div>

                    <!-- Basic Info -->
                    <h4 class="mb-1">{{ $user->name }}</h4>
                    <p class="text-muted mb-2">@{{ $user->username }}</p>
                    
                    <!-- Status Badge -->
                    <div class="mb-3">
                        <span class="badge bg-{{ $user->is_active ? 'success' : 'warning' }} fs-6">
                            <i class="fas fa-{{ $user->is_active ? 'check-circle' : 'pause-circle' }}"></i>
                            {{ $user->is_active ? 'Aktif' : 'Tidak Aktif' }}
                        </span>
                    </div>

                    <!-- Role & Unit -->
                    <div class="row">
                        <div class="col-6">
                            <span class="badge bg-{{ $user->role_badge_color }} w-100 py-2">
                                <i class="fas fa-{{ $user->role === 'admin' ? 'shield-alt' : 'user' }}"></i>
                                {{ $user->role_display_name }}
                            </span>
                        </div>
                        <div class="col-6">
                            <span class="badge bg-{{ $user->unit->badge_color ?? 'info' }} w-100 py-2">
                                <i class="fas fa-building"></i>
                                {{ $user->unit->unit_name }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-address-card"></i> Informasi Kontak
                    </h5>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <i class="fas fa-envelope text-primary"></i>
                        <div class="info-content">
                            <strong>Email</strong>
                            <div>{{ $user->email }}</div>
                        </div>
                    </div>

                    @if($user->nip)
                    <div class="info-item">
                        <i class="fas fa-id-card text-success"></i>
                        <div class="info-content">
                            <strong>NIP</strong>
                            <div>{{ $user->nip }}</div>
                        </div>
                    </div>
                    @endif

                    @if($user->position)
                    <div class="info-item">
                        <i class="fas fa-briefcase text-warning"></i>
                        <div class="info-content">
                            <strong>Jabatan</strong>
                            <div>{{ $user->position }}</div>
                        </div>
                    </div>
                    @endif

                    <div class="info-item">
                        <i class="fas fa-calendar text-info"></i>
                        <div class="info-content">
                            <strong>Bergabung</strong>
                            <div>{{ $user->created_at->format('d F Y') }}</div>
                            <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                        </div>
                    </div>

                    @if($user->email_verified_at)
                    <div class="info-item">
                        <i class="fas fa-check-circle text-success"></i>
                        <div class="info-content">
                            <strong>Email Verified</strong>
                            <div>{{ $user->email_verified_at->format('d F Y') }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Statistics & Activity -->
        <div class="col-lg-8">
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h3 class="mb-0">{{ $user->transactions_count ?? 0 }}</h3>
                                    <p class="mb-0">Total Transaksi</p>
                                </div>
                                <div class="stat-icon">
                                    <i class="fas fa-exchange-alt"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h3 class="mb-0">{{ $user->categories_count ?? 0 }}</h3>
                                    <p class="mb-0">Kategori Dibuat</p>
                                </div>
                                <div class="stat-icon">
                                    <i class="fas fa-tags"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h3 class="mb-0">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Belum Login' }}</h3>
                                    <p class="mb-0">Login Terakhir</p>
                                </div>
                                <div class="stat-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-history"></i> 10 Transaksi Terakhir
                    </h5>
                    @if(isset($recentTransactions) && $recentTransactions->count() > 0)
                    <small class="text-muted">{{ $recentTransactions->count() }} dari {{ $user->transactions_count ?? 0 }} transaksi</small>
                    @endif
                </div>
                <div class="card-body">
                    @if(isset($recentTransactions) && $recentTransactions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Kategori</th>
                                    <th>Keterangan</th>
                                    <th>Tipe</th>
                                    <th class="text-end">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTransactions as $transaction)
                                <tr>
                                    <td>
                                        <small>{{ $transaction->transaction_date->format('d/m/Y') }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $transaction->category->badge_color ?? 'secondary' }}">
                                            {{ $transaction->category->category_name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ Str::limit($transaction->description, 30) }}
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $transaction->transaction_type === 'income' ? 'success' : 'danger' }}">
                                            {{ $transaction->transaction_type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <strong class="text-{{ $transaction->transaction_type === 'income' ? 'success' : 'danger' }}">
                                            {{ $transaction->transaction_type === 'income' ? '+' : '-' }}
                                            Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                        </strong>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="text-center mt-3">
                        <a href="{{ route('transactions.index') }}?user_id={{ $user->user_id }}" class="btn btn-outline-primary">
                            <i class="fas fa-eye"></i> Lihat Semua Transaksi
                        </a>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-inbox text-muted" style="font-size: 3rem;"></i>
                        <h5 class="mt-3 text-muted">Belum Ada Transaksi</h5>
                        <p class="text-muted">User ini belum membuat transaksi apapun.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-large {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(45deg, #007bff, #0056b3);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2rem;
    font-weight: bold;
    border: 4px solid rgba(0, 123, 255, 0.2);
}

.info-item {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
    padding: 0.5rem 0;
    border-bottom: 1px solid #eee;
}

.info-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.info-item i {
    width: 30px;
    margin-right: 1rem;
    font-size: 1.1rem;
}

.info-content {
    flex: 1;
}

.info-content strong {
    display: block;
    color: #333;
    margin-bottom: 0.2rem;
}

.stat-icon {
    font-size: 2rem;
    opacity: 0.7;
}
</style>
@endsection