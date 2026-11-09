<div>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-truck text-kop-primary me-2"></i>Barang Masuk / Pembelian (PO)</h4>
            <p class="text-muted small mb-0">Catatan penerimaan pasokan barang dari supplier ke gudang koperasi.</p>
        </div>
        <div>
            <a href="{{ route('admin.purchases.create') }}" class="btn btn-kop-primary fw-semibold">
                <i class="bi bi-plus-lg me-1"></i> Catat Barang Masuk Baru
            </a>
        </div>
    </div>

    <!-- Search -->
    <div class="card card-custom border-0 p-3 mb-4 shadow-sm bg-white" style="border-radius: 16px;">
        <div class="input-group" style="max-width: 420px;">
            <span class="input-group-text bg-white border-end-0 text-muted ps-3"><i class="bi bi-search text-kop-primary"></i></span>
            <input type="text" wire:model.live.debounce.300ms="search" class="form-control border-start-0 ps-2" placeholder="Cari No. PO, Supplier, atau No. Faktur...">
            @if($search)
                <button class="btn btn-link text-muted pe-3 text-decoration-none" wire:click="$set('search', '')">
                    <i class="bi bi-x-circle-fill"></i>
                </button>
            @endif
        </div>
    </div>

    <!-- Purchases Table -->
    <div class="card card-custom border-0 p-3 shadow-sm bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-muted">
                    <tr>
                        <th>No. Pembelian (PO)</th>
                        <th>Tanggal Masuk</th>
                        <th>Supplier / Pemasok</th>
                        <th>No. Faktur / Invoice</th>
                        <th>Jumlah Barang</th>
                        <th>Total Pembelian</th>
                        <th>Petugas Penerima</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @forelse($purchases as $po)
                        <tr>
                            <td class="fw-bold text-kop-primary">{{ $po->purchase_number }}</td>
                            <td>{{ $po->purchase_date->format('d/m/Y') }}</td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $po->supplier?->name ?? 'Supplier Umum' }}</div>
                                <small class="text-muted">{{ $po->supplier?->phone }}</small>
                            </td>
                            <td>{{ $po->invoice_number ?: '-' }}</td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $po->items->sum('quantity') }} unit</span>
                                <small class="text-muted d-block" style="font-size: 0.7rem;">({{ $po->items->count() }} jenis produk)</small>
                            </td>
                            <td class="fw-bold text-dark">Rp {{ number_format($po->total_amount, 0, ',', '.') }}</td>
                            <td>{{ $po->user?->name ?? 'Admin' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">Belum ada riwayat pembelian barang masuk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $purchases->links() }}
        </div>
    </div>
</div>

