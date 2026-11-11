<div>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-rulers text-kop-primary me-2"></i>Satuan Barang Produk</h4>
            <p class="text-muted small mb-0">Kelola unit kemasan atau takaran penjualan (Pcs, Box, Botol, Rim, Bungkus, dll).</p>
        </div>
        <div>
            <button type="button" wire:click="openCreateModal" class="btn btn-kop-primary fw-semibold">
                <i class="bi bi-plus-lg me-1"></i> Tambah Satuan
            </button>
        </div>
    </div>

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
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control bg-light border-start-0" placeholder="Cari satuan...">
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
                        @include('partials.datatable-th', ['field' => 'symbol', 'label' => 'Simbol / Singkatan'])
                        @include('partials.datatable-th', ['field' => 'name', 'label' => 'Nama Lengkap Satuan'])
                        <th>Keterangan</th>
                        @include('partials.datatable-th', ['field' => 'products_count', 'label' => 'Digunakan Oleh'])
                        <th class="text-center" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @forelse($units as $unit)
                        <tr>
                            <td>
                                <span class="badge bg-kop-light text-kop-primary fw-bold fs-6">{{ $unit->symbol }}</span>
                            </td>
                            <td class="fw-bold text-dark">{{ $unit->name }}</td>
                            <td class="text-muted">{{ $unit->description ?: '-' }}</td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $unit->products_count }} jenis produk</span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <button type="button" wire:click="openEditModal({{ $unit->id }})" class="btn-action-edit" title="Edit Satuan">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button type="button" 
                                            wire:click="deleteUnit({{ $unit->id }})" 
                                            wire:confirm="Yakin ingin menghapus satuan {{ $unit->name }}?"
                                            class="btn-action-delete" title="Hapus Satuan">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">Belum ada satuan yang ditambahkan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $units->links('partials.pagination') }}
        </div>
    </div>

    <!-- Modal Form -->
    @if($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 card-custom">
                    <div class="modal-header border-0 pb-0">
                        <h6 class="modal-title fw-bold text-dark">{{ $unitId ? 'Edit Satuan' : 'Tambah Satuan Baru' }}</h6>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <form wire:submit.prevent="save">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-muted">Nama Satuan Lengkap</label>
                                <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror" placeholder="Contoh: Pieces / Buah">
                                @error('name') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-muted">Simbol / Singkatan</label>
                                <input type="text" wire:model="symbol" class="form-control @error('symbol') is-invalid @enderror" placeholder="Contoh: Pcs, Btl, Rim, Box">
                                @error('symbol') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-2">
                                <label class="form-label small fw-semibold text-muted">Deskripsi Singkat</label>
                                <textarea wire:model="description" rows="2" class="form-control" placeholder="Keterangan satuan..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn btn-light" wire:click="closeModal">Batal</button>
                            <button type="submit" class="btn btn-kop-primary fw-semibold">Simpan Satuan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

