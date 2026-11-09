<div>
    <!-- Banner Promosi / Info Koperasi -->
    <div class="card card-custom border-0 text-white mb-4 overflow-hidden position-relative" style="background: linear-gradient(135deg, #16834A 0%, #0d522e 100%); border-radius: 18px;">
        <div class="card-body p-4 p-md-5 position-relative" style="z-index: 2;">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <span class="badge bg-kop-yellow text-dark fw-bold mb-2 px-3 py-1 shadow-sm">
                        <i class="bi bi-star-fill me-1"></i> Koperasi Digital Pegawai
                    </span>
                    <h3 class="fw-bold mb-2 text-white">Belanja Kebutuhan Kantor & Harian Lebih Cepat!</h3>
                    <p class="mb-3 text-white-50 small pe-md-4">
                        Pesan kebutuhan Anda secara online, bayar via QRIS atau Tunai, dan ambil langsung di kasir atau diantar ke meja kerja Anda.
                    </p>
                    <div class="d-flex gap-2">
                        <a href="{{ route('customer.products') }}" class="btn btn-kop-yellow btn-sm px-3 fw-bold rounded-pill shadow-sm" wire:navigate>
                            <i class="bi bi-grid-fill me-1"></i> Jelajahi Katalog
                        </a>
                        <a href="{{ route('customer.orders') }}" class="btn btn-outline-light btn-sm px-3 rounded-pill" wire:navigate>
                            <i class="bi bi-receipt me-1"></i> Cek Pesanan
                        </a>
                    </div>
                </div>
                <div class="col-md-4 d-none d-md-flex justify-content-center">
                    <div class="text-center opacity-75">
                        <i class="bi bi-bag-heart fs-1" style="font-size: 5.5rem !important;"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- Decorative Circle Accents -->
        <div style="position: absolute; right: -40px; bottom: -40px; width: 220px; height: 220px; background: rgba(255,255,255,0.06); border-radius: 50%;"></div>
        <div style="position: absolute; right: 140px; top: -60px; width: 140px; height: 140px; background: rgba(245,197,66,0.1); border-radius: 50%;"></div>
    </div>

    <!-- Search Bar with Instant Debounce -->
    <div class="card card-custom border-0 p-2 mb-4 shadow-sm" style="border-radius: 16px;">
        <div class="input-group">
            <span class="input-group-text bg-transparent border-0 text-muted ps-3"><i class="bi bi-search fs-5 text-kop-primary"></i></span>
            <input type="text" 
                   wire:model.live.debounce.300ms="search" 
                   class="form-control border-0 ps-2 shadow-none" 
                   placeholder="Cari produk: kopi, air mineral, kertas HVS, snack..." 
                   style="font-size: 0.95rem;">
            @if($search)
                <button class="btn btn-link text-muted text-decoration-none pe-3" wire:click="$set('search', '')">
                    <i class="bi bi-x-circle-fill fs-5"></i>
                </button>
            @endif
        </div>
    </div>

    <!-- Kategori Produk (Horizontally scrollable on mobile) -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="fw-bold text-dark mb-0"><i class="bi bi-grid me-2 text-kop-primary"></i>Kategori Pilihan</h6>
            <a href="{{ route('customer.products') }}" class="small text-kop-primary fw-semibold text-decoration-none" wire:navigate>
                Lihat Semua <i class="bi bi-chevron-right small"></i>
            </a>
        </div>
        <div class="d-flex gap-2 overflow-auto py-2 no-scrollbar">
            <button type="button" 
                    wire:click="selectCategory(null)" 
                    class="cat-pill {{ is_null($selectedCategory) ? 'active' : '' }}">
                <i class="bi bi-collection"></i> Semua Kategori
            </button>
            @foreach($categories as $category)
                <button type="button" 
                        wire:click="selectCategory({{ $category->id }})" 
                        class="cat-pill {{ $selectedCategory == $category->id ? 'active' : '' }}">
                    <i class="bi {{ $category->icon ?? 'bi-tag' }}"></i> {{ $category->name }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Shimmer Skeleton Placeholder Grid while Loading Search / Filter -->
    <div wire:loading.grid wire:target="search, selectedCategory, selectCategory" class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3 mb-4">
        @for($i = 0; $i < 8; $i++)
            <div class="col">
                <div class="skeleton-card">
                    <div class="skeleton-box skeleton-image"></div>
                    <div class="skeleton-box skeleton-sub" style="width: 40%;"></div>
                    <div class="skeleton-box skeleton-title" style="width: 85%;"></div>
                    <div class="skeleton-box skeleton-sub" style="width: 60%; margin-bottom: 16px;"></div>
                    <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top">
                        <div class="skeleton-box skeleton-price"></div>
                        <div class="skeleton-box skeleton-btn"></div>
                    </div>
                </div>
            </div>
        @endfor
    </div>

    <!-- Real Content Area (Hidden while loading search/category) -->
    <div wire:loading.remove wire:target="search, selectedCategory, selectCategory">
        <!-- Produk Populer / Pilihan (Only when not searching) -->
        @if(!$search && is_null($selectedCategory) && $popularProducts->count() > 0)
            <div class="mb-4">
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-fire me-2 text-danger"></i>Produk Paling Banyak Dibeli</h6>
                <div class="row row-cols-2 row-cols-md-4 g-3">
                    @foreach($popularProducts as $pop)
                        <div class="col">
                            <div class="product-card-modern">
                                <div class="product-img-box">
                                    @if($pop->image)
                                        <img src="{{ asset('storage/' . $pop->image) }}" alt="{{ $pop->name }}" loading="lazy">
                                    @else
                                        <i class="bi bi-box-seam text-secondary fs-1 opacity-50"></i>
                                    @endif

                                    @if($pop->stock <= 0)
                                        <span class="badge bg-danger position-absolute top-0 end-0 m-2 rounded-pill small">Habis</span>
                                    @elseif($pop->is_low_stock)
                                        <span class="badge bg-warning text-dark position-absolute top-0 end-0 m-2 rounded-pill small">Sisa {{ $pop->stock }}</span>
                                    @endif
                                </div>
                                <span class="badge bg-kop-light text-kop-primary rounded-pill mb-1 small text-truncate" style="max-width: fit-content; font-size: 0.65rem;">
                                    {{ $pop->category?->name ?? 'Koperasi' }}
                                </span>
                                <h6 class="fw-bold text-dark mb-1 small text-clamp-2" title="{{ $pop->name }}">{{ $pop->name }}</h6>
                                <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top">
                                    <div>
                                        <span class="fw-bold text-kop-primary small">{{ $pop->formatted_price }}</span>
                                        <small class="text-muted d-block" style="font-size: 0.65rem;">/ {{ $pop->unit?->symbol }}</small>
                                    </div>
                                    <button type="button" 
                                            wire:click="addToCart({{ $pop->id }})" 
                                            class="btn btn-kop-primary btn-sm rounded-circle p-0 d-flex align-items-center justify-content-center shadow-sm" 
                                            style="width: 32px; height: 32px;" 
                                            title="Tambah ke Keranjang"
                                            {{ $pop->stock < 1 ? 'disabled' : '' }}>
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Daftar Katalog Produk -->
        <div>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-dark mb-0">
                    <i class="bi bi-bag-check me-2 text-kop-primary"></i>
                    {{ $search ? "Hasil Pencarian: \"$search\"" : "Daftar Produk Koperasi" }}
                </h6>
                <span class="text-muted small">{{ $products->count() }} produk</span>
            </div>

            @if($products->isEmpty())
                <div class="card card-custom text-center p-5 border-0 bg-white">
                    <i class="bi bi-inbox fs-1 text-muted mb-2"></i>
                    <h6 class="fw-bold text-muted">Produk Tidak Ditemukan</h6>
                    <p class="text-muted small mb-0">Silakan coba kata kunci pencarian lain atau pilih kategori lain.</p>
                </div>
            @else
                <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3">
                    @foreach($products as $product)
                        <div class="col">
                            <div class="product-card-modern">
                                <!-- Image container -->
                                <div class="product-img-box">
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" loading="lazy">
                                    @else
                                        <i class="bi bi-box-seam text-secondary fs-1 opacity-50"></i>
                                    @endif

                                    @if($product->stock <= 0)
                                        <span class="badge bg-danger position-absolute top-0 end-0 m-2 rounded-pill small">Habis</span>
                                    @elseif($product->is_low_stock)
                                        <span class="badge bg-warning text-dark position-absolute top-0 end-0 m-2 rounded-pill small">Sisa {{ $product->stock }}</span>
                                    @endif
                                </div>

                                <span class="badge bg-kop-light text-kop-primary rounded-pill mb-1 small text-truncate" style="max-width: fit-content; font-size: 0.68rem;">
                                    {{ $product->category?->name ?? 'Koperasi' }}
                                </span>
                                <h6 class="fw-bold text-dark mb-1 small text-clamp-2" title="{{ $product->name }}">
                                    {{ $product->name }}
                                </h6>
                                <p class="text-muted mb-2 small text-truncate" style="font-size: 0.72rem;">
                                    {{ $product->description ?: 'Produk resmi E-Koperasi' }}
                                </p>

                                <div class="d-flex justify-content-between align-items-end mt-auto pt-2 border-top">
                                    <div>
                                        <span class="fw-bold text-kop-primary">{{ $product->formatted_price }}</span>
                                        <small class="text-muted d-block" style="font-size: 0.68rem;">
                                            Stok: <span class="{{ $product->stock > 0 ? 'text-success fw-semibold' : 'text-danger' }}">{{ $product->stock }} {{ $product->unit?->symbol }}</span>
                                        </small>
                                    </div>
                                    <button type="button" 
                                            wire:click="addToCart({{ $product->id }})" 
                                            class="btn btn-kop-primary btn-sm rounded-pill px-3 py-1 fw-semibold {{ $product->stock < 1 ? 'disabled' : '' }}"
                                            {{ $product->stock < 1 ? 'disabled' : '' }}>
                                        <i class="bi bi-cart-plus me-1"></i> Beli
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
