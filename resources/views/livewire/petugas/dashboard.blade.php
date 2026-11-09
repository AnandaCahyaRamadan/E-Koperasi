<div>
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">Dashboard Kasir & Operasional</h4>
            <p class="text-muted small mb-0">Pantau aktivitas transaksi kasir harian, antrean pembayaran, dan pemrosesan pesanan.</p>
        </div>
        <div>
            <a href="{{ route('petugas.pos') }}" class="btn btn-kop-primary fw-semibold px-4 py-2">
                <i class="bi bi-cart4 me-1"></i> Buka Mesin Kasir (POS)
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card card-custom border-0 p-3 shadow-sm h-100 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Butuh Verifikasi</span>
                    <div class="bg-warning-subtle text-warning rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-qr-code-scan fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-1">{{ $waitingVerificationCount }}</h3>
                <a href="{{ route('petugas.payments.index') }}" class="small text-warning text-decoration-none fw-semibold">
                    Periksa Bukti Bayar <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card card-custom border-0 p-3 shadow-sm h-100 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Dalam Penyiapan</span>
                    <div class="bg-primary-subtle text-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-box-seam fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-1">{{ $processingCount }}</h3>
                <a href="{{ route('petugas.orders.index') }}" class="small text-primary text-decoration-none fw-semibold">
                    Siapkan Barang <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card card-custom border-0 p-3 shadow-sm h-100 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Siap Ambil / Antar</span>
                    <div class="bg-info-subtle text-info rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-truck fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-1">{{ $readyPickupCount + $onDeliveryCount }}</h3>
                <span class="small text-muted">Siap Ambil: {{ $readyPickupCount }} | Diantar: {{ $onDeliveryCount }}</span>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card card-custom border-0 p-3 shadow-sm h-100 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Pendapatan Hari Ini</span>
                    <div class="bg-success-subtle text-success rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-cash-coin fs-5"></i>
                    </div>
                </div>
                <h4 class="fw-bold text-success mb-1">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</h4>
                <span class="small text-muted">{{ $todayOrders }} total transaksi hari ini</span>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Recent Orders Table -->
        <div class="col-lg-8">
            <div class="card card-custom border-0 p-4 shadow-sm bg-white">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-clock-history me-2 text-kop-primary"></i>Pesanan Terakhir Masuk</h6>
                    <a href="{{ route('petugas.orders.index') }}" class="small text-kop-primary fw-semibold text-decoration-none">Lihat Semua</a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small text-muted">
                            <tr>
                                <th>No. Pesanan</th>
                                <th>Pelanggan</th>
                                <th>Tipe</th>
                                <th>Total</th>
                                <th>Pembayaran</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            @forelse($recentOrders as $order)
                                <tr>
                                    <td class="fw-bold text-dark">{{ $order->order_number }}</td>
                                    <td>{{ $order->user?->name ?? 'Pelanggan POS' }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark border">{{ $order->order_type_label }}</span>
                                    </td>
                                    <td class="fw-semibold text-kop-primary">Rp {{ number_format($order->final_amount, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge {{ $order->payment_status_badge_class }}">{{ $order->payment_status_label }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $order->status_badge_class }}">{{ $order->status_label }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('petugas.orders.detail', $order->id) }}" class="btn btn-outline-kop btn-sm py-0 px-2" style="font-size: 0.72rem;">
                                            Kelola
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">Belum ada transaksi yang tercatat hari ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Low Stock Warning Box -->
        <div class="col-lg-4">
            <div class="card card-custom border-0 p-4 shadow-sm bg-white">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-exclamation-triangle text-warning me-2"></i>Peringatan Stok Menipis</h6>
                    <a href="{{ route('petugas.products.index') }}" class="small text-muted text-decoration-none">Cek Stok</a>
                </div>

                @if($lowStockProducts->isEmpty())
                    <div class="text-center text-muted py-3 small">
                        <i class="bi bi-check-circle text-success fs-3 d-block mb-1"></i>
                        Semua stok produk dalam kondisi aman.
                    </div>
                @else
                    <div class="list-group list-group-flush">
                        @foreach($lowStockProducts as $low)
                            <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-semibold text-dark small d-block">{{ $low->name }}</span>
                                    <small class="text-muted">{{ $low->code }}</small>
                                </div>
                                <span class="badge bg-danger rounded-pill">
                                    Sisa {{ $low->stock }} {{ $low->unit?->symbol }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

