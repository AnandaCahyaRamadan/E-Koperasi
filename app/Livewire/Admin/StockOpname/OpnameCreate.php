<?php

namespace App\Livewire\Admin\StockOpname;

use Livewire\Component;
use App\Models\Product;
use App\Models\StockOpname;
use App\Models\OpnameDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OpnameCreate extends Component
{
    public $notes = '';
    
    // Array of rows: [product_id => ['name' => ..., 'code' => ..., 'system_stock' => ..., 'physical_stock' => ..., 'notes' => ...]]
    public $opnameRows = [];

    public function mount()
    {
        $products = Product::with('unit')->where('is_active', true)->orderBy('name')->get();
        foreach ($products as $p) {
            $this->opnameRows[$p->id] = [
                'name' => $p->name,
                'code' => $p->code,
                'unit' => $p->unit?->symbol ?? 'pcs',
                'system_stock' => $p->stock,
                'physical_stock' => $p->stock, // Default matches system initially
                'notes' => '',
            ];
        }
    }

    public function saveOpname($status = 'draft')
    {
        try {
            $opname = DB::transaction(function () use ($status) {
                $user = Auth::user();
                $datePrefix = date('Ymd');
                $count = StockOpname::whereDate('created_at', today())->count() + 1;
                $opnameNumber = 'OPN-' . $datePrefix . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);

                while (StockOpname::where('opname_number', $opnameNumber)->exists()) {
                    $opnameNumber = 'OPN-' . $datePrefix . '-' . strtoupper(Str::random(4));
                }

                $opname = StockOpname::create([
                    'opname_number' => $opnameNumber,
                    'created_by' => $user->id,
                    'status' => $status,
                    'notes' => $this->notes ?: 'Sesi Penghitungan Fisik Stok Opname',
                ]);

                foreach ($this->opnameRows as $pId => $row) {
                    $phys = (int)$row['physical_stock'];
                    $sys = (int)$row['system_stock'];
                    $diff = $phys - $sys;

                    OpnameDetail::create([
                        'stock_opname_id' => $opname->id,
                        'product_id' => $pId,
                        'system_stock' => $sys,
                        'physical_stock' => $phys,
                        'difference' => $diff,
                        'notes' => $row['notes'] ?: null,
                    ]);
                }

                return $opname;
            });

            session()->flash('success', "Sesi Stok Opname {$opname->opname_number} berhasil dibuat!");
            return redirect()->route('admin.stock-opname.detail', $opname->id);

        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.stock-opname.opname-create')->layout('layouts.admin');
    }
}

