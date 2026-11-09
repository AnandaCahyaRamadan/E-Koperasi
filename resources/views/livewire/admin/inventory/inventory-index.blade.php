<div>
    <!-- Title -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-boxes text-kop-primary me-2"></i>Monitoring Persediaan Stok</h4>
            <p class="text-muted small mb-0">Pantau ketersediaan fisik, valuasi aset inventori, dan produk yang memerlukan restock.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.purchases.create') }}" class="btn btn-kop-primary btn-sm fw-semibold">
                <i class="bi bi-truck me-1"></i> Catat Barang Masuk (PO)
            </a>
            <a href="{{ route('admin.stock-opname.create') }}" class="btn btn-outline-kop btn-sm fw-semibold">
                <i class="bi bi-clipboard-check me-1"></i> Stok Opname
            </a>
        </div>
    </div>

    <!-- Inventory Valuation Metrics -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card card-custom border-0 p-3 shadow-sm bg-white h-100">
                <span class="text-muted small fw-semibold">Total Unit Tersedia</span>
                <h4 class="fw-bold text-dark my-1">{{ number_format($totalStockUnits, 0, ',', '.') }}</h4>
                <small class="text-muted">Unit barang dalam toko</small>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card card-custom border-0 p-3 shadow-sm bg-white h-100">
                <span class="text-muted small fw-semibold">Nilai Aset Modal (HPP)</span>
                <h4 class="fw-bold text-dark my-1">Rp {{ number_format($totalStockAssetValue, 0, ',', '.') }}</h4>
                <small class="text-muted">Berdasarkan harga beli pokok</small>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card card-custom border-0 p-3 shadow-sm bg-white h-100">
                <span class="text-muted small fw-semibold">Potensi Nilai Jual</span>
                <h4 class="fw-bold text-kop-primary my-1">Rp {{ number_format($totalStockSellValue, 0, ',', '.') }}</h4>
                <small class="text-muted">Berdasarkan harga jual etalase</small>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card card-custom border-0 p-3 shadow-sm bg-white h-100">
                <span class="text-muted small fw-semibold">Peringatan Restock</span>
                <h4 class="fw-bold text-danger my-1">{{ $lowStockCount }}</h4>
                <small class="text-danger">{{ $outOfStockCount }} produk benar-benar habis</small>
            </div>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="card card-custom border-0 p-3 mb-4 shadow-sm bg-white" style="border-radius: 16px;">
        <div class="row g-2 align-items-center">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted ps-3"><i class="bi bi-search text-kop-primary"></i></span>
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control border-start-0 ps-2" placeholder="Ketik nama atau kode barang...">
                    @if($search)
                        <button class="btn btn-link text-muted pe-3 text-decoration-none" wire:click="$set('search', '')">
                            <i class="bi bi-x-circle-fill"></i>
                        </button>
                    @endif
                </div>
            </div>
            <div class="col-md-6 text-md-end">
                <div class="d-inline-flex gap-1 flex-wrap">
                    <button type="button" wire:click="$set('filterStock', 'all')" class="cat-pill {{ $filterStock === 'all' ? 'active' : '' }}">Semua Stok</button>
                    <button type="button" wire:click="$set('filterStock', 'low')" class="cat-pill {{ $filterStock === 'low' ? 'active' : '' }}"><i class="bi bi-exclamation-triangle"></i> Menipis ({{ $lowStockCount }})</button>
                    <button type="button" wire:click="$set('filterStock', 'out')" class="cat-pill {{ $filterStock === 'out' ? 'active' : '' }}"><i class="bi bi-x-circle"></i> Habis ({{ $outOfStockCount }})</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card card-custom border-0 p-3 shadow-sm bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-muted">
                    <tr>
                        <th>Kode/SKU</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th class="text-end">Harga Beli</th>
                        <th class="text-end">Harga Jual</th>
                        <th class="text-center">Stok Fisik</th>
                        <th class="text-center">Min. Stok</th>
                        <th class="text-end">Valuasi Stok</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @forelse($products as $prod)
                        <tr>
                            <td class="fw-bold text-muted">{{ $prod->code }}</td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $prod->name }}</div>
                                <small class="text-muted">{{ $prod->unit?->symbol }}</small>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $prod->category?->name ?? 'Umum' }}</span></td>
                            <td class="text-end text-muted">Rp {{ number_format($prod->purchase_price, 0, ',', '.') }}</td>
                            <td class="text-end fw-semibold text-dark">Rp {{ number_format($prod->sell_price, 0, ',', '.') }}</td>
                            <td class="text-center">
                                <span class="fw-bold fs-6 {{ $prod->stock <= $prod->min_stock ? 'text-danger' : 'text-success' }}">
                                    {{ $prod->stock }}
                                </span>
                            </td>
                            <td class="text-center text-muted">{{ $prod->min_stock }}</td>
                            <td class="text-end fw-bold text-kop-primary">
                                Rp {{ number_format($prod->stock * $prod->purchase_price, 0, ',', '.') }}
                            </td>
                            <td class="text-center">
                                @if($prod->stock <= 0)
                                    <span class="badge bg-danger">Habis</span>
                                @elseif($prod->is_low_stock)
                                    <span class="badge bg-warning text-dark">Menipis</span>
                                @else
                                    <span class="badge bg-success">Aman</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-5">Tidak ada data produk pada filter ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $products->links() }}
        </div>
    </div>
</div>

