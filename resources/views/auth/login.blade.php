<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Sistem - E-LETING Bagian Hukum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
            background: #ffffff;
            overflow: hidden;
        }
        .login-header {
            background: linear-gradient(135deg, #0d6efd 0%, #0043a8 100%);
            padding: 1.5rem 1.5rem;
            text-align: center;
            color: #ffffff;
        }
        .login-header i {
            font-size: 2.5rem;
            margin-bottom: 0.2rem;
            display: inline-block;
            text-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
        .form-label {
            font-weight: 600;
            color: #475569;
            font-size: 0.85rem;
            margin-bottom: 0.3rem;
        }
        .form-control {
            border-radius: 8px;
            padding: 0.6rem 1rem;
            border: 1px solid #cbd5e1;
            font-size: 0.9rem;
            transition: all 0.2s;
            background-color: #f8fafc;
        }
        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
            border-color: #0d6efd;
            background-color: #ffffff;
        }
        .input-group-text {
            border-radius: 8px 0 0 8px;
            border: 1px solid #cbd5e1;
            border-right: none;
            background-color: #f8fafc;
            color: #64748b;
            padding: 0.5rem 0.8rem;
        }
        .form-control.with-icon {
            border-radius: 0 8px 8px 0;
            border-left: none;
            padding-left: 0;
        }
        .form-control.with-icon:focus + .input-group-text {
            border-color: #0d6efd;
            color: #0d6efd;
        }
        .btn-login {
            border-radius: 8px;
            font-weight: 600;
            padding: 0.6rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            border: none;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(13, 110, 253, 0.3);
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5 col-xl-4">
            
            <div class="login-card">
                <div class="login-header">
                    <i class="bi bi-shield-check"></i>
                    <h4 class="fw-bold mb-1 letter-spacing-tight">E-LETING</h4>
                    <p class="mb-0 text-white-50 small" style="font-size: 0.8rem;">
                        Elektronik Legal Drafting <br> Bagian Hukum Setda Kota Medan
                    </p>
                </div>
                
                <div class="card-body p-4 bg-white">
                    
                    <div class="text-center mb-3">
                        <h6 class="fw-bold text-dark mb-1">Selamat Datang!</h6>
                        <p class="text-muted" style="font-size: 0.8rem;">Silakan masuk menggunakan NIP Anda.</p>
                    </div>

                    {{-- Tangkap error dari ValidationException Laravel --}}
                    @if($errors->any())
                        <div class="alert alert-danger d-flex align-items-center py-2 px-3 mb-3 border-0 bg-danger bg-opacity-10 text-danger rounded-3" style="font-size: 0.8rem;" role="alert">
                            <i class="bi bi-exclamation-octagon-fill me-2 fs-6"></i>
                            <div>{{ $errors->first() }}</div>
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('login.post') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">NIP Pegawai</label>
                            <div class="input-group">
                                <span class="input-group-text bg-transparent"><i class="bi bi-person-badge"></i></span>
                                <input type="text" name="nip" class="form-control with-icon border-start-0" placeholder="Masukkan 18 digit NIP" value="{{ old('nip') }}" required autocomplete="off">
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">Kata Sandi</label>
                            <div class="input-group">
                                <span class="input-group-text bg-transparent"><i class="bi bi-key"></i></span>
                                <input type="password" name="password" class="form-control with-icon border-start-0" placeholder="Masukkan password" required>
                            </div>
                        </div>
                        
                        {{-- Fitur Ingat Saya (Optional tapi bagus di Laravel) --}}
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label small text-muted" for="remember">Ingat Saya</label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-login w-100 text-white d-flex justify-content-center align-items-center">
                            <span>Masuk ke Sistem</span>
                            <i class="bi bi-box-arrow-in-right ms-2 fs-6"></i>
                        </button>
                    </form>
                </div>
                
                <div class="card-footer bg-light border-0 text-center py-2">
                    <small class="text-muted opacity-75" style="font-size: 0.75rem;">&copy; {{ date('Y') }} Bagian Hukum Setda Kota Medan</small>
                </div>
            </div>
            
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const inputs = document.querySelectorAll('.with-icon');
    inputs.forEach(input => {
        input.addEventListener('focus', () => {
            input.previousElementSibling.style.borderColor = '#0d6efd';
            input.previousElementSibling.style.color = '#0d6efd';
        });
        input.addEventListener('blur', () => {
            input.previousElementSibling.style.borderColor = '#cbd5e1';
            input.previousElementSibling.style.color = '#64748b';
        });
    });
</script>
</body>
</html>