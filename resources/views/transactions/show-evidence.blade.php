@extends('layouts.app')

@section('title', 'Bukti Transaksi - Sistem Kas PLN')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="evidence-container">
                <div class="evidence-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3><i class="fas fa-file-alt"></i> Bukti Transaksi</h3>
                            <p class="text-muted mb-0">{{ $transaction->transaction_number }}</p>
                        </div>
                        @if($transaction->evidence_file && $transaction->evidenceFileExists())
                        <a href="{{ $transaction->evidence_file_url }}"
                            class="btn btn-success"
                            download="{{ $transaction->evidence_file }}"
                            title="Download File">
                            <i class="fas fa-download"></i> Download
                        </a>
                        @endif
                    </div>
                </div>

                <div class="evidence-body">
                    <!-- Transaction Info -->
                    <div class="row mb-4">
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
                                <label>Jumlah:</label>
                                <span class="fw-bold text-primary">
                                    Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                        @if($transaction->description)
                        <div class="col-12">
                            <div class="info-item">
                                <label>Deskripsi:</label>
                                <span>{{ $transaction->description }}</span>
                            </div>
                        </div>
                        @endif
                        @if(Auth::user()->role === 'admin')
                        <div class="col-md-6">
                            <div class="info-item">
                                <label>Unit:</label>
                                <span class="badge bg-{{ $transaction->unit->badge_color }}">
                                    {{ $transaction->unit->unit_name }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label>Dibuat oleh:</label>
                                <span>{{ $transaction->creator->name }}</span>
                            </div>
                        </div>
                        @endif
                    </div>

                    <hr>

                    <!-- Evidence File Display -->
                    <div class="text-center">
                        <h5 class="mb-3">
                            <i class="fas fa-paperclip"></i> File Bukti Transaksi
                            @if($transaction->evidence_file && $transaction->evidenceFileExists())
                            <span class="badge bg-info ms-2">{{ $transaction->getFileSize() }}</span>
                            @endif
                        </h5>

                        @if($transaction->evidence_file)
                            @if($transaction->evidenceFileExists())
                                @if($transaction->isImageFile())
                                    <!-- Display Image -->
                                    <div class="image-container mb-3">
                                        <img src="{{ $transaction->evidence_file_url }}"
                                            class="evidence-image"
                                            alt="Bukti Transaksi"
                                            onclick="openImageModal(this.src)">

                                        <div class="image-actions mt-3">
                                            <button class="btn btn-primary me-2" onclick="openImageModal('{{ $transaction->evidence_file_url }}')">
                                                <i class="fas fa-search-plus"></i> Perbesar
                                            </button>
                                            <a href="{{ $transaction->evidence_file_url }}"
                                                class="btn btn-outline-primary me-2"
                                                target="_blank">
                                                <i class="fas fa-external-link-alt"></i> Buka Tab Baru
                                            </a>
                                            <a href="{{ $transaction->evidence_file_url }}"
                                                class="btn btn-success"
                                                download="{{ $transaction->evidence_file }}">
                                                <i class="fas fa-download"></i> Download
                                            </a>
                                        </div>
                                    </div>
                                @else
                                    <!-- Display Document Preview -->
                                    <div class="document-preview">
                                        <div class="row align-items-center">
                                            <div class="col-md-3 text-center">
                                                @php
                                                $extension = pathinfo($transaction->evidence_file, PATHINFO_EXTENSION);
                                                @endphp
                                                @if(in_array(strtolower($extension), ['pdf']))
                                                    <i class="fas fa-file-pdf fa-5x text-danger"></i>
                                                @elseif(in_array(strtolower($extension), ['doc', 'docx']))
                                                    <i class="fas fa-file-word fa-5x text-primary"></i>
                                                @else
                                                    <i class="fas fa-file fa-5x text-secondary"></i>
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <h5 class="mb-2">{{ $transaction->evidence_file }}</h5>
                                                <div class="file-info">
                                                    <p class="mb-1">
                                                        <strong>Format:</strong> {{ strtoupper($extension) }}
                                                    </p>
                                                    <p class="mb-1">
                                                        <strong>Ukuran:</strong> {{ $transaction->getFileSize() }}
                                                    </p>
                                                    <p class="mb-0 text-muted">
                                                        <small>Diupload: {{ $transaction->created_at->format('d/m/Y H:i') }}</small>
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <div class="d-grid gap-2">
                                                    <a href="{{ $transaction->evidence_file_url }}"
                                                        class="btn btn-primary"
                                                        target="_blank">
                                                        <i class="fas fa-eye"></i> Lihat File
                                                    </a>
                                                    <a href="{{ $transaction->evidence_file_url }}"
                                                        class="btn btn-success"
                                                        download="{{ $transaction->evidence_file }}">
                                                        <i class="fas fa-download"></i> Download
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @else
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                                    <h5>File Tidak Ditemukan</h5>
                                    <p class="mb-0">File bukti tidak ditemukan di server. File mungkin telah dipindahkan atau dihapus.</p>
                                </div>
                            @endif
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-info-circle fa-2x mb-2"></i>
                                <h5>Tidak Ada File Bukti</h5>
                                <p class="mb-0">Transaksi ini tidak memiliki file bukti yang dilampirkan.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="evidence-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('transactions.show', $transaction->transaction_id) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali ke Detail
                        </a>

                        <div class="action-buttons">
                            @if($transaction->evidence_file && $transaction->evidenceFileExists())
                            <a href="{{ $transaction->evidence_file_url }}"
                                class="btn btn-outline-info me-2"
                                target="_blank">
                                <i class="fas fa-external-link-alt"></i> Buka File
                            </a>
                            @endif
                            
                            <!-- Print Options Dropdown -->
                            <div class="btn-group me-2">
                                <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown">
                                    <i class="fas fa-print"></i> Cetak Bukti
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('print.stream.receipt', $transaction->transaction_id) }}" target="_blank">
                                            <i class="fas fa-eye"></i> Lihat di Browser
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('print.receipt', $transaction->transaction_id) }}" target="_blank">
                                            <i class="fas fa-download"></i> Download PDF
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('print.preview.receipt', $transaction->transaction_id) }}" target="_blank">
                                            <i class="fas fa-code"></i> Preview HTML
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            @if($transaction->transaction_type === 'expense')
                            <!-- Memo Options Dropdown -->
                            <div class="btn-group">
                                <button type="button" class="btn btn-warning dropdown-toggle" data-bs-toggle="dropdown">
                                    <i class="fas fa-file-alt"></i> Cetak Nota
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('print.stream.memo', $transaction->transaction_id) }}" target="_blank">
                                            <i class="fas fa-eye"></i> Lihat di Browser
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('print.memo', $transaction->transaction_id) }}" target="_blank">
                                            <i class="fas fa-download"></i> Download PDF
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('print.preview.memo', $transaction->transaction_id) }}" target="_blank">
                                            <i class="fas fa-code"></i> Preview HTML
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
    .evidence-container {
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .evidence-header {
        background: linear-gradient(135deg, var(--pln-blue), var(--pln-blue-dark));
        color: white;
        padding: 25px 30px;
    }

    .evidence-header h3 {
        margin: 0;
        font-weight: 600;
    }

    .evidence-body {
        padding: 30px;
    }

    .evidence-footer {
        background-color: #f8f9fa;
        padding: 20px 30px;
        border-top: 1px solid #dee2e6;
    }

    .info-item {
        margin-bottom: 15px;
    }

    .info-item label {
        font-weight: 600;
        color: #495057;
        display: block;
        margin-bottom: 5px;
        font-size: 0.9rem;
    }

    .info-item span {
        color: #333;
        font-size: 1rem;
    }

    .evidence-image {
        max-width: 100%;
        max-height: 500px;
        border-radius: 12px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        cursor: pointer;
        transition: transform 0.3s ease;
    }

    .evidence-image:hover {
        transform: scale(1.02);
    }

    .image-container {
        display: inline-block;
        max-width: 100%;
    }

    .document-preview {
        background: #f8f9fa;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 30px;
        margin: 20px 0;
    }

    .file-info p {
        margin-bottom: 8px;
        color: #6c757d;
    }

    .alert {
        border-radius: 12px;
        border: none;
        padding: 30px;
        text-align: center;
    }

    .alert i {
        display: block;
        margin-bottom: 15px;
    }

    .btn {
        border-radius: 8px;
        font-weight: 500;
        padding: 10px 20px;
    }

    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .modal-content {
        border-radius: 15px;
        border: none;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    }

    .modal-header {
        background: linear-gradient(135deg, var(--pln-blue), var(--pln-blue-dark));
        color: white;
        border-radius: 15px 15px 0 0;
        border: none;
    }

    #modalImage {
        max-height: 70vh;
        border-radius: 8px;
    }

    /* Dropdown menu styles */
    .dropdown-menu {
        border-radius: 8px;
        border: none;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        margin-top: 5px;
    }

    .dropdown-item {
        padding: 8px 16px;
        transition: all 0.2s ease;
    }

    .dropdown-item:hover {
        background-color: #f8f9fa;
        transform: translateX(5px);
    }

    .dropdown-item i {
        margin-right: 8px;
        width: 16px;
    }

    .dropdown-divider {
        margin: 8px 0;
    }

    @media (max-width: 768px) {
        .evidence-header,
        .evidence-body,
        .evidence-footer {
            padding: 20px;
        }

        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 8px;
            width: 100%;
        }

        .evidence-footer {
            flex-direction: column;
            gap: 15px;
        }

        .evidence-footer .d-flex {
            flex-direction: column;
        }

        .btn-group {
            width: 100%;
        }

        .btn-group .btn {
            width: 100%;
        }
    }
</style>

<script>
    function openImageModal(imageUrl) {
        const modal = new bootstrap.Modal(document.getElementById('imageModal'));
        document.getElementById('modalImage').src = imageUrl;
        document.getElementById('modalDownload').href = imageUrl;
        modal.show();
    }

    // Handle image load errors
    document.addEventListener('DOMContentLoaded', function() {
        const images = document.querySelectorAll('.evidence-image');
        images.forEach(function(img) {
            img.onerror = function() {
                this.parentElement.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                    <h5>Gagal Memuat Gambar</h5>
                    <p class="mb-0">Gambar tidak dapat ditampilkan. File mungkin rusak atau format tidak didukung.</p>
                </div>
            `;
            };
        });
    });
</script>
@endsection