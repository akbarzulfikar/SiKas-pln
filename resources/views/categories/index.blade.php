@extends('layouts.app')

@section('title', 'Kelola Kategori - Sistem Kas PLN')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <div class="page-icon me-3">
                <i class="fas fa-tags"></i>
            </div>
            <h1 class="page-title mb-0">Kelola Kategori</h1>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('categories.export') }}" class="btn btn-outline-success">
                <i class="fas fa-file-csv"></i> Export CSV
            </a>
            <a href="{{ route('categories.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Kategori
            </a>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="filter-card">
                <div class="filter-header">
                    <h5><i class="fas fa-filter"></i> Filter Kategori</h5>
                </div>
                <div class="filter-body">
                    <form method="GET" action="{{ route('categories.index') }}" id="filterForm">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">🔍 Pencarian</label>
                                <input type="text" class="form-control" name="search"
                                    value="{{ $search }}"
                                    placeholder="Nama kategori atau deskripsi...">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Jenis Transaksi</label>
                                <select class="form-select" name="type">
                                    <option value="">📋 Semua Jenis</option>
                                    <option value="income" {{ $type == 'income' ? 'selected' : '' }}>
                                        💰 Kas Masuk
                                    </option>
                                    <option value="expense" {{ $type == 'expense' ? 'selected' : '' }}>
                                        💸 Kas Keluar
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="">✅ Semua Status</option>
                                    <option value="1" {{ $status === '1' ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ $status === '0' ? 'selected' : '' }}>Tidak Aktif</option>
                                </select>
                            </div>

                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">
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
                    <i class="fas fa-tags"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $stats['total'] }}</h3>
                    <p>Total Kategori</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card active-card">
                <div class="stats-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $stats['active'] }}</h3>
                    <p>Kategori Aktif</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card income-card">
                <div class="stats-icon">
                    <i class="fas fa-arrow-up"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $stats['income'] }}</h3>
                    <p>Kas Masuk</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card expense-card">
                <div class="stats-icon">
                    <i class="fas fa-arrow-down"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $stats['expense'] }}</h3>
                    <p>Kas Keluar</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Categories Table -->
    <div class="row">
        <div class="col-12">
            <div class="table-card">
                <div class="table-header">
                    <h5><i class="fas fa-list"></i> Daftar Kategori</h5>
                    <div class="table-info">
                        <span class="badge bg-primary">{{ $categories->total() }} Total</span>
                    </div>
                </div>
                <div class="table-body">
                    @if($categories->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nama Kategori</th>
                                    <th>Jenis</th>
                                    <th>Deskripsi</th>
                                    <th>Status</th>
                                    <th>Dibuat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categories as $category)
                                <tr>
                                    <td>
                                        <code>{{ $category->category_id }}</code>
                                    </td>
                                    <td>
                                        <strong>{{ $category->category_name }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $category->badge_color }}">
                                            <i class="{{ $category->icon }}"></i>
                                            {{ $category->type_display }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-muted">
                                            {{ Str::limit($category->description ?: 'Tidak ada deskripsi', 50) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $category->is_active ? 'success' : 'warning' }}">
                                            {{ $category->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $category->created_at->format('d/m/Y') }}<br>
                                            oleh {{ $category->creator->name ?? 'System' }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('categories.show', $category->category_id) }}"
                                                class="btn btn-outline-info" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('categories.edit', $category->category_id) }}"
                                                class="btn btn-outline-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($category->canBeDeleted())
                                            <button class="btn btn-outline-danger"
                                                onclick="confirmDelete('{{ $category->category_id }}', '{{ $category->category_name }}')"
                                                title="Hapus">
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
                    @if($categories->hasPages())
                    <div class="pagination-wrapper">
                        <div class="pagination-info">
                            Menampilkan {{ $categories->firstItem() }} sampai {{ $categories->lastItem() }} dari {{ $categories->total() }} hasil
                        </div>
                        <div class="w-100 d-flex justify-content-center">
                            {{ $categories->links() }}
                        </div>
                    </div>
                    @endif
                    @else
                    <div class="empty-state">
                        <i class="fas fa-tags fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">Belum Ada Kategori</h5>
                        <p class="text-muted">
                            Belum ada kategori yang terdaftar.<br>
                            <a href="{{ route('categories.create') }}" class="btn btn-primary mt-2">
                                <i class="fas fa-plus"></i> Tambah Kategori Pertama
                            </a>
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
    }

    .total-card {
        border-left-color: var(--pln-blue);
    }

    .active-card {
        border-left-color: #28a745;
    }

    .income-card {
        border-left-color: #17a2b8;
    }

    .expense-card {
        border-left-color: #dc3545;
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

    .income-card .stats-icon {
        background: linear-gradient(135deg, #17a2b8, #20c4d6);
    }

    .expense-card .stats-icon {
        background: linear-gradient(135deg, #dc3545, #e85d75);
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

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #666;
    }

    .table th {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        color: #495057;
    }

   /* Replace bagian CSS pagination dengan yang ini di file categories/index.blade.php */

/* PAGINATION SECTION - FIXED ALIGNMENT */
.pagination-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-top: 25px;
    padding: 20px 0;
    border-top: 1px solid #dee2e6;
}

.pagination-info {
    text-align: center;
    margin-bottom: 20px;
    color: #6c757d;
    font-size: 0.9rem;
    font-weight: 500;
}

/* Laravel Pagination Navigation Container */
nav[role="navigation"] {
    display: flex;
    justify-content: center;
    width: 100%;
}

/* Pagination List Styling */
.pagination {
    margin: 0 auto;
    display: flex;
    justify-content: center;
    align-items: center;
    list-style: none;
    padding: 0;
    gap: 8px;
}

.pagination .page-item {
    display: flex;
    margin: 0;
}

.pagination .page-link {
    color: var(--pln-blue);
    background-color: #fff;
    border: 1px solid #dee2e6;
    padding: 10px 14px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 500;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 44px;
    min-height: 44px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.pagination .page-link:hover {
    color: #fff;
    background-color: var(--pln-blue);
    border-color: var(--pln-blue);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,61,122,0.2);
}

.pagination .page-item.active .page-link {
    color: #fff;
    background-color: var(--pln-blue);
    border-color: var(--pln-blue);
    font-weight: 600;
    box-shadow: 0 4px 8px rgba(0,61,122,0.3);
}

.pagination .page-item.disabled .page-link {
    color: #6c757d;
    background-color: #f8f9fa;
    border-color: #dee2e6;
    cursor: not-allowed;
    opacity: 0.6;
}

.pagination .page-item.disabled .page-link:hover {
    transform: none;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    background-color: #f8f9fa;
    color: #6c757d;
}

/* Previous/Next arrow styling */
.pagination .page-link[aria-label*="Previous"] {
    border-radius: 8px 0 0 8px;
    padding-left: 12px;
    padding-right: 12px;
}

.pagination .page-link[aria-label*="Next"] {
    border-radius: 0 8px 8px 0;
    padding-left: 12px;
    padding-right: 12px;
}

/* Responsive behavior */
@media (max-width: 576px) {
    .pagination-wrapper {
        padding: 15px 10px;
    }
    
    .pagination {
        flex-wrap: wrap;
        gap: 4px;
    }
    
    .pagination .page-link {
        padding: 8px 10px;
        font-size: 0.8rem;
        min-width: 36px;
        min-height: 36px;
    }
    
    .pagination .page-item:nth-child(n+7):nth-child(-n+20) {
        display: none;
    }
    
    .pagination-info {
        font-size: 0.8rem;
        margin-bottom: 15px;
    }
}

/* Table card spacing adjustment */
.table-card .table-body {
    padding-bottom: 0;
}

.table-responsive {
    margin-bottom: 0;
}

/* Clear any conflicting Bootstrap styles */
.d-flex.justify-content-center.mt-3 {
    display: none !important;
}

/* Ensure proper container width */
.pagination-wrapper {
    width: 100%;
    max-width: 100%;
    box-sizing: border-box;
}
</style>

<script>
    function confirmDelete(categoryId, categoryName) {
        if (confirm(`Apakah Anda yakin ingin menghapus kategori "${categoryName}"?\n\nTindakan ini tidak dapat dibatalkan!`)) {
            const form = document.getElementById('deleteForm');
            form.action = `/categories/${categoryId}`;
            form.submit();
        }
    }
</script>
@endsection