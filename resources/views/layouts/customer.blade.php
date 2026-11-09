<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'E-Koperasi') - Koperasi Pegawai</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Custom Style -->
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
    @livewireStyles
    @stack('styles')
</head>
<body class="bg-light pb-5 mb-4">
    <!-- Customer Top Navbar -->
    <nav class="navbar navbar-expand-lg bg-white sticky-top border-bottom py-2 shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 text-kop-primary fw-bold" href="{{ route('customer.home') }}" wire:navigate>
                <div class="bg-kop-light text-kop-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                    <i class="bi bi-shop-window fs-5"></i>
                </div>
                <span>{{ \App\Models\Setting::get('koperasi_name', 'E-Koperasi') }}</span>
            </a>

            <!-- Desktop Nav Links -->
            <div class="d-none d-lg-flex align-items-center gap-4">
                <a href="{{ route('customer.home') }}" class="text-decoration-none fw-semibold {{ request()->routeIs('customer.home') ? 'text-kop-primary' : 'text-secondary' }}" wire:navigate>
                    <i class="bi bi-house-door me-1"></i> Beranda
                </a>
                <a href="{{ route('customer.products') }}" class="text-decoration-none fw-semibold {{ request()->routeIs('customer.products*') ? 'text-kop-primary' : 'text-secondary' }}" wire:navigate>
                    <i class="bi bi-grid me-1"></i> Katalog Produk
                </a>
                <a href="{{ route('customer.orders') }}" class="text-decoration-none fw-semibold {{ request()->routeIs('customer.orders*') ? 'text-kop-primary' : 'text-secondary' }}" wire:navigate>
                    <i class="bi bi-receipt me-1"></i> Pesanan Saya
                </a>
            </div>

            <div class="d-flex align-items-center gap-3">
                <!-- Cart Icon Button -->
                <a href="{{ route('customer.cart') }}" class="btn btn-light position-relative rounded-pill px-3 py-2 border" wire:navigate>
                    <i class="bi bi-cart3 fs-5 text-kop-primary"></i>
                    @php
                        $cartCount = auth()->check() && auth()->user()->cart ? auth()->user()->cart->items()->sum('quantity') : 0;
                    @endphp
                    @if($cartCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ $cartCount }}
                        </span>
                    @endif
                </a>

                @auth
                    <!-- Profile dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary rounded-pill dropdown-toggle d-flex align-items-center gap-2 py-1 px-3" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle fs-5 text-kop-primary"></i>
                            <span class="d-none d-md-inline small fw-semibold">{{ auth()->user()->name }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                            <li class="dropdown-header">
                                <div class="fw-bold text-dark">{{ auth()->user()->name }}</div>
                                <small class="text-muted">{{ auth()->user()->employee_id ?? auth()->user()->email }}</small>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('customer.orders') }}" wire:navigate><i class="bi bi-bag-check me-2 text-kop-primary"></i> Riwayat Pesanan</a></li>
                            <li><a class="dropdown-item" href="{{ route('customer.profile') }}" wire:navigate><i class="bi bi-person me-2 text-kop-primary"></i> Profil Saya</a></li>
                            @if(auth()->user()->isAdmin())
                                <li><a class="dropdown-item text-primary" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i> Panel Admin</a></li>
                            @elseif(auth()->user()->isPetugas())
                                <li><a class="dropdown-item text-primary" href="{{ route('petugas.dashboard') }}"><i class="bi bi-calculator me-2"></i> Panel Kasir</a></li>
                            @endif
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
                @else
                    <a href="{{ route('login') }}" class="btn btn-kop-primary rounded-pill px-4">Masuk</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Global Alert / Messages -->
    <div class="container mt-3">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show card-custom border-0" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show card-custom border-0" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <main class="container my-3">
        @yield('content')
    </main>

    <!-- Mobile Bottom Navigation (Always Visible on Mobile / Small Devices) -->
    <nav class="bottom-nav d-lg-none">
        <a href="{{ route('customer.home') }}" class="bottom-nav-item {{ request()->routeIs('customer.home') ? 'active' : '' }}" wire:navigate>
            <i class="bi bi-house-door{{ request()->routeIs('customer.home') ? '-fill' : '' }}"></i>
            <span>Beranda</span>
        </a>
        <a href="{{ route('customer.products') }}" class="bottom-nav-item {{ request()->routeIs('customer.products*') ? 'active' : '' }}" wire:navigate>
            <i class="bi bi-grid{{ request()->routeIs('customer.products*') ? '-fill' : '' }}"></i>
            <span>Produk</span>
        </a>
        <a href="{{ route('customer.cart') }}" class="bottom-nav-item position-relative {{ request()->routeIs('customer.cart*') ? 'active' : '' }}" wire:navigate>
            <i class="bi bi-cart{{ request()->routeIs('customer.cart*') ? '-fill' : '' }}"></i>
            @if(isset($cartCount) && $cartCount > 0)
                <span class="nav-badge-count">{{ $cartCount }}</span>
            @endif
            <span>Keranjang</span>
        </a>
        <a href="{{ route('customer.orders') }}" class="bottom-nav-item {{ request()->routeIs('customer.orders*') ? 'active' : '' }}" wire:navigate>
            <i class="bi bi-receipt{{ request()->routeIs('customer.orders*') ? '-fill' : '' }}"></i>
            <span>Pesanan</span>
        </a>
        <a href="{{ route('customer.profile') }}" class="bottom-nav-item {{ request()->routeIs('customer.profile*') ? 'active' : '' }}" wire:navigate>
            <i class="bi bi-person{{ request()->routeIs('customer.profile*') ? '-fill' : '' }}"></i>
            <span>Profil</span>
        </a>
    </nav>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @livewireScripts
    @stack('scripts')
</body>
</html>

