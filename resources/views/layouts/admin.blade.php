<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - {{ \App\Models\Setting::get('koperasi_name', 'E-Koperasi') }}</title>
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
        <!-- Sidebar -->
        <aside class="dashboard-sidebar">
            <div class="sidebar-brand">
                <div class="bg-kop-light text-kop-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                    <i class="bi bi-shield-check fs-5"></i>
                </div>
                <div class="d-flex flex-column">
                    <span class="fs-6 fw-bold text-kop-primary leading-tight">Admin Koperasi</span>
                    <small class="text-muted" style="font-size: 0.7rem;">Sistem E-Koperasi</small>
                </div>
            </div>

            <ul class="sidebar-menu">
                <li class="sidebar-menu-header">Utama</li>
                <li class="sidebar-menu-item">
                    <a href="{{ route('admin.dashboard') }}" class="sidebar-menu-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>

                <li class="sidebar-menu-header">Katalog & Produk</li>
                <li class="sidebar-menu-item">
                    <a href="{{ route('admin.products.index') }}" class="sidebar-menu-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                        <i class="bi bi-box-seam"></i> Produk & Harga
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="{{ route('admin.categories.index') }}" class="sidebar-menu-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                        <i class="bi bi-tags"></i> Kategori
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="{{ route('admin.units.index') }}" class="sidebar-menu-link {{ request()->routeIs('admin.units.*') ? 'active' : '' }}">
                        <i class="bi bi-rulers"></i> Satuan Barang
                    </a>
                </li>

                <li class="sidebar-menu-header">Persediaan & Stok</li>
                <li class="sidebar-menu-item">
                    <a href="{{ route('admin.inventory.index') }}" class="sidebar-menu-link {{ request()->routeIs('admin.inventory.index') ? 'active' : '' }}">
                        <i class="bi bi-boxes"></i> Monitoring Stok
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="{{ route('admin.purchases.index') }}" class="sidebar-menu-link {{ request()->routeIs('admin.purchases.*') ? 'active' : '' }}">
                        <i class="bi bi-truck"></i> Barang Masuk (PO)
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="{{ route('admin.suppliers.index') }}" class="sidebar-menu-link {{ request()->routeIs('admin.suppliers.*') ? 'active' : '' }}">
                        <i class="bi bi-building"></i> Pemasok / Supplier
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="{{ route('admin.stock-movements.index') }}" class="sidebar-menu-link {{ request()->routeIs('admin.stock-movements.*') ? 'active' : '' }}">
                        <i class="bi bi-arrow-left-right"></i> Mutasi Stok
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="{{ route('admin.stock-opname.index') }}" class="sidebar-menu-link {{ request()->routeIs('admin.stock-opname.*') ? 'active' : '' }}">
                        <i class="bi bi-clipboard-check"></i> Stok Opname (Admin)
                    </a>
                </li>

                <li class="sidebar-menu-header">Transaksi & Pesanan</li>
                <li class="sidebar-menu-item">
                    <a href="{{ route('admin.orders.index') }}" class="sidebar-menu-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                        <i class="bi bi-cart-check"></i> Semua Pesanan
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="{{ route('admin.payments.index') }}" class="sidebar-menu-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                        <i class="bi bi-qr-code-scan"></i> Verifikasi Bayar
                    </a>
                </li>

                <li class="sidebar-menu-header">Laporan & Pengaturan</li>
                <li class="sidebar-menu-item">
                    <a href="{{ route('admin.reports.sales') }}" class="sidebar-menu-link {{ request()->routeIs('admin.reports.sales*') ? 'active' : '' }}">
                        <i class="bi bi-bar-chart-line"></i> Laporan Penjualan
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="{{ route('admin.reports.inventory') }}" class="sidebar-menu-link {{ request()->routeIs('admin.reports.inventory*') ? 'active' : '' }}">
                        <i class="bi bi-journal-text"></i> Laporan Persediaan
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="{{ route('admin.users.index') }}" class="sidebar-menu-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i> Manajemen Pengguna
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="{{ route('admin.settings.index') }}" class="sidebar-menu-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                        <i class="bi bi-gear"></i> Pengaturan & QRIS
                    </a>
                </li>
            </ul>

            <div class="p-3 border-top bg-light">
                <a href="{{ route('customer.home') }}" class="btn btn-sm btn-outline-kop w-100 mb-2">
                    <i class="bi bi-shop me-1"></i> Buka Toko Pegawai
                </a>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="dashboard-content d-flex flex-column">
            <!-- Topbar -->
            <header class="navbar navbar-expand bg-white border-bottom px-4 py-2 sticky-top">
                <div class="d-flex align-items-center gap-3">
                    <span class="badge bg-kop-light text-kop-primary px-3 py-2 fw-semibold">
                        <i class="bi bi-shield-lock me-1"></i> Mode Administrator
                    </span>
                    <span class="text-muted small d-none d-md-inline">
                        <i class="bi bi-building me-1"></i> {{ \App\Models\Setting::get('koperasi_name', 'E-Koperasi') }}
                    </span>
                </div>

                <div class="ms-auto d-flex align-items-center gap-3">
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
                                <small class="text-muted">{{ auth()->user()->role->name ?? 'Admin' }}</small>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('admin.settings.index') }}"><i class="bi bi-gear me-2"></i> Pengaturan</a></li>
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
                &copy; {{ date('Y') }} {{ \App\Models\Setting::get('koperasi_name', 'E-Koperasi') }} — Sistem Informasi Koperasi Terpadu
            </footer>
        </div>
    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @livewireScripts
    @stack('scripts')
</body>
</html>

