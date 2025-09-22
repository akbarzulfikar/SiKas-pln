@extends('layouts.app')

@section('title', 'Detail Transaksi - Sistem Kas PLN')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="detail-container">
                <div class="detail-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3><i class="fas fa-file-invoice"></i> Detail Transaksi</h3>
                            <p class="text-muted mb-0">{{ $transaction->transaction_number }}</p>
                        </div>
                        <div class="header-actions">
                            <span class="badge bg-{{ $transaction->type_badge_color }} fs-6">
                                {{ $transaction->type_display }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="detail-body">
                    <div class="row">
                        <!-- Transaction Info -->
                        <div class="col-md-8">
                            <div class="info-section">
                                <h5><i class="fas fa-info-circle"></i> Informasi Transaksi</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <label>ID Transaksi:</label>
                                            <span class="fw-bold">{{ $transaction->transaction_id }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <label>Nomor Transaksi:</label>
                                            <span class="fw-bold">{{ $transaction->transaction_number }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <label>Tanggal Transaksi:</label>
                                            <span>{{ $transaction->transaction_date->format('d F Y') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <label>Jenis Transaksi:</label>
                                            <span class="badge bg-{{ $transaction->type_badge_color }}">
                                                {{ $transaction->type_display }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <label>Kategori:</label>
                                            <span>{{ $transaction->category->category_name }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <label>Unit:</label>
                                            <span class="badge bg-{{ $transaction->unit->badge_color }}">
                                                {{ $transaction->unit->unit_name }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="info-item">
                                            <label>Deskripsi:</label>
                                            <span>{{ $transaction->description ?: 'Tidak ada deskripsi' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Amount Section -->
                            <div class="amount-section">
                                <h5><i class="fas fa-money-bill-wave"></i> Jumlah</h5>
                                <div class="amount-display">
                                    <div class="amount-number {{ $transaction->transaction_type == 'income' ? 'text-success' : 'text-danger' }}">
                                        Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                    </div>
                                    <div class="amount-words">
                                        {{ ucwords(\App\Helpers\NumberToWords::convert($transaction->amount)) }} Rupiah
                                    </div>
                                </div>
                            </div>

                            <!-- Creator Info -->
                            <div class="creator-section">
                                <h5><i class="fas fa-user"></i> Informasi Pembuat</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <label>Dibuat oleh:</label>
                                            <span>{{ $transaction->creator->name }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <label>Jabatan:</label>
                                            <span>{{ $transaction->creator->position ?: 'Staff' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <label>Tanggal dibuat:</label>
                                            <span>{{ $transaction->created_at->format('d F Y H:i') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <label>Terakhir diupdate:</label>
                                            <span>{{ $transaction->updated_at->format('d F Y H:i') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Evidence Section -->
                        <div class="col-md-4">
                            <div class="evidence-section">
                                <h5><i class="fas fa-paperclip"></i> Bukti Transaksi</h5>
                                @if($transaction->evidence_file)
                                    @if($transaction->evidenceFileExists())
                                        @if($transaction->isImageFile())
                                            <div class="evidence-preview">
                                                <img src="{{ $transaction->evidence_file_url }}" 
                                                     class="evidence-thumbnail" 
                                                     alt="Bukti Transaksi"
                                                     onclick="viewEvidence('{{ $transaction->evidence_file_url }}')">
                                                <div class="evidence-actions">
                                                    <a href="{{ route('transactions.show-evidence', $transaction->transaction_id) }}" 
                                                       class="btn btn-sm btn-outline-primary" target="_blank">
                                                        <i class="fas fa-eye"></i> Lihat Detail
                                                    </a>
                                                    <a href="{{ $transaction->evidence_file_url }}" 
                                                       class="btn btn-sm btn-outline-success" download>
                                                        <i class="fas fa-download"></i> Download
                                                    </a>
                                                </div>
                                            </div>
                                        @else
                                            <div class="evidence-file">
                                                @php
                                                    $extension = pathinfo($transaction->evidence_file, PATHINFO_EXTENSION);
                                                @endphp
                                                <div class="file-icon">
                                                    @if(in_array(strtolower($extension), ['pdf']))
                                                        <i class="fas fa-file-pdf text-danger"></i>
                                                    @elseif(in_array(strtolower($extension), ['doc', 'docx']))
                                                        <i class="fas fa-file-word text-primary"></i>
                                                    @else
                                                        <i class="fas fa-file text-secondary"></i>
                                                    @endif
                                                </div>
                                                <div class="file-info">
                                                    <div class="file-name">{{ $transaction->evidence_file }}</div>
                                                    <div class="file-size">{{ $transaction->getFileSize() }}</div>
                                                </div>
                                                <div class="evidence-actions">
                                                    <a href="{{ route('transactions.show-evidence', $transaction->transaction_id) }}" 
                                                       class="btn btn-sm btn-outline-primary" target="_blank">
                                                        <i class="fas fa-eye"></i> Lihat
                                                    </a>
                                                    <a href="{{ $transaction->evidence_file_url }}" 
                                                       class="btn btn-sm btn-outline-success" download>
                                                        <i class="fas fa-download"></i> Download
                                                    </a>
                                                </div>
                                            </div>
                                        @endif
                                    @else
                                        <div class="evidence-error">
                                            <i class="fas fa-exclamation-triangle text-warning"></i>
                                            <p>File bukti tidak ditemukan</p>
                                        </div>
                                    @endif
                                @else
                                    <div class="no-evidence">
                                        <i class="fas fa-info-circle text-muted"></i>
                                        <p>Tidak ada bukti transaksi</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Print Actions -->
                            <div class="print-section">
                                <h5><i class="fas fa-print"></i> Cetak Dokumen</h5>
                                <div class="print-actions">
                                    <a href="{{ route('print.receipt', $transaction->transaction_id) }}" 
                                       class="btn btn-success w-100 mb-2" target="_blank">
                                        <i class="fas fa-receipt"></i> Cetak Bukti Kas
                                    </a>
                                    @if($transaction->transaction_type === 'expense')
                                    <a href="{{ route('print.memo', $transaction->transaction_id) }}" 
                                       class="btn btn-warning w-100" target="_blank">
                                        <i class="fas fa-file-alt"></i> Cetak Nota Dinas
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="detail-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('transactions.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                        </a>
                        
                        <div class="footer-actions">
                            @if(Auth::user()->role === 'admin' || $transaction->created_by === Auth::user()->user_id)
                            <button class="btn btn-outline-danger" 
                                    onclick="confirmDelete('{{ $transaction->transaction_id }}', '{{ $transaction->transaction_number }}')">
                                <i class="fas fa-trash"></i> Hapus Transaksi
                            </button>
                            @endif
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

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Preview Bukti Transaksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" class="img-fluid" alt="Preview">
            </div>
            <div class="modal-footer">
                <a id="modalDownload" href="" class="btn btn-success" download>
                    <i class="fas fa-download"></i> Download
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

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

    .info-section, .amount-section, .creator-section, .evidence-section, .print-section {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .info-section h5, .amount-section h5, .creator-section h5, .evidence-section h5, .print-section h5 {
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

    .amount-display {
        text-align: center;
        padding: 20px;
        background: white;
        border-radius: 8px;
        border: 2px solid var(--pln-blue);
    }

    .amount-number {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 8px;
    }

    .amount-words {
        font-style: italic;
        color: #666;
        font-size: 0.9rem;
    }

    .evidence-thumbnail {
        width: 100%;
        max-height: 200px;
        object-fit: cover;
        border-radius: 8px;
        cursor: pointer;
        margin-bottom: 10px;
    }

    .evidence-file {
        background: white;
        border-radius: 8px;
        padding: 15px;
        text-align: center;
    }

    .file-icon {
        font-size: 3rem;
        margin-bottom: 10px;
    }

    .file-name {
        font-weight: 600;
        margin-bottom: 5px;
        word-break: break-word;
    }

    .file-size {
        color: #666;
        font-size: 0.8rem;
    }

    .evidence-actions {
        display: flex;
        gap: 8px;
        margin-top: 10px;
    }

    .evidence-actions .btn {
        flex: 1;
    }

    .no-evidence, .evidence-error {
        text-align: center;
        padding: 30px;
        color: #666;
    }

    .no-evidence i, .evidence-error i {
        font-size: 2rem;
        margin-bottom: 10px;
        display: block;
    }

    .print-actions .btn {
        margin-bottom: 8px;
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
    }
</style>

<script>
function viewEvidence(imageUrl) {
    const modal = new bootstrap.Modal(document.getElementById('imageModal'));
    document.getElementById('modalImage').src = imageUrl;
    document.getElementById('modalDownload').href = imageUrl;
    modal.show();
}

function confirmDelete(transactionId, transactionNumber) {
    if (confirm(`Apakah Anda yakin ingin menghapus transaksi "${transactionNumber}"?\n\nTindakan ini tidak dapat dibatalkan!`)) {
        const form = document.getElementById('deleteForm');
        form.action = `/transactions/${transactionId}`;
        form.submit();
    }
}
</script>
@endsection