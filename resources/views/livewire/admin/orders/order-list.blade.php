<div>
    <!-- Title -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-cart-check text-kop-primary me-2"></i>Semua Pesanan & Transaksi</h4>
            <p class="text-muted small mb-0">Daftar seluruh transaksi pemesanan online pegawai dan transaksi langsung kasir POS.</p>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card card-custom border-0 p-3 mb-4 shadow-sm bg-white">
        <div class="row g-2 align-items-center">
            <div class="col-md-3">
                <input type="text" wire:model.live.debounce.300ms="search" class="form-control form-control-sm" placeholder="No. Pesanan / Nama...">
            </div>
            <div class="col-6 col-md-2">
                <select wire:model.live="filterStatus" class="form-select form-select-sm">
                    <option value="all">Semua Status Pesanan</option>
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
                    <option value="all">Semua Pembayaran</option>
                    <option value="paid">Lunas</option>
                    <option value="waiting_verification">Menunggu Verifikasi</option>
                    <option value="pending">Pending</option>
                    <option value="rejected">Ditolak</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <input type="date" wire:model.live="startDate" class="form-control form-control-sm" placeholder="Dari">
            </div>
            <div class="col-6 col-md-2">
                <input type="date" wire:model.live="endDate" class="form-control form-control-sm" placeholder="Sampai">
            </div>
            <div class="col-12 col-md-1">
                <button type="button" wire:click="$set('search', ''); $set('filterStatus', 'all'); $set('filterPayment', 'all'); $set('startDate', ''); $set('endDate', '');" class="btn btn-outline-secondary btn-sm w-100">
                    Reset
                </button>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card card-custom border-0 p-3 shadow-sm bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
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
                                <a href="{{ route('admin.orders.detail', $ord->id) }}" class="btn btn-outline-secondary btn-sm py-0 px-2" style="font-size: 0.72rem;">
                                    Detail
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

        <div class="d-flex justify-content-center mt-3">
            {{ $orders->links() }}
        </div>
    </div>
</div>

