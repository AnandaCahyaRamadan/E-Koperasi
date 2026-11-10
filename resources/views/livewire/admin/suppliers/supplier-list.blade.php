<div>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-building text-kop-primary me-2"></i>Pemasok / Supplier</h4>
            <p class="text-muted small mb-0">Kelola kontak distributor dan mitra penyedia pasokan barang koperasi.</p>
        </div>
        <div>
            <button type="button" wire:click="openCreateModal" class="btn btn-kop-primary fw-semibold">
                <i class="bi bi-plus-lg me-1"></i> Tambah Supplier Baru
            </button>
        </div>
    </div>

    <div class="card card-custom border-0 p-3 shadow-sm bg-white">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
            <div class="input-group" style="max-width: 320px;">
                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" wire:model.live.debounce.300ms="search" class="form-control bg-light border-start-0" placeholder="Cari supplier...">
            </div>
            <div class="text-muted small">
                Total: <span class="fw-bold text-dark">{{ $suppliers->total() }}</span> supplier
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-muted">
                    <tr>
                        <th>Kode</th>
                        <th>Nama Supplier</th>
                        <th>Kontak Person</th>
                        <th>No. Telepon / WA</th>
                        <th>Email</th>
                        <th>Alamat</th>
                        <th>Riwayat PO</th>
                        <th class="text-center" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @forelse($suppliers as $sup)
                        <tr>
                            <td class="fw-bold text-muted">{{ $sup->code }}</td>
                            <td class="fw-bold text-dark">{{ $sup->name }}</td>
                            <td>{{ $sup->contact_person ?: '-' }}</td>
                            <td>{{ $sup->phone ?: '-' }}</td>
                            <td>{{ $sup->email ?: '-' }}</td>
                            <td class="text-truncate" style="max-width: 200px;">{{ $sup->address ?: '-' }}</td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $sup->purchases_count }} transaksi</span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-1.5">
                                    <button type="button" wire:click="openEditModal({{ $sup->id }})" class="btn-action-edit" title="Edit Supplier">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button type="button" 
                                            wire:click="deleteSupplier({{ $sup->id }})" 
                                            wire:confirm="Yakin ingin menghapus supplier {{ $sup->name }}?"
                                            class="btn-action-delete" title="Hapus Supplier">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">Belum ada pemasok yang terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $suppliers->links() }}
        </div>
    </div>

    <!-- Modal Form -->
    @if($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 card-custom">
                    <div class="modal-header border-0 pb-0">
                        <h6 class="modal-title fw-bold text-dark">{{ $supplierId ? 'Edit Supplier' : 'Tambah Supplier Baru' }}</h6>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <form wire:submit.prevent="save">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-muted">Nama Perusahaan / Supplier</label>
                                <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror" placeholder="Contoh: PT Sumber Pangan">
                                @error('name') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">Kode Pemasok</label>
                                    <input type="text" wire:model="code" class="form-control" placeholder="SUP-001">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">Nama Kontak Person</label>
                                    <input type="text" wire:model="contact_person" class="form-control" placeholder="Nama sales / PIC">
                                </div>
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">No. Telepon / WA</label>
                                    <input type="text" wire:model="phone" class="form-control" placeholder="08123456789">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">Email</label>
                                    <input type="email" wire:model="email" class="form-control" placeholder="sales@supplier.com">
                                </div>
                            </div>

                            <div class="mb-2">
                                <label class="form-label small fw-semibold text-muted">Alamat Gudang / Kantor</label>
                                <textarea wire:model="address" rows="2" class="form-control" placeholder="Alamat lengkap..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn btn-light" wire:click="closeModal">Batal</button>
                            <button type="submit" class="btn btn-kop-primary fw-semibold">Simpan Supplier</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

