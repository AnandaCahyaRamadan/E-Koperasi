<div>
    <div class="mb-4">
        <h5 class="fw-bold text-dark mb-1"><i class="bi bi-person-circle text-kop-primary me-2"></i>Profil Pegawai</h5>
        <p class="text-muted small mb-0">Kelola identitas keanggotaan koperasi dan kata sandi akun Anda</p>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card card-custom border-0 p-4 shadow-sm">
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-person-lines-fill text-kop-primary me-2"></i>Informasi Akun Pegawai</h6>

                <form wire:submit.prevent="updateProfile">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Nama Lengkap</label>
                        <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror">
                        @error('name') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">NIP / ID Pegawai</label>
                        <input type="text" wire:model="employee_id" class="form-control @error('employee_id') is-invalid @enderror">
                        @error('employee_id') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Email</label>
                        <input type="email" wire:model="email" class="form-control @error('email') is-invalid @enderror">
                        @error('email') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Nomor WhatsApp / HP</label>
                        <input type="text" wire:model="phone" class="form-control @error('phone') is-invalid @enderror">
                        @error('phone') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                    </div>

                    <button type="submit" class="btn btn-kop-primary rounded-pill px-4 fw-semibold">
                        Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card card-custom border-0 p-4 shadow-sm mb-4">
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-shield-lock text-kop-primary me-2"></i>Ganti Kata Sandi</h6>

                <form wire:submit.prevent="updatePassword">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Kata Sandi Saat Ini</label>
                        <input type="password" wire:model="current_password" class="form-control @error('current_password') is-invalid @enderror">
                        @error('current_password') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Kata Sandi Baru</label>
                        <input type="password" wire:model="new_password" class="form-control @error('new_password') is-invalid @enderror">
                        @error('new_password') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Ulangi Kata Sandi Baru</label>
                        <input type="password" wire:model="new_password_confirmation" class="form-control">
                    </div>

                    <button type="submit" class="btn btn-outline-kop rounded-pill px-4 fw-semibold">
                        Ganti Kata Sandi
                    </button>
                </form>
            </div>

            <!-- Logout card -->
            <div class="card card-custom border-0 p-3 bg-light text-center">
                <div class="small text-muted mb-2">Ingin keluar dari akun ini?</div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-4">
                        <i class="bi bi-box-arrow-right me-1"></i> Logout dari E-Koperasi
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

