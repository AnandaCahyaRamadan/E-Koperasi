<div>
    <div class="mb-4">
        <a href="{{ route('admin.stock-opname.index') }}" class="text-decoration-none small text-muted">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Riwayat Opname
        </a>
        <h4 class="fw-bold text-dark mt-1"><i class="bi bi-clipboard-plus text-kop-primary me-2"></i>Mulai Sesi Stok Opname Baru</h4>
        <p class="text-muted small mb-0">Sistem mengambil snapshot stok saat ini. Masukkan hasil penghitungan fisik untuk menghitung selisih.</p>
    </div>

    <div class="card card-custom border-0 p-4 shadow-sm bg-white mb-4">
        <h6 class="fw-bold text-dark mb-2">Informasi Sesi Opname</h6>
        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label small fw-semibold text-muted">Keterangan / Tujuan Sesi Opname</label>
                <input type="text" wire:model="notes" class="form-control" placeholder="Contoh: Stok Opname Berkala Akhir Bulan November 2026">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold text-muted">Waktu Snapshot Sistem</label>
                <input type="text" class="form-control bg-light" value="{{ now()->format('d/m/Y H:i:s') }} WIB" readonly>
            </div>
        </div>
    </div>

    <!-- Table of products to count -->
    <div class="card card-custom border-0 p-4 shadow-sm bg-white mb-4">
        <h6 class="fw-bold text-dark mb-3">Input Hasil Penghitungan Fisik Lapangan</h6>

        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped align-middle mb-0 datatable-custom">
                <thead class="table-light small text-muted">
                    <tr>
                        <th style="width: 50px;" class="text-center">No</th>
                        <th>Kode/SKU</th>
                        <th>Nama Produk</th>
                        <th class="text-center" style="width: 140px;">Stok Sistem (Snapshot)</th>
                        <th class="text-center" style="width: 150px;">Stok Fisik Nyata</th>
                        <th class="text-center" style="width: 120px;">Selisih</th>
                        <th style="min-width: 180px;">Catatan Selisih</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @php $no = 1; @endphp
                    @foreach($opnameRows as $pId => $row)
                        @php
                            $diff = (int)($row['physical_stock'] ?? 0) - (int)$row['system_stock'];
                        @endphp
                        <tr>
                            <td class="text-center text-muted">{{ $no++ }}</td>
                            <td class="fw-bold text-muted">{{ $row['code'] }}</td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $row['name'] }}</div>
                                <small class="text-muted">Satuan: {{ $row['unit'] }}</small>
                            </td>
                            <td class="text-center fw-bold bg-light">
                                {{ $row['system_stock'] }}
                            </td>
                            <td class="text-center">
                                <input type="number" wire:model.live="opnameRows.{{ $pId }}.physical_stock" class="form-control form-control-sm text-center fw-bold" min="0">
                            </td>
                            <td class="text-center fw-bold fs-6 {{ $diff == 0 ? 'text-success' : ($diff < 0 ? 'text-danger' : 'text-primary') }}">
                                {{ $diff > 0 ? '+' : '' }}{{ $diff }}
                            </td>
                            <td>
                                <input type="text" wire:model="opnameRows.{{ $pId }}.notes" class="form-control form-control-sm" placeholder="Keterangan jika ada selisih...">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2">
        <button type="button" wire:click="saveOpname('draft')" class="btn btn-outline-secondary px-4">
            <i class="bi bi-save me-1"></i> Simpan Draf
        </button>
        <button type="button" wire:click="saveOpname('reviewed')" class="btn btn-kop-primary fw-semibold px-4">
            <i class="bi bi-check2-circle me-1"></i> Simpan & Kirim untuk Ditinjau
        </button>
    </div>
</div>

