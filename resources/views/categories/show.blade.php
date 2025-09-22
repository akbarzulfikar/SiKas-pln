@extends('layouts.app')

@section('title', 'Detail Kategori - Sistem Kas PLN')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="detail-container">
                <div class="detail-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3><i class="fas fa-tag"></i> Detail Kategori</h3>
                            <p class="text-muted mb-0">{{ $category->category_name }}</p>
                        </div>
                        <div class="header-actions">
                            <span class="badge bg-{{ $category->badge_color }} fs-6 me-2">
                                <i class="{{ $category->icon }}"></i>
                                {{ $category->type_display }}
                            </span>
                            <span class="badge bg-{{ $category->is_active ? 'success' : 'warning' }} fs-6">
                                {{ $category->is_active ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="detail-body">
                    <div class="row">
                        <!-- Category Info -->
                        <div class="col-md-8">
                            <div class="info-section">
                                <h5><i class="fas fa-info-circle"></i> Informasi Kategori</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <label>ID Kategori:</label>
                                            <span class="fw-bold">{{ $category->category_id }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <label>Nama Kategori:</label>
                                            <span class="fw-bold">{{ $category->category_name }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <label>Jenis Transaksi:</label>
                                            <span class="badge bg-{{ $category->badge_color }}">
                                                <i class="{{ $category->icon }}"></i>
                                                {{ $category->type_display }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <label>Status:</label>
                                            <span class="badge bg-{{ $category->is_active ? 'success' : 'warning' }}">
                                                {{ $category->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="info-item">
                                            <label>Deskripsi:</label>
                                            <span>{{ $category->description ?: 'Tidak ada deskripsi' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <label>Dibuat oleh:</label>
                                            <span>{{ $category->creator->name ?? 'Unknown' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <label>Tanggal dibuat:</label>
                                            <span>{{ $category->created_at->format('d F Y H:i') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Usage Statistics -->
                            <div class="stats-section">
                                <h5><i class="fas fa-chart-bar"></i> Statistik Penggunaan</h5>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="stat-item">
                                            <div class="stat-number text-primary">{{ number_format($usageStats['total_transactions']) }}</div>
                                            <div class="stat-label">Total Transaksi</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="stat-item">
                                            <div class="stat-number text-success">Rp {{ number_format($usageStats['total_amount'], 0, ',', '.') }}</div>
                                            <div class="stat-label">Total Nominal</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="stat-item">
                                            <div class="stat-number text-info">Rp {{ number_format($usageStats['avg_amount'], 0, ',', '.') }}</div>
                                            <div class="stat-label">Rata-rata per Transaksi</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Recent Transactions -->
                            <div class="transactions-section">
                                <h5><i class="fas fa-history"></i> Transaksi Terbaru (10 Terakhir)</h5>
                                @if($recentTransactions->count() > 0)
                                <div class="transaction-list">
                                    @foreach($recentTransactions as $transaction)
                                    <div class="transaction-item">
                                        <div class="transaction-icon {{ $transaction->transaction_type }}">
                                            <i class="fas fa-{{ $transaction->transaction_type == 'income' ? 'arrow-up' : 'arrow-down' }}"></i>
                                        </div>
                                        <div class="transaction-info">
                                            <div class="transaction-number">{{ $transaction->transaction_number }}</div>
                                            <div class="transaction-desc">{{ Str::limit($transaction->description ?: 'Transaksi kas', 30) }}</div>
                                            <div class="transaction-date">{{ $transaction->transaction_date->format('d/m/Y') }}</div>
                                        </div>
                                        <div class="transaction-amount {{ $transaction->transaction_type == 'income' ? 'income' : 'expense' }}">
                                            Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                        </div>
                                        <div class="transaction-unit">
                                            <span class="badge bg-secondary">{{ $transaction->unit->unit_name }}</span>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @else
                                <div class="no-transactions">
                                    <i class="fas fa-inbox"></i>
                                    <p>Belum ada transaksi menggunakan kategori ini</p>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Actions Panel -->
                        <div class="col-md-4">
                            <div class="actions-section">
                                <h5><i class="fas fa-tools"></i> Aksi Kategori</h5>
                                <div class="action-buttons">
                                    <a href="{{ route('categories.edit', $category->category_id) }}" 
                                       class="btn btn-warning w-100 mb-2">
                                        <i class="fas fa-edit"></i> Edit Kategori
                                    </a>

                                    @if($category->is_active)
                                    <form action="{{ route('categories.toggle-status', $category->category_id) }}" method="POST" class="mb-2">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-warning w-100">
                                            <i class="fas fa-toggle-off"></i> Nonaktifkan
                                        </button>
                                    </form>
                                    @else
                                    <form action="{{ route('categories.toggle-status', $category->category_id) }}" method="POST" class="mb-2">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-success w-100">
                                            <i class="fas fa-toggle-on"></i> Aktifkan
                                        </button>
                                    </form>
                                    @endif

                                    @if($category->canBeDeleted())
                                    <button class="btn btn-outline-danger w-100 mb-2" 
                                            onclick="confirmDelete('{{ $category->category_id }}', '{{ $category->category_name }}')">
                                        <i class="fas fa-trash"></i> Hapus Kategori
                                    </button>
                                    @else
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <small>Kategori tidak dapat dihapus karena masih digunakan dalam {{ $category->transactions_count }} transaksi.</small>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Export Options -->
                            <div class="export-section">
                                <h5><i class="fas fa-download"></i> Export Data</h5>
                                <div class="export-buttons">
                                    <a href="{{ route('categories.export', ['type' => $category->transaction_type]) }}" 
                                       class="btn btn-outline-success w-100 mb-2">
                                        <i class="fas fa-file-csv"></i> Export CSV
                                    </a>
                                    <a href="{{ route('transactions.index', ['category_id' => $category->category_id]) }}" 
                                       class="btn btn-outline-primary w-100">
                                        <i class="fas fa-list"></i> Lihat Transaksi
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="detail-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                        </a>
                        
                        <div class="footer-info">
                            <small class="text-muted">
                                Terakhir diupdate: {{ $category->updated_at->format('d/m/Y H:i') }}
                            </small>
                        </div>
                    </div>
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
    .detail-container {
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .detail-header {
        background: linear-gradient(135deg, var(--pln-blue), var(--pln-blue-dark));
        color: white;
        padding: 25px 30px;
    }

    .detail-header h3 {
        margin: 0;
        font-weight: 600;
    }

    .header-actions .badge {
        padding: 8px 12px;
    }

    .detail-body {
        padding: 30px;
    }

    .info-section, .stats-section, .transactions-section, .actions-section, .export-section {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .info-section h5, .stats-section h5, .transactions-section h5, .actions-section h5, .export-section h5 {
        color: var(--pln-blue);
        margin-bottom: 15px;
        font-weight: 600;
    }

    .info-item {
        margin-bottom: 12px;
    }

    .info-item label {
        font-weight: 600;
        color: #495057;
        display: block;
        margin-bottom: 4px;
        font-size: 0.9rem;
    }

    .info-item span {
        color: #333;
    }

    .stat-item {
        text-align: center;
        padding: 15px;
        background: white;
        border-radius: 8px;
        margin-bottom: 15px;
    }

    .stat-number {
        font-size: 1.5rem;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .stat-label {
        color: #666;
        font-size: 0.9rem;
    }

    .transaction-list {
        max-height: 300px;
        overflow-y: auto;
    }

    .transaction-item {
        display: flex;
        align-items: center;
        padding: 12px;
        background: white;
        border-radius: 8px;
        margin-bottom: 10px;
        gap: 15px;
    }

    .transaction-icon {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 14px;
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

    .transaction-number {
        font-weight: 600;
        color: #333;
        font-size: 0.9rem;
    }

    .transaction-desc {
        color: #666;
        font-size: 0.8rem;
        margin-top: 2px;
    }

    .transaction-date {
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

    .transaction-unit {
        min-width: 100px;
        text-align: center;
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

    .detail-footer {
        background-color: #f8f9fa;
        padding: 20px 30px;
        border-top: 1px solid #dee2e6;
    }

    @media (max-width: 768px) {
        .detail-header, .detail-body, .detail-footer {
            padding: 20px;
        }
        
        .header-actions {
            margin-top: 10px;
        }
        
        .detail-footer {
            flex-direction: column;
            gap: 15px;
        }
        
        .detail-footer .d-flex {
            flex-direction: column;
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