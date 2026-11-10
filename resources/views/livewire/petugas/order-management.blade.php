<div>
    <!-- Title -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-receipt text-kop-primary me-2"></i>Antrean Pemrosesan Pesanan</h4>
            <p class="text-muted small mb-0">Kelola alur persiapan barang, pengambilan di etalase, dan pengantaran ke meja pegawai.</p>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="card card-custom border-0 p-3 mb-4 shadow-sm bg-white" style="border-radius: 16px;">
        <div class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted ps-3"><i class="bi bi-search text-kop-primary"></i></span>
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control border-start-0 ps-2" placeholder="Cari No. Pesanan atau Nama Pegawai...">
                    @if($search)
                        <button class="btn btn-link text-muted pe-3 text-decoration-none" wire:click="$set('search', '')">
                            <i class="bi bi-x-circle-fill"></i>
                        </button>
                    @endif
                </div>
            </div>
            <div class="col-md-7 text-md-end">
                <div class="d-flex gap-1 flex-wrap justify-content-md-end">
                    <button type="button" wire:click="$set('filterStatus', 'all')" class="cat-pill {{ $filterStatus === 'all' ? 'active' : '' }}">Semua</button>
                    <button type="button" wire:click="$set('filterStatus', 'confirmed')" class="cat-pill {{ $filterStatus === 'confirmed' ? 'active' : '' }}">Siap Disiapkan</button>
                    <button type="button" wire:click="$set('filterStatus', 'processing')" class="cat-pill {{ $filterStatus === 'processing' ? 'active' : '' }}">Sedang Disiapkan</button>
                    <button type="button" wire:click="$set('filterStatus', 'ready_for_pickup')" class="cat-pill {{ $filterStatus === 'ready_for_pickup' ? 'active' : '' }}">Siap Diambil</button>
                    <button type="button" wire:click="$set('filterStatus', 'on_delivery')" class="cat-pill {{ $filterStatus === 'on_delivery' ? 'active' : '' }}">Sedang Diantar</button>
                    <button type="button" wire:click="$set('filterStatus', 'completed')" class="cat-pill {{ $filterStatus === 'completed' ? 'active' : '' }}">Selesai</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card card-custom border-0 p-3 shadow-sm bg-white" style="border-radius: 16px;">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle mb-0 datatable-custom">
                <thead class="table-light small text-muted">
                    <tr>
                        <th>Pesanan</th>
                        <th>Pelanggan</th>
                        <th>Layanan</th>
                        <th>Barang</th>
                        <th>Tagihan</th>
                        <th>Bayar</th>
                        <th>Status</th>
                        <th class="text-center" style="width: 200px;">Alur Status</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @forelse($orders as $ord)
                        <tr>
                            <td>
                                <a href="{{ route('petugas.orders.detail', $ord->id) }}" class="fw-bold text-kop-primary text-decoration-none">
                                    {{ $ord->order_number }}
                                </a>
                                <small class="text-muted d-block">{{ $ord->created_at->format('d/m H:i') }}</small>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $ord->user?->name ?? 'Kasir Langsung' }}</div>
                                <small class="text-muted">{{ $ord->user?->employee_id ?? '-' }}</small>
                            </td>
                            <td>
                                <span class="badge {{ $ord->order_type === 'delivery' ? 'bg-primary-subtle text-primary-emphasis' : 'bg-light text-dark border' }}">
                                    {{ $ord->order_type_label }}
                                </span>
                            </td>
                            <td>
                                <span class="fw-semibold">{{ $ord->items->sum('quantity') }} unit</span>
                                <small class="text-muted d-block" style="font-size: 0.72rem;">{{ $ord->items->count() }} jenis produk</small>
                            </td>
                            <td class="fw-bold text-kop-primary">
                                Rp {{ number_format($ord->final_amount, 0, ',', '.') }}
                            </td>
                            <td>
                                <span class="badge {{ $ord->payment_status_badge_class }}">
                                    {{ $ord->payment_status_label }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $ord->status_badge_class }}">
                                    {{ $ord->status_label }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($ord->status === 'confirmed')
                                    <button type="button" wire:click="updateOrderStatus({{ $ord->id }}, 'processing')" class="btn btn-primary btn-sm py-1 px-2" style="font-size: 0.75rem;">
                                        <i class="bi bi-box-seam me-1"></i> Mulai Siapkan
                                    </button>
                                @elseif($ord->status === 'processing')
                                    @if($ord->order_type === 'delivery')
                                        <button type="button" wire:click="updateOrderStatus({{ $ord->id }}, 'on_delivery')" class="btn btn-indigo btn-sm text-white py-1 px-2" style="font-size: 0.75rem; background-color: #6610f2;">
                                            <i class="bi bi-truck me-1"></i> Mulai Diantar
                                        </button>
                                    @else
                                        <button type="button" wire:click="updateOrderStatus({{ $ord->id }}, 'ready_for_pickup')" class="btn btn-info btn-sm text-dark py-1 px-2" style="font-size: 0.75rem;">
                                            <i class="bi bi-bag-check me-1"></i> Siap Diambil
                                        </button>
                                    @endif
                                @elseif(in_array($ord->status, ['ready_for_pickup', 'on_delivery']))
                                    <button type="button" 
                                            wire:click="updateOrderStatus({{ $ord->id }}, 'completed')" 
                                            wire:confirm="Konfirmasi: Pesanan telah diterima dengan baik oleh pelanggan?"
                                            class="btn btn-success btn-sm py-1 px-2" style="font-size: 0.75rem;">
                                        <i class="bi bi-check2-circle me-1"></i> Tandai Selesai
                                    </button>
                                @elseif($ord->status === 'completed')
                                    <span class="text-success small fw-semibold"><i class="bi bi-check2-all me-1"></i>Selesai</span>
                                @elseif($ord->status === 'cancelled')
                                    <span class="text-danger small">Dibatalkan</span>
                                @else
                                    <span class="text-muted small">Menunggu Bayar</span>
                                @endif
                                <a href="{{ route('petugas.orders.detail', $ord->id) }}" class="btn-action-view ms-1" title="Lihat Rincian">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                Tidak ada antrean pesanan pada kriteria ini.
                            </td>
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

