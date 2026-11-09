<div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold text-dark mb-1"><i class="bi bi-cart3 text-kop-primary me-2"></i>Keranjang Belanja</h5>
            <p class="text-muted small mb-0">Tinjau daftar belanjaan Anda sebelum melanjutkan checkout</p>
        </div>
        @if($items->isNotEmpty())
            <button wire:click="clearCart" wire:confirm="Apakah Anda yakin ingin mengosongkan seluruh keranjang?" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                <i class="bi bi-trash me-1"></i> Kosongkan
            </button>
        @endif
    </div>

    @if($items->isEmpty())
        <div class="card card-custom text-center p-5 border-0 bg-white shadow-sm my-4" style="border-radius: 18px;">
            <div class="mb-3">
                <i class="bi bi-cart-x fs-1 text-muted" style="font-size: 4.5rem !important;"></i>
            </div>
            <h5 class="fw-bold text-dark">Keranjang Belanja Masih Kosong</h5>
            <p class="text-muted small mb-4">Yuk, temukan produk kebutuhan kantor atau camilan favorit Anda di katalog!</p>
            <div>
                <a href="{{ route('customer.products') }}" class="btn btn-kop-primary rounded-pill px-4 shadow-sm" wire:navigate>
                    <i class="bi bi-bag-plus me-1"></i> Belanja Sekarang
                </a>
            </div>
        </div>
    @else
        <div class="row g-4">
            <!-- Items List -->
            <div class="col-lg-8">
                <!-- Mobile List (Cards) -->
                <div class="d-md-none d-flex flex-column gap-3">
                    @foreach($items as $item)
                        <div class="card card-custom border-0 p-3 shadow-sm" style="border-radius: 14px;">
                            <div class="d-flex gap-3">
                                <div class="bg-light rounded-3 p-2 text-center d-flex align-items-center justify-content-center" style="width: 70px; height: 70px; flex-shrink: 0;">
                                    @if($item->product->image)
                                        <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" class="img-fluid" style="max-height: 55px; object-fit: contain;">
                                    @else
                                        <i class="bi bi-box-seam text-secondary fs-3"></i>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <h6 class="fw-bold text-dark mb-1 small">{{ $item->product->name }}</h6>
                                        <button type="button" wire:click="removeItem({{ $item->id }})" class="btn btn-link text-danger p-0 ms-2" title="Hapus produk">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                    <small class="text-muted d-block mb-2">Rp {{ number_format($item->product->sell_price, 0, ',', '.') }} / {{ $item->product->unit?->symbol }}</small>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold text-kop-primary">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                        <div class="d-inline-flex align-items-center border rounded-pill p-1 bg-light">
                                            <button type="button" wire:click="decrementQuantity({{ $item->id }})" class="btn btn-sm btn-white rounded-circle p-0 d-flex align-items-center justify-content-center shadow-sm" style="width: 24px; height: 24px;">
                                                <i class="bi bi-dash small"></i>
                                            </button>
                                            <span class="px-2 fw-bold small">{{ $item->quantity }}</span>
                                            <button type="button" wire:click="incrementQuantity({{ $item->id }})" class="btn btn-sm btn-white rounded-circle p-0 d-flex align-items-center justify-content-center shadow-sm" style="width: 24px; height: 24px;" {{ $item->quantity >= $item->product->stock ? 'disabled' : '' }}>
                                                <i class="bi bi-plus small"></i>
                                            </button>
                                        </div>
                                    </div>

                                    @if($item->quantity > $item->product->stock)
                                        <span class="badge bg-danger-subtle text-danger d-block mt-2 small text-wrap">
                                            Stok sisa {{ $item->product->stock }}! Kurangi kuantitas.
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Desktop Table -->
                <div class="d-none d-md-block card card-custom border-0 p-3 shadow-sm" style="border-radius: 16px;">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr class="text-muted small">
                                    <th>Produk</th>
                                    <th class="text-center" style="width: 140px;">Jumlah</th>
                                    <th class="text-end" style="width: 130px;">Subtotal</th>
                                    <th class="text-center" style="width: 50px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="bg-light rounded-3 p-2 text-center" style="width: 60px; height: 60px; flex-shrink: 0;">
                                                    @if($item->product->image)
                                                        <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" class="img-fluid" style="max-height: 44px; object-fit: contain;">
                                                    @else
                                                        <i class="bi bi-box-seam text-secondary fs-4"></i>
                                                    @endif
                                                </div>
                                                <div>
                                                    <h6 class="fw-bold text-dark mb-0 small">{{ $item->product->name }}</h6>
                                                    <small class="text-muted">Rp {{ number_format($item->product->sell_price, 0, ',', '.') }} / {{ $item->product->unit?->symbol }}</small>
                                                    @if($item->quantity > $item->product->stock)
                                                        <span class="badge bg-danger-subtle text-danger d-block mt-1 small">
                                                            Stok sisa {{ $item->product->stock }}! Kurangi kuantitas.
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-inline-flex align-items-center border rounded-pill p-1 bg-light">
                                                <button type="button" wire:click="decrementQuantity({{ $item->id }})" class="btn btn-sm btn-white rounded-circle p-0 d-flex align-items-center justify-content-center shadow-sm" style="width: 26px; height: 26px;">
                                                    <i class="bi bi-dash"></i>
                                                </button>
                                                <span class="px-3 fw-bold small">{{ $item->quantity }}</span>
                                                <button type="button" wire:click="incrementQuantity({{ $item->id }})" class="btn btn-sm btn-white rounded-circle p-0 d-flex align-items-center justify-content-center shadow-sm" style="width: 26px; height: 26px;" {{ $item->quantity >= $item->product->stock ? 'disabled' : '' }}>
                                                    <i class="bi bi-plus"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="text-end fw-bold text-kop-primary small">
                                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                        </td>
                                        <td class="text-center">
                                            <button type="button" wire:click="removeItem({{ $item->id }})" class="btn btn-link text-danger p-0" title="Hapus produk">
                                                <i class="bi bi-trash fs-5"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-3">
                    <a href="{{ route('customer.products') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3" wire:navigate>
                        <i class="bi bi-arrow-left me-1"></i> Tambah Belanjaan Lain
                    </a>
                </div>
            </div>

            <!-- Order Summary Card -->
            <div class="col-lg-4">
                <div class="card card-custom border-0 p-4 shadow-sm sticky-top" style="top: 80px; border-radius: 18px;">
                    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-receipt me-2 text-kop-primary"></i>Ringkasan Belanja</h6>
                    
                    <div class="d-flex justify-content-between mb-2 small text-muted">
                        <span>Total Item Barang</span>
                        <span class="fw-semibold text-dark">{{ $totalItems }} unit</span>
                    </div>

                    <div class="d-flex justify-content-between mb-3 small text-muted">
                        <span>Subtotal Belanja</span>
                        <span class="fw-bold text-dark">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>

                    <hr class="my-3">

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="fw-bold text-dark">Total Pembayaran</span>
                        <span class="fs-5 fw-bold text-kop-primary">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>

                    @if($hasStockError)
                        <div class="alert alert-danger small mb-3">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i> Beberapa item melebihi stok yang tersedia. Harap sesuaikan kuantitas sebelum checkout.
                        </div>
                    @endif

                    <a href="{{ route('customer.checkout') }}" 
                       class="btn btn-kop-primary w-100 py-2 fw-semibold rounded-pill shadow-sm {{ $hasStockError ? 'disabled' : '' }}" 
                       wire:navigate>
                        Lanjut ke Checkout <i class="bi bi-arrow-right ms-1"></i>
                    </a>

                    <div class="text-center mt-3 text-muted small" style="font-size: 0.72rem;">
                        <i class="bi bi-shield-check text-success me-1"></i> Transaksi Koperasi Aman & Terverifikasi
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
