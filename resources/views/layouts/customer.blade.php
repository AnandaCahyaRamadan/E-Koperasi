<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'E-Koperasi') - Koperasi Pegawai</title>
    @include('partials.styles-common')
    @livewireStyles
    @stack('styles')
</head>
<body class="bg-light pb-5 mb-4">
    <!-- Top Progress Bar for Livewire navigation -->
    <div class="top-nav-loader" id="page-nav-loader"></div>

    <!-- Customer Top Navbar -->
    <nav class="navbar navbar-expand-lg sticky-top customer-navbar py-2">
        <div class="container d-flex align-items-center justify-content-between flex-nowrap gap-3">
            <a class="navbar-brand d-flex align-items-center gap-2 text-dark fw-bold min-w-0 flex-shrink-1 me-0 py-0" href="{{ route('customer.home') }}" wire:navigate>
                <div class="bg-kop-primary text-white rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 34px; height: 34px;">
                    <i class="bi bi-shop-window fs-6"></i>
                </div>
                <span class="text-truncate" style="font-size: clamp(0.95rem, 2.2vw, 1.08rem); letter-spacing: -0.015em;">{{ \App\Models\Setting::get('koperasi_name', 'E-Koperasi') }}</span>
            </a>

            <!-- Desktop Nav Links -->
            <div class="d-none d-lg-flex align-items-center gap-1 mx-auto">
                <a href="{{ route('customer.home') }}" class="cust-nav-link {{ request()->routeIs('customer.home') ? 'active' : '' }}" wire:navigate>
                    <i class="bi bi-house-door"></i> Beranda
                </a>
                <a href="{{ route('customer.products') }}" class="cust-nav-link {{ request()->routeIs('customer.products*') ? 'active' : '' }}" wire:navigate>
                    <i class="bi bi-grid"></i> Katalog Produk
                </a>
                <a href="{{ route('customer.orders') }}" class="cust-nav-link {{ request()->routeIs('customer.orders*') ? 'active' : '' }}" wire:navigate>
                    <i class="bi bi-receipt"></i> Pesanan Saya
                </a>
            </div>

            <div class="d-flex align-items-center gap-3 flex-shrink-0">
                <!-- Cart Icon Button -->
                <a href="{{ route('customer.cart') }}" class="cust-cart-link position-relative flex-shrink-0" wire:navigate aria-label="Keranjang Belanja">
                    <i class="bi bi-cart3 fs-5"></i>
                    @php
                        $cartCount = \App\Models\Cart::countForUser();
                    @endphp
                    <span id="desktop-cart-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger {{ $cartCount > 0 ? '' : 'd-none' }}" style="font-size: 0.65rem;">
                        {{ $cartCount }}
                    </span>
                </a>

                @auth
                    <!-- Profile dropdown with Alpine.js and clean styling -->
                    <div class="dropdown" x-data="{ open: false }" @click.outside="open = false">
                        <button class="btn btn-user-pill dropdown-toggle d-flex align-items-center gap-2" 
                                type="button" 
                                @click="open = !open" 
                                data-bs-toggle="dropdown" 
                                aria-expanded="false">
                            <div class="bg-kop-light text-kop-primary rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 26px; height: 26px; font-size: 0.72rem;">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <span class="d-none d-md-inline small fw-semibold text-dark text-truncate" style="max-width: 140px;">{{ auth()->user()->name }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" 
                            :class="{ 'show': open }" 
                            :style="open ? 'display: block; position: absolute; right: 0; top: 100%; z-index: 1050;' : ''">
                            <li class="dropdown-header">
                                <div class="fw-bold text-dark">{{ auth()->user()->name }}</div>
                                <small class="text-muted">{{ auth()->user()->employee_id ?? auth()->user()->email }}</small>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('customer.orders') }}" wire:navigate><i class="bi bi-bag-check me-2 text-kop-primary"></i> Riwayat Pesanan</a></li>
                            <li><a class="dropdown-item" href="{{ route('customer.profile') }}" wire:navigate><i class="bi bi-person me-2 text-kop-primary"></i> Profil Saya</a></li>
                            @if(auth()->user()->isAdmin())
                                <li><a class="dropdown-item text-success fw-semibold" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i> Panel Admin</a></li>
                            @elseif(auth()->user()->isPetugas())
                                <li><a class="dropdown-item text-warning fw-semibold text-dark" href="{{ route('petugas.dashboard') }}"><i class="bi bi-calculator me-2"></i> Panel Kasir</a></li>
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
                    <a href="{{ route('login') }}" class="btn btn-kop-primary btn-sm rounded-pill px-3 text-nowrap">Masuk</a>
                @endauth
            </div>
        </div>
    </nav>

    @include('partials.flash-toasts')

    <!-- Main Content -->
    <main class="container my-4 pt-2 pb-3">
        {{ $slot ?? '' }}
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
            <span id="mobile-cart-badge" class="nav-badge-count {{ ($cartCount ?? 0) > 0 ? '' : 'd-none' }}">{{ $cartCount ?? 0 }}</span>
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

    <!-- Bootstrap 5 Bundle JS (Offline) -->
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    @include('partials.scripts-common')
    <script>
        document.addEventListener('livewire:navigating', () => {
            const loader = document.getElementById('page-nav-loader');
            if (loader) loader.style.display = 'block';
        });
        document.addEventListener('livewire:navigated', () => {
            const loader = document.getElementById('page-nav-loader');
            if (loader) loader.style.display = 'none';
        });

        function updateCustomerCartBadges(count) {
            const total = parseInt(count ?? 0, 10) || 0;
            ['desktop-cart-badge', 'mobile-cart-badge'].forEach((id) => {
                const badge = document.getElementById(id);
                if (!badge) return;
                badge.textContent = total;
                badge.classList.toggle('d-none', total <= 0);
            });
        }

        if (!window._cartBadgeListenerBound) {
            window._cartBadgeListenerBound = true;
            document.addEventListener('livewire:init', () => {
                Livewire.on('cart-updated', (payload) => {
                    const data = Array.isArray(payload) ? payload[0] : payload;
                    const count = (data && typeof data === 'object' && 'count' in data) ? data.count : data;
                    if (count !== undefined && count !== null) {
                        updateCustomerCartBadges(count);
                    }
                });
            });
        }
    </script>
    @livewireScripts
    @stack('scripts')
</body>
</html>

