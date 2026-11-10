<div>
    <!-- Title -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-cart-check text-kop-primary me-2"></i>Semua Pesanan & Transaksi</h4>
            <p class="text-muted small mb-0">Daftar seluruh transaksi pemesanan online pegawai dan transaksi langsung kasir POS.</p>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card card-custom border-0 p-3 mb-4 shadow-sm bg-white" style="border-radius: 16px;">
        <div class="row g-2 align-items-center">
            <div class="col-md-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search text-kop-primary"></i></span>
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control border-start-0 ps-1" placeholder="No. Pesanan / Nama...">
                </div>
            </div>
            <div class="col-6 col-md-2">
                <select wire:model.live="filterStatus" class="form-select form-select-sm">
                    <option value="all">Semua Status</option>
                    <option value="pending_payment">Menunggu Bayar</option>
                    <option value="confirmed">Dikonfirmasi (Lunas)</option>
                    <option value="processing">Sedang Disiapkan</option>
                    <option value="ready_for_pickup">Siap Diambil</option>
                    <option value="on_delivery">Sedang Diantar</option>
                    <option value="completed">Selesai</option>
                    <option value="cancelled">Dibatalkan</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <select wire:model.live="filterPayment" class="form-select form-select-sm">
                    <option value="all">Semua Bayar</option>
                    <option value="paid">Lunas</option>
                    <option value="waiting_verification">Verifikasi</option>
                    <option value="pending">Pending</option>
                    <option value="rejected">Ditolak</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-calendar3"></i></span>
                    <input type="text" wire:model.live="startDate" class="form-control border-start-0 ps-1 datepicker" placeholder="Dari tgl...">
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-calendar-check"></i></span>
                    <input type="text" wire:model.live="endDate" class="form-control border-start-0 ps-1 datepicker" placeholder="Sampai tgl...">
                </div>
            </div>
            <div class="col-12 col-md-1">
                <button type="button" wire:click="$set('search', ''); $set('filterStatus', 'all'); $set('filterPayment', 'all'); $set('startDate', ''); $set('endDate', '');" class="btn btn-outline-secondary btn-sm w-100 rounded-pill">
                    Reset
                </button>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card card-custom border-0 p-3 shadow-sm bg-white" style="border-radius: 16px;">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle mb-0 datatable-custom">
                <thead class="table-light small text-muted">
                    <tr>
                        <th>No. Pesanan</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Tipe</th>
                        <th>Metode Bayar</th>
                        <th>Total</th>
                        <th>Status Bayar</th>
                        <th>Status Pesanan</th>
                        <th class="text-center" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @forelse($orders as $ord)
                        <tr>
                            <td class="fw-bold text-kop-primary">{{ $ord->order_number }}</td>
                            <td>{{ $ord->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $ord->user?->name ?? 'Kasir Langsung (POS)' }}</div>
                                <small class="text-muted">{{ $ord->user?->employee_id ?? '-' }}</small>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $ord->order_type_label }}</span></td>
                            <td><span class="badge bg-secondary">{{ strtoupper($ord->payment_method) }}</span></td>
                            <td class="fw-bold text-kop-primary">Rp {{ number_format($ord->final_amount, 0, ',', '.') }}</td>
                            <td><span class="badge {{ $ord->payment_status_badge_class }}">{{ $ord->payment_status_label }}</span></td>
                            <td><span class="badge {{ $ord->status_badge_class }}">{{ $ord->status_label }}</span></td>
                            <td class="text-center">
                                <a href="{{ route('admin.orders.detail', $ord->id) }}" class="btn-action-view" title="Lihat Rincian Pesanan">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-5">Tidak ada transaksi ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $orders->links('partials.pagination') }}
        </div>
    </div>
</div>

