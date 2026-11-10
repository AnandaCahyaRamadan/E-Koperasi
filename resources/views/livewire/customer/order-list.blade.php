<div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold text-dark mb-1"><i class="bi bi-receipt-cutoff text-kop-primary me-2"></i>Pesanan Saya</h5>
            <p class="text-muted small mb-0">Pantau proses belanja dan status pengantaran kebutuhan Anda</p>
        </div>
        <a href="{{ route('customer.products') }}" class="btn btn-kop-primary btn-sm rounded-pill px-3 shadow-sm" wire:navigate>
            <i class="bi bi-plus-lg me-1"></i> Belanja Baru
        </a>
    </div>

    <!-- Filter Tabs (Horizontal Scrollable) -->
    <div class="d-flex gap-2 overflow-auto pb-2 mb-3 no-scrollbar">
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

    <!-- Orders Content with smooth loading state -->
    <div wire:loading.class="opacity-50" wire:target="setFilter, previousPage, nextPage, gotoPage" class="transition-opacity">
        @if($orders->isEmpty())
            <div class="card card-custom text-center p-5 border-0 bg-white shadow-sm my-3" style="border-radius: 16px;">
                <i class="bi bi-clipboard-x fs-1 text-muted mb-2"></i>
                <h6 class="fw-bold text-muted">Belum Ada Pesanan</h6>
                <p class="text-muted small mb-3">Pesanan Anda dengan filter ini belum tersedia.</p>
                <div>
                    <a href="{{ route('customer.products') }}" class="btn btn-outline-kop btn-sm rounded-pill px-4" wire:navigate>Mulai Berbelanja</a>
                </div>
            </div>
        @else
            <div class="d-flex flex-column gap-3 mb-4">
                @foreach($orders as $order)
                    <div class="card card-custom border-0 p-3 shadow-sm" style="border-radius: 16px;">
                        <div class="d-flex flex-wrap justify-content-between align-items-center border-bottom pb-2 mb-2 gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-kop-light text-kop-primary fw-bold">{{ $order->order_number }}</span>
                                <span class="text-muted small" style="font-size: 0.72rem;">{{ $order->created_at->format('d M Y H:i') }}</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge {{ $order->payment_status_badge_class }}" style="font-size: 0.72rem;">
                                    {{ $order->payment_status_label }}
                                </span>
                                <span class="badge {{ $order->status_badge_class }}" style="font-size: 0.72rem;">
                                    {{ $order->status_label }}
                                </span>
                            </div>
                        </div>

                        <div class="row align-items-center g-2">
                            <div class="col-12 col-md-7">
                                <div class="small fw-semibold text-dark mb-1">
                                    {{ $order->items->first()?->product_name ?? 'Produk Koperasi' }}
                                    @if($order->items->count() > 1)
                                        <span class="text-muted fw-normal">+{{ $order->items->count() - 1 }} item lainnya</span>
                                    @endif
                                </div>
                                <small class="text-muted d-block">
                                    Layanan: <strong class="text-dark">{{ $order->order_type_label }}</strong> 
                                    &bull; Pembayaran: <strong class="text-dark">{{ strtoupper($order->payment_method) }}</strong>
                                </small>
                            </div>
                            <div class="col-7 col-md-3 text-start text-md-end">
                                <small class="text-muted d-block" style="font-size: 0.72rem;">Total Tagihan:</small>
                                <span class="fw-bold text-kop-primary fs-6">Rp {{ number_format($order->final_amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="col-5 col-md-2 text-end">
                                <a href="{{ route('customer.orders.detail', $order->id) }}" class="btn btn-kop-primary btn-sm w-100 rounded-pill fw-semibold shadow-sm" wire:navigate>
                                    Rincian <i class="bi bi-chevron-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-center">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
