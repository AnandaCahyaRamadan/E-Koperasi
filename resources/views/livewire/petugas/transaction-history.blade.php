<div>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-clock-history text-kop-primary me-2"></i>Riwayat Transaksi
                Kasir</h4>
            <p class="text-muted small mb-0">Catatan transaksi penjualan POS dan verifikasi pembayaran yang dilayani oleh
                Anda.</p>
        </div>
        <div>
            <div class="d-flex align-items-center gap-2">
                <label class="small text-muted text-nowrap">Pilih Tanggal:</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light text-kop-primary border-end-0"><i
                            class="bi bi-calendar3"></i></span>
                    <input type="text" wire:model.live="dateFilter"
                        class="form-control form-control-sm border-start-0 datepicker" placeholder="Pilih tanggal...">
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Box -->
    <div class="card card-custom border-0 p-3 mb-4 shadow-sm bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <small class="text-muted d-block">Total Nilai Transaksi Pada Tanggal Dipilih:</small>
                <h4 class="fw-bold text-kop-primary mb-0">Rp {{ number_format($totalAmount, 0, ',', '.') }}</h4>
            </div>
            <span class="badge bg-kop-light text-kop-primary px-3 py-2">
                <i class="bi bi-person-check me-1"></i> Petugas: {{ auth()->user()->name }}
            </span>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card card-custom border-0 p-3 shadow-sm bg-white" style="border-radius: 16px;">
        @include('partials.datatable-top', ['placeholder' => 'Cari No. Pesanan atau Pelanggan...'])

        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle mb-0 datatable-custom">
                <thead class="table-light small text-muted">
                    <tr>
                        @include('partials.datatable-th', [
                            'field' => 'order_number',
                            'label' => 'No. Pesanan',
                        ])
                        @include('partials.datatable-th', ['field' => 'created_at', 'label' => 'Waktu'])
                        <th>Pelanggan</th>
                        @include('partials.datatable-th', ['field' => 'order_type', 'label' => 'Tipe'])
                        @include('partials.datatable-th', [
                            'field' => 'payment_method',
                            'label' => 'Metode Bayar',
                        ])
                        @include('partials.datatable-th', ['field' => 'final_amount', 'label' => 'Total'])
                        @include('partials.datatable-th', ['field' => 'status', 'label' => 'Status'])
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @forelse($orders as $ord)
                        <tr>
                            <td class="fw-bold">{{ $ord->order_number }}</td>
                            <td>{{ $ord->created_at->format('H:i') }} WIB</td>
                            <td>{{ $ord->user?->name ?? 'Pelanggan Langsung (POS)' }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $ord->order_type_label }}</span></td>
                            <td><span class="badge bg-secondary">{{ strtoupper($ord->payment_method) }}</span></td>
                            <td class="fw-bold text-kop-primary">Rp {{ number_format($ord->final_amount, 0, ',', '.') }}
                            </td>
                            <td><span class="badge {{ $ord->status_badge_class }}">{{ $ord->status_label }}</span></td>
                            <td>
                                <a href="{{ route('petugas.orders.detail', $ord->id) }}"
                                    class="btn btn-outline-kop btn-sm py-0 px-2" style="font-size: 0.72rem;">
                                    Rincian
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">Tidak ada riwayat transaksi pada
                                tanggal ini.</td>
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
