<div>
    <!-- Header Page -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
        <div>
            <h5 class="fw-bold text-dark mb-1"><i class="bi bi-grid-3x3-gap-fill text-kop-primary me-2"></i>Katalog Produk Koperasi</h5>
            <p class="text-muted small mb-0">Temukan aneka barang kebutuhan kantor, minuman, makanan, dan sembako</p>
        </div>
        <div class="text-muted small">
            Menampilkan <span class="fw-bold text-dark">{{ $products->total() }}</span> produk
        </div>
    </div>

    <!-- Filter & Search Controls -->
    <div class="card card-custom border-0 p-3 mb-4 shadow-sm">
        <div class="row g-2">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" wire:model.live.debounce.350ms="search" class="form-control border-start-0 ps-0" placeholder="Ketik nama atau kode produk...">
                </div>
            </div>
            <div class="col-6 col-md-4">
                <select wire:model.live="category" class="form-select">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3">
                <select wire:model.live="sort" class="form-select">
                    <option value="latest">Terbaru</option>
                    <option value="price_asc">Harga: Terendah</option>
                    <option value="price_desc">Harga: Tertinggi</option>
                    <option value="stock_desc">Stok Terbanyak</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Product Grid -->
    @if($products->isEmpty())
        <div class="card card-custom text-center p-5 border-0 bg-white shadow-sm">
            <i class="bi bi-bag-x fs-1 text-muted mb-2"></i>
            <h6 class="fw-bold text-muted">Tidak Ada Produk Yang Cocok</h6>
            <p class="text-muted small mb-3">Coba gunakan kata kunci lain atau bersihkan filter Anda.</p>
            <div>
                <button wire:click="$set('search', ''); $set('category', '');" class="btn btn-outline-kop btn-sm">Reset Filter</button>
            </div>
        </div>
    @else
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3 mb-4">
            @foreach($products as $product)
                <div class="col">
                    <div class="card card-custom h-100 p-3 d-flex flex-column border-0">
                        <!-- Image Container with click for detail -->
                        <div role="button" wire:click="showProductDetail({{ $product->id }})" class="bg-light rounded-3 d-flex align-items-center justify-content-center p-3 mb-2 position-relative cursor-pointer" style="height: 140px;">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid" style="max-height: 120px; object-fit: contain;">
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
                        <h6 role="button" wire:click="showProductDetail({{ $product->id }})" class="fw-bold text-dark mb-1 small text-decoration-none" style="line-height: 1.3;" title="{{ $product->name }}">
                            {{ $product->name }}
                        </h6>
                        <small class="text-muted text-uppercase mb-2" style="font-size: 0.65rem;">Kode: {{ $product->code }}</small>

                        <div class="d-flex justify-content-between align-items-end mt-auto pt-2 border-top">
                            <div>
                                <span class="fw-bold text-kop-primary">{{ $product->formatted_price }}</span>
                                <small class="text-muted d-block" style="font-size: 0.68rem;">
                                    Tersedia: <span class="{{ $product->stock > 0 ? 'text-success fw-semibold' : 'text-danger' }}">{{ $product->stock }} {{ $product->unit?->symbol }}</span>
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

        <div class="d-flex justify-content-center">
            {{ $products->links() }}
        </div>
    @endif

    <!-- Product Detail Modal (Livewire Controlled) -->
    @if($selectedProduct)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 card-custom">
                    <div class="modal-header border-0 pb-0">
                        <h6 class="modal-title fw-bold text-dark"><i class="bi bi-info-circle text-kop-primary me-2"></i>Detail Produk</h6>
                        <button type="button" class="btn-close" wire:click="closeProductDetail"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center bg-light rounded-3 p-4 mb-3">
                            @if($selectedProduct->image)
                                <img src="{{ asset('storage/' . $selectedProduct->image) }}" alt="{{ $selectedProduct->name }}" class="img-fluid" style="max-height: 180px; object-fit: contain;">
                            @else
                                <i class="bi bi-box-seam text-secondary fs-1 opacity-50" style="font-size: 4rem !important;"></i>
                            @endif
                        </div>
                        <span class="badge bg-kop-light text-kop-primary rounded-pill mb-2">
                            {{ $selectedProduct->category?->name ?? 'Umum' }}
                        </span>
                        <h5 class="fw-bold text-dark mb-1">{{ $selectedProduct->name }}</h5>
                        <p class="text-muted small mb-2">SKU / Kode: <strong>{{ $selectedProduct->code }}</strong></p>
                        
                        <div class="d-flex align-items-baseline gap-2 mb-3">
                            <h4 class="fw-bold text-kop-primary mb-0">{{ $selectedProduct->formatted_price }}</h4>
                            <span class="text-muted small">/ {{ $selectedProduct->unit?->symbol ?? 'pcs' }}</span>
                        </div>

                        <div class="card bg-light border-0 p-3 mb-3">
                            <span class="fw-semibold small text-dark mb-1">Deskripsi:</span>
                            <p class="small text-muted mb-0">{{ $selectedProduct->description ?: 'Tidak ada deskripsi rinci untuk produk ini.' }}</p>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="small text-muted">Status Ketersediaan:</span>
                            @if($selectedProduct->stock > 0)
                                <span class="badge bg-success-subtle text-success fw-bold px-3 py-1">Tersedia {{ $selectedProduct->stock }} {{ $selectedProduct->unit?->symbol }}</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger fw-bold px-3 py-1">Stok Habis</span>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light" wire:click="closeProductDetail">Tutup</button>
                        <button type="button" 
                                wire:click="addToCart({{ $selectedProduct->id }})" 
                                class="btn btn-kop-primary fw-semibold {{ $selectedProduct->stock < 1 ? 'disabled' : '' }}"
                                {{ $selectedProduct->stock < 1 ? 'disabled' : '' }}>
                            <i class="bi bi-cart-plus me-1"></i> Tambahkan ke Keranjang
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

