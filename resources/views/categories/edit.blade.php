@extends('layouts.app')

@section('title', 'Edit Kategori - Sistem Kas PLN')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="form-container">
                <div class="form-header">
                    <h3><i class="fas fa-edit"></i> Edit Kategori</h3>
                    <p class="text-muted">Ubah informasi kategori "{{ $category->category_name }}"</p>
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

                <form action="{{ route('categories.update', $category->category_id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Read-only info -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="fas fa-hashtag"></i> ID Kategori
                            </label>
                            <input type="text" class="form-control" value="{{ $category->category_id }}" readonly>
                            <small class="text-muted">ID kategori tidak dapat diubah</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="fas fa-exchange-alt"></i> Jenis Transaksi
                            </label>
                            <input type="text" class="form-control" 
                                   value="{{ $category->type_display }}" readonly>
                            <small class="text-muted">Jenis transaksi tidak dapat diubah</small>
                        </div>
                    </div>

                    <!-- Editable fields -->
                    <div class="mb-3">
                        <label for="category_name" class="form-label">
                            <i class="fas fa-tag"></i> Nama Kategori
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                            class="form-control @error('category_name') is-invalid @enderror"
                            id="category_name"
                            name="category_name"
                            value="{{ old('category_name', $category->category_name) }}"
                            placeholder="Contoh: Operasional Kantor"
                            required>
                        @error('category_name')
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
                            placeholder="Deskripsi lengkap tentang kategori ini (opsional)">{{ old('description', $category->description) }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" 
                                   {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                <i class="fas fa-toggle-on text-success"></i>
                                <strong>Kategori Aktif</strong>
                            </label>
                            <small class="d-block text-muted">
                                Kategori aktif akan muncul dalam pilihan saat membuat transaksi
                            </small>
                        </div>
                    </div>

                    <!-- Usage Warning -->
                    @if($category->transactions_count > 0)
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Perhatian:</strong> Kategori ini digunakan dalam {{ $category->transactions_count }} transaksi.
                        Perubahan nama akan mempengaruhi tampilan transaksi yang sudah ada.
                    </div>
                    @endif

                    <!-- Preview Section -->
                    <div class="preview-section">
                        <h5><i class="fas fa-eye"></i> Preview Kategori</h5>
                        <div class="category-preview">
                            <div class="preview-card">
                                <span class="preview-badge {{ $category->transaction_type }}">
                                    <i class="{{ $category->icon }}"></i>
                                    {{ $category->type_display }}
                                </span>
                                <div class="preview-content">
                                    <div class="preview-name" id="previewName">{{ $category->category_name }}</div>
                                    <div class="preview-id">ID: {{ $category->category_id }}</div>
                                    <div class="preview-desc" id="previewDesc">{{ $category->description ?: 'Tidak ada deskripsi' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save"></i> Update Kategori
                        </button>
                        <a href="{{ route('categories.show', $category->category_id) }}" class="btn btn-secondary btn-lg">
                            <i class="fas fa-arrow-left"></i> Batal
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

    .form-control[readonly] {
        background-color: #f8f9fa;
        border-color: #e9ecef;
        color: #6c757d;
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
        const descInput = document.getElementById('description');
        
        // Update preview in real-time
        function updatePreview() {
            const name = nameInput.value.trim();
            const desc = descInput.value.trim();
            
            document.getElementById('previewName').textContent = name || '{{ $category->category_name }}';
            document.getElementById('previewDesc').textContent = desc || 'Tidak ada deskripsi';
        }
        
        // Event listeners
        nameInput.addEventListener('input', updatePreview);
        descInput.addEventListener('input', updatePreview);
        
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const name = nameInput.value.trim();
            
            if (!name) {
                e.preventDefault();
                nameInput.focus();
                alert('Nama kategori harus diisi!');
                return;
            }
        });
    });
</script>
@endsection