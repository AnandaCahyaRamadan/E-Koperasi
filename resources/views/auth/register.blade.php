<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Pegawai - {{ \App\Models\Setting::get('koperasi_name', 'E-Koperasi') }}</title>
    <!-- Bootstrap 5 CSS (Offline) -->
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center min-vh-100 py-4">

<div class="container" style="max-width: 480px;">
    <div class="text-center mb-4">
        <div class="bg-kop-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2 shadow" style="width: 60px; height: 60px;">
            <i class="bi bi-person-plus fs-3"></i>
        </div>
        <h4 class="fw-bold text-kop-primary mb-1">Daftar Akun Pegawai</h4>
        <p class="text-muted small">Registrasi anggota untuk berbelanja di {{ \App\Models\Setting::get('koperasi_name', 'E-Koperasi') }}</p>
    </div>

    <div class="card card-custom p-4 shadow-sm border-0">
        @if($errors->any())
            <div class="alert alert-danger small mb-3">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register.post') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label small fw-semibold text-muted">Nama Lengkap</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-person"></i></span>
                    <input type="text" name="name" class="form-control border-start-0 ps-0" placeholder="Contoh: Budi Santoso" value="{{ old('name') }}" required autofocus>
                </div>
            </div>

            <div class="row g-2 mb-3">
                <div class="col-sm-6">
                    <label class="form-label small fw-semibold text-muted">NIP / ID Pegawai</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-card-text"></i></span>
                        <input type="text" name="employee_id" class="form-control border-start-0 ps-0" placeholder="PEG-12345" value="{{ old('employee_id') }}" required>
                    </div>
                </div>
                <div class="col-sm-6">
                    <label class="form-label small fw-semibold text-muted">No. WhatsApp / HP</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-whatsapp"></i></span>
                        <input type="tel" name="phone" class="form-control border-start-0 ps-0" placeholder="08123456789" value="{{ old('phone') }}" required>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-semibold text-muted">Alamat Email Kantor / Pribadi</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" class="form-control border-start-0 ps-0" placeholder="pegawai@instansi.go.id" value="{{ old('email') }}" required>
                </div>
            </div>

            <div class="row g-2 mb-3">
                <div class="col-sm-6">
                    <label class="form-label small fw-semibold text-muted">Kata Sandi</label>
                    <input type="password" name="password" class="form-control" placeholder="Min. 6 karakter" required>
                </div>
                <div class="col-sm-6">
                    <label class="form-label small fw-semibold text-muted">Ulangi Sandi</label>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi sandi" required>
                </div>
            </div>

            <button type="submit" class="btn btn-kop-primary w-100 py-2 fw-semibold mt-2">
                <i class="bi bi-check2-circle me-1"></i> Buat Akun Pegawai
            </button>
        </form>

        <div class="text-center mt-3 pt-3 border-top">
            <p class="small text-muted mb-0">Sudah memiliki akun?</p>
            <a href="{{ route('login') }}" class="small fw-bold text-kop-primary text-decoration-none">Masuk ke Akun Anda</a>
        </div>
    </div>
</div>

<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>

