@extends('layouts.app')

@section('title', 'Manajemen Unit - SIKAS PLN')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">
                        <i class="fas fa-building text-primary"></i> 
                        Manajemen Unit
                    </h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Manajemen Unit</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('units.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Unit
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                            <p class="mb-0">Total Unit</p>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-building"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="mb-0">{{ $stats['active'] }}</h3>
                            <p class="mb-0">Unit Aktif</p>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="mb-0">{{ $stats['up3'] }}</h3>
                            <p class="mb-0">UP3</p>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-home"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="mb-0">{{ $stats['ulp'] }}</h3>
                            <p class="mb-0">ULP</p>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-building"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('units.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Tipe Unit</label>
                        <select name="type" class="form-select">
                            <option value="">Semua Tipe</option>
                            <option value="UP3" {{ $type === 'UP3' ? 'selected' : '' }}>UP3</option>
                            <option value="ULP" {{ $type === 'ULP' ? 'selected' : '' }}>ULP</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="1" {{ $status === '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ $status === '0' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Cari Unit</label>
                        <input type="text" name="search" class="form-control" 
                               placeholder="Nama unit, ID, atau alamat..." 
                               value="{{ $search }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </button>
                            <a href="{{ route('units.index') }}" class="btn btn-secondary">
                                <i class="fas fa-sync"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Units Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-list"></i> Daftar Unit
            </h5>
            <span class="badge bg-primary">{{ $units->total() }} unit</span>
        </div>
        <div class="card-body">
            @if($units->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Unit</th>
                            <th>Kontak</th>
                            <th>Tipe</th>
                            <th>Status</th>
                            <th>Statistik</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($units as $unit)
                        <tr class="{{ !$unit->is_active ? 'table-warning' : '' }}">
                            <td>
                                <div class="unit-info">
                                    <div class="unit-name">
                                        <strong>{{ $unit->unit_name }}</strong>
                                    </div>
                                    <small class="text-muted">ID: {{ $unit->unit_id }}</small>
                                    @if($unit->address)
                                    <div class="unit-address">
                                        <i class="fas fa-map-marker-alt text-muted"></i>
                                        {{ Str::limit($unit->address, 50) }}
                                    </div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="contact-info">
                                    @if($unit->phone)
                                    <div>
                                        <i class="fas fa-phone text-primary"></i> {{ $unit->phone }}
                                    </div>
                                    @endif
                                    @if($unit->email)
                                    <div>
                                        <i class="fas fa-envelope text-success"></i> {{ $unit->email }}
                                    </div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $unit->badge_color }} fs-6">
                                    <i class="fas fa-{{ $unit->unit_type === 'UP3' ? 'home' : 'building' }}"></i>
                                    {{ $unit->unit_type }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $unit->is_active ? 'success' : 'warning' }}">
                                    <i class="fas fa-{{ $unit->is_active ? 'check-circle' : 'pause-circle' }}"></i>
                                    {{ $unit->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </td>
                            <td>
                                <div class="unit-stats">
                                    <small class="d-block">
                                        <i class="fas fa-users"></i> {{ $unit->users_count }} user
                                    </small>
                                    <small class="d-block">
                                        <i class="fas fa-exchange-alt"></i> {{ $unit->transactions_count }} transaksi
                                    </small>
                                </div>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('units.show', $unit) }}" 
                                       class="btn btn-outline-info" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('units.edit', $unit) }}" 
                                       class="btn btn-outline-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('units.toggle-status', $unit) }}" 
                                          class="d-inline">
                                        @csrf
                                        <button type="submit" 
                                                class="btn btn-outline-{{ $unit->is_active ? 'warning' : 'success' }}" 
                                                title="{{ $unit->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                            <i class="fas fa-{{ $unit->is_active ? 'pause' : 'play' }}"></i>
                                        </button>
                                    </form>
                                    @if($unit->users_count == 0 && $unit->transactions_count == 0)
                                    <button class="btn btn-outline-danger" 
                                            onclick="confirmDelete('{{ $unit->unit_id }}', '{{ $unit->unit_name }}')"
                                            title="Hapus Unit">
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
                <div>
                    <small class="text-muted">
                        Menampilkan {{ $units->firstItem() }} - {{ $units->lastItem() }} 
                        dari {{ $units->total() }} unit
                    </small>
                </div>
                <div>
                    {{ $units->links() }}
                </div>
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-building text-muted" style="font-size: 3rem;"></i>
                <h5 class="mt-3 text-muted">Belum Ada Unit</h5>
                <p class="text-muted">Klik tombol "Tambah Unit" untuk menambahkan unit baru.</p>
                <a href="{{ route('units.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Unit Pertama
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus Unit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Anda yakin ingin menghapus unit <strong id="unitName"></strong>?</p>
                <p class="text-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    Tindakan ini tidak dapat dibatalkan!
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus Unit</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.stat-icon {
    font-size: 2rem;
    opacity: 0.7;
}

.unit-info .unit-name {
    font-weight: 600;
    color: #333;
}

.unit-info .unit-address {
    font-size: 0.85rem;
    color: #6c757d;
    margin-top: 2px;
}

.contact-info div {
    font-size: 0.85rem;
    margin-bottom: 2px;
}

.unit-stats small {
    color: #6c757d;
    font-size: 0.8rem;
}

.card {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border: none;
}

.table th {
    background-color: #f8f9fa;
    font-weight: 600;
    border-bottom: 2px solid #dee2e6;
}

.btn-group .btn {
    border-radius: 4px;
    margin-right: 2px;
}
</style>

<script>
function confirmDelete(unitId, unitName) {
    document.getElementById('unitName').textContent = unitName;
    document.getElementById('deleteForm').action = '{{ route("units.destroy", ":unit") }}'.replace(':unit', unitId);
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
@endsection