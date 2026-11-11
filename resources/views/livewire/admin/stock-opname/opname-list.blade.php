<div>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-clipboard-check text-kop-primary me-2"></i>Stok Opname
                (Khusus Admin)</h4>
            <p class="text-muted small mb-0">Pencocokan berkala antara stok fisik nyata di toko dengan stok tercatat pada
                sistem database.</p>
        </div>
        <div>
            <a href="{{ route('admin.stock-opname.create') }}" class="btn btn-kop-primary fw-semibold">
                <i class="bi bi-plus-lg me-1"></i> Buat Sesi Opname Baru
            </a>
        </div>
    </div>

    <div class="card card-custom border-0 p-3 shadow-sm bg-white" style="border-radius: 16px;">
        @include('partials.datatable-top', ['placeholder' => 'Cari No. Opname atau Pembuat...'])

        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle mb-0 datatable-custom">
                <thead class="table-light small text-muted">
                    <tr>
                        @include('partials.datatable-th', [
                            'field' => 'opname_number',
                            'label' => 'No. Sesi Opname',
                        ])
                        @include('partials.datatable-th', [
                            'field' => 'created_at',
                            'label' => 'Tanggal Dibuat',
                        ])
                        <th>Dibuat Oleh</th>
                        <th>Total Item Diperiksa</th>
                        @include('partials.datatable-th', ['field' => 'status', 'label' => 'Status'])
                        <th>Disetujui Oleh</th>
                        @include('partials.datatable-th', [
                            'field' => 'approved_at',
                            'label' => 'Waktu Persetujuan',
                        ])
                        <th class="text-center" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @forelse($opnames as $op)
                        <tr>
                            <td class="fw-bold text-kop-primary">{{ $op->opname_number }}</td>
                            <td>{{ $op->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $op->creator?->name ?? 'Admin' }}</td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $op->details->count() }} produk</span>
                            </td>
                            <td>
                                <span class="badge {{ $op->status_badge_class }}">{{ $op->status_label }}</span>
                            </td>
                            <td>{{ $op->approver?->name ?: '-' }}</td>
                            <td>{{ $op->approved_at ? $op->approved_at->format('d/m/Y H:i') : '-' }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.stock-opname.detail', $op->id) }}"
                                    class="btn btn-outline-kop btn-sm py-0 px-2" style="font-size: 0.75rem;">
                                    Buka Sesi <i class="bi bi-arrow-right"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">Belum ada sesi stok opname yang
                                dibuat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $opnames->links('partials.pagination') }}
        </div>
    </div>
</div>
