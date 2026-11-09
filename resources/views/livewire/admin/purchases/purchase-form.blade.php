<div>
    <div class="mb-4">
        <a href="{{ route('admin.purchases.index') }}" class="text-decoration-none small text-muted">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Pembelian (PO)
        </a>
        <h4 class="fw-bold text-dark mt-1"><i class="bi bi-truck text-kop-primary me-2"></i>Catat Penerimaan Barang Masuk (PO)</h4>
        <p class="text-muted small mb-0">Input pasokan barang dari supplier. Stok produk akan bertambah secara otomatis.</p>
    </div>

    <form wire:submit.prevent="save">
        <div class="card card-custom border-0 p-4 shadow-sm bg-white mb-4">
            <h6 class="fw-bold text-dark mb-3">Informasi Faktur & Supplier</h6>

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-muted">Pemasok / Supplier</label>
                    <select wire:model="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror">
                        <option value="">Pilih Supplier</option>
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                        @endforeach
                    </select>
                    @error('supplier_id') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-muted">Tanggal Penerimaan <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-kop-primary border-end-0"><i class="bi bi-calendar-date"></i></span>
                        <input type="text" wire:model="purchase_date" class="form-control border-start-0 datepicker @error('purchase_date') is-invalid @enderror" placeholder="Pilih tanggal PO...">
                    </div>
                    @error('purchase_date') <div class="invalid-feedback d-block small">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-muted">No. Faktur / Surat Jalan Supplier</label>
                    <input type="text" wire:model="invoice_number" class="form-control" placeholder="Contoh: INV/2026/0091">
                </div>
            </div>
        </div>

        <!-- Purchase Items Table -->
        <div class="card card-custom border-0 p-4 shadow-sm bg-white mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-dark mb-0">Daftar Barang yang Diterima</h6>
                <button type="button" wire:click="addItem" class="btn btn-outline-kop btn-sm">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Baris Produk
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="table-light small text-muted">
                        <tr>
                            <th style="min-width: 250px;">Produk</th>
                            <th style="width: 130px;" class="text-center">Kuantitas Masuk</th>
                            <th style="width: 160px;" class="text-end">Harga Beli Satuan (Rp)</th>
                            <th style="width: 160px;" class="text-end">Subtotal (Rp)</th>
                            <th style="width: 50px;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $idx => $row)
                            <tr>
                                <td>
                                    <select wire:model.live="items.{{ $idx }}.product_id" class="form-select form-select-sm @error("items.$idx.product_id") is-invalid @enderror">
                                        <option value="">Pilih Produk</option>
                                        @foreach($products as $prod)
                                            <option value="{{ $prod->id }}">{{ $prod->code }} - {{ $prod->name }} (Stok saat ini: {{ $prod->stock }} {{ $prod->unit?->symbol }})</option>
                                        @endforeach
                                    </select>
                                    @error("items.$idx.product_id") <div class="invalid-feedback small">{{ $message }}</div> @enderror
                                </td>
                                <td>
                                    <input type="number" wire:model.live="items.{{ $idx }}.quantity" class="form-control form-control-sm text-center @error("items.$idx.quantity") is-invalid @enderror" min="1">
                                    @error("items.$idx.quantity") <div class="invalid-feedback small">{{ $message }}</div> @enderror
                                </td>
                                <td>
                                    <input type="number" wire:model.live="items.{{ $idx }}.unit_price" class="form-control form-control-sm text-end" min="0" step="500">
                                </td>
                                <td class="text-end fw-bold text-kop-primary small">
                                    Rp {{ number_format(((int)($row['quantity'] ?? 0)) * ((float)($row['unit_price'] ?? 0)), 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    <button type="button" wire:click="removeItem({{ $idx }})" class="btn btn-link text-danger p-0" title="Hapus baris">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="3" class="text-end fw-bold">TOTAL PEMBELIAN:</th>
                            <th class="text-end fw-bold text-kop-primary fs-6">Rp {{ number_format($total, 0, ',', '.') }}</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="mt-3">
                <label class="form-label small text-muted">Catatan Tambahan</label>
                <textarea wire:model="notes" rows="2" class="form-control form-control-sm" placeholder="Catatan PO / penerimaan barang..."></textarea>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('admin.purchases.index') }}" class="btn btn-light px-4">Batal</a>
            <button type="submit" wire:loading.attr="disabled" class="btn btn-kop-primary fw-semibold px-4">
                <i class="bi bi-check2-circle me-1"></i> Simpan & Perbarui Stok Produk
            </button>
        </div>
    </form>
</div>

