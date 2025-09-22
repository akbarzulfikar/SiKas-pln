@extends('layouts.app')

@section('title', 'Tambah User - Sistem Kas PLN')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="form-container">
                <div class="form-header">
                    <h3><i class="fas fa-user-plus"></i> Tambah User Baru</h3>
                    <p class="text-muted">Buat akun user baru untuk mengakses sistem</p>
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

                <form action="{{ route('users.store') }}" method="POST" id="userForm">
                    @csrf

                    <!-- Basic Information Section -->
                    <div class="section-header">
                        <h5><i class="fas fa-user"></i> Informasi Dasar</h5>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">
                                    <i class="fas fa-user"></i> Nama Lengkap
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    class="form-control @error('name') is-invalid @enderror"
                                    id="name"
                                    name="name"
                                    value="{{ old('name') }}"
                                    placeholder="Nama lengkap user"
                                    required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="username" class="form-label">
                                    <i class="fas fa-at"></i> Username
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    class="form-control @error('username') is-invalid @enderror"
                                    id="username"
                                    name="username"
                                    value="{{ old('username') }}"
                                    placeholder="Username untuk login"
                                    required>
                                @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Username harus unik, tanpa spasi</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope"></i> Email
                            <span class="text-danger">*</span>
                        </label>
                        <input type="email"
                            class="form-control @error('email') is-invalid @enderror"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="alamat@email.com"
                            required>
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password Section -->
                    <div class="section-header">
                        <h5><i class="fas fa-lock"></i> Keamanan</h5>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-key"></i> Password
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        id="password"
                                        name="password"
                                        placeholder="Minimal 6 karakter"
                                        required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                        <i class="fas fa-eye" id="password-eye"></i>
                                    </button>
                                </div>
                                @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">
                                    <i class="fas fa-key"></i> Konfirmasi Password
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password"
                                        class="form-control"
                                        id="password_confirmation"
                                        name="password_confirmation"
                                        placeholder="Ulangi password"
                                        required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')">
                                        <i class="fas fa-eye" id="password_confirmation-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Role & Unit Section -->
                    <div class="section-header">
                        <h5><i class="fas fa-cog"></i> Role & Unit</h5>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="role" class="form-label">
                                    <i class="fas fa-shield-alt"></i> Role
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('role') is-invalid @enderror"
                                    id="role"
                                    name="role"
                                    required>
                                    <option value="">-- Pilih Role --</option>
                                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>
                                        🔐 Administrator
                                    </option>
                                    <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>
                                        👤 User
                                    </option>
                                </select>
                                @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Admin dapat akses semua fitur, User hanya unit sendiri</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="unit_id" class="form-label">
                                    <i class="fas fa-building"></i> Unit
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('unit_id') is-invalid @enderror"
                                    id="unit_id"
                                    name="unit_id"
                                    required>
                                    <option value="">-- Pilih Unit --</option>
                                    @foreach($units as $unit)
                                    <option value="{{ $unit->unit_id }}" {{ old('unit_id') == $unit->unit_id ? 'selected' : '' }}>
                                        {{ $unit->unit_name }} ({{ $unit->unit_type }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('unit_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Job Information Section -->
                    <div class="section-header">
                        <h5><i class="fas fa-briefcase"></i> Informasi Pekerjaan</h5>
                        <small class="text-muted">Opsional</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nip" class="form-label">
                                    <i class="fas fa-id-card"></i> NIP
                                </label>
                                <input type="text"
                                    class="form-control @error('nip') is-invalid @enderror"
                                    id="nip"
                                    name="nip"
                                    value="{{ old('nip') }}"
                                    placeholder="Nomor Induk Pegawai">
                                @error('nip')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="position" class="form-label">
                                    <i class="fas fa-user-tie"></i> Jabatan
                                </label>
                                <input type="text"
                                    class="form-control @error('position') is-invalid @enderror"
                                    id="position"
                                    name="position"
                                    value="{{ old('position') }}"
                                    placeholder="Jabatan/posisi">
                                @error('position')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Status Section -->
                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" 
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                <i class="fas fa-toggle-on text-success"></i>
                                <strong>User Aktif</strong>
                            </label>
                            <small class="d-block text-muted">
                                User aktif dapat login dan mengakses sistem
                            </small>
                        </div>
                    </div>

                    <!-- Preview Section -->
                    <div class="preview-section" id="previewSection" style="display: none;">
                        <h5><i class="fas fa-eye"></i> Preview User</h5>
                        <div class="user-preview">
                            <div class="preview-card">
                                <div class="preview-avatar">
                                    <div class="avatar-circle" id="previewAvatar">
                                        --
                                    </div>
                                </div>
                                <div class="preview-content">
                                    <div class="preview-name" id="previewName">Nama User</div>
                                    <div class="preview-username" id="previewUsername">@username</div>
                                    <div class="preview-email" id="previewEmail">email@domain.com</div>
                                    <div class="preview-role" id="previewRole">
                                        <span class="badge bg-secondary">Role</span>
                                    </div>
                                    <div class="preview-unit" id="previewUnit">
                                        <span class="badge bg-info">Unit</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save"></i> Simpan User
                        </button>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary btn-lg">
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

    .section-header {
        background: #f8f9fa;
        padding: 15px 20px;
        border-radius: 8px;
        margin: 25px 0 15px 0;
        border-left: 4px solid var(--pln-blue);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .section-header h5 {
        margin: 0;
        color: var(--pln-blue);
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

    .form-control, 
    .form-select {
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 12px;
        font-size: 14px;
        transition: border-color 0.3s ease;
    }

    .form-control:focus, 
    .form-select:focus {
        outline: none;
        border-color: var(--pln-blue);
        box-shadow: 0 0 0 0.2rem rgba(0, 61, 122, 0.25);
    }

    .input-group-text {
        background: #f8f9fa;
        border: 2px solid #e9ecef;
        border-left: none;
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
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 15px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .preview-avatar .avatar-circle {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--pln-blue), var(--pln-blue-dark));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 1.2rem;
    }

    .preview-name {
        font-weight: 600;
        color: #333;
        font-size: 1.2rem;
        margin-bottom: 5px;
    }

    .preview-username {
        color: #6c757d;
        font-family: 'Courier New', monospace;
        margin-bottom: 5px;
    }

    .preview-email {
        color: #6c757d;
        margin-bottom: 10px;
    }

    .preview-role,
    .preview-unit {
        margin-bottom: 5px;
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

    .password-strength {
        margin-top: 5px;
        font-size: 0.8rem;
    }

    .password-weak {
        color: #dc3545;
    }

    .password-medium {
        color: #ffc107;
    }

    .password-strong {
        color: #28a745;
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
        
        .preview-card {
            flex-direction: column;
            text-align: center;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const nameInput = document.getElementById('name');
        const usernameInput = document.getElementById('username');
        const emailInput = document.getElementById('email');
        const roleSelect = document.getElementById('role');
        const unitSelect = document.getElementById('unit_id');
        const passwordInput = document.getElementById('password');
        const previewSection = document.getElementById('previewSection');
        
        // Auto-generate username from name
        nameInput.addEventListener('input', function() {
            if (!usernameInput.value) {
                const username = this.value.toLowerCase()
                    .replace(/[^a-z0-9]/g, '.')
                    .replace(/\.+/g, '.')
                    .replace(/^\.+|\.+$/g, '');
                usernameInput.value = username;
            }
            updatePreview();
        });
        
        // Update preview when inputs change
        [usernameInput, emailInput, roleSelect, unitSelect].forEach(input => {
            input.addEventListener('change', updatePreview);
            input.addEventListener('input', updatePreview);
        });
        
        // Password strength checker
        passwordInput.addEventListener('input', function() {
            checkPasswordStrength(this.value);
        });
        
        function updatePreview() {
            const name = nameInput.value.trim();
            const username = usernameInput.value.trim();
            const email = emailInput.value.trim();
            const role = roleSelect.value;
            const unitId = unitSelect.value;
            const unitText = unitSelect.options[unitSelect.selectedIndex]?.text || '';
            
            if (name && username && email) {
                previewSection.style.display = 'block';
                
                // Generate initials
                const initials = name.split(' ').map(word => word.charAt(0).toUpperCase()).slice(0, 2).join('');
                document.getElementById('previewAvatar').textContent = initials || '--';
                
                // Update preview content
                document.getElementById('previewName').textContent = name;
                document.getElementById('previewUsername').textContent = '@' + username;
                document.getElementById('previewEmail').textContent = email;
                
                // Update role badge
                const roleBadge = document.getElementById('previewRole');
                if (role) {
                    const roleText = role === 'admin' ? 'Administrator' : 'User';
                    const roleColor = role === 'admin' ? 'danger' : 'primary';
                    roleBadge.innerHTML = `<span class="badge bg-${roleColor}">${roleText}</span>`;
                } else {
                    roleBadge.innerHTML = '<span class="badge bg-secondary">Pilih Role</span>';
                }
                
                // Update unit badge
                const unitBadge = document.getElementById('previewUnit');
                if (unitId && unitText) {
                    unitBadge.innerHTML = `<span class="badge bg-info">${unitText}</span>`;
                } else {
                    unitBadge.innerHTML = '<span class="badge bg-secondary">Pilih Unit</span>';
                }
            } else {
                previewSection.style.display = 'none';
            }
        }
        
        function checkPasswordStrength(password) {
            const strength = document.getElementById('passwordStrength');
            if (!strength) {
                const strengthDiv = document.createElement('div');
                strengthDiv.id = 'passwordStrength';
                strengthDiv.className = 'password-strength';
                passwordInput.parentNode.parentNode.appendChild(strengthDiv);
            }
            
            const strengthEl = document.getElementById('passwordStrength');
            
            if (password.length === 0) {
                strengthEl.textContent = '';
                return;
            }
            
            let score = 0;
            
            // Length check
            if (password.length >= 8) score += 1;
            if (password.length >= 12) score += 1;
            
            // Complexity checks
            if (/[a-z]/.test(password)) score += 1;
            if (/[A-Z]/.test(password)) score += 1;
            if (/[0-9]/.test(password)) score += 1;
            if (/[^A-Za-z0-9]/.test(password)) score += 1;
            
            if (score < 3) {
                strengthEl.textContent = '🔴 Password lemah';
                strengthEl.className = 'password-strength password-weak';
            } else if (score < 5) {
                strengthEl.textContent = '🟡 Password sedang';
                strengthEl.className = 'password-strength password-medium';
            } else {
                strengthEl.textContent = '🟢 Password kuat';
                strengthEl.className = 'password-strength password-strong';
            }
        }
    });
    
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const eye = document.getElementById(fieldId + '-eye');
        
        if (field.type === 'password') {
            field.type = 'text';
            eye.className = 'fas fa-eye-slash';
        } else {
            field.type = 'password';
            eye.className = 'fas fa-eye';
        }
    }
    
    // Form validation
    document.getElementById('userForm').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const passwordConfirm = document.getElementById('password_confirmation').value;
        
        if (password !== passwordConfirm) {
            e.preventDefault();
            alert('Password dan konfirmasi password tidak cocok!');
            return false;
        }
        
        if (password.length < 6) {
            e.preventDefault();
            alert('Password minimal 6 karakter!');
            return false;
        }
    });
</script>
@endsection