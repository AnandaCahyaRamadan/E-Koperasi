<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Kasir Operasional') - {{ \App\Models\Setting::get('koperasi_name', 'E-Koperasi') }}</title>
    @include('partials.styles-common')
    @livewireStyles
    @stack('styles')
</head>
<body>
    <div class="top-nav-loader" id="petugas-nav-loader"></div>
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>
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
                <button type="button" class="btn-close d-lg-none ms-auto" id="sidebarCloseBtn" aria-label="Tutup"></button>
            </div>

            <nav class="sidebar-menu">
                <div class="sidebar-menu-header">Pelayanan & Kasir</div>
                <div class="sidebar-menu-item">
                    <a href="{{ route('petugas.pos') }}" class="sidebar-menu-link {{ request()->routeIs('petugas.pos') ? 'active' : '' }}">
                        <i class="bi bi-cart-plus-fill text-warning"></i> <strong>POS Kasir Langsung</strong>
                    </a>
                </div>
                <div class="sidebar-menu-item">
                    <a href="{{ route('petugas.dashboard') }}" class="sidebar-menu-link {{ request()->routeIs('petugas.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i> Dashboard Operasional
                    </a>
                </div>

                <div class="sidebar-menu-header">Verifikasi & Antrean</div>
                <div class="sidebar-menu-item">
                    <a href="{{ route('petugas.payments.index') }}" class="sidebar-menu-link {{ request()->routeIs('petugas.payments.*') ? 'active' : '' }}">
                        <i class="bi bi-qr-code-scan"></i> Verifikasi Bayar
                        @php
                            $pendingPaymentCount = \App\Models\Payment::where('status', 'waiting_verification')->count();
                        @endphp
                        @if($pendingPaymentCount > 0)
                            <span class="badge bg-danger rounded-pill ms-auto">{{ $pendingPaymentCount }}</span>
                        @endif
                    </a>
                </div>
                <div class="sidebar-menu-item">
                    <a href="{{ route('petugas.orders.index') }}" class="sidebar-menu-link {{ request()->routeIs('petugas.orders.*') ? 'active' : '' }}">
                        <i class="bi bi-receipt"></i> Antrean Pesanan
                    </a>
                </div>

                <div class="sidebar-menu-header">Inventori & Katalog</div>
                <div class="sidebar-menu-item">
                    <a href="{{ route('petugas.products.index') }}" class="sidebar-menu-link {{ request()->routeIs('petugas.products.*') ? 'active' : '' }}">
                        <i class="bi bi-box-seam"></i> Cek Stok & Harga
                    </a>
                </div>
                <div class="sidebar-menu-item">
                    <a href="{{ route('petugas.history') }}" class="sidebar-menu-link {{ request()->routeIs('petugas.history') ? 'active' : '' }}">
                        <i class="bi bi-clock-history"></i> Riwayat Transaksi Kasir
                    </a>
                </div>
            </nav>

            <div class="p-3 border-top bg-light mt-auto">
                <a href="{{ route('customer.home') }}" class="btn btn-sm btn-outline-kop w-100">
                    <i class="bi bi-shop me-1"></i> Mode Toko Pegawai
                </a>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="dashboard-content d-flex flex-column">
            <!-- Topbar -->
            <header class="navbar bg-white border-bottom px-3 px-lg-4 py-2 sticky-top">
                <div class="container-fluid p-0 d-flex align-items-center justify-content-between flex-nowrap gap-2">
                    <div class="d-flex align-items-center gap-2 min-w-0 flex-shrink-1">
                        <button class="btn btn-light d-lg-none border rounded-circle p-0 d-flex align-items-center justify-content-center flex-shrink-0" id="sidebarToggleBtn" aria-label="Toggle navigation" style="width: 36px; height: 36px;">
                            <i class="bi bi-list fs-5"></i>
                        </button>
                        <span class="badge bg-kop-yellow text-dark px-2.5 py-1.5 fw-semibold flex-shrink-0 text-nowrap">
                            <i class="bi bi-person-badge me-1"></i> <span class="d-none d-sm-inline">Petugas </span>Kasir
                        </span>
                        <span class="text-muted small d-none d-xl-inline text-truncate" style="max-width: 260px;" title="{{ \App\Models\Setting::get('koperasi_address', 'Kantor Koperasi') }}">
                            <i class="bi bi-geo-alt me-1"></i> {{ \App\Models\Setting::get('koperasi_address', 'Kantor Koperasi') }}
                        </span>
                    </div>

                    <div class="d-flex align-items-center gap-2 gap-sm-3 flex-shrink-0 ms-auto">
                        <div class="dropdown" x-data="{ open: false }" @click.outside="open = false">
                            <button class="btn btn-user-pill dropdown-toggle d-flex align-items-center gap-2 px-2 py-1" 
                                    type="button" 
                                    @click="open = !open" 
                                    data-bs-toggle="dropdown" 
                                    aria-expanded="false">
                                <div class="bg-kop-light text-kop-primary rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 28px; height: 28px; font-size: 0.75rem;">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <span class="small fw-semibold text-dark text-truncate d-none d-sm-inline" style="max-width: 120px;">{{ auth()->user()->name }}</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" 
                                :class="{ 'show': open }" 
                                :style="open ? 'display: block; position: absolute; right: 0; top: 100%; z-index: 1050;' : ''">
                                <li class="dropdown-header">
                                    <span class="fw-bold text-dark">{{ auth()->user()->name }}</span><br>
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
                </div>
            </header>

            <!-- Page Body -->
            <div class="p-4 flex-grow-1">
                @include('partials.flash-toasts')

                {{ $slot ?? '' }}
                @yield('content')
            </div>

            <!-- Footer -->
            <footer class="bg-white border-top py-3 px-4 text-muted small text-center">
                &copy; {{ date('Y') }} {{ \App\Models\Setting::get('koperasi_name', 'E-Koperasi') }} — Modul Kasir Operasional
            </footer>
        </div>
    </div>

    <!-- Bootstrap 5 Bundle JS (Offline) -->
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    @include('partials.scripts-common')
    <script>
        document.addEventListener('livewire:navigating', () => {
            const loader = document.getElementById('petugas-nav-loader');
            if (loader) loader.style.display = 'block';
        });
        document.addEventListener('livewire:navigated', () => {
            const loader = document.getElementById('petugas-nav-loader');
            if (loader) loader.style.display = 'none';
        });

        function initSidebarToggle() {
            const sidebar = document.querySelector('.dashboard-sidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            const toggleBtn = document.getElementById('sidebarToggleBtn');
            const closeBtn = document.getElementById('sidebarCloseBtn');

            if (!sidebar || !backdrop) return;

            function openSidebar() {
                sidebar.classList.add('show');
                backdrop.classList.add('show');
                document.body.style.overflow = 'hidden';
            }

            function closeSidebar() {
                sidebar.classList.remove('show');
                backdrop.classList.remove('show');
                document.body.style.overflow = '';
            }

            if (toggleBtn) toggleBtn.onclick = (e) => { e.preventDefault(); openSidebar(); };
            if (closeBtn) closeBtn.onclick = (e) => { e.preventDefault(); closeSidebar(); };
            backdrop.onclick = closeSidebar;

            sidebar.querySelectorAll('a.sidebar-menu-link').forEach(link => {
                link.addEventListener('click', () => {
                    if (window.innerWidth < 992) closeSidebar();
                });
            });
        }
        document.addEventListener('DOMContentLoaded', initSidebarToggle);
        document.addEventListener('livewire:navigated', initSidebarToggle);
    </script>
    @livewireScripts
    @stack('scripts')
</body>
</html>

