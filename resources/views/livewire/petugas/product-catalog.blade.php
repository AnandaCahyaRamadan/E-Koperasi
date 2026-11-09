<div>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-box-seam text-kop-primary me-2"></i>Cek Stok & Harga Produk</h4>
            <p class="text-muted small mb-0">Informasi ketersediaan stok fisik dan harga jual produk untuk melayani pertanyaan pelanggan.</p>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="card card-custom border-0 p-3 mb-4 shadow-sm bg-white">
        <div class="row g-2">
            <div class="col-md-7">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control border-start-0 ps-0" placeholder="Ketik nama atau kode produk...">
                </div>
            </div>
            <div class="col-md-5">
                <select wire:model.live="selectedCategory" class="form-select form-select-sm">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Product Table -->
    <div class="card card-custom border-0 p-3 shadow-sm bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-muted">
                    <tr>
                        <th>Kode/SKU</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Harga Jual</th>
                        <th>Stok Tersedia</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @forelse($products as $p)
                        <tr>
                            <td class="fw-bold text-muted">{{ $p->code }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $p->name }}</div>
                                <small class="text-muted">{{ $p->description }}</small>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $p->category?->name ?? 'Umum' }}</span></td>
                            <td class="fw-bold text-kop-primary">Rp {{ number_format($p->sell_price, 0, ',', '.') }}</td>
                            <td>
                                <span class="fw-bold {{ $p->stock <= $p->min_stock ? 'text-danger' : 'text-success' }}">
                                    {{ $p->stock }} {{ $p->unit?->symbol }}
                                </span>
                            </td>
                            <td>
                                @if($p->stock <= 0)
                                    <span class="badge bg-danger">Habis</span>
                                @elseif($p->is_low_stock)
                                    <span class="badge bg-warning text-dark">Menipis</span>
                                @else
                                    <span class="badge bg-success">Tersedia</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">Produk tidak ditemukan.</td>
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

