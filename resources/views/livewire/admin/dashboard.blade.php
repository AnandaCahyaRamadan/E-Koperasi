<div>
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">Dashboard Administrator</h4>
            <p class="text-muted small mb-0">Ringkasan performa penjualan koperasi, persediaan stok barang, dan operasional.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.stock-opname.create') }}" class="btn btn-outline-kop btn-sm fw-semibold">
                <i class="bi bi-clipboard-plus me-1"></i> Sesi Opname Baru
            </a>
            <a href="{{ route('admin.products.create') }}" class="btn btn-kop-primary btn-sm fw-semibold">
                <i class="bi bi-plus-lg me-1"></i> Tambah Produk Baru
            </a>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card card-custom border-0 p-3 shadow-sm bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Omzet Bulan Ini</span>
                    <div class="bg-success-subtle text-success rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-wallet2 fs-5"></i>
                    </div>
                </div>
                <h4 class="fw-bold text-dark mb-1">Rp {{ number_format($monthRevenue, 0, ',', '.') }}</h4>
                <span class="small text-muted">{{ $monthOrdersCount }} transaksi lunas bulan ini</span>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card card-custom border-0 p-3 shadow-sm bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Katalog Produk</span>
                    <div class="bg-primary-subtle text-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-box-seam fs-5"></i>
                    </div>
                </div>
                <h4 class="fw-bold text-dark mb-1">{{ $totalProducts }}</h4>
                <a href="{{ route('admin.products.index') }}" class="small text-primary text-decoration-none">
                    Kelola Katalog & Satuan <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card card-custom border-0 p-3 shadow-sm bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Anggota / Pegawai</span>
                    <div class="bg-info-subtle text-info rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-people fs-5"></i>
                    </div>
                </div>
                <h4 class="fw-bold text-dark mb-1">{{ $totalUsers }}</h4>
                <a href="{{ route('admin.users.index') }}" class="small text-info text-decoration-none">
                    Data Kepegawaian <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card card-custom border-0 p-3 shadow-sm bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Peringatan Stok</span>
                    <div class="bg-danger-subtle text-danger rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-exclamation-triangle fs-5"></i>
                    </div>
                </div>
                <h4 class="fw-bold text-danger mb-1">{{ $lowStockCount }}</h4>
                <a href="{{ route('admin.inventory.index') }}" class="small text-danger text-decoration-none fw-semibold">
                    Cek Barang Menipis <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Chart & Low Stock Section -->
    <div class="row g-4 mb-4">
        <!-- Sales Chart -->
        <div class="col-lg-8">
            <div class="card card-custom border-0 p-4 shadow-sm bg-white h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-graph-up text-kop-primary me-2"></i>Tren Penjualan 7 Hari Terakhir</h6>
                    <span class="badge bg-light text-muted border">Harian</span>
                </div>
                <div style="height: 260px;">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Low Stock Items List -->
        <div class="col-lg-4">
            <div class="card card-custom border-0 p-4 shadow-sm bg-white h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-bell text-warning me-2"></i>Perlu Restock</h6>
                    <a href="{{ route('admin.inventory.index') }}" class="small text-muted text-decoration-none">Semua</a>
                </div>

                @if($lowStockProducts->isEmpty())
                    <div class="text-center text-muted py-4 small">
                        <i class="bi bi-check2-circle text-success fs-1 d-block mb-2"></i>
                        Semua stok produk mencukupi batas minimum.
                    </div>
                @else
                    <div class="list-group list-group-flush">
                        @foreach($lowStockProducts as $low)
                            <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-semibold text-dark small d-block">{{ $low->name }}</span>
                                    <small class="text-muted">{{ $low->category?->name ?? 'Kategori' }}</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-danger">Sisa: {{ $low->stock }} {{ $low->unit?->symbol }}</span>
                                    <small class="text-muted d-block" style="font-size: 0.65rem;">Min: {{ $low->min_stock }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('admin.purchases.create') }}" class="btn btn-outline-kop btn-sm w-100">
                            <i class="bi bi-truck me-1"></i> Buat Penerimaan Barang Masuk (PO)
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="card card-custom border-0 p-4 shadow-sm bg-white">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold text-dark mb-0"><i class="bi bi-receipt-cutoff text-kop-primary me-2"></i>Transaksi Terbaru Sistem</h6>
            <a href="{{ route('admin.orders.index') }}" class="small text-kop-primary fw-semibold text-decoration-none">Lihat Seluruh Transaksi</a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-muted">
                    <tr>
                        <th>No. Pesanan</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Tipe</th>
                        <th>Metode Bayar</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @forelse($recentOrders as $ord)
                        <tr>
                            <td class="fw-bold">{{ $ord->order_number }}</td>
                            <td>{{ $ord->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $ord->user?->name ?? 'Kasir Langsung (POS)' }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $ord->order_type_label }}</span></td>
                            <td><span class="badge bg-secondary">{{ strtoupper($ord->payment_method) }}</span></td>
                            <td class="fw-bold text-kop-primary">Rp {{ number_format($ord->final_amount, 0, ',', '.') }}</td>
                            <td><span class="badge {{ $ord->status_badge_class }}">{{ $ord->status_label }}</span></td>
                            <td>
                                <a href="{{ route('admin.orders.detail', $ord->id) }}" class="btn-action-view" title="Detail Pesanan">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">Belum ada transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let adminSalesChartInstance = null;

    function renderAdminSalesChart() {
        const canvas = document.getElementById('salesChart');
        if (!canvas) return;

        if (adminSalesChartInstance) {
            adminSalesChartInstance.destroy();
            adminSalesChartInstance = null;
        }

        const ctx = canvas.getContext('2d');
        adminSalesChartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! $salesLabels !!},
                datasets: [{
                    label: 'Penjualan (Rp)',
                    data: {!! $salesData !!},
                    borderColor: '#16834A',
                    backgroundColor: 'rgba(22, 131, 74, 0.08)',
                    borderWidth: 2.5,
                    tension: 0.35,
                    fill: true,
                    pointBackgroundColor: '#16834A',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Penjualan: Rp ' + Number(context.raw || 0).toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        },
                        grid: {
                            color: '#f1f5f9'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    document.addEventListener('DOMContentLoaded', renderAdminSalesChart);
    document.addEventListener('livewire:navigated', renderAdminSalesChart);
</script>
@endpush

