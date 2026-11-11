<div>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-truck text-kop-primary me-2"></i>Barang Masuk / Pembelian
                (PO)</h4>
            <p class="text-muted small mb-0">Catatan penerimaan pasokan barang dari supplier ke gudang koperasi.</p>
        </div>
        <div>
            <a href="{{ route('admin.purchases.create') }}" class="btn btn-kop-primary fw-semibold">
                <i class="bi bi-plus-lg me-1"></i> Catat Barang Masuk Baru
            </a>
        </div>
    </div>

    <!-- Purchases DataTable Card -->
    <div class="card card-custom border-0 p-3 shadow-sm bg-white" style="border-radius: 16px;">
        @include('partials.datatable-top', ['placeholder' => 'Cari No. PO, Supplier, atau Faktur...'])

        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle mb-0 datatable-custom">
                <thead class="table-light small text-muted">
                    <tr>
                        @include('partials.datatable-th', [
                            'field' => 'purchase_number',
                            'label' => 'No. Pembelian (PO)',
                        ])
                        @include('partials.datatable-th', [
                            'field' => 'purchase_date',
                            'label' => 'Tanggal Masuk',
                        ])
                        <th>Supplier / Pemasok</th>
                        @include('partials.datatable-th', [
                            'field' => 'invoice_number',
                            'label' => 'No. Faktur / Invoice',
                        ])
                        <th>Jumlah Barang</th>
                        @include('partials.datatable-th', [
                            'field' => 'total_amount',
                            'label' => 'Total Pembelian',
                        ])
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
                                <span class="badge bg-light text-dark border">{{ $po->items->sum('quantity') }}
                                    unit</span>
                                <small class="text-muted d-block" style="font-size: 0.7rem;">({{ $po->items->count() }}
                                    jenis produk)</small>
                            </td>
                            <td class="fw-bold text-dark">Rp {{ number_format($po->total_amount, 0, ',', '.') }}</td>
                            <td>{{ $po->user?->name ?? 'Admin' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">Belum ada riwayat pembelian barang
                                masuk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $purchases->links('partials.pagination') }}
        </div>
    </div>
</div>
