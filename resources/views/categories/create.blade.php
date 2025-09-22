@extends('layouts.app')

@section('title', 'Tambah Kategori - Sistem Kas PLN')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="form-container">
                <div class="form-header">
                    <h3><i class="fas fa-plus-circle"></i> Tambah Kategori Baru</h3>
                    <p class="text-muted">Buat kategori untuk transaksi kas masuk atau kas keluar</p>
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

                <form action="{{ route('categories.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="category_name" class="form-label">
                                    <i class="fas fa-tag"></i> Nama Kategori
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    class="form-control @error('category_name') is-invalid @enderror"
                                    id="category_name"
                                    name="category_name"
                                    value="{{ old('category_name') }}"
                                    placeholder="Contoh: Operasional Kantor"
                                    required>
                                @error('category_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i>
                                    Nama kategori harus unik dan mudah dipahami
                                </small>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="transaction_type" class="form-label">
                                    <i class="fas fa-exchange-alt"></i> Jenis Transaksi
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-control @error('transaction_type') is-invalid @enderror"
                                    id="transaction_type"
                                    name="transaction_type"
                                    required>
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="income" {{ old('transaction_type') == 'income' ? 'selected' : '' }}>
                                        💰 Kas Masuk
                                    </option>
                                    <option value="expense" {{ old('transaction_type') == 'expense' ? 'selected' : '' }}>
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
                        <label for="description" class="form-label">
                            <i class="fas fa-sticky-note"></i> Deskripsi
                        </label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                            id="description"
                            name="description"
                            rows="3"
                            placeholder="Deskripsi lengkap tentang kategori ini (opsional)">{{ old('description') }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">
                            Deskripsi akan membantu pengguna memahami penggunaan kategori ini
                        </small>
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" 
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                <i class="fas fa-toggle-on text-success"></i>
                                <strong>Kategori Aktif</strong>
                            </label>
                            <small class="d-block text-muted">
                                Kategori aktif akan muncul dalam pilihan saat membuat transaksi
                            </small>
                        </div>
                    </div>

                    <!-- Preview Section -->
                    <div class="preview-section" id="previewSection" style="display: none;">
                        <h5><i class="fas fa-eye"></i> Preview Kategori</h5>
                        <div class="category-preview">
                            <div class="preview-card">
                                <span class="preview-badge" id="previewBadge"></span>
                                <div class="preview-content">
                                    <div class="preview-name" id="previewName">Nama Kategori</div>
                                    <div class="preview-id" id="previewId">ID akan di-generate otomatis</div>
                                    <div class="preview-desc" id="previewDesc"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save"></i> Simpan Kategori
                        </button>
                        <a href="{{ route('categories.index') }}" class="btn btn-secondary btn-lg">
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

    .form-check-input:checked {
        background-color: #28a745;
        border-color: #28a745;
    }

    .preview-section {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        border: 2px dashed #dee2e6;
    }

    .preview-section h5 {
        color: var(--pln-blue);
        margin-bottom: 15px;
        font-weight: 600;
    }

    .preview-card {
        background: white;
        border-radius: 8px;
        padding: 15px;
        display: flex;
        align-items: center;
        gap: 15px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .preview-badge {
        padding: 8px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        color: white;
    }

    .preview-badge.income {
        background: linear-gradient(135deg, #28a745, #34ce57);
    }

    .preview-badge.expense {
        background: linear-gradient(135deg, #dc3545, #e85d75);
    }

    .preview-name {
        font-weight: 600;
        color: #333;
        font-size: 1.1rem;
    }

    .preview-id {
        color: #666;
        font-size: 0.9rem;
        font-family: 'Courier New', monospace;
    }

    .preview-desc {
        color: #888;
        font-size: 0.8rem;
        margin-top: 5px;
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

    @media (max-width: 768px) {
        .form-container {
            padding: 20px;
        }
        
        .d-flex.gap-2 {
            flex-direction: column;
            gap: 10px !important;
        }
        
        .btn-lg {
            width: 100%;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const nameInput = document.getElementById('category_name');
        const typeSelect = document.getElementById('transaction_type');
        const descInput = document.getElementById('description');
        const previewSection = document.getElementById('previewSection');
        
        // Update preview in real-time
        function updatePreview() {
            const name = nameInput.value.trim();
            const type = typeSelect.value;
            const desc = descInput.value.trim();
            
            if (name && type) {
                previewSection.style.display = 'block';
                
                // Update preview content
                document.getElementById('previewName').textContent = name;
                document.getElementById('previewDesc').textContent = desc || 'Tidak ada deskripsi';
                
                // Update badge
                const badge = document.getElementById('previewBadge');
                if (type === 'income') {
                    badge.textContent = '💰 Kas Masuk';
                    badge.className = 'preview-badge income';
                    document.getElementById('previewId').textContent = 'ID: KM### (otomatis)';
                } else if (type === 'expense') {
                    badge.textContent = '💸 Kas Keluar';
                    badge.className = 'preview-badge expense';
                    document.getElementById('previewId').textContent = 'ID: KK### (otomatis)';
                }
            } else {
                previewSection.style.display = 'none';
            }
        }
        
        // Event listeners
        nameInput.addEventListener('input', updatePreview);
        typeSelect.addEventListener('change', updatePreview);
        descInput.addEventListener('input', updatePreview);
        
        // Auto-focus on name input
        nameInput.focus();
        
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const name = nameInput.value.trim();
            const type = typeSelect.value;
            
            if (!name) {
                e.preventDefault();
                nameInput.focus();
                alert('Nama kategori harus diisi!');
                return;
            }
            
            if (!type) {
                e.preventDefault();
                typeSelect.focus();
                alert('Jenis transaksi harus dipilih!');
                return;
            }
        });
    });
</script>
@endsection