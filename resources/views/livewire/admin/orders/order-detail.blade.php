<div>
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-3">
        <div>
            <a href="{{ route('admin.orders.index') }}" class="text-decoration-none small text-muted">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Transaksi
            </a>
            <div class="d-flex align-items-center gap-2 mt-1">
                <h4 class="fw-bold text-dark mb-0">{{ $order->order_number }}</h4>
                <span class="badge {{ $order->status_badge_class }}">{{ $order->status_label }}</span>
                <span class="badge {{ $order->payment_status_badge_class }}">{{ $order->payment_status_label }}</span>
            </div>
            <small class="text-muted">Tanggal: {{ $order->created_at->format('d M Y, H:i') }} WIB | Pelanggan: <strong>{{ $order->user?->name ?? 'Pelanggan Kasir POS' }}</strong></small>
        </div>

        <div>
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Cetak Invoice Transaksi
            </button>
        </div>
    </div>

    <div class="row g-4">
        <!-- Items breakdown -->
        <div class="col-lg-8">
            <div class="card card-custom border-0 p-4 shadow-sm bg-white mb-4">
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-basket2 text-kop-primary me-2"></i>Rincian Barang yang Dipesan</h6>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small text-muted">
                            <tr>
                                <th>Produk</th>
                                <th class="text-center">Kuantitas</th>
                                <th class="text-end">Harga Satuan (Snapshot)</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            @foreach($order->items as $it)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $it->product_name }}</div>
                                        <small class="text-muted">{{ $it->product_code }}</small>
                                    </td>
                                    <td class="text-center fw-semibold">{{ $it->quantity }} {{ $it->product?->unit?->symbol }}</td>
                                    <td class="text-end">Rp {{ number_format($it->price, 0, ',', '.') }}</td>
                                    <td class="text-end fw-bold text-kop-primary">Rp {{ number_format($it->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Total Akhir:</th>
                                <th class="text-end text-kop-primary fs-6">Rp {{ number_format($order->final_amount, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            @if($order->order_type === 'delivery' && $order->delivery)
                <div class="card card-custom border-0 p-4 shadow-sm bg-white mb-4">
                    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-truck text-kop-primary me-2"></i>Data Pengantaran ke Ruangan Pegawai</h6>
                    <div class="row g-3 small">
                        <div class="col-md-6">
                            <span class="text-muted d-block">Nama Penerima:</span>
                            <span class="fw-bold text-dark">{{ $order->delivery->recipient_name }}</span>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted d-block">Nomor Telepon:</span>
                            <span class="fw-semibold text-dark">{{ $order->delivery->recipient_phone }}</span>
                        </div>
                        <div class="col-12">
                            <span class="text-muted d-block">Alamat Ruangan / Meja:</span>
                            <span class="fw-semibold text-dark">{{ $order->delivery->delivery_address }}</span>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted d-block">Petugas Pengantar:</span>
                            <span class="fw-semibold text-dark">{{ $order->delivery->courier_name ?: 'Petugas Kasir' }}</span>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted d-block">Status Pengantaran:</span>
                            <span class="badge bg-kop-light text-kop-primary">{{ $order->delivery->status_label }}</span>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar Info -->
        <div class="col-lg-4">
            <div class="card card-custom border-0 p-4 shadow-sm bg-white mb-4">
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-wallet2 text-kop-primary me-2"></i>Informasi Pembayaran</h6>
                <div class="mb-2 small">
                    <span class="text-muted d-block">Metode:</span>
                    <span class="fw-bold text-dark">{{ strtoupper($order->payment_method) }}</span>
                </div>
                <div class="mb-2 small">
                    <span class="text-muted d-block">Status:</span>
                    <span class="badge {{ $order->payment_status_badge_class }}">{{ $order->payment_status_label }}</span>
                </div>
                <div class="mb-3 small">
                    <span class="text-muted d-block">Total Nominal:</span>
                    <span class="fw-bold text-kop-primary fs-6">Rp {{ number_format($order->final_amount, 0, ',', '.') }}</span>
                </div>

                @if($order->latestPayment && $order->latestPayment->proof_image)
                    <div class="border rounded p-2 text-center bg-light">
                        <small class="text-muted d-block mb-1">Bukti Transfer QRIS:</small>
                        <a href="{{ asset('storage/' . $order->latestPayment->proof_image) }}" target="_blank">
                            <img src="{{ asset('storage/' . $order->latestPayment->proof_image) }}" alt="Bukti Transfer" class="img-thumbnail" style="max-height: 120px;">
                        </a>
                    </div>
                @endif
            </div>

            <!-- Audit Trail -->
            <div class="card card-custom border-0 p-4 shadow-sm bg-white">
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-journal-text text-kop-primary me-2"></i>Catatan Riwayat Audit</h6>
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

