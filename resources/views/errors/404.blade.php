<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - Halaman Tidak Ditemukan | {{ config('app.name') }}</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --pln-blue: #0066cc;
            --pln-blue-dark: #004499;
        }
        
        body {
            background: linear-gradient(135deg, var(--pln-blue), var(--pln-blue-dark));
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        
        .error-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .error-icon {
            font-size: 5rem;
            color: #ffc107;
            margin-bottom: 1rem;
        }
        
        .error-code {
            font-size: 6rem;
            font-weight: 900;
            color: var(--pln-blue);
            line-height: 1;
            margin-bottom: 0.5rem;
        }
        
        .btn-home {
            background: var(--pln-blue);
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            transition: all 0.3s ease;
        }
        
        .btn-home:hover {
            background: var(--pln-blue-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,102,204,0.3);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="error-container text-center p-5">
                    <div class="error-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    
                    <div class="error-code">404</div>
                    
                    <h2 class="h4 mb-3 text-dark">Halaman Tidak Ditemukan</h2>
                    
                    <p class="text-muted mb-4">
                        Halaman yang Anda cari tidak ditemukan. Mungkin halaman telah dipindahkan, 
                        dihapus, atau URL yang Anda masukkan salah.
                    </p>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <a href="{{ route('dashboard.index') }}" class="btn btn-primary btn-home me-md-2">
                            <i class="fas fa-home me-2"></i>
                            Kembali ke Dashboard
                        </a>
                        
                        <button onclick="history.back()" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>
                            Kembali
                        </button>
                    </div>
                    
                    <div class="mt-4 pt-4 border-top">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            {{ config('app.name') }} - Sistem Kas PLN
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>