<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi Kas - {{ $dateRange['label'] }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            
            body {
                margin: 0;
                padding: 15px;
                font-size: 12px;
            }
            
            .container-fluid {
                padding: 0;
            }
            
            .page-break {
                page-break-before: always;
            }
            
            .table {
                font-size: 10px;
            }
            
            .table th,
            .table td {
                padding: 4px 6px;
                border: 1px solid #dee2e6 !important;
            }
        }
        
        .header-section {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #003d7a;
        }
        
        .header-section h1 {
            color: #003d7a;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .header-section h2 {
            color: #003d7a;
            font-size: 18px;
            margin-bottom: 5px;
        }
        
        .period-info-box {
            background: #f8f9fa;
            border-left: 4px solid #003d7a;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .summary-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .print-auto {
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Print Button -->
        <div class="no-print mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="fas fa-print me-1"></i> Print Laporan
                </button>
                <button onclick="window.close()" class="btn btn-secondary">
                    <i class="fas fa-times me-1"></i> Tutup
                </button>
            </div>
            <hr>
        </div>

        <!-- Header -->
        <div class="header-section">
            <h1>LAPORAN TRANSAKSI KAS</h1>
            <h2>PT PLN (Persero) UP3 Langsa</h2>
            <p class="mb-1"><strong>Sistem Informasi Kas (SiKas)</strong></p>
            <p class="text-muted">Dicetak pada: {{ now()->format('d F Y, H:i:s') }} WIB</p>
        </div>

        <!-- Access Information -->
        @if($user->role !== 'admin')
        <div class="alert alert-info">
            <strong>Laporan Unit:</strong> {{ $user->unit->unit_name }}
            <br>
            <small>User hanya dapat melihat laporan dari unit sendiri</small>
        </div>
        @endif

        <!-- Period Information -->
        <div class="period-info-box">
            <h5 class="text-primary mb-2">
                <i class="fas fa-calendar me-2"></i>
                Periode Laporan: {{ $dateRange['label'] }}
            </h5>
            <p class="mb-1">
                <strong>Dari:</strong> {{ \Carbon\Carbon::parse($dateRange['start'])->format('d F Y') }} 
                <strong>sampai:</strong> {{ \Carbon\Carbon::parse($dateRange['end'])->format('d F Y') }}
                ({{ \Carbon\Carbon::parse($dateRange['start'])->diffInDays($dateRange['end']) + 1 }} hari)
            </p>
            
            @if(!empty($filters['transaction_type']) || !empty($filters['category_id']) || (!empty($filters['unit_id']) && $user->role === 'admin'))
            <hr class="my-2">
            <h6 class="text-primary">Filter yang Diterapkan:</h6>
            <ul class="mb-0">
                @if(!empty($filters['transaction_type']))
                    <li>Jenis Transaksi: {{ $filters['transaction_type'] === 'income' ? 'Kas Masuk' : 'Kas Keluar' }}</li>
                @endif
                @if(!empty($filters['category_id']))
                    @php
                        $category = \App\Models\Category::find($filters['category_id']);
                    @endphp
                    @if($category)
                        <li>Kategori: {{ $category->category_name }}</li>
                    @endif
                @endif
                @if(!empty($filters['unit_id']) && $user->role === 'admin')
                    @php
                        $unit = \App\Models\Unit::find($filters['unit_id']);
                    @endphp
                    @if($unit)
                        <li>Unit: {{ $unit->unit_name }}</li>
                    @endif
                @endif
            </ul>
            @endif
        </div>

        <!-- Summary Section -->
        <div class="summary-card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-chart-pie me-2"></i>
                    RINGKASAN TRANSAKSI
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="p-3 bg-success bg-opacity-10 rounded">
                            <h5 class="text-success mb-1">
                                <i class="fas fa-arrow-up me-1"></i>
                                Rp {{ number_format($summary['income_total'], 0, ',', '.') }}
                            </h5>
                            <small class="text-muted">Total Pemasukan</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-danger bg-opacity-10 rounded">
                            <h5 class="text-danger mb-1">
                                <i class="fas fa-arrow-down me-1"></i>
                                Rp {{ number_format($summary['expense_total'], 0, ',', '.') }}
                            </h5>
                            <small class="text-muted">Total Pengeluaran</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-{{ $summary['balance'] >= 0 ? 'primary' : 'warning' }} bg-opacity-10 rounded">
                            <h5 class="text-{{ $summary['balance'] >= 0 ? 'primary' : 'warning' }} mb-1">
                                <i class="fas fa-balance-scale me-1"></i>
                                Rp {{ number_format($summary['balance'], 0, ',', '.') }}
                            </h5>
                            <small class="text-muted">Saldo/Selisih</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-info bg-opacity-10 rounded">
                            <h5 class="text-info mb-1">
                                <i class="fas fa-receipt me-1"></i>
                                {{ number_format($summary['total_transactions'], 0, ',', '.') }}
                            </h5>
                            <small class="text-muted">Total Transaksi</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category Breakdown -->
        @if($summary['category_breakdown']->count() > 0)
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>
                    RINCIAN PER KATEGORI
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0">
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
                            @foreach($summary['category_breakdown'] as $index => $breakdown)
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
        @endif

        <!-- Transactions Section -->
        <div class="card page-break">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-list-ul me-2"></i>
                    DAFTAR TRANSAKSI ({{ $transactions->count() }} transaksi)
                </h5>
            </div>
            <div class="card-body p-0">
                @if($transactions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="50">#</th>
                                <th width="80">Tanggal</th>
                                <th width="120">No. Transaksi</th>
                                <th>Deskripsi</th>
                                <th width="120">Kategori</th>
                                @if($user->role === 'admin')
                                <th width="100">Unit</th>
                                @endif
                                <th width="80" class="text-center">Jenis</th>
                                <th width="130" class="text-end">Nominal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $index => $transaction)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="text-center">{{ $transaction->transaction_date->format('d/m/Y') }}</td>
                                <td>
                                    <small>{{ $transaction->transaction_number }}</small>
                                </td>
                                <td>{{ $transaction->description ?: '-' }}</td>
                                <td>
                                    <small class="badge bg-{{ $transaction->category->transaction_type === 'income' ? 'success' : 'danger' }}">
                                        {{ $transaction->category->category_name }}
                                    </small>
                                </td>
                                @if($user->role === 'admin')
                                <td>
                                    <small>{{ $transaction->unit->unit_name }}</small>
                                </td>
                                @endif
                                <td class="text-center">
                                    <span class="badge bg-{{ $transaction->category->transaction_type === 'income' ? 'success' : 'danger' }}">
                                        {{ $transaction->category->transaction_type === 'income' ? 'Masuk' : 'Keluar' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <strong class="text-{{ $transaction->category->transaction_type === 'income' ? 'success' : 'danger' }}">
                                        Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                    </strong>
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
                </div>
                @endif
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-4 pt-3 border-top">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-1">
                        <strong>Sistem Informasi Kas PLN UP3 Langsa</strong>
                    </p>
                    <small class="text-muted">
                        Version 1.0 &copy; {{ date('Y') }} - Built with Laravel {{ app()->version() }}
                    </small>
                </div>
                <div class="col-md-6 text-end">
                    <p class="mb-1">
                        <strong>Laporan dibuat oleh:</strong>
                    </p>
                    <p class="mb-1">{{ $user->name }} ({{ $user->role === 'admin' ? 'Administrator' : 'User' }})</p>
                    <small class="text-muted">{{ $user->unit->unit_name }}</small>
                </div>
            </div>
        </div>

        <!-- Auto Print Script -->
        <div class="print-auto no-print">
            <small class="text-muted">
                <i class="fas fa-info-circle me-1"></i>
                Halaman ini akan otomatis print dalam 3 detik...
                <button onclick="cancelAutoPrint()" class="btn btn-sm btn-outline-secondary ms-2">Batal</button>
            </small>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto print after 3 seconds
        let autoPrintTimer = setTimeout(function() {
            window.print();
        }, 3000);

        function cancelAutoPrint() {
            clearTimeout(autoPrintTimer);
            document.querySelector('.print-auto').style.display = 'none';
        }

        // Print on Ctrl+P
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
        });
    </script>
</body>
</html>