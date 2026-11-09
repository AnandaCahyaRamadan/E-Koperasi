<div>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-clipboard-check text-kop-primary me-2"></i>Stok Opname (Khusus Admin)</h4>
            <p class="text-muted small mb-0">Pencocokan berkala antara stok fisik nyata di toko dengan stok tercatat pada sistem database.</p>
        </div>
        <div>
            <a href="{{ route('admin.stock-opname.create') }}" class="btn btn-kop-primary fw-semibold">
                <i class="bi bi-plus-lg me-1"></i> Buat Sesi Opname Baru
            </a>
        </div>
    </div>

    <div class="card card-custom border-0 p-3 shadow-sm bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-muted">
                    <tr>
                        <th>No. Sesi Opname</th>
                        <th>Tanggal Dibuat</th>
                        <th>Dibuat Oleh</th>
                        <th>Total Item Diperiksa</th>
                        <th>Status</th>
                        <th>Disetujui Oleh</th>
                        <th>Waktu Persetujuan</th>
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
                                <a href="{{ route('admin.stock-opname.detail', $op->id) }}" class="btn btn-outline-kop btn-sm py-0 px-2" style="font-size: 0.75rem;">
                                    Buka Sesi <i class="bi bi-arrow-right"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">Belum ada sesi stok opname yang dibuat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $opnames->links() }}
        </div>
    </div>
</div>

