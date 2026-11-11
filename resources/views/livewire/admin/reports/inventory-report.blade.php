<div>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-journal-text text-kop-primary me-2"></i>Laporan Persediaan
                & Nilai Aset</h4>
            <p class="text-muted small mb-0">Valuasi stok inventori koperasi, total modal berjalan, dan estimasi margin
                keuntungan.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.reports.inventory.pdf') }}" class="btn btn-outline-danger btn-sm fw-semibold">
                <i class="bi bi-file-earmark-pdf me-1"></i> Unduh PDF
            </a>
            <a href="{{ route('admin.reports.inventory.csv') }}" class="btn btn-outline-success btn-sm fw-semibold">
                <i class="bi bi-file-earmark-excel me-1"></i> Unduh Excel (CSV)
            </a>
        </div>
    </div>

    <!-- Summary Metrics -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card card-custom border-0 p-3 shadow-sm bg-white text-center">
                <span class="small text-muted fw-semibold">Total Unit Barang</span>
                <h4 class="fw-bold text-dark my-1">{{ number_format($totalStock, 0, ',', '.') }}</h4>
                <small class="text-muted">Kuantitas fisik total</small>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card card-custom border-0 p-3 shadow-sm bg-white text-center">
                <span class="small text-muted fw-semibold">Total Modal HPP</span>
                <h4 class="fw-bold text-dark my-1">Rp {{ number_format($totalHppValue, 0, ',', '.') }}</h4>
                <small class="text-muted">Nilai aset modal beli</small>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card card-custom border-0 p-3 shadow-sm bg-white text-center">
                <span class="small text-muted fw-semibold">Total Nilai Jual</span>
                <h4 class="fw-bold text-kop-primary my-1">Rp {{ number_format($totalSellValue, 0, ',', '.') }}</h4>
                <small class="text-muted">Jika semua stok terjual</small>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card card-custom border-0 p-3 shadow-sm bg-white text-center">
                <span class="small text-muted fw-semibold">Potensi Margin Laba</span>
                <h4 class="fw-bold text-success my-1">Rp {{ number_format($potentialProfit, 0, ',', '.') }}</h4>
                <small class="text-success">Estimasi keuntungan kotor</small>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card card-custom border-0 p-3 shadow-sm bg-white" style="border-radius: 16px;">
        <div class="row align-items-center mb-3 g-2">
            <div class="col-sm-4">
                <div class="d-flex align-items-center gap-2">
                    <span class="small text-muted fw-semibold text-nowrap">Tampilkan</span>
                    <select wire:model.live="perPage" class="form-select form-select-sm w-auto">
                        <option value="15">15</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span class="small text-muted fw-semibold text-nowrap">baris</span>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="d-flex flex-column flex-sm-row gap-2 justify-content-sm-end">
                    <select wire:model.live="category_id" class="form-select form-select-sm bg-light w-auto">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    <div class="input-group input-group-sm" style="max-width: 260px;">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i
                                class="bi bi-search text-kop-primary"></i></span>
                        <input type="text" wire:model.live.debounce.300ms="search"
                            class="form-control bg-light border-start-0" placeholder="Cari nama atau kode produk...">
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle mb-0 datatable-custom">
                <thead class="table-light small text-muted">
                    <tr>
                        @include('partials.datatable-th', ['field' => 'code', 'label' => 'Kode'])
                        @include('partials.datatable-th', ['field' => 'name', 'label' => 'Nama Produk'])
                        <th>Kategori</th>
                        @include('partials.datatable-th', [
                            'field' => 'purchase_price',
                            'label' => 'Harga Beli',
                            'align' => 'end',
                        ])
                        @include('partials.datatable-th', [
                            'field' => 'sell_price',
                            'label' => 'Harga Jual',
                            'align' => 'end',
                        ])
                        @include('partials.datatable-th', [
                            'field' => 'stock',
                            'label' => 'Sisa Stok',
                            'align' => 'center',
                        ])
                        <th class="text-end">Subtotal HPP</th>
                        <th class="text-end">Subtotal Nilai Jual</th>
                        <th class="text-end">Potensi Margin</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @forelse($products as $p)
                        @php
                            $subHpp = $p->stock * $p->purchase_price;
                            $subSell = $p->stock * $p->sell_price;
                            $margin = $subSell - $subHpp;
                        @endphp
                        <tr>
                            <td class="fw-bold text-muted">{{ $p->code }}</td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $p->name }}</div>
                                <small class="text-muted">{{ $p->unit?->symbol }}</small>
                            </td>
                            <td><span
                                    class="badge bg-light text-dark border">{{ $p->category?->name ?? 'Umum' }}</span>
                            </td>
                            <td class="text-end text-muted">Rp {{ number_format($p->purchase_price, 0, ',', '.') }}
                            </td>
                            <td class="text-end text-dark">Rp {{ number_format($p->sell_price, 0, ',', '.') }}</td>
                            <td class="text-center fw-bold">{{ $p->stock }}</td>
                            <td class="text-end text-muted">Rp {{ number_format($subHpp, 0, ',', '.') }}</td>
                            <td class="text-end fw-semibold text-dark">Rp {{ number_format($subSell, 0, ',', '.') }}
                            </td>
                            <td class="text-end fw-bold text-success">Rp {{ number_format($margin, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-5">Tidak ada data produk yang cocok.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $products->links('partials.pagination') }}
        </div>
    </div>
</div>
