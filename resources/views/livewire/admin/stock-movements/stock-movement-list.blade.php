<div>
    <!-- Title -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-arrow-left-right text-kop-primary me-2"></i>Mutasi & Pergerakan Stok</h4>
            <p class="text-muted small mb-0">Catatan audit log setiap perubahan stok barang (Barang Masuk, Transaksi Keluar, Penyesuaian Opname).</p>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card card-custom border-0 p-3 mb-4 shadow-sm bg-white">
        <div class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" wire:model.live.debounce.300ms="searchProduct" class="form-control border-start-0 ps-0" placeholder="Cari nama atau kode produk...">
                </div>
            </div>
            <div class="col-md-7 text-md-end">
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" wire:click="$set('filterType', 'all')" class="btn {{ $filterType === 'all' ? 'btn-kop-primary fw-bold' : 'btn-outline-secondary' }}">Semua Tipe</button>
                    <button type="button" wire:click="$set('filterType', 'in')" class="btn {{ $filterType === 'in' ? 'btn-success fw-bold' : 'btn-outline-secondary' }}">Barang Masuk</button>
                    <button type="button" wire:click="$set('filterType', 'out')" class="btn {{ $filterType === 'out' ? 'btn-danger fw-bold' : 'btn-outline-secondary' }}">Barang Keluar</button>
                    <button type="button" wire:click="$set('filterType', 'adjustment')" class="btn {{ $filterType === 'adjustment' ? 'btn-warning text-dark fw-bold' : 'btn-outline-secondary' }}">Opname / Penyesuaian</button>
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
                        <th>Waktu</th>
                        <th>Produk</th>
                        <th>Tipe Pergerakan</th>
                        <th>Kategori Alasan</th>
                        <th class="text-center">Perubahan Kuantitas</th>
                        <th class="text-center">Stok Awal &rarr; Akhir</th>
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

        <div class="d-flex justify-content-center mt-3">
            {{ $movements->links() }}
        </div>
    </div>
</div>

