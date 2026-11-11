<div>
    <!-- Title -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-3">
        <div>
            <a href="{{ route('admin.stock-opname.index') }}" class="text-decoration-none small text-muted">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Sesi Opname
            </a>
            <div class="d-flex align-items-center gap-2 mt-1">
                <h4 class="fw-bold text-dark mb-0">{{ $opname->opname_number }}</h4>
                <span class="badge {{ $opname->status_badge_class }}">{{ $opname->status_label }}</span>
            </div>
            <small class="text-muted">Tanggal: {{ $opname->created_at->format('d M Y, H:i') }} WIB | Oleh: {{ $opname->creator?->name }}</small>
        </div>

        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Cetak Berita Acara
            </button>
            @if($opname->status !== 'approved')
                <button type="button" 
                        wire:click="approveOpname" 
                        wire:confirm="PERINGATAN: Menyetujui stok opname ini akan langsung menimpa stok sistem seluruh produk dengan angka stok fisik nyata dan mencatat pergerakan stok. Lanjutkan persetujuan?"
                        class="btn btn-success btn-sm fw-semibold">
                    <i class="bi bi-check-circle me-1"></i> Setujui & Sinkronkan Stok Produk
                </button>
            @endif
        </div>
    </div>

    <!-- Summary Metrics -->
    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <div class="card card-custom border-0 p-3 shadow-sm bg-white text-center">
                <span class="small text-muted">Total Produk Diperiksa</span>
                <h4 class="fw-bold text-dark my-1">{{ $opname->details->count() }}</h4>
                <small class="text-muted">Item barang</small>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card card-custom border-0 p-3 shadow-sm bg-white text-center">
                <span class="small text-muted">Stok Sesuai (Cocok)</span>
                <h4 class="fw-bold text-success my-1">{{ $matchCount }}</h4>
                <small class="text-muted">Tidak ada selisih</small>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card card-custom border-0 p-3 shadow-sm bg-white text-center">
                <span class="small text-muted">Ada Selisih (Surplus / Minus)</span>
                <h4 class="fw-bold {{ $mismatchCount > 0 ? 'text-danger' : 'text-success' }} my-1">{{ $mismatchCount }}</h4>
                <small class="text-muted">Net selisih: {{ $totalDifferenceUnits > 0 ? "+$totalDifferenceUnits" : $totalDifferenceUnits }} unit</small>
            </div>
        </div>
    </div>

    @if($opname->status === 'approved')
        <div class="alert alert-success border-0 p-3 mb-4 d-flex align-items-center gap-2">
            <i class="bi bi-check-circle-fill fs-4 text-success"></i>
            <div>
                <strong class="d-block">Sesi Stok Opname Ini Telah Disetujui (Approved)</strong>
                <small class="text-muted">
                    Disetujui oleh <strong>{{ $opname->approver?->name }}</strong> pada {{ $opname->approved_at?->format('d M Y, H:i') }} WIB. 
                    Semua stok produk pada sistem telah disesuaikan dan tercatat di mutasi stok.
                </small>
            </div>
        </div>
    @endif

    <!-- Opname Details Table -->
    <div class="card card-custom border-0 p-4 shadow-sm bg-white mb-4" style="border-radius: 16px;">
        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-list-check text-kop-primary me-2"></i>Daftar Pemeriksaan Fisik</h6>

        @include('partials.datatable-top', ['placeholder' => 'Cari kode atau nama produk...'])

        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle mb-0 datatable-custom">
                <thead class="table-light small text-muted">
                    <tr>
                        <th>No</th>
                        <th>Kode/SKU</th>
                        <th>Nama Produk</th>
                        @include('partials.datatable-th', ['field' => 'system_stock', 'label' => 'Stok Sistem (Awal)', 'align' => 'center'])
                        @include('partials.datatable-th', ['field' => 'physical_stock', 'label' => 'Stok Fisik Nyata', 'align' => 'center'])
                        @include('partials.datatable-th', ['field' => 'difference', 'label' => 'Selisih', 'align' => 'center'])
                        <th>Catatan Selisih</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @forelse($details as $index => $det)
                        <tr>
                            <td class="text-muted">{{ ($details->currentPage() - 1) * $details->perPage() + $index + 1 }}</td>
                            <td class="fw-bold text-muted">{{ $det->product?->code }}</td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $det->product?->name }}</div>
                                <small class="text-muted">{{ $det->product?->unit?->symbol }}</small>
                            </td>
                            <td class="text-center fw-bold bg-light">{{ $det->system_stock }}</td>
                            <td class="text-center fw-bold">{{ $det->physical_stock }}</td>
                            <td class="text-center fw-bold fs-6 {{ $det->difference == 0 ? 'text-success' : ($det->difference < 0 ? 'text-danger' : 'text-primary') }}">
                                {{ $det->difference > 0 ? "+{$det->difference}" : $det->difference }}
                            </td>
                            <td class="text-muted">{{ $det->notes ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Tidak ada item pemeriksaan yang cocok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $details->links('partials.pagination') }}
        </div>
    </div>
</div>

