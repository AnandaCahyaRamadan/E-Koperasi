<div>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-tags text-kop-primary me-2"></i>Kategori Produk</h4>
            <p class="text-muted small mb-0">Kelompokkan barang dagangan koperasi agar mudah ditemukan oleh pelanggan.</p>
        </div>
        <div>
            <button type="button" wire:click="openCreateModal" class="btn btn-kop-primary fw-semibold">
                <i class="bi bi-plus-lg me-1"></i> Tambah Kategori
            </button>
        </div>
    </div>

    <!-- Categories List Table -->
    <div class="card card-custom border-0 p-3 shadow-sm bg-white" style="border-radius: 16px;">
        <!-- DataTables Controls Header -->
        <div class="row align-items-center mb-3 g-2">
            <div class="col-sm-6">
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
            <div class="col-sm-6">
                <div class="d-flex align-items-center justify-content-sm-end gap-2">
                    <div class="input-group input-group-sm" style="max-width: 260px;">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search text-kop-primary"></i></span>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control bg-light border-start-0" placeholder="Cari kategori...">
                        @if($search)
                            <button class="btn btn-light border border-start-0 text-muted" wire:click="$set('search', '')">
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
                        <th style="width: 50px;">Icon</th>
                        @include('partials.datatable-th', ['field' => 'name', 'label' => 'Nama Kategori'])
                        @include('partials.datatable-th', ['field' => 'slug', 'label' => 'Slug URL'])
                        <th>Deskripsi</th>
                        @include('partials.datatable-th', ['field' => 'products_count', 'label' => 'Jumlah Produk'])
                        @include('partials.datatable-th', ['field' => 'is_active', 'label' => 'Status'])
                        <th class="text-center" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @forelse($categories as $cat)
                        <tr>
                            <td class="text-center">
                                <i class="bi {{ $cat->icon ?? 'bi-tag' }} fs-5 text-kop-primary"></i>
                            </td>
                            <td class="fw-bold text-dark">{{ $cat->name }}</td>
                            <td><code>{{ $cat->slug }}</code></td>
                            <td class="text-muted">{{ $cat->description ?: '-' }}</td>
                            <td>
                                <span class="badge bg-kop-light text-kop-primary">{{ $cat->products_count }} produk</span>
                            </td>
                            <td>
                                <span class="badge {{ $cat->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $cat->is_active ? 'Aktif' : 'Non-Aktif' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <button type="button" wire:click="openEditModal({{ $cat->id }})" class="btn-action-edit" title="Edit Kategori">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button type="button" 
                                            wire:click="deleteCategory({{ $cat->id }})" 
                                            wire:confirm="Yakin ingin menghapus kategori {{ $cat->name }}?"
                                            class="btn-action-delete" title="Hapus Kategori">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">Belum ada kategori yang dibuat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $categories->links('partials.pagination') }}
        </div>
    </div>

    <!-- Modal Form -->
    @if($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 card-custom">
                    <div class="modal-header border-0 pb-0">
                        <h6 class="modal-title fw-bold text-dark">{{ $categoryId ? 'Edit Kategori' : 'Tambah Kategori Baru' }}</h6>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <form wire:submit.prevent="save">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-muted">Nama Kategori</label>
                                <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror" placeholder="Contoh: Makanan & Snack">
                                @error('name') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-muted">Class Icon Bootstrap (Opsional)</label>
                                <input type="text" wire:model="icon" class="form-control" placeholder="Contoh: bi-cup-hot, bi-basket2, bi-pencil">
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-muted">Deskripsi Singkat</label>
                                <textarea wire:model="description" rows="2" class="form-control" placeholder="Deskripsi kategori..."></textarea>
                            </div>

                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" wire:model="is_active" id="catActive">
                                <label class="form-check-label small" for="catActive">Aktifkan Kategori</label>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn btn-light" wire:click="closeModal">Batal</button>
                            <button type="submit" class="btn btn-kop-primary fw-semibold">Simpan Kategori</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

