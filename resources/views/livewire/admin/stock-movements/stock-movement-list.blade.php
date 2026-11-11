<div>
    <!-- Title -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-arrow-left-right text-kop-primary me-2"></i>Mutasi & Pergerakan Stok</h4>
            <p class="text-muted small mb-0">Catatan audit log setiap perubahan stok barang (Barang Masuk, Transaksi Keluar, Penyesuaian Opname).</p>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card card-custom border-0 p-3 shadow-sm bg-white" style="border-radius: 16px;">
        <div class="row align-items-center mb-3 g-2">
            <div class="col-sm-4 col-md-3">
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
            <div class="col-sm-8 col-md-9">
                <div class="d-flex align-items-center justify-content-sm-end gap-2 flex-wrap">
                    <div class="d-inline-flex gap-1 flex-wrap">
                        <button type="button" wire:click="$set('filterType', 'all')" class="cat-pill {{ $filterType === 'all' ? 'active' : '' }}">Semua Tipe</button>
                        <button type="button" wire:click="$set('filterType', 'in')" class="cat-pill {{ $filterType === 'in' ? 'active' : '' }}"><i class="bi bi-box-arrow-in-down"></i> Masuk</button>
                        <button type="button" wire:click="$set('filterType', 'out')" class="cat-pill {{ $filterType === 'out' ? 'active' : '' }}"><i class="bi bi-box-arrow-up"></i> Keluar</button>
                        <button type="button" wire:click="$set('filterType', 'adjustment')" class="cat-pill {{ $filterType === 'adjustment' ? 'active' : '' }}"><i class="bi bi-sliders"></i> Penyesuaian</button>
                    </div>
                    <div class="input-group input-group-sm" style="max-width: 240px;">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search text-kop-primary"></i></span>
                        <input type="text" wire:model.live.debounce.300ms="searchProduct" class="form-control bg-light border-start-0" placeholder="Cari produk atau SKU...">
                        @if($searchProduct)
                            <button type="button" class="btn btn-light border border-start-0 text-muted" wire:click="$set('searchProduct', '')">
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
                        @include('partials.datatable-th', ['field' => 'created_at', 'label' => 'Waktu'])
                        <th>Produk</th>
                        @include('partials.datatable-th', ['field' => 'type', 'label' => 'Tipe Pergerakan'])
                        @include('partials.datatable-th', ['field' => 'reference_type', 'label' => 'Kategori Alasan'])
                        @include('partials.datatable-th', ['field' => 'quantity', 'label' => 'Perubahan Kuantitas', 'align' => 'center'])
                        @include('partials.datatable-th', ['field' => 'stock_after', 'label' => 'Stok Awal → Akhir', 'align' => 'center'])
                        <th>Keterangan</th>
                        <th>User Pelaksana</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @forelse($movements as $m)
                        <tr>
                            <td>
                                <span class="fw-semibold text-dark">{{ $m->created_at->format('d/m/Y') }}</span>
                                <small class="text-muted d-block">{{ $m->created_at->format('H:i:s') }}</small>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $m->product?->name ?? 'Produk Dihapus' }}</div>
                                <small class="text-muted">{{ $m->product?->code }}</small>
                            </td>
                            <td>
                                @if($m->type === 'in')
                                    <span class="badge bg-success-subtle text-success fw-bold"><i class="bi bi-arrow-down-left me-1"></i>Masuk</span>
                                @elseif($m->type === 'out')
                                    <span class="badge bg-danger-subtle text-danger fw-bold"><i class="bi bi-arrow-up-right me-1"></i>Keluar</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning-emphasis fw-bold"><i class="bi bi-sliders me-1"></i>Penyesuaian</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ strtoupper($m->reference_type) }}</span>
                            </td>
                            <td class="text-center fw-bold fs-6 {{ $m->type === 'in' ? 'text-success' : ($m->type === 'out' ? 'text-danger' : 'text-primary') }}">
                                {{ $m->type === 'in' ? '+' : ($m->type === 'out' ? '-' : ($m->stock_after >= $m->stock_before ? '+' : '-')) }}{{ $m->quantity }}
                            </td>
                            <td class="text-center">
                                <span class="text-muted">{{ $m->stock_before }}</span>
                                <i class="bi bi-arrow-right mx-1 text-muted"></i>
                                <span class="fw-bold text-dark">{{ $m->stock_after }}</span>
                            </td>
                            <td class="text-muted text-truncate" style="max-width: 200px;">{{ $m->notes ?: '-' }}</td>
                            <td>{{ $m->user?->name ?? 'Sistem' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">Belum ada catatan mutasi pergerakan stok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $movements->links('partials.pagination') }}
        </div>
    </div>
</div>

