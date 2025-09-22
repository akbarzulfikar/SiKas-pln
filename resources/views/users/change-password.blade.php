@extends('layouts.app')

@section('title', 'Ganti Password - Sistem Kas PLN')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="form-container">
                <div class="form-header">
                    <h3><i class="fas fa-key"></i> Ganti Password</h3>
                    <p class="text-muted">Ubah password untuk user "{{ $user->name }}"</p>
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

                <!-- User Info -->
                <div class="user-info-card">
                    <div class="user-avatar">
                        <div class="avatar-circle">
                            {{ $user->initials }}
                        </div>
                    </div>
                    <div class="user-details">
                        <h5>{{ $user->name }}</h5>
                        <div class="user-meta">
                            <span class="badge bg-{{ $user->role_badge_color }}">{{ $user->role_display_name }}</span>
                            <span class="badge bg-info">{{ $user->unit->unit_name }}</span>
                        </div>
                        <small class="text-muted">@{{ $user->username }} • {{ $user->email }}</small>
                    </div>
                </div>

                <form action="{{ route('users.change-password.update', $user->user_id) }}" method="POST" id="passwordForm">
                    @csrf

                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock"></i> Password Baru
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
                        <div id="passwordStrength" class="password-strength"></div>
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label">
                            <i class="fas fa-lock"></i> Konfirmasi Password Baru
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="password"
                                class="form-control"
                                id="password_confirmation"
                                name="password_confirmation"
                                placeholder="Ulangi password baru"
                                required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')">
                                <i class="fas fa-eye" id="password_confirmation-eye"></i>
                            </button>
                        </div>
                        <div id="passwordMatch" class="password-match"></div>
                    </div>

                    <!-- Security Tips -->
                    <div class="security-tips">
                        <h6><i class="fas fa-shield-alt"></i> Tips Keamanan Password</h6>
                        <ul class="tips-list">
                            <li><i class="fas fa-check text-success"></i> Minimal 6 karakter (disarankan 8+ karakter)</li>
                            <li><i class="fas fa-check text-success"></i> Kombinasi huruf besar dan kecil</li>
                            <li><i class="fas fa-check text-success"></i> Sertakan angka dan karakter khusus</li>
                            <li><i class="fas fa-check text-success"></i> Hindari informasi pribadi (nama, tanggal lahir)</li>
                            <li><i class="fas fa-check text-success"></i> Gunakan password yang berbeda untuk setiap akun</li>
                        </ul>
                    </div>

                    <!-- Warning Notice -->
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Perhatian:</strong> 
                        Setelah password diubah, user harus login ulang dengan password baru.
                        @if($user->user_id === Auth::user()->user_id)
                        <br><strong>Anda akan logout otomatis setelah mengubah password Anda sendiri.</strong>
                        @endif
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save"></i> Ganti Password
                        </button>
                        <a href="{{ route('users.show', $user->user_id) }}" class="btn btn-secondary btn-lg">
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

    .user-info-card {
        background: linear-gradient(135deg, var(--pln-blue), var(--pln-blue-dark));
        color: white;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .user-avatar .avatar-circle {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 1.3rem;
        border: 2px solid rgba(255, 255, 255, 0.3);
    }

    .user-details h5 {
        margin: 0 0 8px 0;
        font-weight: 600;
    }

    .user-meta {
        margin-bottom: 5px;
    }

    .user-meta .badge {
        margin-right: 8px;
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

    .form-control {
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 12px;
        font-size: 14px;
        transition: border-color 0.3s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--pln-blue);
        box-shadow: 0 0 0 0.2rem rgba(0, 61, 122, 0.25);
    }

    .input-group-text {
        background: #f8f9fa;
        border: 2px solid #e9ecef;
        border-left: none;
    }

    .password-strength {
        margin-top: 8px;
        font-size: 0.85rem;
        font-weight: 500;
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

    .password-match {
        margin-top: 8px;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .password-match.match {
        color: #28a745;
    }

    .password-match.no-match {
        color: #dc3545;
    }

    .security-tips {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        border-left: 4px solid #17a2b8;
    }

    .security-tips h6 {
        color: #17a2b8;
        margin-bottom: 12px;
        font-weight: 600;
    }

    .tips-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .tips-list li {
        padding: 4px 0;
        font-size: 0.9rem;
    }

    .tips-list i {
        margin-right: 8px;
        width: 16px;
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
        
        .user-info-card {
            flex-direction: column;
            text-align: center;
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
        const passwordInput = document.getElementById('password');
        const confirmInput = document.getElementById('password_confirmation');
        
        // Password strength checker
        passwordInput.addEventListener('input', function() {
            checkPasswordStrength(this.value);
            checkPasswordMatch();
        });
        
        // Password match checker
        confirmInput.addEventListener('input', checkPasswordMatch);
        
        function checkPasswordStrength(password) {
            const strengthEl = document.getElementById('passwordStrength');
            
            if (password.length === 0) {
                strengthEl.textContent = '';
                return;
            }
            
            let score = 0;
            let feedback = [];
            
            // Length check
            if (password.length >= 6) score += 1;
            else feedback.push('minimal 6 karakter');
            
            if (password.length >= 8) score += 1;
            else feedback.push('disarankan 8+ karakter');
            
            // Complexity checks
            if (/[a-z]/.test(password)) score += 1;
            else feedback.push('huruf kecil');
            
            if (/[A-Z]/.test(password)) score += 1;
            else feedback.push('huruf besar');
            
            if (/[0-9]/.test(password)) score += 1;
            else feedback.push('angka');
            
            if (/[^A-Za-z0-9]/.test(password)) score += 1;
            else feedback.push('karakter khusus (!@#$%^&*)');
            
            // Display strength
            if (score < 3) {
                strengthEl.innerHTML = '🔴 <strong>Password lemah</strong> - Tambahkan: ' + feedback.slice(0, 2).join(', ');
                strengthEl.className = 'password-strength password-weak';
            } else if (score < 5) {
                strengthEl.innerHTML = '🟡 <strong>Password sedang</strong> - Bisa ditingkatkan: ' + (feedback.length > 0 ? feedback.slice(0, 1).join(', ') : 'sudah cukup baik');
                strengthEl.className = 'password-strength password-medium';
            } else {
                strengthEl.innerHTML = '🟢 <strong>Password kuat</strong> - Keamanan baik!';
                strengthEl.className = 'password-strength password-strong';
            }
        }
        
        function checkPasswordMatch() {
            const password = passwordInput.value;
            const confirm = confirmInput.value;
            const matchEl = document.getElementById('passwordMatch');
            
            if (confirm.length === 0) {
                matchEl.textContent = '';
                return;
            }
            
            if (password === confirm) {
                matchEl.innerHTML = '✅ <strong>Password cocok</strong>';
                matchEl.className = 'password-match match';
            } else {
                matchEl.innerHTML = '❌ <strong>Password tidak cocok</strong>';
                matchEl.className = 'password-match no-match';
            }
        }
        
        // Form validation
        document.getElementById('passwordForm').addEventListener('submit', function(e) {
            const password = passwordInput.value;
            const confirm = confirmInput.value;
            
            if (password !== confirm) {
                e.preventDefault();
                alert('Password dan konfirmasi password tidak cocok!');
                confirmInput.focus();
                return false;
            }
            
            if (password.length < 6) {
                e.preventDefault();
                alert('Password minimal 6 karakter!');
                passwordInput.focus();
                return false;
            }
            
            // Confirm if changing own password
            const isOwnPassword = '{{ $user->user_id }}' === '{{ Auth::user()->user_id }}';
            if (isOwnPassword) {
                if (!confirm('Anda akan logout setelah mengubah password Anda sendiri. Lanjutkan?')) {
                    e.preventDefault();
                    return false;
                }
            }
        });
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
</script>
@endsection