<div>
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-box-seam text-kop-primary me-2"></i>Manajemen Produk & Harga</h4>
            <p class="text-muted small mb-0">Kelola inventaris barang dagangan, penetapan harga jual, dan status ketersediaan.</p>
        </div>
        <div>
            <a href="{{ route('admin.products.create') }}" class="btn btn-kop-primary fw-semibold shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Tambah Produk Baru
            </a>
        </div>
    </div>

    <!-- Products DataTable Card -->
    <div class="card card-custom border-0 p-3 shadow-sm bg-white" style="border-radius: 16px;">
        <!-- DataTables Controls Header -->
        <div class="row align-items-center mb-3 g-2">
            <div class="col-sm-5 col-md-4">
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
            <div class="col-sm-7 col-md-8">
                <div class="d-flex align-items-center justify-content-sm-end gap-2 flex-wrap">
                    <div class="w-auto">
                        <select wire:model.live="selectedCategory" class="form-select form-select-sm">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="input-group input-group-sm" style="max-width: 260px;">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search text-kop-primary"></i></span>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control bg-light border-start-0" placeholder="Cari produk...">
                        @if($search)
                            <button class="btn btn-light border border-start-0 text-muted" wire:click="$set('search', '')">
                                <i class="bi bi-x-circle-fill"></i>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle mb-0 datatable-custom">
                <thead class="table-light small text-muted">
                    <tr>
                        <th style="width: 70px;">Foto</th>
                        <th wire:click="sortBy('code')" style="cursor: pointer;">
                            <div class="d-flex align-items-center justify-content-between gap-1">
                                <span>Kode / SKU</span>
                                @if($sortField === 'code')
                                    <i class="bi bi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-kop-primary fw-bold"></i>
                                @else
                                    <i class="bi bi-arrow-down-up text-muted opacity-50"></i>
                                @endif
                            </div>
                        </th>
                        <th wire:click="sortBy('name')" style="cursor: pointer;">
                            <div class="d-flex align-items-center justify-content-between gap-1">
                                <span>Nama Produk</span>
                                @if($sortField === 'name')
                                    <i class="bi bi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-kop-primary fw-bold"></i>
                                @else
                                    <i class="bi bi-arrow-down-up text-muted opacity-50"></i>
                                @endif
                            </div>
                        </th>
                        <th>Kategori</th>
                        <th wire:click="sortBy('purchase_price')" style="cursor: pointer;">
                            <div class="d-flex align-items-center justify-content-between gap-1">
                                <span>Harga Beli</span>
                                @if($sortField === 'purchase_price')
                                    <i class="bi bi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-kop-primary fw-bold"></i>
                                @else
                                    <i class="bi bi-arrow-down-up text-muted opacity-50"></i>
                                @endif
                            </div>
                        </th>
                        <th wire:click="sortBy('sell_price')" style="cursor: pointer;">
                            <div class="d-flex align-items-center justify-content-between gap-1">
                                <span>Harga Jual</span>
                                @if($sortField === 'sell_price')
                                    <i class="bi bi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-kop-primary fw-bold"></i>
                                @else
                                    <i class="bi bi-arrow-down-up text-muted opacity-50"></i>
                                @endif
                            </div>
                        </th>
                        <th wire:click="sortBy('stock')" style="cursor: pointer;">
                            <div class="d-flex align-items-center justify-content-between gap-1">
                                <span>Stok</span>
                                @if($sortField === 'stock')
                                    <i class="bi bi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-kop-primary fw-bold"></i>
                                @else
                                    <i class="bi bi-arrow-down-up text-muted opacity-50"></i>
                                @endif
                            </div>
                        </th>
                        <th wire:click="sortBy('is_active')" style="cursor: pointer;">
                            <div class="d-flex align-items-center justify-content-between gap-1">
                                <span>Status</span>
                                @if($sortField === 'is_active')
                                    <i class="bi bi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-kop-primary fw-bold"></i>
                                @else
                                    <i class="bi bi-arrow-down-up text-muted opacity-50"></i>
                                @endif
                            </div>
                        </th>
                        <th class="text-center" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @forelse($products as $prod)
                        <tr>
                            <td>
                                <div class="bg-light rounded p-1 text-center" style="width: 48px; height: 48px;">
                                    @if($prod->image)
                                        <img src="{{ asset('storage/' . $prod->image) }}" alt="{{ $prod->name }}" class="img-fluid" style="max-height: 40px; object-fit: contain;">
                                    @else
                                        <i class="bi bi-box-seam text-secondary fs-4"></i>
                                    @endif
                                </div>
                            </td>
                            <td class="fw-bold text-muted">{{ $prod->code }}</td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $prod->name }}</div>
                                <small class="text-muted">{{ $prod->unit?->name ?? 'Pcs' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $prod->category?->name ?? 'Umum' }}</span>
                            </td>
                            <td class="text-muted">Rp {{ number_format($prod->purchase_price, 0, ',', '.') }}</td>
                            <td class="fw-bold text-kop-primary">Rp {{ number_format($prod->sell_price, 0, ',', '.') }}</td>
                            <td>
                                <span class="fw-bold {{ $prod->stock <= $prod->min_stock ? 'text-danger' : 'text-success' }}">
                                    {{ $prod->stock }} {{ $prod->unit?->symbol }}
                                </span>
                                @if($prod->stock <= $prod->min_stock)
                                    <small class="badge bg-danger-subtle text-danger d-block mt-1" style="font-size: 0.65rem;">Menipis</small>
                                @endif
                            </td>
                            <td>
                                <button type="button" 
                                        wire:click="toggleActive({{ $prod->id }})" 
                                        class="badge {{ $prod->is_active ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-secondary-subtle text-secondary border border-secondary-subtle' }} rounded-pill px-2.5 py-1.5 fw-semibold border-0" 
                                        style="cursor: pointer; font-size: 0.75rem;" 
                                        title="Klik untuk ubah status">
                                    <i class="bi {{ $prod->is_active ? 'bi-check-circle-fill' : 'bi-dash-circle' }} me-1"></i>
                                    {{ $prod->is_active ? 'Aktif' : 'Non-Aktif' }}
                                </button>
                            </td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <a href="{{ route('admin.products.edit', $prod->id) }}" class="btn-action-edit" title="Edit Produk">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <button type="button" 
                                            wire:click="deleteProduct({{ $prod->id }})" 
                                            wire:confirm="Yakin ingin menghapus atau menonaktifkan produk {{ $prod->name }}?"
                                            class="btn-action-delete" title="Hapus Produk">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-5">
                                Belum ada data produk yang terdaftar.
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

