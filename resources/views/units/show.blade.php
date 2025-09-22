@extends('layouts.app')

@section('title', 'Detail Unit - ' . $unit->unit_name)

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">
                        <i class="fas fa-building text-primary"></i> 
                        Detail Unit
                    </h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('units.index') }}">Manajemen Unit</a></li>
                            <li class="breadcrumb-item active">Detail Unit</li>
                        </ol>
                    </nav>
                </div>
                <div class="btn-group">
                    <a href="{{ route('units.edit', $unit) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit Unit
                    </a>
                    <a href="{{ route('units.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Unit Information Card -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-building"></i> Informasi Unit
                    </h5>
                </div>
                <div class="card-body text-center">
                    <!-- Icon -->
                    <div class="mb-3">
                        <div class="unit-icon mx-auto">
                            <i class="fas fa-{{ $unit->unit_type === 'UP3' ? 'home' : 'building' }}"></i>
                        </div>
                    </div>

                    <!-- Basic Info -->
                    <h4 class="mb-1">{{ $unit->unit_name }}</h4>
                    <p class="text-muted mb-2">ID: {{ $unit->unit_id }}</p>
                    
                    <!-- Type & Status Badge -->
                    <div class="mb-3">
                        <span class="badge bg-{{ $unit->badge_color }} fs-6 me-2">
                            <i class="fas fa-{{ $unit->unit_type === 'UP3' ? 'home' : 'building' }}"></i>
                            {{ $unit->unit_type }}
                        </span>
                        <span class="badge bg-{{ $unit->is_active ? 'success' : 'warning' }} fs-6">
                            <i class="fas fa-{{ $unit->is_active ? 'check-circle' : 'pause-circle' }}"></i>
                            {{ $unit->is_active ? 'Aktif' : 'Tidak Aktif' }}
                        </span>
                    </div>

                    <!-- Created Info -->
                    <small class="text-muted">
                        <i class="fas fa-calendar"></i>
                        Dibuat {{ $unit->created_at->format('d F Y') }}
                        <br>
                        {{ $unit->created_at->diffForHumans() }}
                    </small>
                </div>
            </div>

            <!-- Contact Information -->
            @if($unit->address || $unit->phone || $unit->email)
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-address-card"></i> Informasi Kontak
                    </h5>
                </div>
                <div class="card-body">
                    @if($unit->address)
                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt text-danger"></i>
                        <div class="contact-content">
                            <strong>Alamat</strong>
                            <div>{{ $unit->address }}</div>
                        </div>
                    </div>
                    @endif

                    @if($unit->phone)
                    <div class="contact-item">
                        <i class="fas fa-phone text-primary"></i>
                        <div class="contact-content">
                            <strong>Telepon</strong>
                            <div>
                                <a href="tel:{{ $unit->phone }}" class="text-decoration-none">
                                    {{ $unit->phone }}
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($unit->email)
                    <div class="contact-item">
                        <i class="fas fa-envelope text-success"></i>
                        <div class="contact-content">
                            <strong>Email</strong>
                            <div>
                                <a href="mailto:{{ $unit->email }}" class="text-decoration-none">
                                    {{ $unit->email }}
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Statistics & Details -->
        <div class="col-lg-8">
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h3 class="mb-0">{{ $unit->users_count ?? 0 }}</h3>
                                    <p class="mb-0">Total User</p>
                                </div>
                                <div class="stat-icon">
                                    <i class="fas fa-users"></i>
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
                                    <h3 class="mb-0">{{ $unit->transactions_count ?? 0 }}</h3>
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
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h3 class="mb-0">Rp {{ number_format($balance ?? 0, 0, ',', '.') }}</h3>
                                    <p class="mb-0">Saldo</p>
                                </div>
                                <div class="stat-icon">
                                    <i class="fas fa-wallet"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Summary -->
            @if($totalIncome > 0 || $totalExpense > 0)
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie"></i> Ringkasan Keuangan
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center">
                                <div class="text-success">
                                    <i class="fas fa-arrow-up fs-2"></i>
                                </div>
                                <h5 class="text-success">Pemasukan</h5>
                                <h4>Rp {{ number_format($totalIncome, 0, ',', '.') }}</h4>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <div class="text-danger">
                                    <i class="fas fa-arrow-down fs-2"></i>
                                </div>
                                <h5 class="text-danger">Pengeluaran</h5>
                                <h4>Rp {{ number_format($totalExpense, 0, ',', '.') }}</h4>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <div class="text-{{ $balance >= 0 ? 'primary' : 'warning' }}">
                                    <i class="fas fa-equals fs-2"></i>
                                </div>
                                <h5 class="text-{{ $balance >= 0 ? 'primary' : 'warning' }}">Saldo</h5>
                                <h4 class="text-{{ $balance >= 0 ? 'primary' : 'warning' }}">
                                    Rp {{ number_format($balance, 0, ',', '.') }}
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Users List -->
            @if($users && $users->count() > 0)
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-users"></i> Daftar User ({{ $users->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Username</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Last Login</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="user-avatar me-2">
                                                {{ $user->initials }}
                                            </div>
                                            <div>
                                                <strong>{{ $user->name }}</strong>
                                                @if($user->position)
                                                <br><small class="text-muted">{{ $user->position }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $user->username }}</td>
                                    <td>
                                        <span class="badge bg-{{ $user->role_badge_color }}">
                                            {{ $user->role_display_name }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $user->is_active ? 'success' : 'warning' }}">
                                            {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($user->last_login)
                                            {{ $user->last_login->diffForHumans() }}
                                        @else
                                            <span class="text-muted">Belum login</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Recent Transactions -->
            @if($recentTransactions && $recentTransactions->count() > 0)
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-history"></i> 10 Transaksi Terakhir
                    </h5>
                </div>
                <div class="card-body">
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
                                        {{ $transaction->transaction_date->format('d/m/Y') }}
                                    </td>
                                    <td>
                                        @if($transaction->category)
                                        <span class="badge bg-secondary">
                                            {{ $transaction->category->category_name }}
                                        </span>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ Str::limit($transaction->description, 40) }}
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
                </div>
            </div>
            @endif

            <!-- No Data Messages -->
            @if((!$users || $users->count() === 0) && (!$recentTransactions || $recentTransactions->count() === 0))
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="fas fa-inbox text-muted" style="font-size: 4rem;"></i>
                    <h4 class="mt-3 text-muted">Unit Kosong</h4>
                    <p class="text-muted">Unit ini belum memiliki user atau transaksi.</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.unit-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(45deg, #007bff, #0056b3);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2.5rem;
    margin-bottom: 1rem;
}

.contact-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 1rem;
    padding: 0.5rem 0;
    border-bottom: 1px solid #eee;
}

.contact-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.contact-item i {
    width: 30px;
    margin-right: 1rem;
    font-size: 1.1rem;
    margin-top: 0.2rem;
}

.contact-content {
    flex: 1;
}

.contact-content strong {
    display: block;
    color: #333;
    margin-bottom: 0.2rem;
}

.stat-icon {
    font-size: 2rem;
    opacity: 0.7;
}

.user-avatar {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    background: #007bff;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.8rem;
    font-weight: bold;
}

.card {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.card-header {
    background: linear-gradient(45deg, #f8f9fa, #e9ecef);
    border-bottom: 1px solid #dee2e6;
}
</style>
@endsection