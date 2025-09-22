@extends('layouts.app')

@section('title', 'Dashboard - Sistem Kas PLN')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <div class="page-icon me-3">
                <i class="fas fa-tachometer-alt"></i>
            </div>
            <h1 class="page-title mb-0">Dashboard</h1>
        </div>
        <div class="d-flex gap-2">
            <div class="btn-group">
                <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fas fa-calendar"></i> Filter Periode
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" onclick="changePeriod('today')">Hari ini</a></li>
                    <li><a class="dropdown-item" href="#" onclick="changePeriod('week')">Minggu ini</a></li>
                    <li><a class="dropdown-item" href="#" onclick="changePeriod('month')">Bulan ini</a></li>
                    <li><a class="dropdown-item" href="#" onclick="changePeriod('year')">Tahun ini</a></li>
                </ul>
            </div>
            <a href="#" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Laporan
            </a>
            <a href="{{ route('transactions.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Transaksi Baru
            </a>
        </div>
    </div>

    <!-- Welcome Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-card">
                <div class="welcome-content">
                    <h2>Selamat Datang, {{ Auth::user()->name }}! 👋</h2>
                    <p class="mb-2">
                        <strong>Unit:</strong> {{ Auth::user()->unit->unit_name }}
                        <span class="badge bg-{{ Auth::user()->unit->badge_color }}">{{ Auth::user()->unit->unit_type }}</span>
                    </p>
                    <p class="mb-2">
                        <strong>Role:</strong>
                        <span class="badge bg-{{ Auth::user()->role_badge_color }}">{{ Auth::user()->role_display_name }}</span>
                    </p>
                    <p class="mb-0">
                        <strong>Last Login:</strong>
                        {{ Auth::user()->last_login ? Auth::user()->last_login->format('d/m/Y H:i') : 'Pertama kali login' }}
                    </p>
                </div>
                <div class="welcome-icon">
                    <i class="fas fa-bolt"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="kpi-card balance-card">
                <div class="kpi-icon">
                    <i class="fas fa-wallet"></i>
                </div>
                <div class="kpi-content">
                    <h6>Saldo Kas</h6>
                    <h3 class="{{ $balance >= 0 ? 'text-success' : 'text-danger' }}">
                        Rp {{ number_format($balance, 0, ',', '.') }}
                    </h3>
                    @if($totalTransactions > 0)
                    <small class="text-muted">{{ $totalTransactions }} transaksi</small>
                    @else
                    <small class="text-muted">Belum ada transaksi</small>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card income-card">
                <div class="kpi-icon">
                    <i class="fas fa-arrow-up"></i>
                </div>
                <div class="kpi-content">
                    <h6>Kas Masuk Minggu Ini</h6>
                    <h3 class="text-success">Rp {{ number_format($incomeThisWeek, 0, ',', '.') }}</h3>
                    @if($incomeThisWeek > 0)
                    <small class="text-muted">Minggu ini</small>
                    @else
                    <small class="text-muted">Belum ada kas masuk</small>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card expense-card">
                <div class="kpi-icon">
                    <i class="fas fa-arrow-down"></i>
                </div>
                <div class="kpi-content">
                    <h6>Kas Keluar Minggu Ini</h6>
                    <h3 class="text-danger">Rp {{ number_format($expenseThisWeek, 0, ',', '.') }}</h3>
                    @if($expenseThisWeek > 0)
                    <small class="text-muted">Minggu ini</small>
                    @else
                    <small class="text-muted">Belum ada kas keluar</small>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card total-card">
                <div class="kpi-icon">
                    <i class="fas fa-list"></i>
                </div>
                <div class="kpi-content">
                    <h6>Total Transaksi</h6>
                    <h3 class="text-primary">{{ number_format($totalTransactions) }}</h3>
                    @if($totalTransactions > 0)
                    <small class="text-muted">Semua transaksi</small>
                    @else
                    <small class="text-muted">Belum ada transaksi</small>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section - FIXED VERSION -->
    <div class="row mb-4">
        <!-- Kas Harian Chart -->
        <div class="col-md-8">
            <div class="chart-card">
                <div class="chart-header">
                    <h5><i class="fas fa-chart-line"></i> Tren Kas Harian</h5>
                    <div class="chart-controls">
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-primary active" onclick="changeChartPeriod('7days')">7 Hari</button>
                            <button type="button" class="btn btn-outline-primary" onclick="changeChartPeriod('30days')">30 Hari</button>
                            <button type="button" class="btn btn-outline-primary" onclick="changeChartPeriod('90days')">90 Hari</button>
                        </div>
                    </div>
                </div>
                <div class="chart-body">
                    <canvas id="trendChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Kategori Kas Keluar Chart - FIXED VERSION -->
        <div class="col-md-4">
            <div class="chart-card chart-card-fixed">
                <div class="chart-header">
                    <h5><i class="fas fa-chart-pie"></i> Kategori Kas Keluar</h5>
                    @if(Auth::user()->role === 'admin' && isset($units) && $units->count() > 0)
                    <div class="chart-controls">
                        <select class="form-select form-select-sm text-white bg-transparent border-light unit-filter-select" 
                                id="unitFilter" onchange="loadCategoryDataWithFilter()">
                            <option value="">Semua Unit</option>
                            @foreach($units as $unit)
                            <option value="{{ $unit->unit_id }}">{{ $unit->unit_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                </div>
                <div class="chart-body chart-body-fixed">
                    <div class="chart-container-fixed">
                        <canvas id="categoryChart"></canvas>
                    </div>
                    <div id="chartInfo" class="chart-info-fixed">
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i>
                            <span id="chartInfoText">
                                @if(Auth::user()->role === 'admin')
                                    Menampilkan: Semua Unit
                                @else
                                    Menampilkan: {{ Auth::user()->unit->unit_name }}
                                @endif
                            </span>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Latest Transactions & Quick Actions -->
    <div class="row mb-4">
        <!-- Latest Transactions -->
        <div class="col-md-8">
            <div class="transactions-card">
                <div class="transactions-header">
                    <h5><i class="fas fa-clock"></i> Transaksi Hari Ini</h5>
                    <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-outline-primary">
                        Lihat Semua <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="transactions-body">
                    @if($todaysTransactions->count() > 0)
                    <div class="transaction-list">
                        @foreach($todaysTransactions as $transaction)
                        <div class="transaction-item">
                            <div class="transaction-icon {{ $transaction->transaction_type }}">
                                <i class="fas fa-{{ $transaction->transaction_type == 'income' ? 'arrow-up' : 'arrow-down' }}"></i>
                            </div>
                            <div class="transaction-info">
                                <div class="transaction-category">{{ $transaction->category->category_name }}</div>
                                <div class="transaction-desc">{{ Str::limit($transaction->description ?: 'Transaksi kas', 30) }}</div>
                                <small class="transaction-time">{{ $transaction->created_at->format('H:i') }}</small>
                            </div>
                            <div class="transaction-amount {{ $transaction->transaction_type == 'income' ? 'income' : 'expense' }}">
                                {{ $transaction->transaction_type == 'income' ? '+' : '-' }}Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                            </div>
                            <div class="transaction-actions">
                                <a href="{{ route('transactions.show', $transaction->transaction_id) }}"
                                    class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="no-transactions">
                        <i class="fas fa-inbox"></i>
                        <p>Belum ada transaksi hari ini</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-md-4">
            <div class="quick-actions-card">
                <h5><i class="fas fa-bolt"></i> Quick Actions</h5>
                <div class="actions-grid">
                    <a href="{{ route('transactions.create') }}?type=income" class="action-item income-action">
                        <i class="fas fa-plus-circle"></i>
                        <span>Tambah Kas Masuk</span>
                    </a>
                    <a href="{{ route('transactions.create') }}?type=expense" class="action-item expense-action">
                        <i class="fas fa-minus-circle"></i>
                        <span>Tambah Kas Keluar</span>
                    </a>
                    <a href="{{ route('transactions.index') }}" class="action-item report-action">
                        <i class="fas fa-list"></i>
                        <span>Lihat Transaksi</span>
                    </a>
                    @if(Auth::user()->role === 'admin')
                    <a href="#" class="action-item admin-action">
                        <i class="fas fa-cog"></i>
                        <span>Pengaturan</span>
                    </a>
                    @endif
                </div>
            </div>

            <!-- Unit Stats for Admin -->
            @if(Auth::user()->role === 'admin' && isset($unitStats) && $unitStats->count() > 0)
            <div class="unit-stats-card">
                <h5><i class="fas fa-building"></i> Statistik Unit</h5>
                <div class="unit-list">
                    @foreach($unitStats as $unit)
                    <div class="unit-item">
                        <div class="unit-info">
                            <div class="unit-name">{{ $unit['unit_name'] }}</div>
                            <div class="unit-balance">
                                Saldo:
                                <span class="{{ $unit['balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    Rp {{ number_format($unit['balance'], 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                        <div class="unit-badge">
                            <span class="badge bg-{{ $unit['unit_type'] === 'UP3' ? 'success' : 'info' }}">
                                {{ $unit['unit_type'] }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- System Info -->
    <div class="row">
        <div class="col-md-6">
            <div class="info-card">
                <h5><i class="fas fa-info-circle"></i> Informasi Sistem</h5>
                <ul class="info-list">
                    <li><strong>Version:</strong> 1.0.0</li>
                    <li><strong>Database:</strong> MySQL</li>
                    <li><strong>Framework:</strong> Laravel 12</li>
                    <li><strong>Environment:</strong> {{ app()->environment() }}</li>
                    <li><strong>Timezone:</strong> {{ config('app.timezone') }}</li>
                </ul>
            </div>
        </div>
        <div class="col-md-6">
            <div class="info-card">
                <h5><i class="fas fa-database"></i> Data Summary</h5>
                <ul class="info-list">
                    <li><strong>Total Users:</strong> {{ \App\Models\User::count() }}</li>
                    <li><strong>Total Units:</strong> {{ \App\Models\Unit::count() }}</li>
                    <li><strong>Total Categories:</strong> {{ \App\Models\Category::count() }}</li>
                    <li><strong>Total Transactions:</strong> {{ \App\Models\Transaction::count() }}</li>
                    <li><strong>Last Update:</strong> {{ now()->format('d/m/Y H:i') }}</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="chartLoading" class="chart-loading" style="display: none;">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

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

    .welcome-card {
        background: linear-gradient(135deg, var(--pln-blue), var(--pln-blue-dark));
        color: white;
        border-radius: 15px;
        padding: 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .welcome-content h2 {
        margin-bottom: 15px;
        font-weight: 600;
    }

    .welcome-icon {
        font-size: 4rem;
        opacity: 0.3;
    }

    .kpi-card {
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

    .kpi-card:hover {
        transform: translateY(-2px);
    }

    .balance-card {
        border-left-color: var(--pln-blue);
    }

    .income-card {
        border-left-color: #28a745;
    }

    .expense-card {
        border-left-color: #dc3545;
    }

    .total-card {
        border-left-color: #007bff;
    }

    .kpi-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: white;
    }

    .balance-card .kpi-icon {
        background: linear-gradient(135deg, var(--pln-blue), var(--pln-blue-dark));
    }

    .income-card .kpi-icon {
        background: linear-gradient(135deg, #28a745, #34ce57);
    }

    .expense-card .kpi-icon {
        background: linear-gradient(135deg, #dc3545, #e85d75);
    }

    .total-card .kpi-icon {
        background: linear-gradient(135deg, #007bff, #0d6efd);
    }

    .kpi-content h6 {
        margin: 0;
        font-size: 0.9rem;
        color: #666;
        font-weight: 500;
    }

    .kpi-content h3 {
        margin: 5px 0 0 0;
        font-size: 1.5rem;
        font-weight: 700;
    }

    /* Chart Cards */
    .chart-card,
    .transactions-card,
    .quick-actions-card,
    .unit-stats-card,
    .info-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
        border: none;
    }

    /* FIXED VERSION - Chart Card untuk Donut */
    .chart-card-fixed {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
        border: none;
        height: 480px !important; /* FIXED HEIGHT */
        max-height: 480px !important;
        overflow: hidden;
    }

    .chart-header,
    .transactions-header {
        background: linear-gradient(135deg, var(--pln-blue), var(--pln-blue-dark));
        color: white;
        padding: 15px 20px;
        border-radius: 12px 12px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .chart-header h5,
    .transactions-header h5 {
        margin: 0;
        font-weight: 600;
    }

    .chart-body {
        padding: 20px;
    }

    /* FIXED VERSION - Chart Body untuk Donut */
    .chart-body-fixed {
        padding: 20px;
        height: 420px !important; /* FIXED HEIGHT */
        max-height: 420px !important;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .chart-container-fixed {
        height: 340px !important; /* FIXED HEIGHT untuk chart */
        max-height: 340px !important;
        position: relative;
        flex: 1;
    }

    #categoryChart {
        max-height: 340px !important;
        width: 100% !important;
    }

    .chart-info-fixed {
        padding: 8px;
        background: #f8f9fa;
        border-radius: 6px;
        border: 1px solid #e9ecef;
        margin-top: 15px;
        height: 40px !important; /* FIXED HEIGHT */
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0; /* Prevent shrinking */
    }

    .chart-info-fixed .text-muted {
        font-size: 0.75rem;
        margin: 0;
    }

    .chart-controls .btn-group .btn {
        border-color: rgba(255, 255, 255, 0.3);
        color: rgba(255, 255, 255, 0.8);
    }

    .chart-controls .btn-group .btn.active,
    .chart-controls .btn-group .btn:hover {
        background-color: rgba(255, 255, 255, 0.2);
        color: white;
        border-color: rgba(255, 255, 255, 0.5);
    }

    /* FIXED VERSION - Unit Filter Select */
    .unit-filter-select {
        min-width: 140px !important;
        max-width: 180px !important;
        font-size: 0.8rem !important;
        color: rgba(255, 255, 255, 0.9) !important;
        border-color: rgba(255, 255, 255, 0.3) !important;
        background-color: rgba(255, 255, 255, 0.1) !important;
        height: 32px !important;
    }

    .unit-filter-select:focus {
        border-color: rgba(255, 255, 255, 0.5) !important;
        box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.25) !important;
        background-color: rgba(255, 255, 255, 0.2) !important;
    }

    .unit-filter-select option {
        background-color: var(--pln-blue);
        color: white;
    }

    /* Transaction List */
    .transaction-list {
        max-height: 300px;
        overflow-y: auto;
    }

    .transaction-item {
        display: flex;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #f0f0f0;
        gap: 15px;
    }

    .transaction-item:last-child {
        border-bottom: none;
    }

    .transaction-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 16px;
    }

    .transaction-icon.income {
        background: linear-gradient(135deg, #28a745, #34ce57);
    }

    .transaction-icon.expense {
        background: linear-gradient(135deg, #dc3545, #e85d75);
    }

    .transaction-info {
        flex: 1;
    }

    .transaction-category {
        font-weight: 600;
        color: #333;
        font-size: 0.9rem;
    }

    .transaction-desc {
        color: #666;
        font-size: 0.8rem;
        margin-top: 2px;
    }

    .transaction-time {
        color: #999;
        font-size: 0.7rem;
    }

    .transaction-amount {
        font-weight: 700;
        font-size: 0.9rem;
        min-width: 120px;
        text-align: right;
    }

    .transaction-amount.income {
        color: #28a745;
    }

    .transaction-amount.expense {
        color: #dc3545;
    }

    .no-transactions {
        text-align: center;
        padding: 40px;
        color: #666;
    }

    .no-transactions i {
        font-size: 3rem;
        margin-bottom: 15px;
        display: block;
        color: #ddd;
    }

    /* Quick Actions */
    .quick-actions-card,
    .unit-stats-card,
    .info-card {
        padding: 25px;
    }

    .quick-actions-card h5,
    .unit-stats-card h5,
    .info-card h5 {
        margin-bottom: 20px;
        color: var(--pln-blue);
        font-weight: 600;
    }

    .actions-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }

    .action-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 20px;
        border-radius: 10px;
        text-decoration: none;
        transition: all 0.3s ease;
        border: 2px solid transparent;
        text-align: center;
    }

    .action-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .income-action {
        background: rgba(40, 167, 69, 0.1);
        color: #28a745;
        border-color: rgba(40, 167, 69, 0.2);
    }

    .expense-action {
        background: rgba(220, 53, 69, 0.1);
        color: #dc3545;
        border-color: rgba(220, 53, 69, 0.2);
    }

    .report-action {
        background: rgba(0, 123, 255, 0.1);
        color: #007bff;
        border-color: rgba(0, 123, 255, 0.2);
    }

    .admin-action {
        background: rgba(108, 117, 125, 0.1);
        color: #6c757d;
        border-color: rgba(108, 117, 125, 0.2);
    }

    .action-item i {
        font-size: 2rem;
        margin-bottom: 10px;
    }

    .action-item span {
        font-weight: 500;
        font-size: 0.85rem;
    }

    /* Unit Stats */
    .unit-list {
        max-height: 200px;
        overflow-y: auto;
    }

    .unit-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .unit-item:last-child {
        border-bottom: none;
    }

    .unit-name {
        font-weight: 600;
        color: #333;
        font-size: 0.9rem;
    }

    .unit-balance {
        color: #666;
        font-size: 0.8rem;
        margin-top: 2px;
    }

    /* Info List */
    .info-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .info-list li {
        padding: 8px 0;
        border-bottom: 1px solid #f0f0f0;
        font-size: 0.9rem;
    }

    .info-list li:last-child {
        border-bottom: none;
    }

    /* Chart Loading */
    .chart-loading {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .actions-grid {
            grid-template-columns: 1fr;
        }

        .transaction-item {
            flex-wrap: wrap;
        }

        .transaction-amount {
            min-width: auto;
            text-align: left;
            width: 100%;
            margin-top: 5px;
        }

        .chart-controls {
            margin-top: 10px;
        }

        .unit-filter-select {
            min-width: 120px !important;
            max-width: 150px !important;
            font-size: 0.7rem !important;
        }

        .chart-card-fixed {
            height: 420px !important;
        }

        .chart-body-fixed {
            height: 360px !important;
        }

        .chart-container-fixed {
            height: 280px !important;
        }

        #categoryChart {
            max-height: 280px !important;
        }
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Variables for charts - FIXED VERSION
    let trendChart = null;
    let categoryChart = null;
    let currentPeriod = '7days';
    let currentUnitFilter = '';
    let isLoadingCategory = false; // Prevent multiple requests
    let filterTimeout; // Debouncing timeout

    // Initialize charts when page loads
    document.addEventListener('DOMContentLoaded', function() {
        initializeTrendChart();
        initializeCategoryChart();
    });

    // Trend Chart (unchanged)
    function initializeTrendChart() {
        const ctx = document.getElementById('trendChart').getContext('2d');

        trendChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Kas Masuk',
                    data: [],
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Kas Keluar',
                    data: [],
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': Rp ' +
                                    new Intl.NumberFormat('id-ID').format(context.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Tanggal'
                        }
                    },
                    y: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Jumlah (Rp)'
                        },
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                            }
                        }
                    }
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                }
            }
        });

        // Load initial data
        loadTrendData(currentPeriod);
    }

    // Category Chart - FIXED VERSION
    function initializeCategoryChart() {
        const ctx = document.getElementById('categoryChart').getContext('2d');

        categoryChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: [],
                datasets: [{
                    data: [],
                    backgroundColor: [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0',
                        '#9966FF',
                        '#FF9F40',
                        '#FF6384',
                        '#C9CBCF'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                aspectRatio: 1,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 12,
                            usePointStyle: true,
                            boxWidth: 10,
                            font: {
                                size: 10
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = 'Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed);
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                },
                animation: {
                    duration: 400
                },
                interaction: {
                    intersect: false
                }
            }
        });

        // Load initial data
        loadCategoryData();
    }

    // Load trend chart data (unchanged)
    function loadTrendData(period) {
        showChartLoading();

        // Calculate date range based on period
        const endDate = new Date();
        const startDate = new Date();

        switch (period) {
            case '7days':
                startDate.setDate(endDate.getDate() - 6);
                break;
            case '30days':
                startDate.setDate(endDate.getDate() - 29);
                break;
            case '90days':
                startDate.setDate(endDate.getDate() - 89);
                break;
        }

        // Format dates for API
        const startDateStr = startDate.toISOString().split('T')[0];
        const endDateStr = endDate.toISOString().split('T')[0];

        // Fetch income and expense data
        Promise.all([
                fetch(`{{ route('dashboard.chart-data') }}?type=income&start_date=${startDateStr}&end_date=${endDateStr}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                }).then(response => response.json()),

                fetch(`{{ route('dashboard.chart-data') }}?type=expense&start_date=${startDateStr}&end_date=${endDateStr}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                }).then(response => response.json())
            ])
            .then(([incomeData, expenseData]) => {
                // Generate date labels
                const labels = [];
                const currentDate = new Date(startDate);

                while (currentDate <= endDate) {
                    labels.push(currentDate.toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: '2-digit'
                    }));
                    currentDate.setDate(currentDate.getDate() + 1);
                }

                // Process data to match labels
                const incomeValues = labels.map(label => {
                    const found = incomeData.find(item => {
                        const itemDate = new Date(item.date).toLocaleDateString('id-ID', {
                            day: '2-digit',
                            month: '2-digit'
                        });
                        return itemDate === label;
                    });
                    return found ? found.total : 0;
                });

                const expenseValues = labels.map(label => {
                    const found = expenseData.find(item => {
                        const itemDate = new Date(item.date).toLocaleDateString('id-ID', {
                            day: '2-digit',
                            month: '2-digit'
                        });
                        return itemDate === label;
                    });
                    return found ? found.total : 0;
                });

                // Update chart
                trendChart.data.labels = labels;
                trendChart.data.datasets[0].data = incomeValues;
                trendChart.data.datasets[1].data = expenseValues;
                trendChart.update();

                hideChartLoading();
            })
            .catch(error => {
                console.error('Error loading trend data:', error);
                hideChartLoading();
            });
    }

    // Load category chart data - FIXED VERSION
    function loadCategoryData(unitId = '') {
        // Prevent multiple requests
        if (isLoadingCategory) {
            console.log('Category data loading in progress, skipping...');
            return;
        }

        isLoadingCategory = true;
        showChartLoading();

        // Build URL dengan parameter unit_id jika ada
        let url = `{{ route('dashboard.category-data') }}?type=expense&days=30`;
        if (unitId) {
            url += `&unit_id=${unitId}`;
        }

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data && data.length > 0) {
                const labels = data.map(item => item.category);
                const values = data.map(item => item.total);
                const colors = data.map(item => item.color);

                categoryChart.data.labels = labels;
                categoryChart.data.datasets[0].data = values;
                categoryChart.data.datasets[0].backgroundColor = colors;
                categoryChart.update('none'); // Update without animation
            } else {
                // Show empty state
                categoryChart.data.labels = ['Belum ada data'];
                categoryChart.data.datasets[0].data = [1];
                categoryChart.data.datasets[0].backgroundColor = ['#e9ecef'];
                categoryChart.update('none');
            }

            // Update chart info text
            updateChartInfo(unitId);
            hideChartLoading();
            isLoadingCategory = false;
        })
        .catch(error => {
            console.error('Error loading category data:', error);
            hideChartLoading();
            isLoadingCategory = false;
            
            // Show error state
            categoryChart.data.labels = ['Error loading data'];
            categoryChart.data.datasets[0].data = [1];
            categoryChart.data.datasets[0].backgroundColor = ['#dc3545'];
            categoryChart.update('none');
        });
    }

    // Handle unit filter change with debouncing - FIXED VERSION
    function loadCategoryDataWithFilter() {
        if ('{{ Auth::user()->role }}' === 'admin') {
            // Clear previous timeout
            clearTimeout(filterTimeout);

            // Set new timeout with debouncing
            filterTimeout = setTimeout(function() {
                const unitSelect = document.getElementById('unitFilter');
                if (unitSelect && !isLoadingCategory) {
                    const selectedUnitId = unitSelect.value;
                    currentUnitFilter = selectedUnitId;
                    loadCategoryData(selectedUnitId);
                }
            }, 300); // 300ms delay
        }
    }

    // Update info text chart with safety check - FIXED VERSION
    function updateChartInfo(unitId) {
        const chartInfoText = document.getElementById('chartInfoText');
        if (!chartInfoText) return;

        try {
            if ('{{ Auth::user()->role }}' === 'admin') {
                if (unitId) {
                    const unitSelect = document.getElementById('unitFilter');
                    if (unitSelect) {
                        const selectedOption = unitSelect.querySelector(`option[value="${unitId}"]`);
                        const unitName = selectedOption ? selectedOption.textContent : 'Unit Terpilih';
                        chartInfoText.innerHTML = `Menampilkan: ${unitName}`;
                    } else {
                        chartInfoText.innerHTML = 'Menampilkan: Unit Terpilih';
                    }
                } else {
                    chartInfoText.innerHTML = 'Menampilkan: Semua Unit';
                }
            } else {
                chartInfoText.innerHTML = 'Menampilkan: {{ Auth::user()->unit->unit_name }}';
            }
        } catch (error) {
            console.error('Error updating chart info:', error);
            chartInfoText.innerHTML = 'Menampilkan: Data Grafik';
        }
    }

    // Change chart period
    function changeChartPeriod(period) {
        currentPeriod = period;

        // Update button states
        document.querySelectorAll('.chart-controls .btn').forEach(btn => {
            btn.classList.remove('active');
        });
        event.target.classList.add('active');

        // Reload chart data
        loadTrendData(period);
    }

    // Change dashboard period filter
    function changePeriod(period) {
        showChartLoading();
        window.location.href = window.location.pathname + '?period=' + period;
    }

    // Loading functions
    function showChartLoading() {
        const loadingElement = document.getElementById('chartLoading');
        if (loadingElement) {
            loadingElement.style.display = 'flex';
        }
    }

    function hideChartLoading() {
        const loadingElement = document.getElementById('chartLoading');
        if (loadingElement) {
            loadingElement.style.display = 'none';
        }
    }

    // Responsive chart resize
    window.addEventListener('resize', function() {
        if (trendChart) trendChart.resize();
        if (categoryChart) categoryChart.resize();
    });

    // REMOVE AUTO REFRESH to prevent infinite loop
    // Auto refresh is now disabled for performance
    console.log('Dashboard charts initialized successfully!');
</script>
@endsection