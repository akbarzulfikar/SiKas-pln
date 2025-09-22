@extends('layouts.app')

@section('title', 'Edit User - Sistem Kas PLN')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="form-container">
                <div class="form-header">
                    <h3><i class="fas fa-user-edit"></i> Edit User</h3>
                    <p class="text-muted">Ubah informasi user "{{ $user->name }}"</p>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Manajemen User</a></li>
                            <li class="breadcrumb-item active">Edit User</li>
                        </ol>
                    </nav>
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

                <form action="{{ route('users.update', $user->user_id) }}" method="POST" id="userForm">
                    @csrf
                    @method('PUT')

                    <!-- Read-only info -->
                    <div class="readonly-section">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="fas fa-hashtag"></i> ID User
                                </label>
                                <input type="text" class="form-control" value="{{ $user->user_id }}" readonly>
                                <small class="text-muted">ID user tidak dapat diubah</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="fas fa-calendar"></i> Bergabung Sejak
                                </label>
                                <input type="text" class="form-control" value="{{ $user->created_at->format('d F Y') }}" readonly>
                                <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    </div>

                    <!-- Editable Information Section -->
                    <div class="section-header">
                        <h5><i class="fas fa-user"></i> Informasi Dasar</h5>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">
                                <i class="fas fa-user"></i> Nama Lengkap <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $user->name) }}" 
                                   placeholder="Masukkan nama lengkap..."
                                   required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="username" class="form-label">
                                <i class="fas fa-at"></i> Username <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('username') is-invalid @enderror" 
                                   id="username" 
                                   name="username" 
                                   value="{{ old('username', $user->username) }}" 
                                   placeholder="Masukkan username..."
                                   required>
                            @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Username harus unik</small>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope"></i> Email <span class="text-danger">*</span>
                            </label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $user->email) }}" 
                                   placeholder="Masukkan email..."
                                   required>
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="role" class="form-label">
                                <i class="fas fa-shield-alt"></i> Role <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('role') is-invalid @enderror" 
                                    id="role" 
                                    name="role" 
                                    required>
                                <option value="">-- Pilih Role --</option>
                                <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>
                                    Administrator
                                </option>
                                <option value="user" {{ old('role', $user->role) === 'user' ? 'selected' : '' }}>
                                    User
                                </option>
                            </select>
                            @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Unit & Job Information -->
                    <div class="section-header">
                        <h5><i class="fas fa-building"></i> Informasi Unit & Jabatan</h5>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="unit_id" class="form-label">
                                <i class="fas fa-building"></i> Unit <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('unit_id') is-invalid @enderror" 
                                    id="unit_id" 
                                    name="unit_id" 
                                    required>
                                <option value="">-- Pilih Unit --</option>
                                @foreach($units as $unit)
                                <option value="{{ $unit->unit_id }}" {{ old('unit_id', $user->unit_id) === $unit->unit_id ? 'selected' : '' }}>
                                    {{ $unit->unit_name }} ({{ $unit->unit_type }})
                                </option>
                                @endforeach
                            </select>
                            @error('unit_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="nip" class="form-label">
                                <i class="fas fa-id-card"></i> NIP
                            </label>
                            <input type="text" 
                                   class="form-control @error('nip') is-invalid @enderror" 
                                   id="nip" 
                                   name="nip" 
                                   value="{{ old('nip', $user->nip) }}" 
                                   placeholder="Masukkan NIP (opsional)...">
                            @error('nip')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="position" class="form-label">
                                <i class="fas fa-briefcase"></i> Jabatan
                            </label>
                            <input type="text" 
                                   class="form-control @error('position') is-invalid @enderror" 
                                   id="position" 
                                   name="position" 
                                   value="{{ old('position', $user->position) }}" 
                                   placeholder="Masukkan jabatan (opsional)...">
                            @error('position')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-toggle-on"></i> Status User
                                </label>
                                <div class="form-check form-switch">
                                    <input type="checkbox" 
                                           class="form-check-input" 
                                           id="is_active" 
                                           name="is_active" 
                                           value="1" 
                                           {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        <i class="fas fa-check-circle text-success"></i>
                                        <strong>User Aktif</strong>
                                    </label>
                                    <small class="d-block text-muted">
                                        User aktif dapat login dan mengakses sistem
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Activity Statistics (Optional) -->
                    @if($user->transactions_count > 0 || $user->categories_count > 0)
                    <div class="section-header">
                        <h5><i class="fas fa-chart-bar"></i> Statistik Aktivitas</h5>
                        <small class="text-muted">Read-only data</small>
                    </div>

                    <div class="activity-info">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="info-card">
                                    <i class="fas fa-exchange-alt text-primary"></i>
                                    <div>
                                        <div class="fw-bold">{{ $user->transactions_count ?? 0 }} transaksi</div>
                                        <small class="text-muted">Sejak bergabung</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-card">
                                    <i class="fas fa-tags text-success"></i>
                                    <div>
                                        <div class="fw-bold">{{ $user->categories_count ?? 0 }} kategori</div>
                                        <small class="text-muted">Dibuat user ini</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-card">
                                    <i class="fas fa-calendar text-info"></i>
                                    <div>
                                        <div class="fw-bold">{{ $user->last_login ? $user->last_login->diffForHumans() : 'Belum Login' }}</div>
                                        <small class="text-muted">Login terakhir</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Preview Section -->
                    <div class="preview-section">
                        <h5><i class="fas fa-eye"></i> Preview User</h5>
                        <div class="user-preview">
                            <div class="preview-card">
                                <div class="preview-avatar">
                                    <div class="avatar-circle" id="previewAvatar">
                                        {{ $user->initials }}
                                    </div>
                                </div>
                                <div class="preview-content">
                                    <div class="preview-name" id="previewName">{{ $user->name }}</div>
                                    <div class="preview-username" id="previewUsername">@{{ $user->username }}</div>
                                    <div class="preview-email" id="previewEmail">{{ $user->email }}</div>
                                    <div class="preview-role" id="previewRole">
                                        <span class="badge bg-{{ $user->role_badge_color }}">{{ $user->role_display_name }}</span>
                                    </div>
                                    <div class="preview-unit" id="previewUnit">
                                        <span class="badge bg-info">{{ $user->unit->unit_name }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TOMBOL ACTION - PERBAIKAN UTAMA DI SINI -->
                    <div class="d-flex gap-2 mt-4 justify-content-center flex-wrap">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save"></i> Update User
                        </button>
                        
                        <!-- TOMBOL BATAL - KEMBALI KE USERS INDEX -->
                        <a href="{{ route('users.index') }}" class="btn btn-secondary btn-lg">
                            <i class="fas fa-arrow-left"></i> Batal
                        </a>
                        
                        <!-- TOMBOL GANTI PASSWORD -->
                        <a href="{{ route('users.change-password', $user->user_id) }}" class="btn btn-warning btn-lg">
                            <i class="fas fa-key"></i> Ganti Password
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

    .readonly-section {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 25px;
        border: 1px solid #e9ecef;
    }

    .readonly-section .form-control {
        background-color: #f8f9fa;
        border-color: #e9ecef;
        color: #6c757d;
    }

    .section-header {
        background: #f8f9fa;
        padding: 15px 20px;
        border-radius: 8px;
        margin: 25px 0 15px 0;
        border-left: 4px solid var(--pln-blue);
        display: flex;
        justify-content: between;
        align-items: center;
    }

    .section-header h5 {
        margin: 0;
        color: var(--pln-blue);
        font-weight: 600;
        flex: 1;
    }

    .activity-info {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .info-card {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 15px;
        background: white;
        border-radius: 6px;
        margin-bottom: 10px;
        border: 1px solid #e9ecef;
    }

    .info-card i {
        font-size: 1.5rem;
    }

    .form-label {
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
        display: block;
    }

    .form-label i {
        color: var(--pln-blue);
        margin-right: 8px;
    }

    .form-control, .form-select {
        border-radius: 8px;
        border: 2px solid #e9ecef;
        padding: 10px 15px;
        transition: border-color 0.3s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--pln-blue);
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
    }

    .preview-section h5 {
        color: var(--pln-blue);
        margin-bottom: 15px;
    }

    .user-preview {
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

    .preview-avatar .avatar-circle {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(45deg, #007bff, #0056b3);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 1.2rem;
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

    .preview-username {
        color: #6c757d;
        margin-bottom: 3px;
    }

    .preview-email {
        color: #6c757d;
        font-size: 0.9rem;
        margin-bottom: 8px;
    }

    .preview-role, .preview-unit {
        margin-bottom: 5px;
    }

    .breadcrumb {
        background: none;
        padding: 0;
        margin-bottom: 10px;
    }

    .breadcrumb a {
        color: var(--pln-blue);
        text-decoration: none;
    }

    .breadcrumb a:hover {
        text-decoration: underline;
    }

    .breadcrumb-item.active {
        color: #6c757d;
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
        
        .info-card {
            margin-bottom: 15px;
        }

        .section-header {
            flex-direction: column;
            text-align: center;
            gap: 5px;
        }
    }

    /* Alert styling */
    .alert {
        border-radius: 8px;
        border: none;
        padding: 15px 20px;
    }

    .alert-danger {
        background: #f8d7da;
        color: #721c24;
    }

    /* Form validation */
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
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const nameInput = document.getElementById('name');
        const usernameInput = document.getElementById('username');
        const emailInput = document.getElementById('email');
        const roleSelect = document.getElementById('role');
        const unitSelect = document.getElementById('unit_id');
        
        // Update preview when inputs change
        [nameInput, usernameInput, emailInput, roleSelect, unitSelect].forEach(input => {
            input.addEventListener('change', updatePreview);
            input.addEventListener('input', updatePreview);
        });
        
        function updatePreview() {
            const name = nameInput.value.trim();
            const username = usernameInput.value.trim();
            const email = emailInput.value.trim();
            const role = roleSelect.value;
            const unitId = unitSelect.value;
            const unitText = unitSelect.options[unitSelect.selectedIndex]?.text || '';
            
            if (name) {
                // Generate initials
                const initials = name.split(' ').map(word => word.charAt(0).toUpperCase()).slice(0, 2).join('');
                document.getElementById('previewAvatar').textContent = initials || '--';
                document.getElementById('previewName').textContent = name;
            }
            
            if (username) {
                document.getElementById('previewUsername').textContent = '@' + username;
            }
            
            if (email) {
                document.getElementById('previewEmail').textContent = email;
            }
            
            // Update role badge
            const roleBadge = document.getElementById('previewRole');
            if (role) {
                const roleText = role === 'admin' ? 'Administrator' : 'User';
                const roleColor = role === 'admin' ? 'danger' : 'primary';
                roleBadge.innerHTML = `<span class="badge bg-${roleColor}">${roleText}</span>`;
            }
            
            // Update unit badge
            const unitBadge = document.getElementById('previewUnit');
            if (unitId && unitText) {
                // Extract unit name only (remove the type part in parentheses)
                const cleanUnitName = unitText.replace(/\s*\([^)]*\)/, '');
                unitBadge.innerHTML = `<span class="badge bg-info">${cleanUnitName}</span>`;
            }
        }
        
        // Form validation
        document.getElementById('userForm').addEventListener('submit', function(e) {
            const role = document.getElementById('role').value;
            const currentUserRole = '{{ Auth::user()->role }}';
            const isEditingSelf = '{{ $user->user_id }}' === '{{ Auth::user()->user_id }}';
            
            if (isEditingSelf && currentUserRole === 'admin' && role !== 'admin') {
                if (!confirm('Anda akan mengubah role Anda sendiri dari Administrator ke User. Anda akan kehilangan akses admin setelah ini. Yakin melanjutkan?')) {
                    e.preventDefault();
                    return false;
                }
            }
        });
        
        // Warning for self-edit
        const roleSelect = document.getElementById('role');
        const isEditingSelf = '{{ $user->user_id }}' === '{{ Auth::user()->user_id }}';
        
        if (isEditingSelf) {
            roleSelect.addEventListener('change', function() {
                if (this.value === 'user' && '{{ Auth::user()->role }}' === 'admin') {
                    const warning = document.createElement('div');
                    warning.className = 'alert alert-warning mt-2';
                    warning.innerHTML = '<i class="fas fa-exclamation-triangle"></i> <strong>Peringatan!</strong> Anda akan kehilangan akses administrator jika mengubah role ke User.';
                    
                    // Remove existing warning
                    const existingWarning = this.parentNode.querySelector('.alert-warning');
                    if (existingWarning) {
                        existingWarning.remove();
                    }
                    
                    this.parentNode.appendChild(warning);
                } else {
                    // Remove warning if exists
                    const existingWarning = this.parentNode.querySelector('.alert-warning');
                    if (existingWarning) {
                        existingWarning.remove();
                    }
                }
            });
        }
    });
</script>
@endsection