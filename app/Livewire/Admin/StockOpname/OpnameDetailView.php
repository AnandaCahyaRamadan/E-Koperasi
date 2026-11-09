<?php

namespace App\Livewire\Admin\StockOpname;

use Livewire\Component;
use App\Models\StockOpname;
use App\Models\OpnameDetail;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OpnameDetailView extends Component
{
    public $opnameId;

    public function mount($id)
    {
        $this->opnameId = $id;
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
        $opname = StockOpname::with(['creator', 'approver', 'details.product.unit'])->findOrFail($this->opnameId);

        $totalDifferenceUnits = $opname->details->sum('difference');
        $matchCount = $opname->details->where('difference', 0)->count();
        $mismatchCount = $opname->details->where('difference', '!=', 0)->count();

        return view('livewire.admin.stock-opname.opname-detail-view', [
            'opname' => $opname,
            'totalDifferenceUnits' => $totalDifferenceUnits,
            'matchCount' => $matchCount,
            'mismatchCount' => $mismatchCount,
        ])->layout('layouts.admin');
    }
}

