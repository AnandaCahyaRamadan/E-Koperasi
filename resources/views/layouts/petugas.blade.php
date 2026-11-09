<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Kasir Operasional') - {{ \App\Models\Setting::get('koperasi_name', 'E-Koperasi') }}</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Custom Style -->
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
    @livewireStyles
    @stack('styles')
</head>
<body>
    <div class="dashboard-wrapper">
        <!-- Sidebar Petugas -->
        <aside class="dashboard-sidebar">
            <div class="sidebar-brand bg-white">
                <div class="bg-kop-light text-kop-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                    <i class="bi bi-calculator fs-5"></i>
                </div>
                <div class="d-flex flex-column">
                    <span class="fs-6 fw-bold text-kop-primary">Kasir Operasional</span>
                    <small class="text-muted" style="font-size: 0.7rem;">Operasional & Pelayanan</small>
                </div>
            </div>

            <ul class="sidebar-menu">
                <li class="sidebar-menu-header">Pelayanan & Kasir</li>
                <li class="sidebar-menu-item">
                    <a href="{{ route('petugas.pos') }}" class="sidebar-menu-link {{ request()->routeIs('petugas.pos') ? 'active' : '' }}">
                        <i class="bi bi-cart-plus-fill text-warning"></i> <strong>POS Kasir Langsung</strong>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="{{ route('petugas.dashboard') }}" class="sidebar-menu-link {{ request()->routeIs('petugas.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i> Dashboard Operasional
                    </a>
                </li>

                <li class="sidebar-menu-header">Verifikasi & Antrean</li>
                <li class="sidebar-menu-item">
                    <a href="{{ route('petugas.payments.index') }}" class="sidebar-menu-link {{ request()->routeIs('petugas.payments.*') ? 'active' : '' }}">
                        <i class="bi bi-qr-code-scan"></i> Verifikasi Bayar
                        @php
                            $pendingPaymentCount = \App\Models\Payment::where('status', 'waiting_verification')->count();
                        @endphp
                        @if($pendingPaymentCount > 0)
                            <span class="badge bg-danger rounded-pill ms-auto">{{ $pendingPaymentCount }}</span>
                        @endif
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="{{ route('petugas.orders.index') }}" class="sidebar-menu-link {{ request()->routeIs('petugas.orders.*') ? 'active' : '' }}">
                        <i class="bi bi-receipt"></i> Antrean Pesanan
                    </a>
                </li>

                <li class="sidebar-menu-header">Inventori & Katalog</li>
                <li class="sidebar-menu-item">
                    <a href="{{ route('petugas.products.index') }}" class="sidebar-menu-link {{ request()->routeIs('petugas.products.*') ? 'active' : '' }}">
                        <i class="bi bi-box-seam"></i> Cek Stok & Harga
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="{{ route('petugas.history') }}" class="sidebar-menu-link {{ request()->routeIs('petugas.history') ? 'active' : '' }}">
                        <i class="bi bi-clock-history"></i> Riwayat Transaksi Kasir
                    </a>
                </li>
            </ul>

            <div class="p-3 border-top bg-light">
                <a href="{{ route('customer.home') }}" class="btn btn-sm btn-outline-kop w-100">
                    <i class="bi bi-shop me-1"></i> Mode Toko Pegawai
                </a>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="dashboard-content d-flex flex-column">
            <!-- Topbar -->
            <header class="navbar navbar-expand bg-white border-bottom px-4 py-2 sticky-top">
                <div class="d-flex align-items-center gap-3">
                    <span class="badge bg-kop-yellow text-dark px-3 py-2 fw-semibold">
                        <i class="bi bi-person-badge me-1"></i> Petugas Kasir Aktif
                    </span>
                    <span class="text-muted small d-none d-md-inline">
                        <i class="bi bi-geo-alt me-1"></i> {{ \App\Models\Setting::get('koperasi_address', 'Kantor Koperasi') }}
                    </span>
                </div>

                <div class="ms-auto d-flex align-items-center gap-3">
                    <a href="{{ route('petugas.pos') }}" class="btn btn-sm btn-kop-primary fw-semibold">
                        <i class="bi bi-plus-circle me-1"></i> Transaksi Baru (POS)
                    </a>

                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle d-flex align-items-center gap-2 border-0" type="button" data-bs-toggle="dropdown">
                            <div class="bg-kop-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.85rem;">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <span class="small fw-semibold">{{ auth()->user()->name }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                            <li class="dropdown-header">
                                <span class="fw-bold">{{ auth()->user()->name }}</span><br>
                                <small class="text-muted">Kasir ID: {{ auth()->user()->employee_id ?? 'KAS' }}</small>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button class="dropdown-item text-danger" type="submit">
                                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            <!-- Page Body -->
            <div class="p-4 flex-grow-1">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show card-custom border-0 mb-4" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show card-custom border-0 mb-4" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>

            <!-- Footer -->
            <footer class="bg-white border-top py-3 px-4 text-muted small text-center">
                &copy; {{ date('Y') }} {{ \App\Models\Setting::get('koperasi_name', 'E-Koperasi') }} — Modul Kasir Operasional
            </footer>
        </div>
    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @livewireScripts
    @stack('scripts')
</body>
</html>

