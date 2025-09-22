<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Informasi Kas PLN UP3 Langsa</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --pln-blue: #003d7a;
            --pln-blue-dark: #002952;
            --pln-yellow: #ffd700;
        }

        body {
            background: linear-gradient(135deg, var(--pln-blue) 0%, #004d96 50%, #0056b3 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
            margin: 20px;
        }

        .login-header {
            background: linear-gradient(135deg, var(--pln-blue), var(--pln-blue-dark));
            color: white;
            text-align: center;
            padding: 40px 30px 30px 30px;
        }

        .login-header i {
            font-size: 4rem;
            color: var(--pln-yellow);
            margin-bottom: 15px;
        }

        .login-header h3 {
            margin: 0;
            font-weight: 600;
            font-size: 1.5rem;
        }

        .login-header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 0.9rem;
        }

        .login-body {
            padding: 40px 30px;
        }

        .form-floating {
            margin-bottom: 20px;
        }

        .form-floating > .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            height: 60px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .form-floating > .form-control:focus {
            border-color: var(--pln-blue);
            box-shadow: 0 0 0 0.2rem rgba(0, 61, 122, 0.25);
        }

        .form-floating > label {
            color: #6c757d;
            font-weight: 500;
        }

        .btn-login {
            background: linear-gradient(135deg, var(--pln-blue), var(--pln-blue-dark));
            border: none;
            border-radius: 10px;
            padding: 15px;
            font-size: 16px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s ease;
            color: white;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 61, 122, 0.3);
            color: white;
        }

        .remember-checkbox {
            margin: 20px 0;
        }

        .remember-checkbox .form-check-input:checked {
            background-color: var(--pln-blue);
            border-color: var(--pln-blue);
        }

        .alert {
            border-radius: 10px;
            border: none;
            margin-bottom: 20px;
        }

        .demo-accounts {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            font-size: 0.85rem;
        }

        .demo-accounts h6 {
            color: var(--pln-blue);
            margin-bottom: 15px;
            font-weight: 600;
        }

        .demo-account {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding: 8px;
            background: white;
            border-radius: 6px;
            border-left: 3px solid var(--pln-blue);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .demo-account:hover {
            background: #f0f4f8;
            transform: translateX(5px);
        }

        .demo-admin {
            border-left-color: #dc3545;
        }

        .demo-user {
            border-left-color: #007bff;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <i class="fas fa-bolt"></i>
            <h3>PT PLN (PERSERO)</h3>
            <p>Sistem Informasi Kas UP3 Langsa</p>
        </div>

        <div class="login-body">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i>
                @foreach($errors->all() as $error)
                {{ $error }}<br>
                @endforeach
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                <div class="form-floating">
                    <input type="text"
                        class="form-control @error('username') is-invalid @enderror"
                        id="username"
                        name="username"
                        placeholder="Username"
                        value="{{ old('username') }}"
                        required
                        autofocus>
                    <label for="username">
                        <i class="fas fa-user"></i> Username
                    </label>
                </div>

                <div class="form-floating">
                    <input type="password"
                        class="form-control @error('password') is-invalid @enderror"
                        id="password"
                        name="password"
                        placeholder="Password"
                        required>
                    <label for="password">
                        <i class="fas fa-lock"></i> Password
                    </label>
                </div>

                <div class="remember-checkbox">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label" for="remember">
                            Ingat saya
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-login" id="loginButton">
                    <i class="fas fa-sign-in-alt"></i> Masuk Sistem
                </button>
            </form>

            <!-- Demo Accounts Info -->
            <div class="demo-accounts">
                <h6><i class="fas fa-info-circle"></i> Akun Demo</h6>
                <div class="demo-account demo-admin" onclick="fillLogin('admin', 'admin123')">
                    <span><strong>Admin:</strong> admin</span>
                    <span>admin123</span>
                </div>
                <div class="demo-account demo-user" onclick="fillLogin('up3.langsa', 'up3123')">
                    <span><strong>UP3 Langsa:</strong> up3.langsa</span>
                    <span>up3123</span>
                </div>
                <div class="demo-account demo-user" onclick="fillLogin('ulp.langsakota', 'ulp123')">
                    <span><strong>ULP Langsa Kota:</strong> ulp.langsakota</span>
                    <span>ulp123</span>
                </div>
                <small class="text-muted">
                    <i class="fas fa-shield-alt"></i>
                    Admin dapat mengelola semua data, User hanya dapat mengelola data unit masing-masing.
                    <br><strong>Klik akun demo untuk mengisi otomatis</strong>
                </small>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fill login function
        function fillLogin(username, password) {
            document.getElementById('username').value = username;
            document.getElementById('password').value = password;
        }

        // Handle form submission dengan loading state
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('loginButton');
            
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
            submitBtn.disabled = true;
        });
    </script>
</body>
</html>