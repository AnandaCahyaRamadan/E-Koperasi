<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - {{ \App\Models\Setting::get('koperasi_name', 'E-Koperasi') }}</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center min-vh-100 py-4">

<div class="container" style="max-width: 440px;">
    <!-- Logo & Header -->
    <div class="text-center mb-4">
        <div class="bg-kop-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2 shadow" style="width: 68px; height: 68px;">
            <i class="bi bi-shop-window fs-2"></i>
        </div>
        <h4 class="fw-bold text-kop-primary mb-1">{{ \App\Models\Setting::get('koperasi_name', 'E-Koperasi') }}</h4>
        <p class="text-muted small">Sistem Informasi & Belanja Digital Pegawai</p>
    </div>

    <!-- Login Card -->
    <div class="card card-custom p-4 shadow-sm border-0">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show small" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show small" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger small mb-3">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label small fw-semibold text-muted">Email atau NIP Pegawai</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-person"></i></span>
                    <input type="text" name="email" id="emailInput" class="form-control border-start-0 ps-0" placeholder="nama@koperasi.com atau NIP" value="{{ old('email') }}" required autofocus>
                </div>
            </div>

            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <label class="form-label small fw-semibold text-muted">Kata Sandi</label>
                </div>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" id="passwordInput" class="form-control border-start-0 ps-0" placeholder="Masukkan kata sandi" required>
                </div>
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                <label class="form-check-label small text-muted" for="remember">
                    Ingat saya di perangkat ini
                </label>
            </div>

            <button type="submit" class="btn btn-kop-primary w-100 py-2 fw-semibold">
                <i class="bi bi-box-arrow-in-right me-1"></i> Masuk Sekarang
            </button>
        </form>

        <div class="text-center mt-3 pt-3 border-top">
            <p class="small text-muted mb-0">Belum memiliki akun pegawai?</p>
            <a href="{{ route('register') }}" class="small fw-bold text-kop-primary text-decoration-none">Daftar Akun Pegawai Baru</a>
        </div>
    </div>

    <!-- Quick Role Login (Helper for Demonstration / Testing) -->
    <div class="card card-custom p-3 mt-3 bg-white border-0 shadow-sm">
        <div class="small fw-bold text-muted mb-2 text-center text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">
            <i class="bi bi-lightning-charge-fill text-warning me-1"></i> Akun Demo / Uji Coba Cepat
        </div>
        <div class="row g-2">
            <div class="col-4">
                <button type="button" class="btn btn-outline-success btn-sm w-100 py-1" onclick="fillLogin('admin@koperasi.com', 'password')">
                    <small class="fw-bold d-block">Admin</small>
                    <span style="font-size: 0.65rem;">Kelola Toko</span>
                </button>
            </div>
            <div class="col-4">
                <button type="button" class="btn btn-outline-warning btn-sm w-100 py-1" onclick="fillLogin('petugas@koperasi.com', 'password')">
                    <small class="fw-bold d-block text-dark">Petugas</small>
                    <span style="font-size: 0.65rem;" class="text-muted">Kasir / POS</span>
                </button>
            </div>
            <div class="col-4">
                <button type="button" class="btn btn-outline-primary btn-sm w-100 py-1" onclick="fillLogin('pegawai@koperasi.com', 'password')">
                    <small class="fw-bold d-block">Pelanggan</small>
                    <span style="font-size: 0.65rem;">Pegawai</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function fillLogin(email, password) {
        document.getElementById('emailInput').value = email;
        document.getElementById('passwordInput').value = password;
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

