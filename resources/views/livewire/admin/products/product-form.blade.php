<div>
    <div class="mb-4">
        <a href="{{ route('admin.products.index') }}" class="text-decoration-none small text-muted">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Produk
        </a>
        <h4 class="fw-bold text-dark mt-1">
            <i class="bi {{ $productId ? 'bi-pencil-square' : 'bi-plus-circle' }} text-kop-primary me-2"></i>
            {{ $productId ? 'Edit Data Produk' : 'Tambah Produk Koperasi Baru' }}
        </h4>
    </div>

    <form wire:submit.prevent="save">
        <div class="row g-4">
            <!-- Left: Main Information -->
            <div class="col-lg-8">
                <div class="card card-custom border-0 p-4 shadow-sm bg-white mb-4">
                    <h6 class="fw-bold text-dark mb-3">Informasi Utama Produk</h6>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror" placeholder="Contoh: Kertas HVS A4 PaperOne">
                        @error('name') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Kode / Barcode / SKU <span class="text-danger">*</span></label>
                            <input type="text" wire:model="code" class="form-control @error('code') is-invalid @enderror">
                            @error('code') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Kategori Produk</label>
                            <select wire:model="category_id" class="form-select @error('category_id') is-invalid @enderror">
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Satuan Barang</label>
                            <select wire:model="unit_id" class="form-select @error('unit_id') is-invalid @enderror">
                                <option value="">Pilih Satuan</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->symbol }})</option>
                                @endforeach
                            </select>
                            @error('unit_id') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Status Tayang</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" wire:model="is_active" id="isActiveSwitch">
                                <label class="form-check-label small" for="isActiveSwitch">Tampilkan di Toko Pegawai</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-semibold text-muted">Deskripsi Rinci</label>
                        <textarea wire:model="description" rows="3" class="form-control" placeholder="Rincian informasi produk, ukuran, rasa, spesifikasi..."></textarea>
                    </div>
                </div>

                <!-- Price & Stock Card -->
                <div class="card card-custom border-0 p-4 shadow-sm bg-white">
                    <h6 class="fw-bold text-dark mb-3">Harga & Kuantitas Stok</h6>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Harga Beli / Pokok (Rp)</label>
                            <input type="number" wire:model="purchase_price" class="form-control @error('purchase_price') is-invalid @enderror" min="0">
                            @error('purchase_price') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Harga Jual Koperasi (Rp) <span class="text-danger">*</span></label>
                            <input type="number" wire:model="sell_price" class="form-control fw-bold text-kop-primary @error('sell_price') is-invalid @enderror" min="0">
                            @error('sell_price') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Jumlah Stok Saat Ini <span class="text-danger">*</span></label>
                            <input type="number" wire:model="stock" class="form-control @error('stock') is-invalid @enderror" min="0">
                            @error('stock') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Batas Minimum Peringatan Stok</label>
                            <input type="number" wire:model="min_stock" class="form-control @error('min_stock') is-invalid @enderror" min="0">
                            @error('min_stock') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Photo & Actions -->
            <div class="col-lg-4">
                <div class="card card-custom border-0 p-4 shadow-sm bg-white mb-4">
                    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-image text-kop-primary me-2"></i>Foto Produk</h6>

                    <div class="bg-light rounded text-center p-3 mb-3 border">
                        @if ($image)
                            <img src="{{ $image->temporaryUrl() }}" class="img-fluid rounded" style="max-height: 180px;">
                            <small class="text-muted d-block mt-2">Pratinjau Foto Baru</small>
                        @elseif($existing_image)
                            <img src="{{ asset('storage/' . $existing_image) }}" class="img-fluid rounded" style="max-height: 180px;">
                            <small class="text-muted d-block mt-2">Foto Saat Ini</small>
                        @else
                            <i class="bi bi-card-image text-secondary fs-1 d-block mb-2"></i>
                            <span class="small text-muted">Belum ada foto produk</span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-muted">Unggah Foto (Maks 2MB)</label>
                        <input type="file" wire:model="image" class="form-control form-control-sm @error('image') is-invalid @enderror" accept="image/*">
                        @error('image') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                        <div wire:loading wire:target="image" class="small text-muted mt-1">Mengunggah gambar...</div>
                    </div>

                    <div class="border-top pt-3">
                        <button type="submit" wire:loading.attr="disabled" class="btn btn-kop-primary w-100 py-2 fw-semibold mb-2">
                            <i class="bi bi-check2 me-1"></i> Simpan Data Produk
                        </button>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-light w-100 py-2">Batal</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

