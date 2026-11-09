<div>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-qr-code-scan text-kop-primary me-2"></i>Verifikasi Pembayaran (Admin)</h4>
            <p class="text-muted small mb-0">Pantau seluruh catatan pembayaran transaksi QRIS dan tunai pelanggan.</p>
        </div>
        <div class="d-inline-flex gap-1 flex-wrap">
            <button type="button" wire:click="$set('filterStatus', 'all')" class="cat-pill {{ $filterStatus === 'all' ? 'active' : '' }}">Semua</button>
            <button type="button" wire:click="$set('filterStatus', 'waiting_verification')" class="cat-pill {{ $filterStatus === 'waiting_verification' ? 'active' : '' }}"><i class="bi bi-hourglass-split"></i> Verifikasi</button>
            <button type="button" wire:click="$set('filterStatus', 'paid')" class="cat-pill {{ $filterStatus === 'paid' ? 'active' : '' }}"><i class="bi bi-check-circle"></i> Lunas</button>
            <button type="button" wire:click="$set('filterStatus', 'rejected')" class="cat-pill {{ $filterStatus === 'rejected' ? 'active' : '' }}"><i class="bi bi-x-circle"></i> Ditolak</button>
        </div>
    </div>

    <div class="card card-custom border-0 p-3 shadow-sm bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-muted">
                    <tr>
                        <th>No. Pesanan</th>
                        <th>Pelanggan</th>
                        <th>Metode</th>
                        <th>Nominal</th>
                        <th>Bukti Transfer</th>
                        <th>Status</th>
                        <th>Verifikator</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @forelse($payments as $pay)
                        <tr>
                            <td>
                                <a href="{{ route('admin.orders.detail', $pay->order_id) }}" class="fw-bold text-kop-primary text-decoration-none">
                                    {{ $pay->order?->order_number ?? 'ORD-' . $pay->order_id }}
                                </a>
                                <small class="text-muted d-block">{{ $pay->created_at->format('d/m/Y H:i') }}</small>
                            </td>
                            <td>{{ $pay->order?->user?->name ?? 'Kasir Langsung' }}</td>
                            <td><span class="badge bg-secondary">{{ strtoupper($pay->payment_method) }}</span></td>
                            <td class="fw-bold text-kop-primary">Rp {{ number_format($pay->amount, 0, ',', '.') }}</td>
                            <td>
                                @if($pay->proof_image)
                                    <div role="button" wire:click="viewProofImage('{{ asset('storage/' . $pay->proof_image) }}')" class="d-inline-flex align-items-center gap-1 border p-1 rounded bg-light" title="Perbesar Bukti">
                                        <img src="{{ asset('storage/' . $pay->proof_image) }}" alt="Bukti Transfer" class="rounded" style="width: 40px; height: 40px; object-fit: cover;">
                                        <i class="bi bi-zoom-in text-muted"></i>
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td><span class="badge {{ $pay->status === 'paid' ? 'bg-success' : ($pay->status === 'waiting_verification' ? 'bg-warning text-dark' : 'bg-secondary') }}">{{ $pay->status_label }}</span></td>
                            <td>{{ $pay->verifier?->name ?: '-' }}</td>
                            <td class="text-center">
                                @if($pay->status !== 'paid')
                                    <button type="button" wire:click="approvePayment({{ $pay->id }})" class="btn btn-success btn-sm py-0 px-2" style="font-size: 0.72rem;">
                                        ACC Bayar
                                    </button>
                                @else
                                    <span class="text-success small"><i class="bi bi-check-circle-fill"></i> Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">Tidak ada data pembayaran.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $payments->links() }}
        </div>
    </div>

    <!-- Image Modal -->
    @if($showImageModal && $previewImageUrl)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.7);" wire:click="closeImageModal">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 bg-transparent text-center" wire:click.stop>
                    <div class="p-2">
                        <img src="{{ $previewImageUrl }}" alt="Bukti Transfer" class="img-fluid rounded shadow-lg" style="max-height: 80vh; object-fit: contain;">
                    </div>
                    <div class="mt-2 text-center">
                        <button type="button" class="btn btn-light btn-sm rounded-pill px-4" wire:click="closeImageModal">
                            <i class="bi bi-x-lg me-1"></i> Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

