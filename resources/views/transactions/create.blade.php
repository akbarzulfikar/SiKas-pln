@extends('layouts.app')

@section('title', 'Tambah Transaksi - Sistem Kas PLN')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="form-container">
                <div class="form-header">
                    <h3><i class="fas fa-plus-circle"></i> Tambah Transaksi Kas</h3>
                    <p class="text-muted">Silakan isi form di bawah untuk menambah transaksi kas baru</p>
                </div>

                @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ route('transactions.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="transaction_date" class="form-label">
                                    <i class="fas fa-calendar"></i> Tanggal Transaksi 
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="date"
                                    class="form-control @error('transaction_date') is-invalid @enderror"
                                    id="transaction_date"
                                    name="transaction_date"
                                    value="{{ old('transaction_date', date('Y-m-d')) }}"
                                    required>
                                @error('transaction_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="transaction_type" class="form-label">
                                    <i class="fas fa-exchange-alt"></i> Jenis Transaksi 
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-control @error('transaction_type') is-invalid @enderror"
                                    id="transaction_type"
                                    name="transaction_type"
                                    required>
                                    <option value="">-- Pilih Jenis Transaksi --</option>
                                    <option value="income" {{ (old('transaction_type') == 'income' || request('type') == 'income') ? 'selected' : '' }}>
                                        💰 Kas Masuk
                                    </option>
                                    <option value="expense" {{ (old('transaction_type') == 'expense' || request('type') == 'expense') ? 'selected' : '' }}>
                                        💸 Kas Keluar
                                    </option>
                                </select>
                                @error('transaction_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="category_id" class="form-label">
                            <i class="fas fa-tags"></i> Kategori 
                            <span class="text-danger">*</span>
                        </label>
                        <select class="form-control @error('category_id') is-invalid @enderror"
                            id="category_id"
                            name="category_id"
                            required>
                            <option value="">-- Pilih jenis transaksi terlebih dahulu --</option>
                        </select>
                        @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i>
                            Kategori akan muncul setelah Anda memilih jenis transaksi.
                        </small>
                    </div>

                    <div class="mb-3">
                        <label for="amount" class="form-label">
                            <i class="fas fa-money-bill-wave"></i> Jumlah (Rupiah) 
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text"
                                class="form-control @error('amount') is-invalid @enderror"
                                id="amount"
                                name="amount"
                                value="{{ old('amount') }}"
                                placeholder="0"
                                required>
                        </div>
                        <small class="text-muted">Contoh: 50000 atau 1500000</small>
                        @error('amount')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">
                            <i class="fas fa-sticky-note"></i> Deskripsi
                        </label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                            id="description"
                            name="description"
                            rows="3"
                            placeholder="Keterangan tambahan tentang transaksi ini (opsional)">{{ old('description') }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="evidence_file" class="form-label">
                            <i class="fas fa-paperclip"></i> Upload Bukti Transaksi
                        </label>
                        <input type="file"
                            class="form-control @error('evidence_file') is-invalid @enderror"
                            id="evidence_file"
                            name="evidence_file"
                            accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                        <div class="form-text">
                            <i class="fas fa-info-circle text-info"></i>
                            <strong>Format yang diizinkan:</strong> JPG, PNG, PDF, DOC, DOCX
                            <br>
                            <strong>Ukuran maksimal:</strong> 5MB
                            <br>
                            <strong>Opsional:</strong> Anda dapat menambahkan bukti transaksi nanti
                        </div>
                        @error('evidence_file')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <!-- Preview area -->
                        <div id="file-preview" class="mt-3" style="display: none;">
                            <div class="preview-container">
                                <h6>Preview File:</h6>
                                <div id="preview-content"></div>
                                <button type="button" class="btn btn-sm btn-outline-danger mt-2" id="remove-file">
                                    <i class="fas fa-times"></i> Hapus File
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save"></i> Simpan Transaksi
                        </button>
                        <a href="{{ route('transactions.index') }}" class="btn btn-secondary btn-lg">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .form-container {
        max-width: 100%;
        background: white;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    }

    .form-header {
        text-align: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid #f8f9fa;
    }

    .form-header h3 {
        color: var(--pln-blue);
        margin-bottom: 10px;
        font-weight: 600;
    }

    .form-label {
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
        display: block;
    }

    .form-label i {
        color: var(--pln-blue);
        margin-right: 5px;
    }

    .form-control, .form-select {
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 12px;
        font-size: 14px;
        transition: border-color 0.3s ease;
    }

    .form-control:focus, .form-select:focus {
        outline: none;
        border-color: var(--pln-blue);
        box-shadow: 0 0 0 0.2rem rgba(0, 61, 122, 0.25);
    }

    .input-group-text {
        background: var(--pln-blue);
        color: white;
        border: 2px solid var(--pln-blue);
        font-weight: 600;
    }

    .preview-container {
        background: #f8f9fa;
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        padding: 15px;
        text-align: center;
    }

    .preview-image {
        max-width: 200px;
        max-height: 200px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .preview-file {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 20px;
        background: white;
        border-radius: 8px;
        border: 1px solid #dee2e6;
    }

    .preview-file i {
        font-size: 2rem;
        color: var(--pln-blue);
    }

    .btn-lg {
        padding: 12px 30px;
        font-weight: 600;
    }

    .alert {
        border-radius: 8px;
        border: none;
        margin-bottom: 20px;
    }
</style>

<script>
    // Data kategori untuk JavaScript
    window.categoryData = {
        incomeCategories: @json($incomeCategories),
        expenseCategories: @json($expenseCategories),
        oldCategoryId: "{{ old('category_id') }}"
    };

    // Format amount input
    document.getElementById('amount').addEventListener('input', function(e) {
        let value = e.target.value.replace(/[^0-9]/g, '');
        e.target.value = value;
    });

    // Dynamic category loading
    document.getElementById('transaction_type').addEventListener('change', function(e) {
        const type = e.target.value;
        const categorySelect = document.getElementById('category_id');

        // Clear existing options
        categorySelect.innerHTML = '<option value="">-- Loading... --</option>';

        if (type === '') {
            categorySelect.innerHTML = '<option value="">-- Pilih jenis transaksi terlebih dahulu --</option>';
            return;
        }

        let categories = [];
        if (type === 'income') {
            categories = window.categoryData.incomeCategories;
        } else if (type === 'expense') {
            categories = window.categoryData.expenseCategories;
        }

        // Populate select options
        categorySelect.innerHTML = '<option value="">-- Pilih Kategori --</option>';

        if (!categories || categories.length === 0) {
            categorySelect.innerHTML += '<option value="" disabled>Belum ada kategori untuk jenis ini</option>';
        } else {
            const oldCategoryId = window.categoryData.oldCategoryId;
            categories.forEach(function(category) {
                const selected = oldCategoryId == category.category_id ? ' selected' : '';
                categorySelect.innerHTML += `<option value="${category.category_id}"${selected}>${category.category_name}</option>`;
            });
        }
    });

    // File upload preview
    document.getElementById('evidence_file').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('file-preview');
        const previewContent = document.getElementById('preview-content');

        if (file) {
            // Check file size (5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('Ukuran file terlalu besar! Maksimal 5MB.');
                e.target.value = '';
                preview.style.display = 'none';
                return;
            }

            preview.style.display = 'block';

            // Show preview based on file type
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewContent.innerHTML = `<img src="${e.target.result}" class="preview-image" alt="Preview">`;
                };
                reader.readAsDataURL(file);
            } else {
                const fileName = file.name;
                const fileSize = (file.size / 1024 / 1024).toFixed(2);
                let icon = 'fas fa-file';
                
                if (file.type.includes('pdf')) icon = 'fas fa-file-pdf text-danger';
                else if (file.type.includes('word')) icon = 'fas fa-file-word text-primary';

                previewContent.innerHTML = `
                    <div class="preview-file">
                        <i class="${icon}"></i>
                        <div>
                            <div class="fw-bold">${fileName}</div>
                            <small class="text-muted">${fileSize} MB</small>
                        </div>
                    </div>
                `;
            }
        } else {
            preview.style.display = 'none';
        }
    });

    // Remove file
    document.getElementById('remove-file').addEventListener('click', function() {
        document.getElementById('evidence_file').value = '';
        document.getElementById('file-preview').style.display = 'none';
    });

    // Trigger change event on page load to handle old values
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.getElementById('transaction_type');
        if (typeSelect.value) {
            typeSelect.dispatchEvent(new Event('change'));
        }
    });
</script>
@endsection