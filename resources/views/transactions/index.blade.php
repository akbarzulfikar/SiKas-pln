@extends('layouts.app')

@section('title', 'Transaksi Kas - Sistem Kas PLN')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <div class="page-icon me-3">
                <i class="fas fa-exchange-alt"></i>
            </div>
            <h1 class="page-title mb-0">Kelola Transaksi</h1>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary" onclick="resetFilters()">
                <i class="fas fa-undo"></i> Reset Filter
            </button>
            <a href="{{ route('transactions.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Transaksi
            </a>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="filter-card">
                <div class="filter-header">
                    <h5><i class="fas fa-filter"></i> Filter & Pencarian Transaksi</h5>
                    <button class="btn btn-sm btn-outline-light" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                        <i class="fas fa-chevron-down"></i> Toggle Filter
                    </button>
                </div>
                <div class="collapse show" id="filterCollapse">
                    <div class="filter-body">
                        <form method="GET" action="{{ route('transactions.index') }}" id="filterForm">
                            <div class="row">
                                <!-- Search -->
                                <div class="col-md-3">
                                    <label class="form-label">🔍 Pencarian</label>
                                    <input type="text" class="form-control" name="search"
                                        value="{{ $search }}"
                                        placeholder="No. transaksi, deskripsi, kategori...">
                                </div>

                                <!-- Transaction Type -->
                                <div class="col-md-2">
                                    <label class="form-label">Jenis Transaksi</label>
                                    <select class="form-select" name="transaction_type" id="transactionTypeFilter">
                                        <option value="">📋 Semua</option>
                                        <option value="income" {{ $transactionType == 'income' ? 'selected' : '' }}>
                                            💰 Kas Masuk
                                        </option>
                                        <option value="expense" {{ $transactionType == 'expense' ? 'selected' : '' }}>
                                            💸 Kas Keluar
                                        </option>
                                    </select>
                                </div>

                                <!-- Category -->
                                <div class="col-md-2">
                                    <label class="form-label">Kategori</label>
                                    <select class="form-select" name="category_id" id="categoryFilter">
                                        <option value="">🏷️ Semua Kategori</option>
                                        @foreach($categories as $category)
                                        <option value="{{ $category->category_id }}"
                                            {{ $categoryId == $category->category_id ? 'selected' : '' }}
                                            data-type="{{ $category->transaction_type }}">
                                            {{ $category->category_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                @if(Auth::user()->role === 'admin')
                                <!-- Unit -->
                                <div class="col-md-2">
                                    <label class="form-label">Unit</label>
                                    <select class="form-select" name="unit_id">
                                        <option value="">🏢 Semua Unit</option>
                                        @foreach($units as $unit)
                                        <option value="{{ $unit->unit_id }}" {{ $unitId == $unit->unit_id ? 'selected' : '' }}>
                                            {{ $unit->unit_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif

                                <!-- Date Range Column -->
                                <div class="col-md-{{ Auth::user()->role === 'admin' ? '3' : '5' }}">
                                    <div class="row">
                                        <div class="col-6">
                                            <label class="form-label">Tanggal Mulai</label>
                                            <input type="date" class="form-control" name="start_date" value="{{ $startDate }}">
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label">Tanggal Akhir</label>
                                            <input type="date" class="form-control" name="end_date" value="{{ $endDate }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <!-- Filter Actions -->
                                <div class="col-12 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary me-2">
                                        <i class="fas fa-search"></i> Filter
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary me-2" onclick="resetFilters()">
                                        <i class="fas fa-undo"></i> Reset
                                    </button>
                                    <button type="button" class="btn btn-outline-info me-1" onclick="setQuickFilter('today')">
                                        📅 Hari Ini
                                    </button>
                                    <button type="button" class="btn btn-outline-info me-1" onclick="setQuickFilter('week')">
                                        📊 Minggu Ini
                                    </button>
                                    <button type="button" class="btn btn-outline-info" onclick="setQuickFilter('month')">
                                        📈 Bulan Ini
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    @if($summaryData['total_transactions'] > 0)
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="summary-card income-card">
                <div class="summary-icon">
                    <i class="fas fa-arrow-up"></i>
                </div>
                <div class="summary-content">
                    <h6>Total Kas Masuk</h6>
                    <h3 class="text-success">Rp {{ number_format($summaryData['total_income'], 0, ',', '.') }}</h3>
                    <small class="text-muted">Dari filter yang dipilih</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card expense-card">
                <div class="summary-icon">
                    <i class="fas fa-arrow-down"></i>
                </div>
                <div class="summary-content">
                    <h6>Total Kas Keluar</h6>
                    <h3 class="text-danger">Rp {{ number_format($summaryData['total_expense'], 0, ',', '.') }}</h3>
                    <small class="text-muted">Dari filter yang dipilih</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card balance-card">
                <div class="summary-icon">
                    <i class="fas fa-wallet"></i>
                </div>
                <div class="summary-content">
                    <h6>Saldo</h6>
                    <h3 class="{{ $summaryData['total_balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                        Rp {{ number_format($summaryData['total_balance'], 0, ',', '.') }}
                    </h3>
                    <small class="text-muted">Selisih kas masuk & keluar</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card transaction-card">
                <div class="summary-icon">
                    <i class="fas fa-list"></i>
                </div>
                <div class="summary-content">
                    <h6>Total Transaksi</h6>
                    <h3 class="text-primary">{{ number_format($summaryData['total_transactions']) }}</h3>
                    <small class="text-muted">{{ $summaryData['total_transactions'] }} transaksi</small>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Transaction Table -->
    <div class="row">
        <div class="col-12">
            <div class="table-card">
                <div class="table-header">
                    <h5><i class="fas fa-list"></i> Daftar Transaksi</h5>
                    <div class="table-info">
                        <span class="badge bg-primary">{{ $transactions->total() }} Total</span>
                        <span class="text-muted">Halaman {{ $transactions->currentPage() }} dari {{ $transactions->lastPage() }}</span>
                    </div>
                </div>
                <div class="table-body">
                    @if($transactions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover transaction-table">
                            <thead>
                                <tr>
                                    <th width="12%">No. Transaksi</th>
                                    <th width="10%">Tanggal</th>
                                    <th width="8%">Jenis</th>
                                    <th width="15%">Kategori</th>
                                    <th width="25%">Deskripsi</th>
                                    <th width="12%">Jumlah</th>
                                    @if(Auth::user()->role === 'admin')
                                    <th width="10%">Unit</th>
                                    @endif
                                    <th width="8%">Bukti</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $transaction)
                                <tr>
                                    <td>
                                        <code class="transaction-number">{{ $transaction->transaction_number }}</code>
                                        <br><small class="text-muted">{{ $transaction->created_at->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ $transaction->transaction_date->format('d/m/Y') }}</strong>
                                        <br><small class="text-muted">{{ $transaction->transaction_date->format('l') }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $transaction->type_badge_color }}">
                                            @if($transaction->transaction_type == 'income')
                                            💰 Masuk
                                            @else
                                            💸 Keluar
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        <span class="category-name">{{ $transaction->category->category_name }}</span>
                                    </td>
                                    <td>
                                        @if($transaction->description)
                                        <span class="description">{{ Str::limit($transaction->description, 50) }}</span>
                                        @else
                                        <em class="text-muted">Tidak ada deskripsi</em>
                                        @endif
                                    </td>
                                    <td>
                                        <strong class="amount {{ $transaction->transaction_type == 'income' ? 'text-success' : 'text-danger' }}">
                                            Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                        </strong>
                                    </td>
                                    @if(Auth::user()->role === 'admin')
                                    <td>
                                        <span class="badge bg-{{ $transaction->unit->badge_color }}">
                                            {{ $transaction->unit->unit_name }}
                                        </span>
                                    </td>
                                    @endif
                                    <td class="text-center">
                                        @if($transaction->evidence_file)
                                        <a href="{{ route('transactions.show-evidence', $transaction->transaction_id) }}"
                                            class="btn btn-sm btn-outline-info" target="_blank" title="Lihat Bukti">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('transactions.show', $transaction->transaction_id) }}"
                                                class="btn btn-outline-info" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if(Auth::user()->role === 'admin' || $transaction->created_by === Auth::user()->user_id)
                                            <button class="btn btn-outline-danger"
                                                data-transaction-id="{{ $transaction->transaction_id }}"
                                                data-transaction-number="{{ $transaction->transaction_number }}"
                                                onclick="confirmDelete(this.dataset.transactionId, this.dataset.transactionNumber)"
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
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="pagination-info">
                            <small class="text-muted">
                                Menampilkan {{ $transactions->firstItem() }} - {{ $transactions->lastItem() }}
                                dari {{ $transactions->total() }} transaksi
                            </small>
                        </div>
                        <div class="pagination-links">
                            {{ $transactions->links() }}
                        </div>
                    </div>
                    @else
                    <div class="empty-state">
                        <i class="fas fa-search fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">Tidak Ada Data</h5>
                        @if(request()->hasAny(['search', 'transaction_type', 'category_id', 'unit_id', 'start_date', 'end_date']))
                        <p class="text-muted">
                            Tidak ada transaksi yang sesuai dengan filter yang dipilih.<br>
                            Coba ubah kriteria pencarian atau <a href="{{ route('transactions.index') }}">reset filter</a>.
                        </p>
                        @else
                        <p class="text-muted">
                            Belum ada transaksi yang tercatat.<br>
                            <a href="{{ route('transactions.create') }}" class="btn btn-primary mt-2">
                                <i class="fas fa-plus"></i> Tambah Transaksi Pertama
                            </a>
                        </p>
                        @endif
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

    .filter-header h5,
    .table-header h5 {
        margin: 0;
        font-weight: 600;
    }

    .filter-body,
    .table-body {
        padding: 25px;
    }

    .summary-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border-left: 4px solid;
        display: flex;
        align-items: center;
        gap: 15px;
        transition: transform 0.2s ease;
        margin-bottom: 20px;
    }

    .summary-card:hover {
        transform: translateY(-2px);
    }

    .income-card {
        border-left-color: #28a745;
    }

    .expense-card {
        border-left-color: #dc3545;
    }

    .balance-card {
        border-left-color: var(--pln-blue);
    }

    .transaction-card {
        border-left-color: #007bff;
    }

    .summary-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: white;
    }

    .income-card .summary-icon {
        background: linear-gradient(135deg, #28a745, #34ce57);
    }

    .expense-card .summary-icon {
        background: linear-gradient(135deg, #dc3545, #e85d75);
    }

    .balance-card .summary-icon {
        background: linear-gradient(135deg, var(--pln-blue), var(--pln-blue-dark));
    }

    .transaction-card .summary-icon {
        background: linear-gradient(135deg, #007bff, #0d6efd);
    }

    .summary-content h6 {
        margin: 0;
        font-size: 0.9rem;
        color: #666;
        font-weight: 500;
    }

    .summary-content h3 {
        margin: 5px 0 0 0;
        font-size: 1.3rem;
        font-weight: 700;
    }

    .transaction-table th {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        color: #495057;
        font-size: 0.9rem;
    }

    .transaction-table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .transaction-number {
        background-color: #e9ecef;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 0.8rem;
    }

    .category-name {
        font-weight: 500;
        color: #495057;
    }

    .description {
        color: #666;
    }

    .amount {
        font-weight: 700;
        font-size: 0.95rem;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #666;
    }

    .empty-state i {
        display: block;
        margin-bottom: 20px;
    }

    .form-label {
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
        font-size: 0.9rem;
    }

    .form-control,
    .form-select {
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 14px;
        transition: border-color 0.3s ease;
    }

    .form-control:focus,
    .form-select:focus {
        outline: none;
        border-color: var(--pln-blue);
        box-shadow: 0 0 0 0.2rem rgba(0, 61, 122, 0.25);
    }

    .btn {
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .btn:hover {
        transform: translateY(-1px);
    }

    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.8rem;
    }

    .table-info {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .pagination-info {
        flex: 1;
    }

    .pagination-links .pagination {
        margin: 0;
    }

    .pagination .page-link {
        border-radius: 6px;
        margin: 0 2px;
        border: 1px solid #dee2e6;
        color: var(--pln-blue);
    }

    .pagination .page-item.active .page-link {
        background-color: var(--pln-blue);
        border-color: var(--pln-blue);
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .page-title {
            font-size: 1.5rem;
        }

        .d-flex.gap-2 {
            flex-direction: column;
            gap: 8px !important;
        }

        .summary-content h3 {
            font-size: 1.1rem;
        }

        .filter-body,
        .table-body {
            padding: 15px;
        }

        .transaction-table {
            font-size: 0.8rem;
        }

        .btn-group {
            flex-direction: column;
        }

        .pagination-links {
            width: 100%;
            overflow-x: auto;
        }
    }
</style>

<script>
    // Global variables for URLs
    const TRANSACTIONS_INDEX_URL = '{{ route("transactions.index") }}';

    // Handle transaction type change to filter categories
    document.getElementById('transactionTypeFilter').addEventListener('change', function() {
        const transactionType = this.value;
        const categorySelect = document.getElementById('categoryFilter');

        // Reset category selection
        categorySelect.value = '';

        // Show/hide categories based on transaction type
        const categoryOptions = categorySelect.querySelectorAll('option[data-type]');
        categoryOptions.forEach(option => {
            if (transactionType === '' || option.dataset.type === transactionType) {
                option.style.display = '';
            } else {
                option.style.display = 'none';
            }
        });

        // If we have a specific type, load categories via AJAX for better UX
        if (transactionType) {
            loadCategoriesByType(transactionType);
        }
    });

    // Load categories by transaction type via AJAX
    function loadCategoriesByType(transactionType) {
        fetch(`{{ route('transactions.categories-by-type') }}?type=${transactionType}`)
            .then(response => response.json())
            .then(categories => {
                const categorySelect = document.getElementById('categoryFilter');
                const currentValue = categorySelect.value;

                // Clear existing options except the first one
                categorySelect.innerHTML = '<option value="">🏷️ Semua Kategori</option>';

                // Add new options
                categories.forEach(category => {
                    const option = new Option(category.category_name, category.category_id);
                    option.selected = currentValue === category.category_id;
                    categorySelect.add(option);
                });
            })
            .catch(error => {
                console.error('Error loading categories:', error);
            });
    }

    // Reset all filters
    function resetFilters() {
        // Reset form
        document.getElementById('filterForm').reset();

        // Reset category filter
        const categorySelect = document.getElementById('categoryFilter');
        categorySelect.innerHTML = '<option value="">🏷️ Semua Kategori</option>';

        // Redirect to clean URL
        window.location.href = TRANSACTIONS_INDEX_URL;
    }

    // Quick filter functions
    function setQuickFilter(period) {
        const today = new Date();
        let startDate, endDate;

        switch (period) {
            case 'today':
                startDate = endDate = today.toISOString().split('T')[0];
                break;
            case 'week':
                const startOfWeek = new Date(today);
                startOfWeek.setDate(today.getDate() - today.getDay());
                startDate = startOfWeek.toISOString().split('T')[0];
                endDate = today.toISOString().split('T')[0];
                break;
            case 'month':
                startDate = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
                endDate = today.toISOString().split('T')[0];
                break;
        }

        // Set dates and submit form
        document.querySelector('input[name="start_date"]').value = startDate;
        document.querySelector('input[name="end_date"]').value = endDate;
        document.getElementById('filterForm').submit();
    }

    // Confirm delete function
    function confirmDelete(transactionId, transactionNumber) {
        if (confirm(`Apakah Anda yakin ingin menghapus transaksi "${transactionNumber}"?\n\nTindakan ini tidak dapat dibatalkan!`)) {
            const form = document.getElementById('deleteForm');
            form.action = `/transactions/${transactionId}`;
            form.submit();
        }
    }

    // Initialize category filter on page load
    document.addEventListener('DOMContentLoaded', function() {
        const transactionType = document.getElementById('transactionTypeFilter').value;
        if (transactionType) {
            // Trigger change event to filter categories
            document.getElementById('transactionTypeFilter').dispatchEvent(new Event('change'));
        }
    });
</script>
@endsection