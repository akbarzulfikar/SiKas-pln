@extends('layouts.app')

@section('title', 'Tambah Unit - SIKAS PLN')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="form-container">
                <div class="form-header">
                    <h3><i class="fas fa-plus-circle"></i> Tambah Unit Baru</h3>
                    <p class="text-muted">Tambahkan unit organisasi PLN</p>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('units.index') }}">Manajemen Unit</a></li>
                            <li class="breadcrumb-item active">Tambah Unit</li>
                        </ol>
                    </nav>
                </div>

                @if($errors->any())
                <div class="alert alert-danger">
                    <h6><i class="fas fa-exclamation-triangle"></i> Terdapat Kesalahan:</h6>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ route('units.store') }}" method="POST" id="unitForm">
                    @csrf

                    <!-- Basic Information -->
                    <div class="section-header">
                        <h5><i class="fas fa-building"></i> Informasi Dasar</h5>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="unit_id" class="form-label">
                                <i class="fas fa-hashtag"></i> ID Unit <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('unit_id') is-invalid @enderror" 
                                   id="unit_id" 
                                   name="unit_id" 
                                   value="{{ old('unit_id') }}" 
                                   placeholder="Contoh: up3_lgs, ulp_xyz"
                                   pattern="[a-zA-Z0-9_]+"
                                   required>
                            @error('unit_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i>
                                Format: huruf kecil, angka, dan underscore. Contoh: ulp_medan
                            </small>
                        </div>
                        <div class="col-md-6">
                            <label for="unit_type" class="form-label">
                                <i class="fas fa-layer-group"></i> Tipe Unit <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('unit_type') is-invalid @enderror" 
                                    id="unit_type" 
                                    name="unit_type" 
                                    required>
                                <option value="">-- Pilih Tipe Unit --</option>
                                <option value="UP3" {{ old('unit_type') === 'UP3' ? 'selected' : '' }}>
                                    UP3 (Unit Pelaksana Pelayanan Pelanggan)
                                </option>
                                <option value="ULP" {{ old('unit_type') === 'ULP' ? 'selected' : '' }}>
                                    ULP (Unit Layanan Pelanggan)
                                </option>
                            </select>
                            @error('unit_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="unit_name" class="form-label">
                            <i class="fas fa-building"></i> Nama Unit <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('unit_name') is-invalid @enderror" 
                               id="unit_name" 
                               name="unit_name" 
                               value="{{ old('unit_name') }}" 
                               placeholder="Contoh: UP3 Langsa, ULP Medan Kota"
                               required>
                        @error('unit_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Contact Information -->
                    <div class="section-header">
                        <h5><i class="fas fa-address-book"></i> Informasi Kontak</h5>
                        <small class="text-muted">Opsional</small>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">
                            <i class="fas fa-map-marker-alt"></i> Alamat
                        </label>
                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                  id="address" 
                                  name="address" 
                                  rows="3"
                                  placeholder="Alamat lengkap unit...">{{ old('address') }}</textarea>
                        @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="phone" class="form-label">
                                <i class="fas fa-phone"></i> Nomor Telepon
                            </label>
                            <input type="tel" 
                                   class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" 
                                   name="phone" 
                                   value="{{ old('phone') }}" 
                                   placeholder="Contoh: 0641-234567">
                            @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope"></i> Email
                            </label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   placeholder="Contoh: unit@pln.co.id">
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="section-header">
                        <h5><i class="fas fa-toggle-on"></i> Status Unit</h5>
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input type="checkbox" 
                                   class="form-check-input" 
                                   id="is_active" 
                                   name="is_active" 
                                   value="1" 
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                <i class="fas fa-check-circle text-success"></i>
                                <strong>Unit Aktif</strong>
                            </label>
                            <small class="d-block text-muted">
                                Unit aktif dapat digunakan dalam sistem
                            </small>
                        </div>
                    </div>

                    <!-- Preview Section -->
                    <div class="preview-section" id="previewSection" style="display: none;">
                        <h5><i class="fas fa-eye"></i> Preview Unit</h5>
                        <div class="unit-preview">
                            <div class="preview-card">
                                <div class="preview-icon">
                                    <i class="fas fa-building" id="previewIcon"></i>
                                </div>
                                <div class="preview-content">
                                    <div class="preview-name" id="previewName">Nama Unit</div>
                                    <div class="preview-id" id="previewId">ID: unit_id</div>
                                    <div class="preview-type" id="previewType">
                                        <span class="badge bg-secondary">Tipe Unit</span>
                                    </div>
                                    <div class="preview-contact" id="previewContact"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="d-flex gap-2 mt-4 justify-content-center flex-wrap">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save"></i> Simpan Unit
                        </button>
                        <a href="{{ route('units.index') }}" class="btn btn-secondary btn-lg">
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
    color: var(--pln-blue, #007bff);
    margin-bottom: 10px;
    font-weight: 600;
}

.section-header {
    background: #f8f9fa;
    padding: 15px 20px;
    border-radius: 8px;
    margin: 25px 0 15px 0;
    border-left: 4px solid var(--pln-blue, #007bff);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.section-header h5 {
    margin: 0;
    color: var(--pln-blue, #007bff);
    font-weight: 600;
}

.form-label {
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
    display: block;
}

.form-label i {
    color: var(--pln-blue, #007bff);
    margin-right: 8px;
}

.form-control, .form-select {
    border-radius: 8px;
    border: 2px solid #e9ecef;
    padding: 10px 15px;
    transition: border-color 0.3s ease;
}

.form-control:focus, .form-select:focus {
    border-color: var(--pln-blue, #007bff);
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.btn-lg {
    padding: 12px 30px;
    font-weight: 600;
    border-radius: 8px;
}

.preview-section {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin-top: 20px;
    border: 2px dashed #dee2e6;
    transition: all 0.3s ease;
}

.preview-section.show {
    border-color: var(--pln-blue, #007bff);
    background: #e7f3ff;
}

.preview-section h5 {
    color: var(--pln-blue, #007bff);
    margin-bottom: 15px;
}

.unit-preview {
    background: white;
    border-radius: 8px;
    padding: 15px;
    border: 1px solid #e9ecef;
}

.preview-card {
    display: flex;
    align-items: center;
    gap: 15px;
}

.preview-icon {
    width: 50px;
    height: 50px;
    border-radius: 8px;
    background: linear-gradient(45deg, #007bff, #0056b3);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    transition: all 0.3s ease;
}

.preview-content {
    flex: 1;
}

.preview-name {
    font-weight: 600;
    font-size: 1.1rem;
    color: #333;
    margin-bottom: 3px;
}

.preview-id {
    color: #6c757d;
    font-size: 0.9rem;
    margin-bottom: 5px;
}

.preview-type {
    margin-bottom: 8px;
}

.preview-contact {
    font-size: 0.85rem;
    color: #6c757d;
}

.breadcrumb {
    background: none;
    padding: 0;
    margin-bottom: 10px;
}

.breadcrumb a {
    color: var(--pln-blue, #007bff);
    text-decoration: none;
}

.breadcrumb a:hover {
    text-decoration: underline;
}

.breadcrumb-item.active {
    color: #6c757d;
}

.alert {
    border-radius: 8px;
    border: none;
    padding: 15px 20px;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
}

.is-invalid {
    border-color: #dc3545 !important;
}

.invalid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875em;
    color: #dc3545;
}

.form-check-input:checked {
    background-color: #28a745;
    border-color: #28a745;
}

/* Responsive */
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
    
    .preview-card {
        flex-direction: column;
        text-align: center;
    }
    
    .section-header {
        flex-direction: column;
        text-align: center;
        gap: 5px;
    }
}

/* Animation */
@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.preview-section.show {
    animation: slideInUp 0.3s ease;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const unitIdInput = document.getElementById('unit_id');
    const unitNameInput = document.getElementById('unit_name');
    const unitTypeSelect = document.getElementById('unit_type');
    const phoneInput = document.getElementById('phone');
    const emailInput = document.getElementById('email');
    const addressInput = document.getElementById('address');
    const previewSection = document.getElementById('previewSection');
    
    // Update preview when inputs change
    [unitIdInput, unitNameInput, unitTypeSelect, phoneInput, emailInput, addressInput].forEach(input => {
        input.addEventListener('change', updatePreview);
        input.addEventListener('input', updatePreview);
    });
    
    function updatePreview() {
        const unitId = unitIdInput.value.trim();
        const unitName = unitNameInput.value.trim();
        const unitType = unitTypeSelect.value;
        const phone = phoneInput.value.trim();
        const email = emailInput.value.trim();
        const address = addressInput.value.trim();
        
        if (unitId || unitName) {
            previewSection.style.display = 'block';
            previewSection.classList.add('show');
            
            // Update icon based on type
            const icon = document.getElementById('previewIcon');
            if (unitType === 'UP3') {
                icon.className = 'fas fa-home';
                icon.parentElement.style.background = 'linear-gradient(45deg, #28a745, #1e7e34)';
            } else if (unitType === 'ULP') {
                icon.className = 'fas fa-building';
                icon.parentElement.style.background = 'linear-gradient(45deg, #17a2b8, #117a8b)';
            } else {
                icon.className = 'fas fa-building';
                icon.parentElement.style.background = 'linear-gradient(45deg, #007bff, #0056b3)';
            }
            
            // Update name and ID
            document.getElementById('previewName').textContent = unitName || 'Nama Unit';
            document.getElementById('previewId').textContent = 'ID: ' + (unitId || 'unit_id');
            
            // Update type badge
            const typeBadge = document.getElementById('previewType');
            if (unitType) {
                const typeColor = unitType === 'UP3' ? 'success' : 'info';
                const typeName = unitType === 'UP3' ? 'UP3' : 'ULP';
                typeBadge.innerHTML = `<span class="badge bg-${typeColor}">${typeName}</span>`;
            } else {
                typeBadge.innerHTML = '<span class="badge bg-secondary">Tipe Unit</span>';
            }
            
            // Update contact info
            const contactDiv = document.getElementById('previewContact');
            let contactInfo = '';
            if (phone) contactInfo += `<i class="fas fa-phone"></i> ${phone}<br>`;
            if (email) contactInfo += `<i class="fas fa-envelope"></i> ${email}<br>`;
            if (address) contactInfo += `<i class="fas fa-map-marker-alt"></i> ${address.substring(0, 50)}${address.length > 50 ? '...' : ''}`;
            contactDiv.innerHTML = contactInfo;
            
        } else {
            previewSection.style.display = 'none';
            previewSection.classList.remove('show');
        }
    }
    
    // Auto-generate unit ID suggestion based on name and type
    unitNameInput.addEventListener('input', function() {
        if (!unitIdInput.value.trim()) {
            const name = this.value.trim();
            const type = unitTypeSelect.value;
            
            if (name && type) {
                // Simple ID generation
                let suggestion = '';
                if (type === 'UP3') {
                    suggestion = 'up3_' + name.toLowerCase()
                        .replace(/up3\s*/gi, '')
                        .replace(/\s+/g, '_')
                        .replace(/[^a-z0-9_]/g, '')
                        .substring(0, 6);
                } else if (type === 'ULP') {
                    suggestion = 'ulp_' + name.toLowerCase()
                        .replace(/ulp\s*/gi, '')
                        .replace(/\s+/g, '_')
                        .replace(/[^a-z0-9_]/g, '')
                        .substring(0, 6);
                }
                
                if (suggestion) {
                    unitIdInput.placeholder = `Saran: ${suggestion}`;
                }
            }
        }
    });
    
    // Unit ID formatting
    unitIdInput.addEventListener('input', function() {
        // Convert to lowercase and replace spaces with underscores
        this.value = this.value.toLowerCase().replace(/\s+/g, '_');
    });
    
    // Form validation
    document.getElementById('unitForm').addEventListener('submit', function(e) {
        const unitId = unitIdInput.value.trim();
        const unitName = unitNameInput.value.trim();
        const unitType = unitTypeSelect.value;
        
        if (!unitId || !unitName || !unitType) {
            e.preventDefault();
            alert('Mohon lengkapi semua field yang wajib diisi!');
            
            // Focus on first empty required field
            if (!unitId) unitIdInput.focus();
            else if (!unitName) unitNameInput.focus();
            else if (!unitType) unitTypeSelect.focus();
            
            return;
        }
        
        // Validate unit ID format
        if (!/^[a-zA-Z0-9_]+$/.test(unitId)) {
            e.preventDefault();
            alert('ID Unit hanya boleh berisi huruf, angka, dan underscore!');
            unitIdInput.focus();
            return;
        }
        
        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
        submitBtn.disabled = true;
    });
});
</script>
@endsection