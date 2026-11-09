<div>
    <!-- Title -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-people text-kop-primary me-2"></i>Manajemen Pengguna</h4>
            <p class="text-muted small mb-0">Kelola akun hak akses Administrator, Petugas Kasir, dan Anggota / Pegawai Koperasi.</p>
        </div>
        <div>
            <button type="button" wire:click="openCreateModal" class="btn btn-kop-primary fw-semibold">
                <i class="bi bi-person-plus me-1"></i> Tambah Pengguna Baru
            </button>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card card-custom border-0 p-3 mb-4 shadow-sm bg-white">
        <div class="row g-2 align-items-center">
            <div class="col-md-6">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control border-start-0 ps-0" placeholder="Cari nama, email, atau NIP pegawai...">
                </div>
            </div>
            <div class="col-md-6 text-md-end">
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" wire:click="$set('filterRole', 'all')" class="btn {{ $filterRole === 'all' ? 'btn-kop-primary fw-bold' : 'btn-outline-secondary' }}">Semua Role</button>
                    @foreach($roles as $r)
                        <button type="button" wire:click="$set('filterRole', {{ $r->id }})" class="btn {{ $filterRole == $r->id ? 'btn-kop-primary fw-bold' : 'btn-outline-secondary' }}">{{ $r->name }}</button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card card-custom border-0 p-3 shadow-sm bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-muted">
                    <tr>
                        <th>Nama Pengguna</th>
                        <th>Role Akses</th>
                        <th>Email Akun</th>
                        <th>NIP / ID Pegawai</th>
                        <th>No. Telepon / WA</th>
                        <th>Status</th>
                        <th class="text-center" style="width: 130px;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @forelse($users as $u)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="bg-kop-light text-kop-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px;">
                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                    </div>
                                    <div class="fw-bold text-dark">{{ $u->name }}</div>
                                </div>
                            </td>
                            <td>
                                <span class="badge {{ $u->isAdmin() ? 'bg-danger-subtle text-danger' : ($u->isPetugas() ? 'bg-warning-subtle text-warning-emphasis' : 'bg-primary-subtle text-primary') }} fw-bold">
                                    {{ $u->role?->name ?? 'User' }}
                                </span>
                            </td>
                            <td>{{ $u->email }}</td>
                            <td>{{ $u->employee_id ?: '-' }}</td>
                            <td>{{ $u->phone ?: '-' }}</td>
                            <td>
                                <button type="button" wire:click="toggleActive({{ $u->id }})" class="btn btn-sm py-0 px-2 rounded-pill {{ $u->is_active ? 'btn-success' : 'btn-secondary' }}" style="font-size: 0.72rem;">
                                    {{ $u->is_active ? 'Aktif' : 'Non-Aktif' }}
                                </button>
                            </td>
                            <td class="text-center">
                                <button type="button" wire:click="openEditModal({{ $u->id }})" class="btn btn-outline-primary btn-sm py-0 px-2" style="font-size: 0.72rem;" title="Edit Pengguna">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">Pengguna tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $users->links() }}
        </div>
    </div>

    <!-- Modal Form -->
    @if($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 card-custom">
                    <div class="modal-header border-0 pb-0">
                        <h6 class="modal-title fw-bold text-dark">{{ $userId ? 'Edit Pengguna' : 'Tambah Pengguna Baru' }}</h6>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <form wire:submit.prevent="save">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-muted">Role Hak Akses <span class="text-danger">*</span></label>
                                <select wire:model="role_id" class="form-select @error('role_id') is-invalid @enderror">
                                    @foreach($roles as $r)
                                        <option value="{{ $r->id }}">{{ $r->name }} ({{ $r->slug }})</option>
                                    @endforeach
                                </select>
                                @error('role_id') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-muted">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror" placeholder="Nama pengguna">
                                @error('name') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">Email <span class="text-danger">*</span></label>
                                    <input type="email" wire:model="email" class="form-control @error('email') is-invalid @enderror" placeholder="email@koperasi.com">
                                    @error('email') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">NIP / ID Pegawai</label>
                                    <input type="text" wire:model="employee_id" class="form-control @error('employee_id') is-invalid @enderror" placeholder="PEG-001">
                                    @error('employee_id') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">No. WhatsApp / HP</label>
                                    <input type="text" wire:model="phone" class="form-control" placeholder="08123456789">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">Kata Sandi {{ $userId ? '(Kosongkan jika tidak ubah)' : '*' }}</label>
                                    <input type="password" wire:model="password" class="form-control @error('password') is-invalid @enderror" placeholder="Min. 6 karakter">
                                    @error('password') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" wire:model="is_active" id="userActive">
                                <label class="form-check-label small" for="userActive">Akun Aktif</label>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn btn-light" wire:click="closeModal">Batal</button>
                            <button type="submit" class="btn btn-kop-primary fw-semibold">Simpan Pengguna</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

