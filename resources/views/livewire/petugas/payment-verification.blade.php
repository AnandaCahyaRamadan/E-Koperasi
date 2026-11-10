<div>
    <!-- Page Title -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-qr-code-scan text-kop-primary me-2"></i>Verifikasi Pembayaran</h4>
            <p class="text-muted small mb-0">Periksa bukti transfer QRIS manual pada aplikasi merchant bank dan konfirmasi pembayaran tunai kasir.</p>
        </div>
    </div>

    <!-- Filter Buttons -->
    <div class="card card-custom border-0 p-3 mb-4 shadow-sm bg-white">
        <div class="d-flex flex-column flex-xl-row justify-content-between align-items-xl-center gap-3">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="small fw-semibold text-muted text-nowrap"><i class="bi bi-funnel me-1"></i>Status:</span>
                <div class="d-flex gap-1 flex-wrap">
                    <button type="button" wire:click="$set('filterStatus', 'waiting_verification')" class="cat-pill {{ $filterStatus === 'waiting_verification' ? 'active' : '' }}">
                        <i class="bi bi-hourglass-split"></i> Menunggu Verifikasi
                    </button>
                    <button type="button" wire:click="$set('filterStatus', 'paid')" class="cat-pill {{ $filterStatus === 'paid' ? 'active' : '' }}">
                        <i class="bi bi-check-circle"></i> Lunas / ACC
                    </button>
                    <button type="button" wire:click="$set('filterStatus', 'rejected')" class="cat-pill {{ $filterStatus === 'rejected' ? 'active' : '' }}">
                        <i class="bi bi-x-circle"></i> Ditolak
                    </button>
                    <button type="button" wire:click="$set('filterStatus', 'all')" class="cat-pill {{ $filterStatus === 'all' ? 'active' : '' }}">
                        Semua
                    </button>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="small fw-semibold text-muted text-nowrap">Metode:</span>
                <div class="d-flex gap-1 flex-wrap">
                    <button type="button" wire:click="$set('filterMethod', 'all')" class="cat-pill {{ $filterMethod === 'all' ? 'active' : '' }}">Semua</button>
                    <button type="button" wire:click="$set('filterMethod', 'qris')" class="cat-pill {{ $filterMethod === 'qris' ? 'active' : '' }}"><i class="bi bi-qr-code"></i> QRIS</button>
                    <button type="button" wire:click="$set('filterMethod', 'cash')" class="cat-pill {{ $filterMethod === 'cash' ? 'active' : '' }}"><i class="bi bi-cash"></i> Tunai</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Payments Table -->
    <div class="card card-custom border-0 p-3 shadow-sm bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-muted">
                    <tr>
                        <th>No. Pesanan</th>
                        <th>Pelanggan</th>
                        <th>Metode</th>
                        <th>Nominal</th>
                        <th>Bukti Bayar (QRIS)</th>
                        <th>Status</th>
                        <th>Verifikator</th>
                        <th class="text-center" style="width: 170px;">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @forelse($payments as $pay)
                        <tr>
                            <td>
                                <a href="{{ route('petugas.orders.detail', $pay->order_id) }}" class="fw-bold text-kop-primary text-decoration-none">
                                    {{ $pay->order?->order_number ?? 'ORD-' . $pay->order_id }}
                                </a>
                                <small class="text-muted d-block">{{ $pay->created_at->format('d/m/Y H:i') }}</small>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $pay->order?->user?->name ?? 'Pelanggan Umum' }}</div>
                                <small class="text-muted">{{ $pay->order?->user?->employee_id ?? '-' }}</small>
                            </td>
                            <td>
                                <span class="badge {{ $pay->payment_method === 'qris' ? 'bg-warning-subtle text-warning-emphasis' : 'bg-info-subtle text-info-emphasis' }}">
                                    {{ strtoupper($pay->payment_method) }}
                                </span>
                            </td>
                            <td class="fw-bold text-kop-primary">
                                Rp {{ number_format($pay->amount, 0, ',', '.') }}
                            </td>
                            <td>
                                @if($pay->proof_image)
                                    <div role="button" wire:click="viewProofImage('{{ asset('storage/' . $pay->proof_image) }}')" class="d-inline-flex align-items-center gap-1 border p-1 rounded bg-light" title="Klik untuk perbesar bukti transfer">
                                        <img src="{{ asset('storage/' . $pay->proof_image) }}" alt="Bukti Transfer" class="rounded" style="width: 44px; height: 44px; object-fit: cover;">
                                        <i class="bi bi-zoom-in text-muted fs-6"></i>
                                    </div>
                                @else
                                    <span class="text-muted small">Tidak ada berkas (Tunai/Kasir)</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $pay->status === 'paid' ? 'bg-success' : ($pay->status === 'waiting_verification' ? 'bg-warning text-dark' : ($pay->status === 'rejected' ? 'bg-danger' : 'bg-secondary')) }}">
                                    {{ $pay->status_label }}
                                </span>
                                @if($pay->status === 'rejected' && $pay->rejection_reason)
                                    <small class="text-danger d-block mt-1">Alasan: {{ $pay->rejection_reason }}</small>
                                @endif
                            </td>
                            <td>
                                @if($pay->verifier)
                                    <div class="fw-semibold text-dark" style="font-size: 0.72rem;">{{ $pay->verifier->name }}</div>
                                    <small class="text-muted" style="font-size: 0.65rem;">{{ $pay->verified_at?->format('d/m H:i') }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if(in_array($pay->status, ['waiting_verification', 'pending', 'rejected']))
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" 
                                                wire:click="approvePayment({{ $pay->id }})" 
                                                wire:confirm="Konfirmasi: Anda sudah memeriksa mutasi dan memastikan uang Rp {{ number_format($pay->amount, 0, ',', '.') }} telah masuk?"
                                                class="btn btn-success fw-semibold" 
                                                title="Setujui (ACC) Pembayaran">
                                            <i class="bi bi-check-lg me-1"></i> ACC
                                        </button>
                                        <button type="button" 
                                                wire:click="openRejectModal({{ $pay->id }})" 
                                                class="btn btn-outline-danger" 
                                                title="Tolak Pembayaran">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </div>
                                @else
                                    <span class="badge bg-light text-success border">
                                        <i class="bi bi-check-circle-fill me-1"></i> Terverifikasi
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="bi bi-clipboard2-check fs-2 d-block mb-2"></i>
                                Tidak ada data pembayaran yang sesuai dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $payments->links() }}
        </div>
    </div>

    <!-- Reject Modal -->
    @if($showRejectModal && $selectedPayment)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 card-custom">
                    <div class="modal-header border-0 pb-0">
                        <h6 class="modal-title fw-bold text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Tolak Pembayaran</h6>
                        <button type="button" class="btn-close" wire:click="closeRejectModal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="small text-muted mb-2">
                            Pesanan: <strong>{{ $selectedPayment->order?->order_number }}</strong> &bull; Total: <strong>Rp {{ number_format($selectedPayment->amount, 0, ',', '.') }}</strong>
                        </p>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-dark">Alasan Penolakan (Wajib)</label>
                            <textarea wire:model="rejectionReason" class="form-control @error('rejectionReason') is-invalid @enderror" rows="3" placeholder="Contoh: Bukti transfer buram / nominal tidak sesuai / dana belum masuk rekening"></textarea>
                            @error('rejectionReason') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light" wire:click="closeRejectModal">Batal</button>
                        <button type="button" wire:click="rejectPayment" class="btn btn-danger fw-semibold">Konfirmasi Tolak</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Image Preview Modal -->
    @if($showImageModal && $previewImageUrl)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.7);" wire:click="closeImageModal">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 bg-transparent text-center" wire:click.stop>
                    <div class="p-2">
                        <img src="{{ $previewImageUrl }}" alt="Bukti Pembayaran Penuh" class="img-fluid rounded shadow-lg" style="max-height: 80vh; object-fit: contain;">
                    </div>
                    <div class="mt-2 text-center">
                        <button type="button" class="btn btn-light btn-sm rounded-pill px-4" wire:click="closeImageModal">
                            <i class="bi bi-x-lg me-1"></i> Tutup Pratinjau
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

