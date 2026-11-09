<div>
    <div class="row g-3">
        <!-- Left: Product Selection Grid -->
        <div class="col-lg-7 col-xl-8">
            <div class="card card-custom border-0 p-3 shadow-sm bg-white mb-3">
                <!-- Search & Category Filters -->
                <div class="row g-2 mb-3">
                    <div class="col-md-7">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted ps-3"><i class="bi bi-upc-scan text-kop-primary"></i></span>
                            <input type="text" wire:model.live.debounce.300ms="search" class="form-control border-start-0 ps-2" placeholder="Ketik nama atau scan barcode...">
                            @if($search)
                                <button class="btn btn-link text-muted pe-3 text-decoration-none" wire:click="$set('search', '')">
                                    <i class="bi bi-x-circle-fill"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-5">
                        <select wire:model.live="selectedCategory" class="form-select">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="row row-cols-2 row-cols-md-3 row-cols-xl-4 g-2 overflow-auto" style="max-height: 600px;">
                    @forelse($products as $prod)
                        <div class="col">
                            <div wire:click="addToCart({{ $prod->id }})" 
                                 class="pos-product-card d-flex flex-column {{ $prod->stock < 1 ? 'opacity-50 pointer-events-none' : '' }}" 
                                 title="Klik untuk tambah ke transaksi">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <span class="badge bg-light text-muted border" style="font-size: 0.65rem;">{{ $prod->code }}</span>
                                    <span class="badge {{ $prod->stock > 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}" style="font-size: 0.65rem;">
                                        {{ $prod->stock }} {{ $prod->unit?->symbol }}
                                    </span>
                                </div>
                                <h6 class="fw-semibold text-dark small mb-1 text-truncate" title="{{ $prod->name }}">{{ $prod->name }}</h6>
                                <div class="mt-auto pt-1 d-flex justify-content-between align-items-center">
                                    <span class="fw-bold text-kop-primary small">Rp {{ number_format($prod->sell_price, 0, ',', '.') }}</span>
                                    <i class="bi bi-plus-circle-fill text-kop-primary fs-5"></i>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center text-muted py-5">
                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                            Produk tidak ditemukan.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right: POS Register & Payment -->
        <div class="col-lg-5 col-xl-4">
            <div class="card card-custom border-0 p-3 shadow-sm bg-white sticky-top" style="top: 80px;">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-calculator me-1 text-kop-primary"></i>Transaksi Kasir</h6>
                    @if(!empty($posCart))
                        <button type="button" wire:click="clearCart" class="btn btn-outline-danger btn-sm py-0 px-2" style="font-size: 0.72rem;">
                            Batal
                        </button>
                    @endif
                </div>

                <div class="mb-2">
                    <input type="text" wire:model="customerName" class="form-control form-control-sm" placeholder="Nama Pelanggan (opsional)">
                </div>

                <!-- Items Table in POS -->
                <div class="border rounded p-2 mb-3 bg-light overflow-auto" style="max-height: 250px;">
                    @if(empty($posCart))
                        <div class="text-center text-muted py-4 small">
                            <i class="bi bi-cart3 fs-3 d-block mb-1 opacity-50"></i>
                            Pilih produk dari sisi kiri untuk memulai transaksi.
                        </div>
                    @else
                        <table class="table table-sm table-borderless align-middle mb-0" style="font-size: 0.82rem;">
                            <tbody>
                                @foreach($posCart as $pId => $item)
                                    <tr class="border-bottom">
                                        <td class="ps-0">
                                            <div class="fw-semibold text-dark text-truncate" style="max-width: 140px;">{{ $item['name'] }}</div>
                                            <small class="text-muted">@ Rp {{ number_format($item['price'], 0, ',', '.') }}</small>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex align-items-center justify-content-center gap-1">
                                                <button type="button" wire:click="updateQty({{ $pId }}, {{ $item['qty'] - 1 }})" class="btn btn-light btn-sm p-0 rounded-circle border" style="width: 22px; height: 22px;">-</button>
                                                <span class="fw-bold px-1">{{ $item['qty'] }}</span>
                                                <button type="button" wire:click="updateQty({{ $pId }}, {{ $item['qty'] + 1 }})" class="btn btn-light btn-sm p-0 rounded-circle border" style="width: 22px; height: 22px;">+</button>
                                            </div>
                                        </td>
                                        <td class="text-end fw-bold text-kop-primary">
                                            Rp {{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}
                                        </td>
                                        <td class="pe-0 text-end">
                                            <button type="button" wire:click="removeItem({{ $pId }})" class="btn btn-link text-danger p-0">
                                                <i class="bi bi-x"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>

                <!-- Payment Form -->
                <div class="border-top pt-2">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small">Total Tagihan:</span>
                        <h4 class="fw-bold text-kop-primary mb-0">Rp {{ number_format($total, 0, ',', '.') }}</h4>
                    </div>

                    <!-- Payment Method Toggle -->
                    <div class="btn-group w-100 mb-3" role="group">
                        <input type="radio" class="btn-check" wire:model.live="paymentMethod" value="cash" id="posCash" autocomplete="off">
                        <label class="btn btn-outline-success btn-sm fw-semibold" for="posCash">
                            <i class="bi bi-cash-stack me-1"></i> Tunai (Cash)
                        </label>

                        <input type="radio" class="btn-check" wire:model.live="paymentMethod" value="qris" id="posQris" autocomplete="off">
                        <label class="btn btn-outline-warning btn-sm fw-semibold text-dark" for="posQris">
                            <i class="bi bi-qr-code me-1"></i> QRIS
                        </label>
                    </div>

                    @if($paymentMethod === 'cash')
                        <div class="mb-3">
                            <label class="form-label small text-muted mb-1">Uang Tunai Diterima (Rp)</label>
                            <input type="number" wire:model.live="cashReceived" class="form-control form-control-lg fw-bold text-end" placeholder="0" step="500">
                            
                            <!-- Quick Cash Presets -->
                            <div class="d-flex gap-1 mt-1">
                                <button type="button" wire:click="$set('cashReceived', {{ $total }})" class="btn btn-outline-secondary btn-sm py-0 flex-fill" style="font-size: 0.72rem;">Uang Pas</button>
                                <button type="button" wire:click="$set('cashReceived', 20000)" class="btn btn-outline-secondary btn-sm py-0 flex-fill" style="font-size: 0.72rem;">20k</button>
                                <button type="button" wire:click="$set('cashReceived', 50000)" class="btn btn-outline-secondary btn-sm py-0 flex-fill" style="font-size: 0.72rem;">50k</button>
                                <button type="button" wire:click="$set('cashReceived', 100000)" class="btn btn-outline-secondary btn-sm py-0 flex-fill" style="font-size: 0.72rem;">100k</button>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-2 p-2 bg-light rounded">
                                <span class="small fw-semibold text-muted">Kembalian:</span>
                                <span class="fw-bold fs-6 text-success">Rp {{ number_format($change, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning small p-2 mb-3">
                            <i class="bi bi-info-circle me-1"></i> Pelanggan memindai QRIS kasir. Konfirmasi setelah notifikasi masuk.
                        </div>
                    @endif

                    <button type="button" 
                            wire:click="processTransaction" 
                            class="btn btn-kop-primary w-100 py-2 fw-semibold {{ empty($posCart) ? 'disabled' : '' }}"
                            {{ empty($posCart) ? 'disabled' : '' }}>
                        <i class="bi bi-check2-circle me-1"></i> Bayar & Cetak Struk
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Cetak Struk Setelah Transaksi Selesai -->
    @if($lastCompletedOrder)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 380px;">
                <div class="modal-content border-0 card-custom">
                    <div class="modal-header border-0 pb-0 no-print">
                        <h6 class="modal-title fw-bold text-dark"><i class="bi bi-printer text-kop-primary me-2"></i>Struk Pembayaran POS</h6>
                        <button type="button" class="btn-close" wire:click="closeReceiptModal"></button>
                    </div>
                    <div class="modal-body p-4" id="printableReceipt">
                        <!-- Thermal Receipt Style -->
                        <div class="text-center mb-3">
                            <h6 class="fw-bold mb-0 text-dark">{{ \App\Models\Setting::get('koperasi_name', 'E-KOPERASI') }}</h6>
                            <small class="text-muted d-block" style="font-size: 0.7rem;">{{ \App\Models\Setting::get('koperasi_address', 'Kantor Pusat') }}</small>
                            <small class="text-muted d-block" style="font-size: 0.7rem;">{{ \App\Models\Setting::get('koperasi_phone', '08123456789') }}</small>
                            <div class="border-top border-bottom border-dark border-dashed my-2 py-1 small">
                                <strong>{{ $lastCompletedOrder->order_number }}</strong><br>
                                {{ $lastCompletedOrder->created_at->format('d/m/Y H:i') }} | Kasir: {{ auth()->user()->name }}
                            </div>
                        </div>

                        <table class="w-100 small mb-2" style="font-size: 0.8rem;">
                            <tbody>
                                @foreach($lastCompletedOrder->items as $it)
                                    <tr>
                                        <td colspan="2" class="fw-semibold">{{ $it->product_name }}</td>
                                    </tr>
                                    <tr class="border-bottom border-light">
                                        <td class="text-muted ps-2">{{ $it->quantity }} x {{ number_format($it->price, 0, ',', '.') }}</td>
                                        <td class="text-end fw-semibold">Rp {{ number_format($it->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="border-top border-dark border-dashed pt-2 small">
                            <div class="d-flex justify-content-between fw-bold mb-1">
                                <span>TOTAL:</span>
                                <span>Rp {{ number_format($lastCompletedOrder->final_amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between text-muted mb-1">
                                <span>Metode:</span>
                                <span>{{ strtoupper($lastCompletedOrder->payment_method) }}</span>
                            </div>
                        </div>

                        <div class="text-center mt-3 pt-2 border-top border-dashed small text-muted" style="font-size: 0.72rem;">
                            Terima kasih atas kunjungan Anda!<br>
                            Simpan struk ini sebagai bukti pembayaran.
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 no-print">
                        <button type="button" class="btn btn-light" wire:click="closeReceiptModal">Tutup</button>
                        <button type="button" class="btn btn-kop-primary fw-semibold" onclick="window.print()">
                            <i class="bi bi-printer me-1"></i> Cetak Struk
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

