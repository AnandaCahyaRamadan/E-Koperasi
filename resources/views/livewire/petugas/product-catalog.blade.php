<div>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-box-seam text-kop-primary me-2"></i>Cek Stok & Harga Produk</h4>
            <p class="text-muted small mb-0">Informasi ketersediaan stok fisik dan harga jual produk untuk melayani pertanyaan pelanggan.</p>
        </div>
    </div>

    <!-- Product Table Card -->
    <div class="card card-custom border-0 p-3 shadow-sm bg-white" style="border-radius: 16px;">
        <div class="row align-items-center mb-3 g-2">
            <div class="col-sm-4">
                <div class="d-flex align-items-center gap-2">
                    <span class="small text-muted fw-semibold text-nowrap">Tampilkan</span>
                    <select wire:model.live="perPage" class="form-select form-select-sm w-auto">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span class="small text-muted fw-semibold text-nowrap">baris</span>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="d-flex flex-column flex-sm-row gap-2 justify-content-sm-end">
                    <select wire:model.live="selectedCategory" class="form-select form-select-sm w-auto">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    <div class="input-group input-group-sm" style="max-width: 260px;">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search text-kop-primary"></i></span>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control bg-light border-start-0" placeholder="Ketik nama atau kode produk...">
                        @if($search)
                            <button type="button" class="btn btn-light border border-start-0 text-muted" wire:click="$set('search', '')">
                                <i class="bi bi-x-circle-fill"></i>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle mb-0 datatable-custom">
                <thead class="table-light small text-muted">
                    <tr>
                        @include('partials.datatable-th', ['field' => 'code', 'label' => 'Kode/SKU'])
                        @include('partials.datatable-th', ['field' => 'name', 'label' => 'Nama Produk'])
                        <th>Kategori</th>
                        @include('partials.datatable-th', ['field' => 'sell_price', 'label' => 'Harga Jual'])
                        @include('partials.datatable-th', ['field' => 'stock', 'label' => 'Stok Tersedia'])
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

        <div class="mt-3">
            {{ $products->links('partials.pagination') }}
        </div>
    </div>
</div>

