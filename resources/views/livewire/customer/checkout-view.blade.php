<div>
    <div class="mb-3">
        <a href="{{ route('customer.cart') }}" class="text-decoration-none small text-muted" wire:navigate>
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Keranjang
        </a>
        <h5 class="fw-bold text-dark mt-1"><i class="bi bi-bag-check-fill text-kop-primary me-2"></i>Checkout Pesanan</h5>
    </div>

    @if($items->isEmpty())
        <div class="card card-custom text-center p-5 border-0 bg-white shadow-sm">
            <i class="bi bi-cart-x fs-1 text-muted mb-2"></i>
            <h5 class="fw-bold text-muted">Tidak Ada Item untuk Dicheckout</h5>
            <p class="text-muted small mb-3">Keranjang Anda sedang kosong.</p>
            <div>
                <a href="{{ route('customer.products') }}" class="btn btn-kop-primary rounded-pill px-4" wire:navigate>Buka Katalog</a>
            </div>
        </div>
    @else
        <form wire:submit.prevent="processCheckout">
            <div class="row g-4">
                <!-- Checkout Form -->
                <div class="col-lg-7">
                    <!-- 1. Metode Pengambilan / Pengantaran -->
                    <div class="card card-custom border-0 p-4 mb-3 shadow-sm">
                        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-geo-alt-fill text-kop-primary me-2"></i>1. Pilihan Pengambilan / Pengantaran</h6>
                        
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="card card-custom border p-3 h-100 cursor-pointer {{ $order_type === 'pickup' ? 'border-success bg-kop-light' : 'bg-white' }}" style="cursor: pointer;">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" wire:model.live="order_type" value="pickup" id="typePickup">
                                        <label class="form-check-label fw-bold small" for="typePickup">
                                            Ambil Sendiri
                                        </label>
                                    </div>
                                    <small class="text-muted d-block" style="font-size: 0.72rem;">
                                        Ambil di etalase kasir koperasi kantor setelah pesanan siap.
                                    </small>
                                </label>
                            </div>
                            <div class="col-6">
                                <label class="card card-custom border p-3 h-100 cursor-pointer {{ $order_type === 'delivery' ? 'border-success bg-kop-light' : 'bg-white' }}" style="cursor: pointer;">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" wire:model.live="order_type" value="delivery" id="typeDelivery">
                                        <label class="form-check-label fw-bold small" for="typeDelivery">
                                            Antar ke Ruangan
                                        </label>
                                    </div>
                                    <small class="text-muted d-block" style="font-size: 0.72rem;">
                                        Petugas koperasi akan mengantarkan pesanan ke meja/ruangan Anda.
                                    </small>
                                </label>
                            </div>
                        </div>

                        @if($order_type === 'delivery')
                            <div class="bg-light rounded-3 p-3 mt-2">
                                <h6 class="fw-semibold small text-dark mb-2"><i class="bi bi-info-circle me-1 text-kop-primary"></i>Alamat & Kontak Pengantaran</h6>
                                
                                <div class="row g-2 mb-2">
                                    <div class="col-sm-6">
                                        <label class="form-label small text-muted mb-1">Nama Penerima</label>
                                        <input type="text" wire:model="recipient_name" class="form-control form-control-sm @error('recipient_name') is-invalid @enderror">
                                        @error('recipient_name') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label small text-muted mb-1">No. WhatsApp / HP</label>
                                        <input type="text" wire:model="recipient_phone" class="form-control form-control-sm @error('recipient_phone') is-invalid @enderror">
                                        @error('recipient_phone') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="mb-0">
                                    <label class="form-label small text-muted mb-1">Lokasi Ruangan / Meja Kerja</label>
                                    <textarea wire:model="delivery_address" rows="2" class="form-control form-control-sm @error('delivery_address') is-invalid @enderror" placeholder="Contoh: Gedung B Lantai 2, Ruang Subbag Kepegawaian, Meja Rina"></textarea>
                                    @error('delivery_address') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- 2. Pilihan Metode Pembayaran -->
                    <div class="card card-custom border-0 p-4 mb-3 shadow-sm">
                        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-wallet2 text-kop-primary me-2"></i>2. Metode Pembayaran</h6>

                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label class="card card-custom border p-3 h-100 {{ $payment_method === 'qris' ? 'border-success bg-kop-light' : 'bg-white' }}" style="cursor: pointer;">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" wire:model.live="payment_method" value="qris" id="payQris">
                                        <label class="form-check-label fw-bold small" for="payQris">
                                            QRIS Koperasi
                                        </label>
                                    </div>
                                    <div class="d-flex align-items-center gap-1">
                                        <span class="badge bg-kop-yellow text-dark fw-bold" style="font-size: 0.65rem;">QRIS Statis</span>
                                        <small class="text-muted" style="font-size: 0.72rem;">Scan & upload bukti bayar</small>
                                    </div>
                                </label>
                            </div>
                            <div class="col-6">
                                <label class="card card-custom border p-3 h-100 {{ $payment_method === 'cash' ? 'border-success bg-kop-light' : 'bg-white' }}" style="cursor: pointer;">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" wire:model.live="payment_method" value="cash" id="payCash">
                                        <label class="form-check-label fw-bold small" for="payCash">
                                            Tunai (Cash)
                                        </label>
                                    </div>
                                    <small class="text-muted d-block" style="font-size: 0.72rem;">
                                        Bayar uang pas langsung saat ambil barang atau saat kurir tiba.
                                    </small>
                                </label>
                            </div>
                        </div>

                        <div class="form-text small text-muted">
                            <i class="bi bi-shield-check text-success me-1"></i> 
                            @if($payment_method === 'qris')
                                QRIS resmi koperasi akan ditampilkan di halaman detail pesanan setelah checkout.
                            @else
                                Petugas kasir akan mengonfirmasi penerimaan uang tunai Anda di sistem.
                            @endif
                        </div>
                    </div>

                    <!-- 3. Catatan Pesanan -->
                    <div class="card card-custom border-0 p-4 shadow-sm">
                        <h6 class="fw-bold text-dark mb-2"><i class="bi bi-chat-left-text text-kop-primary me-2"></i>3. Catatan Tambahan (Opsional)</h6>
                        <textarea wire:model="notes" rows="2" class="form-control form-control-sm" placeholder="Contoh: Tolong pilihkan minuman yang dingin, terima kasih."></textarea>
                    </div>
                </div>

                <!-- Order Items Summary Sidebar -->
                <div class="col-lg-5">
                    <div class="card card-custom border-0 p-4 shadow-sm sticky-top" style="top: 80px;">
                        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-receipt-cutoff text-kop-primary me-2"></i>Daftar Barang Pesanan</h6>
                        
                        <div class="list-group list-group-flush mb-3" style="max-height: 240px; overflow-y: auto;">
                            @foreach($items as $it)
                                <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                                    <div class="pe-2">
                                        <h6 class="fw-semibold text-dark mb-0 small text-truncate" style="max-width: 180px;">{{ $it->product->name }}</h6>
                                        <small class="text-muted">{{ $it->quantity }}x @ Rp {{ number_format($it->product->sell_price, 0, ',', '.') }}</small>
                                    </div>
                                    <span class="fw-bold text-dark small">
                                        Rp {{ number_format($it->subtotal, 0, ',', '.') }}
                                    </span>
                                </div>
                            @endforeach
                        </div>

                        <div class="border-top pt-3">
                            <div class="d-flex justify-content-between mb-2 small text-muted">
                                <span>Subtotal Barang</span>
                                <span class="fw-semibold text-dark">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3 small text-muted">
                                <span>Biaya Pengantaran</span>
                                <span class="text-success fw-bold">GRATIS</span>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <span class="fw-bold text-dark">Total Tagihan</span>
                                <span class="fs-5 fw-bold text-kop-primary">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>

                            <button type="submit" wire:loading.attr="disabled" class="btn btn-kop-primary w-100 py-2 fw-semibold rounded-pill">
                                <span wire:loading.remove><i class="bi bi-check2-circle me-1"></i> Konfirmasi & Buat Pesanan</span>
                                <span wire:loading><div class="spinner-border spinner-border-sm me-1" role="status"></div> Memproses Pesanan...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    @endif
</div>

