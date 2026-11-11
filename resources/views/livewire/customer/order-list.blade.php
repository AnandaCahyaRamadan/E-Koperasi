<div>
    <!-- Page Header -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <h5 class="fw-bold text-dark mb-1">Pesanan Saya</h5>
            <p class="text-muted small mb-0">Riwayat transaksi dan status pengantaran belanja Anda.</p>
        </div>
        <a href="{{ route('customer.products') }}" class="btn btn-kop-primary btn-sm px-3 py-2 align-self-start align-self-sm-auto" style="border-radius: 7px;" wire:navigate>
            <i class="bi bi-plus-lg me-1"></i> Belanja Baru
        </a>
    </div>

    <!-- Filter & Search Bar -->
    <div class="card card-custom bg-white p-3 mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div class="d-flex gap-2 overflow-auto pb-1 pb-md-0 no-scrollbar">
                <button type="button" wire:click="setFilter('all')" class="cat-pill {{ $filterStatus === 'all' ? 'active' : '' }}">
                    Semua
                </button>
                <button type="button" wire:click="setFilter('pending_payment')" class="cat-pill {{ $filterStatus === 'pending_payment' ? 'active' : '' }}">
                    Menunggu Bayar
                </button>
                <button type="button" wire:click="setFilter('processing')" class="cat-pill {{ $filterStatus === 'processing' ? 'active' : '' }}">
                    Sedang Diproses
                </button>
                <button type="button" wire:click="setFilter('completed')" class="cat-pill {{ $filterStatus === 'completed' ? 'active' : '' }}">
                    Selesai
                </button>
                <button type="button" wire:click="setFilter('cancelled')" class="cat-pill {{ $filterStatus === 'cancelled' ? 'active' : '' }}">
                    Dibatalkan
                </button>
            </div>

            <div class="input-group input-group-sm flex-md-shrink-0" style="max-width: 280px;">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" wire:model.live.debounce.300ms="search" class="form-control bg-white border-start-0" placeholder="Cari no. pesanan atau produk...">
                @if($search)
                    <button type="button" class="btn btn-white border border-start-0 text-muted" wire:click="$set('search', '')">
                        <i class="bi bi-x"></i>
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Orders Content -->
    <div wire:loading.class="opacity-50" wire:target="setFilter, search, previousPage, nextPage, gotoPage" class="transition-opacity">
        @if($orders->isEmpty())
            <div class="card card-custom text-center p-5 bg-white my-3">
                <i class="bi bi-clipboard-x fs-1 text-muted opacity-50 mb-2"></i>
                <h6 class="fw-bold text-dark mb-1">Belum Ada Pesanan</h6>
                <p class="text-muted small mb-3">Tidak ada transaksi yang sesuai dengan filter saat ini.</p>
                <div>
                    <a href="{{ route('customer.products') }}" class="btn btn-outline-kop btn-sm px-4" style="border-radius: 7px;" wire:navigate>Mulai Berbelanja</a>
                </div>
            </div>
        @else
            <div class="d-flex flex-column gap-3 mb-4">
                @foreach($orders as $order)
                    @php
                        $firstItem = $order->items->first();
                        $firstProductImg = $firstItem?->product?->image;
                        $extraCount = max(0, $order->items->count() - 1);
                    @endphp
                    <div class="card card-custom bg-white overflow-hidden">
                        <!-- Card Header Strip -->
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 px-3 py-2 border-bottom" style="background-color: #fafbfc;">
                            <div class="d-flex flex-wrap align-items-center gap-2 small">
                                <span class="fw-bold text-dark" style="font-variant-numeric: tabular-nums;">{{ $order->order_number }}</span>
                                <span class="text-muted">&middot;</span>
                                <span class="text-muted">{{ $order->created_at->format('d M Y, H:i') }}</span>
                                <span class="text-muted d-none d-sm-inline">&middot;</span>
                                <span class="text-muted d-none d-sm-inline">{{ $order->order_type_label }} ({{ strtoupper($order->payment_method) }})</span>
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <span class="badge {{ $order->payment_status_badge_class }}" style="font-size: 0.72rem; font-weight: 500; border-radius: 6px; padding: 0.35em 0.65em;">
                                    {{ $order->payment_status_label }}
                                </span>
                                <span class="badge {{ $order->status_badge_class }}" style="font-size: 0.72rem; font-weight: 500; border-radius: 6px; padding: 0.35em 0.65em;">
                                    {{ $order->status_label }}
                                </span>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="p-3 d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                            <div class="d-flex align-items-center gap-3 min-w-0">
                                <div class="rounded border bg-light d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px;">
                                    @if($firstProductImg)
                                        <img src="{{ asset('storage/' . $firstProductImg) }}" alt="{{ $firstItem->product_name }}" class="img-fluid rounded" style="max-height: 40px; object-fit: contain;">
                                    @else
                                        <i class="bi bi-box-seam text-secondary fs-5"></i>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <div class="fw-semibold text-dark text-truncate" style="font-size: 0.9rem;">
                                        {{ $firstItem?->product_name ?? 'Produk Koperasi' }}
                                    </div>
                                    <div class="small text-muted">
                                        @if($firstItem)
                                            {{ $firstItem->quantity }} &times; Rp {{ number_format($firstItem->price, 0, ',', '.') }}
                                        @endif
                                        @if($extraCount > 0)
                                            <span class="text-kop-primary fw-medium ms-1">(+{{ $extraCount }} produk lainnya)</span>
                                        @endif
                                    </div>
                                    <div class="small text-muted d-sm-none mt-1">
                                        {{ $order->order_type_label }} &middot; {{ strtoupper($order->payment_method) }}
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-between justify-content-sm-end gap-3 pt-2 pt-sm-0 border-top border-sm-0 flex-shrink-0">
                                <div class="text-sm-end">
                                    <div class="text-muted" style="font-size: 0.72rem;">Total Belanja</div>
                                    <div class="fw-bold text-dark" style="font-size: 0.95rem; font-variant-numeric: tabular-nums;">
                                        Rp {{ number_format($order->final_amount, 0, ',', '.') }}
                                    </div>
                                </div>
                                <a href="{{ route('customer.orders.detail', $order->id) }}" class="btn btn-outline-kop btn-sm px-3 fw-semibold text-nowrap" style="border-radius: 7px;" wire:navigate>
                                    Lihat Rincian
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-2">
                {{ $orders->links('partials.pagination') }}
            </div>
        @endif
    </div>
</div>
