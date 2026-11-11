<div>
    <!-- Back & Order Heading -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-3">
        <div>
            <a href="{{ route('customer.orders') }}" class="text-decoration-none small text-muted" wire:navigate>
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Riwayat Pesanan
            </a>
            <div class="d-flex align-items-center gap-2 mt-1">
                <h5 class="fw-bold text-dark mb-0">{{ $order->order_number }}</h5>
                <span class="badge {{ $order->status_badge_class }}">{{ $order->status_label }}</span>
            </div>
            <small class="text-muted">Dibuat pada: {{ $order->created_at->format('d M Y, H:i') }} WIB</small>
        </div>

        <div class="d-flex gap-2">
            @if($order->status === 'pending_payment' && $order->payment_status !== 'paid')
                <button type="button" wire:click="cancelOrder" wire:confirm="Yakin ingin membatalkan pesanan ini?" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-x-circle me-1"></i> Batalkan Pesanan
                </button>
            @endif
        </div>
    </div>

    <!-- Progress Tracker Card -->
    <div class="card card-custom border-0 p-4 mb-4 shadow-sm">
        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-clock-history text-kop-primary me-2"></i>Status Pesanan</h6>
        
        @if($order->status === 'cancelled')
            <div class="alert alert-danger border-0 d-flex align-items-center gap-2 mb-0">
                <i class="bi bi-x-octagon-fill fs-4"></i>
                <div>
                    <h6 class="fw-bold mb-0">Pesanan Dibatalkan</h6>
                    <small>{{ $order->cancelled_reason ?: 'Pesanan tidak diproses lebih lanjut.' }}</small>
                </div>
            </div>
        @else
            <!-- Progress Stepper -->
            <div class="position-relative py-2">
                <div class="d-flex justify-content-between text-center position-relative">
                    <!-- Step 1: Menunggu Bayar -->
                    <div style="flex: 1;">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-1 {{ in_array($order->status, ['pending_payment', 'confirmed', 'processing', 'ready_for_pickup', 'on_delivery', 'completed']) ? 'bg-kop-primary text-white' : 'bg-light text-muted' }}" style="width: 36px; height: 36px;">
                            <i class="bi bi-credit-card"></i>
                        </div>
                        <div class="small fw-semibold {{ in_array($order->status, ['pending_payment', 'confirmed', 'processing', 'ready_for_pickup', 'on_delivery', 'completed']) ? 'text-kop-primary' : 'text-muted' }}" style="font-size: 0.75rem;">
                            1. Pembayaran
                        </div>
                    </div>

                    <!-- Step 2: Dikonfirmasi / Lunas -->
                    <div style="flex: 1;">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-1 {{ in_array($order->status, ['confirmed', 'processing', 'ready_for_pickup', 'on_delivery', 'completed']) ? 'bg-kop-primary text-white' : 'bg-light text-muted' }}" style="width: 36px; height: 36px;">
                            <i class="bi bi-check-lg"></i>
                        </div>
                        <div class="small fw-semibold {{ in_array($order->status, ['confirmed', 'processing', 'ready_for_pickup', 'on_delivery', 'completed']) ? 'text-kop-primary' : 'text-muted' }}" style="font-size: 0.75rem;">
                            2. Terverifikasi
                        </div>
                    </div>

                    <!-- Step 3: Sedang Disiapkan -->
                    <div style="flex: 1;">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-1 {{ in_array($order->status, ['processing', 'ready_for_pickup', 'on_delivery', 'completed']) ? 'bg-kop-primary text-white' : 'bg-light text-muted' }}" style="width: 36px; height: 36px;">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <div class="small fw-semibold {{ in_array($order->status, ['processing', 'ready_for_pickup', 'on_delivery', 'completed']) ? 'text-kop-primary' : 'text-muted' }}" style="font-size: 0.75rem;">
                            3. Disiapkan
                        </div>
                    </div>

                    <!-- Step 4: Siap Diambil / Diantar -->
                    <div style="flex: 1;">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-1 {{ in_array($order->status, ['ready_for_pickup', 'on_delivery', 'completed']) ? 'bg-kop-primary text-white' : 'bg-light text-muted' }}" style="width: 36px; height: 36px;">
                            <i class="bi {{ $order->order_type === 'delivery' ? 'bi-truck' : 'bi-bag-check' }}"></i>
                        </div>
                        <div class="small fw-semibold {{ in_array($order->status, ['ready_for_pickup', 'on_delivery', 'completed']) ? 'text-kop-primary' : 'text-muted' }}" style="font-size: 0.75rem;">
                            4. {{ $order->order_type === 'delivery' ? 'Pengantaran' : 'Siap Diambil' }}
                        </div>
                    </div>

                    <!-- Step 5: Selesai -->
                    <div style="flex: 1;">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-1 {{ $order->status === 'completed' ? 'bg-success text-white' : 'bg-light text-muted' }}" style="width: 36px; height: 36px;">
                            <i class="bi bi-check2-all"></i>
                        </div>
                        <div class="small fw-semibold {{ $order->status === 'completed' ? 'text-success' : 'text-muted' }}" style="font-size: 0.75rem;">
                            5. Selesai
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="row g-4">
        <!-- Payment & Instructions Column -->
        <div class="col-lg-6">
            <!-- QRIS / Payment Box -->
            <div class="card card-custom border-0 p-4 mb-3 shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-qr-code text-kop-primary me-2"></i>Instruksi Pembayaran</h6>
                    <span class="badge {{ $order->payment_status_badge_class }}">{{ $order->payment_status_label }}</span>
                </div>

                @if($order->payment_method === 'qris')
                    @if($order->payment_status === 'paid')
                        <div class="alert alert-success border-0 text-center py-3">
                            <i class="bi bi-check-circle-fill fs-2 text-success mb-2 d-block"></i>
                            <h6 class="fw-bold mb-1">Pembayaran Lunas & Terverifikasi!</h6>
                            <p class="small text-muted mb-0">Pesanan Anda saat ini masuk ke antrean penyiapan petugas kasir koperasi.</p>
                        </div>
                    @else
                        <!-- QRIS Code Container -->
                        <div class="text-center p-3 bg-light rounded-3 mb-3 border">
                            <div class="fw-bold text-dark mb-1">{{ $qrisMerchant }}</div>
                            <small class="text-muted d-block mb-3">NMID: {{ $qrisNmid }}</small>
                            
                            <!-- Static QRIS Display Image / Dynamic Generated SVG fallback -->
                            <div class="d-inline-block bg-white p-3 rounded-3 shadow-sm border mb-2">
                                @if($qrisImage)
                                    <img src="{{ asset('storage/' . $qrisImage) }}" alt="QRIS Koperasi" style="max-width: 200px; height: auto;">
                                @else
                                    <!-- Stylized QRIS Illustration -->
                                    <svg viewBox="0 0 160 160" width="160" height="160" xmlns="http://www.w3.org/2000/svg">
                                        <rect width="160" height="160" fill="white"/>
                                        <!-- Top Left Corner -->
                                        <rect x="15" y="15" width="40" height="40" fill="#16834A" rx="4"/>
                                        <rect x="23" y="23" width="24" height="24" fill="white"/>
                                        <rect x="29" y="29" width="12" height="12" fill="#16834A"/>
                                        <!-- Top Right Corner -->
                                        <rect x="105" y="15" width="40" height="40" fill="#16834A" rx="4"/>
                                        <rect x="113" y="23" width="24" height="24" fill="white"/>
                                        <rect x="119" y="29" width="12" height="12" fill="#16834A"/>
                                        <!-- Bottom Left Corner -->
                                        <rect x="15" y="105" width="40" height="40" fill="#16834A" rx="4"/>
                                        <rect x="23" y="113" width="24" height="24" fill="white"/>
                                        <rect x="29" y="119" width="12" height="12" fill="#16834A"/>
                                        <!-- Pattern Pixels -->
                                        <rect x="65" y="20" width="12" height="12" fill="#252B33"/>
                                        <rect x="85" y="25" width="12" height="12" fill="#252B33"/>
                                        <rect x="65" y="45" width="12" height="12" fill="#252B33"/>
                                        <rect x="85" y="55" width="12" height="12" fill="#252B33"/>
                                        <rect x="20" y="65" width="12" height="12" fill="#252B33"/>
                                        <rect x="40" y="75" width="12" height="12" fill="#252B33"/>
                                        <rect x="65" y="70" width="28" height="20" rx="3" fill="#F5C542"/>
                                        <text x="79" y="84" font-family="Arial" font-size="9" font-weight="bold" fill="#252B33" text-anchor="middle">QRIS</text>
                                        <rect x="105" y="65" width="12" height="12" fill="#252B33"/>
                                        <rect x="125" y="75" width="12" height="12" fill="#252B33"/>
                                        <rect x="65" y="105" width="12" height="12" fill="#252B33"/>
                                        <rect x="85" y="115" width="12" height="12" fill="#252B33"/>
                                        <rect x="105" y="105" width="12" height="12" fill="#252B33"/>
                                        <rect x="125" y="125" width="14" height="14" fill="#252B33"/>
                                    </svg>
                                @endif
                            </div>

                            <div class="mt-1">
                                <span class="small text-muted">Nominal Pembayaran Persis:</span>
                                <h4 class="fw-bold text-kop-primary my-1">Rp {{ number_format($order->final_amount, 0, ',', '.') }}</h4>
                                <small class="text-muted" style="font-size: 0.72rem;">Scan QR di atas menggunakan BCA, Mandiri, BRI, BNI, GoPay, OVO, atau ShopeePay.</small>
                            </div>
                        </div>

                        <!-- Rejection Warning -->
                        @if($order->payment_status === 'rejected' && $latestPayment && $latestPayment->rejection_reason)
                            <div class="alert alert-danger border-0 small mb-3">
                                <div class="fw-bold"><i class="bi bi-x-circle-fill me-1"></i> Pembayaran Sebelumnya Ditolak Kasir</div>
                                <div>Alasan: <strong>{{ $latestPayment->rejection_reason }}</strong></div>
                                <div class="mt-1">Silakan lakukan transfer ulang atau unggah bukti pembayaran yang valid di bawah ini.</div>
                            </div>
                        @endif

                        <!-- Waiting Verification Info -->
                        @if($order->payment_status === 'waiting_verification')
                            <div class="alert alert-warning border-0 small mb-3">
                                <i class="bi bi-hourglass-split me-1"></i> <strong>Bukti pembayaran telah terkirim!</strong> Petugas kasir sedang mencocokkan mutasi transaksi pada sistem merchant. Halaman ini akan diperbarui setelah di-ACC.
                            </div>
                        @endif

                        <!-- Upload Proof Form -->
                        @if(in_array($order->payment_status, ['pending', 'rejected', 'waiting_verification']))
                            <form wire:submit.prevent="uploadProof" class="bg-white p-3 border rounded-3">
                                <label class="form-label small fw-semibold text-dark mb-1">
                                    <i class="bi bi-upload text-kop-primary me-1"></i> Unggah Bukti Transfer QRIS
                                </label>
                                
                                <div class="mb-2">
                                    <input type="file" wire:model="proofImage" class="form-control form-control-sm @error('proofImage') is-invalid @enderror" accept="image/*">
                                    @error('proofImage') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                                </div>

                                <div wire:loading wire:target="proofImage" class="small text-muted mb-2">
                                    <div class="spinner-border spinner-border-sm me-1" role="status"></div> Mengunggah berkas...
                                </div>

                                @if ($proofImage)
                                    <div class="mb-3 text-center">
                                        <small class="text-muted d-block mb-1">Pratinjau Bukti:</small>
                                        <img src="{{ $proofImage->temporaryUrl() }}" class="img-thumbnail" style="max-height: 140px;">
                                    </div>
                                @endif

                                <button type="submit" wire:loading.attr="disabled" class="btn btn-kop-primary btn-sm w-100 fw-semibold">
                                    <i class="bi bi-send-check me-1"></i> Kirim Bukti Pembayaran
                                </button>
                            </form>
                        @endif
                    @endif
                @else
                    <!-- Cash / Tunai Instructions -->
                    <div class="alert alert-info border-0 p-3 mb-0">
                        <div class="d-flex gap-2">
                            <i class="bi bi-cash-stack fs-3 text-primary"></i>
                            <div>
                                <h6 class="fw-bold mb-1">Metode Pembayaran Tunai</h6>
                                <p class="small mb-2">
                                    Silakan siapkan uang pas sebesar <strong>Rp {{ number_format($order->final_amount, 0, ',', '.') }}</strong> dan bayarkan saat mengambil barang di kasir koperasi atau saat diantar ke meja Anda.
                                </p>
                                <span class="badge {{ $order->payment_status_badge_class }}">Status: {{ $order->payment_status_label }}</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Delivery / Pickup Details -->
            <div class="card card-custom border-0 p-4 shadow-sm">
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-geo-alt text-kop-primary me-2"></i>Informasi Pengambilan / Pengantaran</h6>
                
                <div class="mb-2 small">
                    <span class="text-muted d-block">Tipe Layanan:</span>
                    <span class="fw-bold text-dark">{{ $order->order_type_label }}</span>
                </div>

                @if($order->order_type === 'delivery' && $order->delivery)
                    <div class="mb-2 small">
                        <span class="text-muted d-block">Penerima & Kontak:</span>
                        <span class="fw-semibold text-dark">{{ $order->delivery->recipient_name }} ({{ $order->delivery->recipient_phone }})</span>
                    </div>
                    <div class="mb-2 small">
                        <span class="text-muted d-block">Alamat / Ruangan Meja:</span>
                        <span class="fw-semibold text-dark">{{ $order->delivery->delivery_address }}</span>
                    </div>
                    <div class="small">
                        <span class="text-muted d-block">Status Pengantaran:</span>
                        <span class="badge bg-kop-light text-kop-primary">{{ $order->delivery->status_label }}</span>
                    </div>
                @else
                    <div class="small text-muted">
                        Lokasi Pengambilan: <strong>{{ \App\Models\Setting::get('koperasi_address', 'Kantor Koperasi Lt. 1') }}</strong>
                    </div>
                @endif
            </div>
        </div>

        <!-- Order Items Column -->
        <div class="col-lg-6">
            <div class="card card-custom border-0 p-4 shadow-sm">
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-bag-check text-kop-primary me-2"></i>Daftar Barang Belanja</h6>

                <div class="table-responsive mb-3">
                    <table class="table table-sm table-hover table-striped align-middle mb-0 datatable">
                        <thead class="table-light">
                            <tr class="text-muted small">
                                <th>Produk</th>
                                <th class="text-center">Kuantitas</th>
                                <th class="text-end">Harga</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <h6 class="fw-bold text-dark mb-0 small">{{ $item->product_name }}</h6>
                                        <small class="text-muted">{{ $item->product_code }}</small>
                                    </td>
                                    <td class="text-center fw-semibold small">{{ $item->quantity }}</td>
                                    <td class="text-end small">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                    <td class="text-end fw-bold text-kop-primary small">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="border-top">
                            <tr>
                                <th colspan="3" class="text-end small text-muted">Total Pembayaran:</th>
                                <th class="text-end fw-bold text-kop-primary fs-6">Rp {{ number_format($order->final_amount, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if($order->notes)
                    <div class="bg-light rounded p-2 small mb-3">
                        <span class="text-muted">Catatan Pelanggan:</span>
                        <div class="fw-semibold text-dark">{{ $order->notes }}</div>
                    </div>
                @endif

                <!-- Audit History Stepper -->
                <h6 class="fw-bold text-dark mb-2 small"><i class="bi bi-journal-text me-1 text-kop-primary"></i>Riwayat Status Transaksi</h6>
                <div class="border rounded p-3 bg-light" style="max-height: 200px; overflow-y: auto;">
                    @forelse($order->statusHistories as $history)
                        <div class="d-flex align-items-start gap-2 mb-2 pb-2 border-bottom border-secondary-subtle">
                            <i class="bi bi-dot fs-4 text-kop-primary leading-none"></i>
                            <div class="small">
                                <div class="fw-semibold text-dark">{{ $history->status }}</div>
                                <div class="text-muted" style="font-size: 0.72rem;">{{ $history->notes }}</div>
                                <div class="text-muted" style="font-size: 0.65rem;">{{ $history->created_at->format('d/m/Y H:i:s') }}</div>
                            </div>
                        </div>
                    @empty
                        <small class="text-muted">Belum ada catatan riwayat perubahan status.</small>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

