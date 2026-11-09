<div>
    <!-- Header -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-3">
        <div>
            <a href="{{ route('petugas.orders.index') }}" class="text-decoration-none small text-muted">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Antrean Pesanan
            </a>
            <div class="d-flex align-items-center gap-2 mt-1">
                <h4 class="fw-bold text-dark mb-0">{{ $order->order_number }}</h4>
                <span class="badge {{ $order->status_badge_class }}">{{ $order->status_label }}</span>
                <span class="badge {{ $order->payment_status_badge_class }}">{{ $order->payment_status_label }}</span>
            </div>
            <small class="text-muted">Tanggal: {{ $order->created_at->format('d M Y, H:i') }} WIB | Pelanggan: <strong>{{ $order->user?->name ?? 'Pelanggan Kasir' }}</strong> ({{ $order->user?->employee_id ?? '-' }})</small>
        </div>

        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Cetak Dokumen Pesanan
            </button>
        </div>
    </div>

    <!-- Quick Action Bar -->
    <div class="card card-custom border-0 p-3 mb-4 shadow-sm bg-white">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-2">
                <span class="small fw-semibold text-muted">Tindakan Cepat:</span>
                @if($order->payment_status !== 'paid' && $order->payment_method === 'cash')
                    <button type="button" wire:click="confirmCashPayment" wire:confirm="Konfirmasi: Anda telah menerima uang tunai dari pelanggan?" class="btn btn-success btn-sm fw-semibold">
                        <i class="bi bi-cash-stack me-1"></i> Konfirmasi Uang Diterima (Lunas)
                    </button>
                @endif

                @if($order->status === 'confirmed')
                    <button type="button" wire:click="updateStatus('processing')" class="btn btn-primary btn-sm">
                        <i class="bi bi-box-seam me-1"></i> Mulai Siapkan Pesanan
                    </button>
                @elseif($order->status === 'processing')
                    @if($order->order_type === 'delivery')
                        <button type="button" wire:click="updateStatus('on_delivery')" class="btn btn-indigo text-white btn-sm" style="background-color: #6610f2;">
                            <i class="bi bi-truck me-1"></i> Antar ke Meja Pegawai
                        </button>
                    @else
                        <button type="button" wire:click="updateStatus('ready_for_pickup')" class="btn btn-info text-dark btn-sm">
                            <i class="bi bi-bag-check me-1"></i> Siap Diambil di Kasir
                        </button>
                    @endif
                @elseif(in_array($order->status, ['ready_for_pickup', 'on_delivery']))
                    <button type="button" wire:click="updateStatus('completed')" wire:confirm="Tandai pesanan ini telah selesai diterima pelanggan?" class="btn btn-success btn-sm">
                        <i class="bi bi-check2-all me-1"></i> Selesaikan Pesanan
                    </button>
                @endif
            </div>

            <div>
                <span class="small text-muted">Layanan: <strong class="text-dark">{{ $order->order_type_label }}</strong></span>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Order Items -->
        <div class="col-lg-8">
            <div class="card card-custom border-0 p-4 shadow-sm bg-white mb-4">
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-basket2 text-kop-primary me-2"></i>Rincian Barang Dipesan</h6>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small text-muted">
                            <tr>
                                <th>Produk</th>
                                <th class="text-center">Kuantitas</th>
                                <th class="text-end">Harga Satuan</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $item->product_name }}</div>
                                        <small class="text-muted">Kode: {{ $item->product_code }}</small>
                                    </td>
                                    <td class="text-center fw-semibold">{{ $item->quantity }} {{ $item->product?->unit?->symbol }}</td>
                                    <td class="text-end">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                    <td class="text-end fw-bold text-kop-primary">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Total Pembayaran:</th>
                                <th class="text-end text-kop-primary fs-6">Rp {{ number_format($order->final_amount, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if($order->notes)
                    <div class="bg-light p-3 rounded mt-3 small">
                        <strong>Catatan Pelanggan:</strong>
                        <div class="text-muted">{{ $order->notes }}</div>
                    </div>
                @endif
            </div>

            <!-- Delivery Information for Delivery Orders -->
            @if($order->order_type === 'delivery')
                <div class="card card-custom border-0 p-4 shadow-sm bg-white mb-4">
                    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-truck text-kop-primary me-2"></i>Informasi & Kurir Pengantaran</h6>
                    
                    <form wire:submit.prevent="updateDeliveryInfo">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small text-muted mb-1">Nama Petugas Pengantar / Kurir</label>
                                <input type="text" wire:model="courierName" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted mb-1">Status Pengantaran</label>
                                <input type="text" class="form-control form-control-sm bg-light" value="{{ $order->delivery?->status_label ?? 'Disiapkan' }}" readonly>
                            </div>
                            <div class="col-12">
                                <label class="form-label small text-muted mb-1">Catatan Pengantaran (Contoh: Diterima oleh rekan satu ruangan)</label>
                                <input type="text" wire:model="deliveryNotes" class="form-control form-control-sm" placeholder="Catatan pengantaran...">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-kop-primary btn-sm">Simpan Info Kurir</button>
                    </form>
                </div>
            @endif
        </div>

        <!-- Sidebar Payment & Audit Timeline -->
        <div class="col-lg-4">
            <!-- Payment Details Card -->
            <div class="card card-custom border-0 p-4 shadow-sm bg-white mb-4">
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-wallet2 text-kop-primary me-2"></i>Data Pembayaran</h6>

                <div class="mb-2 small">
                    <span class="text-muted d-block">Metode:</span>
                    <span class="fw-bold text-dark">{{ strtoupper($order->payment_method) }}</span>
                </div>
                <div class="mb-2 small">
                    <span class="text-muted d-block">Status Bayar:</span>
                    <span class="badge {{ $order->payment_status_badge_class }}">{{ $order->payment_status_label }}</span>
                </div>
                <div class="mb-3 small">
                    <span class="text-muted d-block">Total Nominal:</span>
                    <span class="fw-bold text-kop-primary fs-6">Rp {{ number_format($order->final_amount, 0, ',', '.') }}</span>
                </div>

                @if($order->latestPayment && $order->latestPayment->proof_image)
                    <div class="border rounded p-2 text-center bg-light mb-2">
                        <small class="text-muted d-block mb-1">Bukti Transfer:</small>
                        <a href="{{ asset('storage/' . $order->latestPayment->proof_image) }}" target="_blank">
                            <img src="{{ asset('storage/' . $order->latestPayment->proof_image) }}" alt="Bukti Transfer" class="img-thumbnail" style="max-height: 120px;">
                        </a>
                    </div>
                @endif
            </div>

            <!-- Audit Trail -->
            <div class="card card-custom border-0 p-4 shadow-sm bg-white">
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-journal-check text-kop-primary me-2"></i>Riwayat Audit Status</h6>
                <div style="max-height: 250px; overflow-y: auto;">
                    @forelse($order->statusHistories as $hist)
                        <div class="border-start border-success border-2 ps-2 mb-2">
                            <div class="fw-semibold small text-dark">{{ $hist->status }}</div>
                            <div class="text-muted" style="font-size: 0.72rem;">{{ $hist->notes }}</div>
                            <div class="text-muted" style="font-size: 0.65rem;">{{ $hist->created_at->format('d/m/Y H:i:s') }}</div>
                        </div>
                    @empty
                        <small class="text-muted">Belum ada riwayat audit.</small>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

