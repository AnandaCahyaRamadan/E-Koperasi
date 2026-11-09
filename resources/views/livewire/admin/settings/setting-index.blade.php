<div>
    <div class="mb-4">
        <h4 class="fw-bold text-dark mb-1"><i class="bi bi-gear text-kop-primary me-2"></i>Pengaturan Sistem & QRIS</h4>
        <p class="text-muted small mb-0">Atur profil identitas koperasi dan konfigurasi QRIS statis untuk pembayaran pelanggan.</p>
    </div>

    <form wire:submit.prevent="save">
        <div class="row g-4">
            <!-- Left: Identitas Koperasi -->
            <div class="col-lg-7">
                <div class="card card-custom border-0 p-4 shadow-sm bg-white mb-4">
                    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-building text-kop-primary me-2"></i>Identitas Koperasi</h6>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Nama Koperasi <span class="text-danger">*</span></label>
                        <input type="text" wire:model="koperasi_name" class="form-control @error('koperasi_name') is-invalid @enderror">
                        @error('koperasi_name') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Alamat Kantor / Toko Koperasi <span class="text-danger">*</span></label>
                        <textarea wire:model="koperasi_address" rows="3" class="form-control @error('koperasi_address') is-invalid @enderror"></textarea>
                        @error('koperasi_address') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-sm-6">
                            <label class="form-label small fw-semibold text-muted">Nomor Telepon / WhatsApp <span class="text-danger">*</span></label>
                            <input type="text" wire:model="koperasi_phone" class="form-control @error('koperasi_phone') is-invalid @enderror">
                            @error('koperasi_phone') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small fw-semibold text-muted">Alamat Email Koperasi <span class="text-danger">*</span></label>
                            <input type="email" wire:model="koperasi_email" class="form-control @error('koperasi_email') is-invalid @enderror">
                            @error('koperasi_email') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Konfigurasi QRIS Statis -->
            <div class="col-lg-5">
                <div class="card card-custom border-0 p-4 shadow-sm bg-white mb-4">
                    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-qr-code text-kop-primary me-2"></i>Konfigurasi QRIS Resmi</h6>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Nama Merchant Tertera di QRIS <span class="text-danger">*</span></label>
                        <input type="text" wire:model="qris_merchant_name" class="form-control @error('qris_merchant_name') is-invalid @enderror" placeholder="Contoh: KOPERASI PEGAWAI KANTOR">
                        @error('qris_merchant_name') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">NMID (National Merchant ID) <span class="text-danger">*</span></label>
                        <input type="text" wire:model="qris_nmid" class="form-control @error('qris_nmid') is-invalid @enderror" placeholder="Contoh: ID1020030040506">
                        @error('qris_nmid') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Upload Gambar Barcode QRIS Resmi</label>
                        <div class="bg-light p-3 rounded text-center border mb-2">
                            @if ($qris_image)
                                <img src="{{ $qris_image->temporaryUrl() }}" class="img-fluid" style="max-height: 140px;">
                                <small class="text-muted d-block mt-1">Pratinjau Gambar Baru</small>
                            @elseif ($existing_qris_image)
                                <img src="{{ asset('storage/' . $existing_qris_image) }}" class="img-fluid" style="max-height: 140px;">
                                <small class="text-muted d-block mt-1">Gambar QRIS Aktif</small>
                            @else
                                <i class="bi bi-qr-code text-muted fs-1 d-block mb-1"></i>
                                <small class="text-muted">Menggunakan QRIS placeholder standar</small>
                            @endif
                        </div>
                        <input type="file" wire:model="qris_image" class="form-control form-control-sm @error('qris_image') is-invalid @enderror" accept="image/*">
                        @error('qris_image') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                    </div>

                    <button type="submit" wire:loading.attr="disabled" class="btn btn-kop-primary w-100 py-2 fw-semibold">
                        <i class="bi bi-check2-circle me-1"></i> Simpan Semua Pengaturan
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

