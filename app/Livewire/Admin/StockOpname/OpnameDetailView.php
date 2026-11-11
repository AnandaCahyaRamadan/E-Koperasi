<?php

namespace App\Livewire\Admin\StockOpname;

use Livewire\Component;
use Livewire\WithPagination;
use App\Traits\WithDataTable;
use App\Models\StockOpname;
use App\Models\OpnameDetail;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OpnameDetailView extends Component
{
    use WithPagination, WithDataTable;

    protected $paginationTheme = 'bootstrap';

    public $opnameId;
    public $search = '';

    public function mount($id)
    {
        $this->opnameId = $id;
        $this->sortField = 'id';
        $this->sortDirection = 'asc';
    }

    public function approveOpname()
    {
        $opname = StockOpname::with('details.product')->findOrFail($this->opnameId);

        if ($opname->status === 'approved') {
            session()->flash('error', 'Sesi stok opname ini sudah pernah disetujui sebelumnya.');
            return;
        }

        try {
            DB::transaction(function () use ($opname) {
                $user = Auth::user();

                // 1. Update each product's stock and record stock movement
                foreach ($opname->details as $detail) {
                    $product = Product::lockForUpdate()->find($detail->product_id);
                    if (!$product) continue;

                    $diff = $detail->physical_stock - $detail->system_stock;

                    if ($diff != 0) {
                        $stockBefore = $product->stock;
                        $stockAfter = $detail->physical_stock;

                        // Update product stock
                        $product->update(['stock' => $stockAfter]);

                        // Record movement
                        StockMovement::create([
                            'product_id' => $product->id,
                            'user_id' => $user->id,
                            'type' => 'adjustment',
                            'reference_type' => 'opname',
                            'reference_id' => $opname->id,
                            'quantity' => abs($diff),
                            'stock_before' => $stockBefore,
                            'stock_after' => $stockAfter,
                            'notes' => "Penyesuaian stok dari Sesi Opname {$opname->opname_number} (Selisih: " . ($diff > 0 ? "+$diff" : $diff) . ")",
                        ]);
                    }
                }

                // 2. Mark opname as approved
                $opname->update([
                    'status' => 'approved',
                    'approved_by' => $user->id,
                    'approved_at' => now(),
                ]);
            });

            session()->flash('success', "Stok Opname {$opname->opname_number} berhasil disetujui! Stok seluruh produk telah disinkronkan sesuai hasil fisik nyata.");

        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $opname = StockOpname::with(['creator', 'approver', 'details'])->findOrFail($this->opnameId);

        $totalDifferenceUnits = $opname->details->sum('difference');
        $matchCount = $opname->details->where('difference', 0)->count();
        $mismatchCount = $opname->details->where('difference', '!=', 0)->count();

        $detailsQuery = OpnameDetail::with('product.unit')
            ->where('stock_opname_id', $opname->id);

        if (!empty($this->search)) {
            $detailsQuery->whereHas('product', function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('code', 'like', "%{$this->search}%");
            });
        }

        $this->applySorting($detailsQuery, ['system_stock', 'physical_stock', 'difference', 'id'], 'id', 'asc');
        $details = $detailsQuery->paginate($this->perPage);

        return view('livewire.admin.stock-opname.opname-detail-view', [
            'opname' => $opname,
            'details' => $details,
            'totalDifferenceUnits' => $totalDifferenceUnits,
            'matchCount' => $matchCount,
            'mismatchCount' => $mismatchCount,
        ])->layout('layouts.admin');
    }
}

