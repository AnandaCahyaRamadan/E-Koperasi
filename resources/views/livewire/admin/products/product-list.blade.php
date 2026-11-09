<div>
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-box-seam text-kop-primary me-2"></i>Manajemen Produk & Harga</h4>
            <p class="text-muted small mb-0">Kelola inventaris barang dagangan, penetapan harga jual, dan status ketersediaan.</p>
        </div>
        <div>
            <a href="{{ route('admin.products.create') }}" class="btn btn-kop-primary fw-semibold">
                <i class="bi bi-plus-lg me-1"></i> Tambah Produk Baru
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card card-custom border-0 p-3 mb-4 shadow-sm bg-white" style="border-radius: 16px;">
        <div class="row g-2 align-items-center">
            <div class="col-md-7">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted ps-3"><i class="bi bi-search text-kop-primary"></i></span>
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control border-start-0 ps-2" placeholder="Cari nama, SKU/kode, atau deskripsi...">
                    @if($search)
                        <button class="btn btn-link text-muted pe-3 text-decoration-none" wire:click="$set('search', '')">
                            <i class="bi bi-x-circle-fill"></i>
                        </button>
                    @endif
                </div>
            </div>
            <div class="col-md-5">
                <select wire:model.live="selectedCategory" class="form-select">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card card-custom border-0 p-3 shadow-sm bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-muted">
                    <tr>
                        <th style="width: 70px;">Foto</th>
                        <th>Kode / SKU</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Harga Beli</th>
                        <th>Harga Jual</th>
                        <th>Stok</th>
                        <th>Status</th>
                        <th class="text-center" style="width: 140px;">Aksi</th>
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
                                <button type="button" wire:click="toggleActive({{ $prod->id }})" class="btn btn-sm py-0 px-2 rounded-pill {{ $prod->is_active ? 'btn-success' : 'btn-secondary' }}" style="font-size: 0.72rem;">
                                    {{ $prod->is_active ? 'Aktif' : 'Non-Aktif' }}
                                </button>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.products.edit', $prod->id) }}" class="btn btn-outline-primary btn-sm py-0 px-2" style="font-size: 0.75rem;" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" 
                                        wire:click="deleteProduct({{ $prod->id }})" 
                                        wire:confirm="Yakin ingin menghapus atau menonaktifkan produk {{ $prod->name }}?"
                                        class="btn btn-outline-danger btn-sm py-0 px-2 ms-1" style="font-size: 0.75rem;" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
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

        <div class="d-flex justify-content-center mt-3">
            {{ $products->links() }}
        </div>
    </div>
</div>

