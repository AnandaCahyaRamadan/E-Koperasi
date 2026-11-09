<?php

namespace App\Livewire\Admin\Purchases;

use Livewire\Component;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PurchaseForm extends Component
{
    public $supplier_id;
    public $purchase_date;
    public $invoice_number;
    public $notes;
    
    // Array of rows: [['product_id' => '', 'quantity' => 1, 'unit_price' => 0]]
    public $items = [];

    public function mount()
    {
        $this->purchase_date = date('Y-m-d');
        $this->addItem();
    }

    public function addItem()
    {
        $this->items[] = [
            'product_id' => '',
            'quantity' => 1,
            'unit_price' => 0,
        ];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        if (empty($this->items)) {
            $this->addItem();
        }
    }

    public function updatedItems($value, $key)
    {
        // If product selected, auto fill default purchase price
        $parts = explode('.', $key);
        if (count($parts) === 2 && $parts[1] === 'product_id') {
            $index = $parts[0];
            $product = Product::find($value);
            if ($product) {
                $this->items[$index]['unit_price'] = (float)$product->purchase_price;
            }
        }
    }

    public function calculateTotal()
    {
        return array_sum(array_map(function ($it) {
            return (int)($it['quantity'] ?? 0) * (float)($it['unit_price'] ?? 0);
        }, $this->items));
    }

    public function save()
    {
        $this->validate([
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'purchase_date' => ['required', 'date'],
            'invoice_number' => ['nullable', 'string', 'max:100'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ], [
            'items.*.product_id.required' => 'Pilih produk pada setiap baris item.',
            'items.*.quantity.min' => 'Jumlah minimal 1 unit.',
        ]);

        try {
            DB::transaction(function () {
                $total = $this->calculateTotal();
                $datePrefix = date('Ymd');
                $countToday = Purchase::whereDate('created_at', today())->count() + 1;
                $poNumber = 'PO-' . $datePrefix . '-' . str_pad($countToday, 4, '0', STR_PAD_LEFT);

                while (Purchase::where('purchase_number', $poNumber)->exists()) {
                    $poNumber = 'PO-' . $datePrefix . '-' . strtoupper(Str::random(5));
                }

                $purchase = Purchase::create([
                    'purchase_number' => $poNumber,
                    'supplier_id' => $this->supplier_id ?: null,
                    'user_id' => Auth::id(),
                    'purchase_date' => $this->purchase_date,
                    'total_amount' => $total,
                    'status' => 'received',
                    'invoice_number' => $this->invoice_number,
                    'notes' => $this->notes,
                ]);

                foreach ($this->items as $row) {
                    $product = Product::lockForUpdate()->find($row['product_id']);
                    $qty = (int)$row['quantity'];
                    $price = (float)$row['unit_price'];

                    PurchaseItem::create([
                        'purchase_id' => $purchase->id,
                        'product_id' => $product->id,
                        'quantity' => $qty,
                        'unit_price' => $price,
                        'subtotal' => $qty * $price,
                    ]);

                    $stockBefore = $product->stock;
                    $stockAfter = $stockBefore + $qty;

                    // Update product stock and optionally update purchase_price
                    $product->update([
                        'stock' => $stockAfter,
                        'purchase_price' => $price > 0 ? $price : $product->purchase_price,
                    ]);

                    // Audit stock movement
                    StockMovement::create([
                        'product_id' => $product->id,
                        'user_id' => Auth::id(),
                        'type' => 'in',
                        'reference_type' => 'purchase',
                        'reference_id' => $purchase->id,
                        'quantity' => $qty,
                        'stock_before' => $stockBefore,
                        'stock_after' => $stockAfter,
                        'notes' => "Penerimaan pasokan barang masuk dari PO {$purchase->purchase_number}",
                    ]);
                }
            });

            session()->flash('success', 'Barang masuk berhasil dicatat dan stok produk otomatis bertambah.');
            return redirect()->route('admin.purchases.index');

        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.purchases.purchase-form', [
            'suppliers' => Supplier::where('is_active', true)->orderBy('name')->get(),
            'products' => Product::where('is_active', true)->orderBy('name')->get(),
            'total' => $this->calculateTotal(),
        ])->layout('layouts.admin');
    }
}

