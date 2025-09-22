<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi Kas - {{ $dateRange['label'] }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Courier', monospace;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            background: white;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #003d7a;
        }
        
        .header h1 {
            font-size: 18px;
            color: #003d7a;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .header h2 {
            font-size: 14px;
            color: #003d7a;
            margin-bottom: 3px;
        }
        
        .header p {
            font-size: 10px;
            color: #666;
            margin-bottom: 2px;
        }
        
        .period-info {
            background: #f8f9fa;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 4px solid #003d7a;
            border-radius: 4px;
        }
        
        .period-info strong {
            color: #003d7a;
            font-size: 12px;
        }
        
        .summary {
            margin-bottom: 25px;
        }
        
        .summary-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }
        
        .summary-row {
            display: table-row;
        }
        
        .summary-item {
            display: table-cell;
            width: 25%;
            padding: 15px;
            text-align: center;
            border: 1px solid #dee2e6;
            vertical-align: middle;
        }
        
        .summary-item h3 {
            font-size: 10px;
            color: #666;
            margin-bottom: 8px;
            text-transform: uppercase;
            font-weight: normal;
        }
        
        .amount {
            font-size: 12px;
            font-weight: bold;
            margin: 0;
        }
        
        .amount.income {
            color: #28a745;
        }
        
        .amount.expense {
            color: #dc3545;
        }
        
        .amount.balance {
            color: #007bff;
        }
        
        .amount.info {
            color: #17a2b8;
        }
        
        .transactions-section {
            margin-top: 25px;
        }
        
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #003d7a;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 1px solid #dee2e6;
        }
        
        .transactions-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .transactions-table th,
        .transactions-table td {
            border: 1px solid #dee2e6;
            padding: 6px 4px;
            text-align: left;
            font-size: 9px;
        }
        
        .transactions-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #003d7a;
            text-align: center;
        }
        
        .transactions-table .text-center {
            text-align: center;
        }
        
        .transactions-table .text-right {
            text-align: right;
        }
        
        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
            color: white;
        }
        
        .badge.income {
            background-color: #28a745;
        }
        
        .badge.expense {
            background-color: #dc3545;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
            font-style: italic;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }
        
        .access-info {
            background: #e7f3ff;
            padding: 10px 15px;
            margin-bottom: 20px;
            border-left: 4px solid #007bff;
            font-size: 10px;
            border-radius: 4px;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #dee2e6;
            padding-top: 15px;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        /* Category breakdown */
        .category-breakdown {
            margin-top: 25px;
        }
        
        .breakdown-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .breakdown-table th,
        .breakdown-table td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left;
            font-size: 10px;
        }
        
        .breakdown-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #003d7a;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 20px;
            }
            
            .page-break {
                page-break-before: always;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>LAPORAN TRANSAKSI KAS</h1>
        <h2>PT PLN (Persero) UP3 Langsa</h2>
        <p><strong>Sistem Informasi Kas (SiKas)</strong></p>
        <p>Dicetak pada: {{ now()->format('d F Y, H:i:s') }} WIB</p>
    </div>

    <!-- Access Information -->
    @if($user->role !== 'admin')
    <div class="access-info">
        <strong>Laporan Unit:</strong> {{ $user->unit->unit_name }}
        <br>
        <small>User hanya dapat melihat laporan dari unit sendiri</small>
    </div>
    @endif

    <!-- Period Information -->
    <div class="period-info">
        <strong>Periode Laporan: {{ $dateRange['label'] }}</strong>
        <br>
        <small>
            Dari: {{ \Carbon\Carbon::parse($dateRange['start'])->format('d F Y') }} 
            sampai: {{ \Carbon\Carbon::parse($dateRange['end'])->format('d F Y') }}
            ({{ \Carbon\Carbon::parse($dateRange['start'])->diffInDays($dateRange['end']) + 1 }} hari)
        </small>
        
        @if(!empty($filters['transaction_type']) || !empty($filters['category_id']) || (!empty($filters['unit_id']) && $user->role === 'admin'))
        <br><br>
        <strong>Filter yang Diterapkan:</strong>
        <ul style="margin: 5px 0 0 20px; font-size: 9px;">
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
    <div class="summary">
        <div class="section-title">RINGKASAN TRANSAKSI</div>
        <div class="summary-grid">
            <div class="summary-row">
                <div class="summary-item">
                    <h3>Total Pemasukan</h3>
                    <p class="amount income">Rp {{ number_format($summary['income_total'], 0, ',', '.') }}</p>
                </div>
                <div class="summary-item">
                    <h3>Total Pengeluaran</h3>
                    <p class="amount expense">Rp {{ number_format($summary['expense_total'], 0, ',', '.') }}</p>
                </div>
                <div class="summary-item">
                    <h3>Saldo/Selisih</h3>
                    <p class="amount balance">Rp {{ number_format($summary['balance'], 0, ',', '.') }}</p>
                </div>
                <div class="summary-item">
                    <h3>Total Transaksi</h3>
                    <p class="amount info">{{ number_format($summary['total_transactions'], 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Breakdown -->
    @if($summary['category_breakdown']->count() > 0)
    <div class="category-breakdown">
        <div class="section-title">RINCIAN PER KATEGORI</div>
        <table class="breakdown-table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="40%">Kategori</th>
                    <th width="15%">Jenis</th>
                    <th width="15%" class="text-center">Jumlah Transaksi</th>
                    <th width="25%" class="text-right">Total Nominal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($summary['category_breakdown'] as $index => $breakdown)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $breakdown['category_name'] }}</td>
                    <td class="text-center">
                        <span class="badge {{ $breakdown['transaction_type'] }}">
                            {{ $breakdown['transaction_type'] === 'income' ? 'Masuk' : 'Keluar' }}
                        </span>
                    </td>
                    <td class="text-center">{{ number_format($breakdown['count'], 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($breakdown['total_amount'], 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Transactions Section -->
    <div class="transactions-section page-break">
        <div class="section-title">DAFTAR TRANSAKSI ({{ $transactions->count() }} transaksi)</div>
        
        @if($transactions->count() > 0)
        <table class="transactions-table">
            <thead>
                <tr>
                    <th width="4%">No</th>
                    <th width="8%">Tanggal</th>
                    <th width="12%">No. Transaksi</th>
                    <th width="25%">Deskripsi</th>
                    <th width="12%">Kategori</th>
                    @if($user->role === 'admin')
                    <th width="12%">Unit</th>
                    @endif
                    <th width="8%">Jenis</th>
                    <th width="15%" class="text-right">Nominal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $index => $transaction)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $transaction->transaction_date->format('d/m/Y') }}</td>
                    <td>{{ $transaction->transaction_number }}</td>
                    <td>{{ $transaction->description ?: '-' }}</td>
                    <td>{{ $transaction->category->category_name }}</td>
                    @if($user->role === 'admin')
                    <td>{{ $transaction->unit->unit_name }}</td>
                    @endif
                    <td class="text-center">
                        <span class="badge {{ $transaction->category->transaction_type }}">
                            {{ $transaction->category->transaction_type === 'income' ? 'Masuk' : 'Keluar' }}
                        </span>
                    </td>
                    <td class="text-right">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="no-data">
            <strong>Tidak Ada Data Transaksi</strong>
            <br>
            <small>Tidak ada transaksi ditemukan untuk periode dan filter yang dipilih.</small>
        </div>
        @endif
    </div>

    <!-- Footer -->
    <div class="footer">
        <div style="display: table; width: 100%;">
            <div style="display: table-row;">
                <div style="display: table-cell; width: 50%; text-align: left;">
                    <strong>Sistem Informasi Kas PLN UP3 Langsa</strong>
                    <br>
                    <small>Version 1.0 &copy; {{ date('Y') }} - Built with Laravel {{ app()->version() }}</small>
                </div>
                <div style="display: table-cell; width: 50%; text-align: right;">
                    <strong>Laporan dibuat oleh:</strong>
                    <br>
                    {{ $user->name }} ({{ $user->role === 'admin' ? 'Administrator' : 'User' }})
                    <br>
                    <small>{{ $user->unit->unit_name }}</small>
                </div>
            </div>
        </div>
    </div>
</body>
</html>