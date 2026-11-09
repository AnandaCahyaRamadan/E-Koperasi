<div>
    <!-- Title -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-bar-chart-line text-kop-primary me-2"></i>Laporan Penjualan Koperasi</h4>
            <p class="text-muted small mb-0">Analisis pendapatan transaksi lunas, statistik rata-rata belanja, dan produk terlaris.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.reports.sales.pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-outline-danger btn-sm fw-semibold">
                <i class="bi bi-file-earmark-pdf me-1"></i> Ekspor PDF
            </a>
            <a href="{{ route('admin.reports.sales.csv', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-outline-success btn-sm fw-semibold">
                <i class="bi bi-file-earmark-excel me-1"></i> Ekspor Excel (CSV)
            </a>
        </div>
    </div>

    <!-- Date Filter -->
    <div class="card card-custom border-0 p-3 mb-4 shadow-sm bg-white">
        <div class="row g-2 align-items-center">
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Mulai Tanggal:</label>
                <input type="date" wire:model.live="startDate" class="form-control form-control-sm">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Sampai Tanggal:</label>
                <input type="date" wire:model.live="endDate" class="form-control form-control-sm">
            </div>
            <div class="col-md-6 text-md-end mt-md-4">
                <span class="badge bg-kop-light text-kop-primary p-2">
                    <i class="bi bi-calendar-check me-1"></i> Periode Aktif: {{ date('d M Y', strtotime($startDate)) }} s/d {{ date('d M Y', strtotime($endDate)) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Metrics -->
    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <div class="card card-custom border-0 p-3 shadow-sm bg-white text-center">
                <span class="text-muted small fw-semibold">Total Omzet Penjualan</span>
                <h4 class="fw-bold text-kop-primary my-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h4>
                <small class="text-muted">Total transaksi lunas</small>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card card-custom border-0 p-3 shadow-sm bg-white text-center">
                <span class="text-muted small fw-semibold">Jumlah Transaksi Lunas</span>
                <h4 class="fw-bold text-dark my-1">{{ $totalOrders }}</h4>
                <small class="text-muted">Struk / pesanan terlayani</small>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card card-custom border-0 p-3 shadow-sm bg-white text-center">
                <span class="text-muted small fw-semibold">Rata-rata Nilai Transaksi</span>
                <h4 class="fw-bold text-dark my-1">Rp {{ number_format($avgOrderValue, 0, ',', '.') }}</h4>
                <small class="text-muted">Per transaksi pelanggan</small>
            </div>
        </div>
    </div>

    <!-- Top Selling Products -->
    <div class="card card-custom border-0 p-4 shadow-sm bg-white mb-4">
        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-fire text-danger me-2"></i>Produk Paling Banyak Terjual (Terlaris)</h6>
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0">
                <thead class="table-light small text-muted">
                    <tr>
                        <th style="width: 40px;" class="text-center">#</th>
                        <th>Nama Produk</th>
                        <th class="text-center">Total Terjual (Qty)</th>
                        <th class="text-end">Total Penjualan (Rp)</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @forelse($topProducts as $idx => $tp)
                        <tr>
                            <td class="text-center fw-bold">{{ $idx + 1 }}</td>
                            <td class="fw-semibold text-dark">{{ $tp->product_name }}</td>
                            <td class="text-center"><span class="badge bg-kop-light text-kop-primary fs-6">{{ $tp->total_qty }} unit</span></td>
                            <td class="text-end fw-bold text-kop-primary">Rp {{ number_format($tp->total_sales, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3">Belum ada data produk terjual pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Transactions Breakdown Table -->
    <div class="card card-custom border-0 p-3 shadow-sm bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-muted">
                    <tr>
                        <th>No. Pesanan</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Layanan</th>
                        <th>Metode Bayar</th>
                        <th class="text-end">Nominal Tagihan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @forelse($orders as $ord)
                        <tr>
                            <td class="fw-bold text-kop-primary">{{ $ord->order_number }}</td>
                            <td>{{ $ord->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $ord->user?->name ?? 'Kasir Langsung (POS)' }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $ord->order_type_label }}</span></td>
                            <td><span class="badge bg-secondary">{{ strtoupper($ord->payment_method) }}</span></td>
                            <td class="text-end fw-bold text-dark">Rp {{ number_format($ord->final_amount, 0, ',', '.') }}</td>
                            <td><span class="badge bg-success">Lunas</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Tidak ada transaksi tercatat pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $orders->links() }}
        </div>
    </div>
</div>

