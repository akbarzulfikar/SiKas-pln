@extends('layouts.app')

@section('title', 'Laporan Transaksi Kas')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">
                <i class="fas fa-chart-bar text-primary me-2"></i>
                Laporan Transaksi Kas
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Laporan</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Access Info Alert -->
    @if(Auth::user()->role !== 'admin')
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Informasi:</strong> Anda hanya dapat melihat laporan transaksi dari unit <strong>{{ Auth::user()->unit->unit_name }}</strong>.
    </div>
    @endif

    <!-- Filter Section -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter me-2"></i>Filter Laporan
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reports.index') }}" class="row g-3" id="filterForm">
                <!-- Period Type -->
                <div class="col-md-3">
                    <label for="period_type" class="form-label">Periode</label>
                    <select class="form-select" id="period_type" name="period_type" onchange="toggleCustomDate()">
                        <option value="today" {{ $periodType === 'today' ? 'selected' : '' }}>Hari Ini</option>
                        <option value="last_7_days" {{ $periodType === 'last_7_days' ? 'selected' : '' }}>7 Hari Terakhir</option>
                        <option value="last_30_days" {{ $periodType === 'last_30_days' ? 'selected' : '' }}>30 Hari Terakhir</option>
                        <option value="weekly" {{ $periodType === 'weekly' ? 'selected' : '' }}>Minggu Ini</option>
                        <option value="monthly" {{ $periodType === 'monthly' ? 'selected' : '' }}>Bulan Ini</option>
                        <option value="yearly" {{ $periodType === 'yearly' ? 'selected' : '' }}>Tahun Ini</option>
                        <option value="custom" {{ $periodType === 'custom' ? 'selected' : '' }}>Periode Kustom</option>
                    </select>
                </div>

                <!-- Custom Date Range -->
                <div class="col-md-3 custom-date-range" id="customDateRange" @if($periodType !== 'custom') style="display: none;" @endif>
                    <label for="start_date" class="form-label">Tanggal Mulai</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
                </div>
                
                <div class="col-md-3 custom-date-range" id="customDateRange2" @if($periodType !== 'custom') style="display: none;" @endif>
                    <label for="end_date" class="form-label">Tanggal Akhir</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
                </div>

                <!-- Transaction Type -->
                <div class="col-md-3">
                    <label for="transaction_type" class="form-label">Jenis Transaksi</label>
                    <select class="form-select" id="transaction_type" name="transaction_type" onchange="loadCategories()">
                        <option value="">Semua Jenis</option>
                        <option value="income" {{ $transactionType === 'income' ? 'selected' : '' }}>Kas Masuk</option>
                        <option value="expense" {{ $transactionType === 'expense' ? 'selected' : '' }}>Kas Keluar</option>
                    </select>
                </div>

                <!-- Category -->
                <div class="col-md-3">
                    <label for="category_id" class="form-label">Kategori</label>
                    <select class="form-select" id="category_id" name="category_id">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                            @if(!$transactionType || $category->transaction_type === $transactionType)
                                <option value="{{ $category->category_id }}" 
                                    {{ $categoryId == $category->category_id ? 'selected' : '' }}>
                                    {{ $category->category_name }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <!-- Unit (Only for Admin) -->
                @if(Auth::user()->role === 'admin')
                <div class="col-md-3">
                    <label for="unit_id" class="form-label">Unit</label>
                    <select class="form-select" id="unit_id" name="unit_id">
                        <option value="">Semua Unit</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->unit_id }}" {{ $unitId == $unit->unit_id ? 'selected' : '' }}>
                                {{ $unit->unit_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <!-- Filter Actions -->
                <div class="col-12">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i> Tampilkan Laporan
                        </button>
                        <a href="{{ route('reports.index') }}" class="btn btn-secondary">
                            <i class="fas fa-refresh me-1"></i> Reset Filter
                        </a>
                        
                        <!-- Export Buttons -->
                        <button type="button" class="btn btn-danger" onclick="exportPdf()">
                            <i class="fas fa-file-pdf me-1"></i> Export PDF
                        </button>
                        <button type="button" class="btn btn-info" onclick="printReport()">
                            <i class="fas fa-print me-1"></i> Print
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Report Summary -->
    @if(isset($reportData))
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie me-2"></i>Ringkasan Laporan
                        <span class="badge bg-info ms-2">{{ $dateRange['label'] }}</span>
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="p-3 bg-success bg-opacity-10 rounded">
                                <h5 class="text-success mb-1">
                                    <i class="fas fa-arrow-up me-1"></i>
                                    Rp {{ number_format($reportData['summary']['income_total'], 0, ',', '.') }}
                                </h5>
                                <small class="text-muted">Total Pemasukan</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 bg-danger bg-opacity-10 rounded">
                                <h5 class="text-danger mb-1">
                                    <i class="fas fa-arrow-down me-1"></i>
                                    Rp {{ number_format($reportData['summary']['expense_total'], 0, ',', '.') }}
                                </h5>
                                <small class="text-muted">Total Pengeluaran</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 bg-{{ $reportData['summary']['balance'] >= 0 ? 'primary' : 'warning' }} bg-opacity-10 rounded">
                                <h5 class="text-{{ $reportData['summary']['balance'] >= 0 ? 'primary' : 'warning' }} mb-1">
                                    <i class="fas fa-balance-scale me-1"></i>
                                    Rp {{ number_format($reportData['summary']['balance'], 0, ',', '.') }}
                                </h5>
                                <small class="text-muted">Saldo/Selisih</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 bg-info bg-opacity-10 rounded">
                                <h5 class="text-info mb-1">
                                    <i class="fas fa-receipt me-1"></i>
                                    {{ number_format($reportData['summary']['total_transactions'], 0, ',', '.') }}
                                </h5>
                                <small class="text-muted">Total Transaksi</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Breakdown -->
    @if($reportData['summary']['category_breakdown']->count() > 0)
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list me-2"></i>Rincian per Kategori
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">#</th>
                                    <th>Kategori</th>
                                    <th width="100" class="text-center">Jenis</th>
                                    <th width="120" class="text-center">Jumlah Transaksi</th>
                                    <th width="150" class="text-end">Total Nominal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reportData['summary']['category_breakdown'] as $index => $breakdown)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $breakdown['category_name'] }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $breakdown['transaction_type'] === 'income' ? 'success' : 'danger' }}">
                                            {{ $breakdown['transaction_type'] === 'income' ? 'Masuk' : 'Keluar' }}
                                        </span>
                                    </td>
                                    <td class="text-center">{{ number_format($breakdown['count'], 0, ',', '.') }}</td>
                                    <td class="text-end">
                                        <strong>Rp {{ number_format($breakdown['total_amount'], 0, ',', '.') }}</strong>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Transaction List -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list me-2"></i>Daftar Transaksi
                <span class="badge bg-secondary ms-2">{{ $reportData['transactions']->count() }} transaksi</span>
            </h6>
        </div>
        <div class="card-body">
            @if($reportData['transactions']->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="50">#</th>
                                <th width="120">Tanggal</th>
                                <th width="150">No. Transaksi</th>
                                <th>Deskripsi</th>
                                <th width="120">Kategori</th>
                                @if(Auth::user()->role === 'admin')
                                <th width="120">Unit</th>
                                @endif
                                <th width="80">Jenis</th>
                                <th width="130" class="text-end">Nominal</th>
                                <th width="100" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reportData['transactions'] as $index => $transaction)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $transaction->transaction_date->format('d/m/Y') }}</td>
                                <td>
                                    <small class="text-muted">{{ $transaction->transaction_number }}</small>
                                </td>
                                <td>{{ $transaction->description ?: '-' }}</td>
                                <td>
                                    <small class="badge bg-{{ $transaction->category->transaction_type === 'income' ? 'success' : 'danger' }}">
                                        {{ $transaction->category->category_name }}
                                    </small>
                                </td>
                                @if(Auth::user()->role === 'admin')
                                <td>
                                    <small class="text-muted">{{ $transaction->unit->unit_name }}</small>
                                </td>
                                @endif
                                <td>
                                    <span class="badge bg-{{ $transaction->category->transaction_type === 'income' ? 'success' : 'danger' }}">
                                        {{ $transaction->category->transaction_type === 'income' ? 'Masuk' : 'Keluar' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <strong class="text-{{ $transaction->category->transaction_type === 'income' ? 'success' : 'danger' }}">
                                        Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                    </strong>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('transactions.show', $transaction->transaction_id) }}" 
                                           class="btn btn-outline-info btn-sm" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Tidak Ada Data Transaksi</h5>
                    <p class="text-muted">Tidak ada transaksi ditemukan untuk periode dan filter yang dipilih.</p>
                    <a href="{{ route('transactions.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Tambah Transaksi
                    </a>
                </div>
            @endif
        </div>
    </div>
    @else
    <!-- No Report Data -->
    <div class="card shadow">
        <div class="card-body text-center py-5">
            <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Belum Ada Laporan</h5>
            <p class="text-muted">Pilih filter dan klik "Tampilkan Laporan" untuk melihat data transaksi.</p>
        </div>
    </div>
    @endif
</div>

<!-- Data Script -->
<script type="application/json" id="categories-data">
@json($categories)
</script>

<!-- JavaScript -->
<script>
// Get categories data from JSON script
const categoriesData = JSON.parse(document.getElementById('categories-data').textContent);

function toggleCustomDate() {
    const periodType = document.getElementById('period_type').value;
    const customRange1 = document.getElementById('customDateRange');
    const customRange2 = document.getElementById('customDateRange2');
    
    if (periodType === 'custom') {
        customRange1.style.display = 'block';
        customRange2.style.display = 'block';
    } else {
        customRange1.style.display = 'none';
        customRange2.style.display = 'none';
    }
}

function loadCategories() {
    const transactionType = document.getElementById('transaction_type').value;
    const categorySelect = document.getElementById('category_id');
    
    // Clear existing options except first
    categorySelect.innerHTML = '<option value="">Semua Kategori</option>';
    
    if (transactionType) {
        fetch(`{{ route('reports.categories-by-type') }}?type=${transactionType}`)
            .then(response => response.json())
            .then(categories => {
                categories.forEach(category => {
                    const option = document.createElement('option');
                    option.value = category.category_id;
                    option.textContent = category.category_name;
                    categorySelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error loading categories:', error));
    } else {
        // Load all categories
        categoriesData.forEach(category => {
            const option = document.createElement('option');
            option.value = category.category_id;
            option.textContent = category.category_name;
            categorySelect.appendChild(option);
        });
    }
}

function exportPdf() {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    
    const params = new URLSearchParams();
    for (let [key, value] of formData.entries()) {
        if (value) params.append(key, value);
    }
    
    window.open(`{{ route('reports.export.pdf') }}?${params.toString()}`, '_blank');
}

function printReport() {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    
    const params = new URLSearchParams();
    for (let [key, value] of formData.entries()) {
        if (value) params.append(key, value);
    }
    
    const printWindow = window.open(`{{ route('reports.print') }}?${params.toString()}`, '_blank');
    printWindow.focus();
}

// Auto load categories on page load
document.addEventListener('DOMContentLoaded', function() {
    // Set initial custom date visibility
    toggleCustomDate();
    
    // Auto submit form when filters change (optional)
    const form = document.getElementById('filterForm');
    const selects = form.querySelectorAll('select, input[type="date"]');
    
    selects.forEach(select => {
        select.addEventListener('change', function() {
            // Optional: auto-submit when filter changes
            // form.submit();
        });
    });
});
</script>
@endsection