<?php

namespace App\Livewire\Petugas;

use Livewire\Component;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PosKasir extends Component
{
    public $search = '';
    public $selectedCategory = '';
    
    // POS Cart items: [productId => ['product' => Product, 'qty' => int, 'price' => float]]
    public $posCart = [];
    
    public $paymentMethod = 'cash'; // 'cash' or 'qris'
    public $cashReceived = 0;
    public $customerName = 'Pelanggan Toko';
    public $lastCompletedOrder = null;

    public function addToCart($productId)
    {
        $product = Product::findOrFail($productId);

        if ($product->stock < 1) {
            session()->flash('error', "Stok {$product->name} habis!");
            return;
        }

        $currentQty = isset($this->posCart[$productId]) ? $this->posCart[$productId]['qty'] : 0;

        if ($currentQty + 1 > $product->stock) {
            session()->flash('error', "Stok {$product->name} tersisa {$product->stock} {$product->unit?->symbol}.");
            return;
        }

        if (isset($this->posCart[$productId])) {
            $this->posCart[$productId]['qty']++;
        } else {
            $this->posCart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'code' => $product->code,
                'price' => (float)$product->sell_price,
                'qty' => 1,
                'unit' => $product->unit?->symbol ?? 'pcs',
                'stock' => $product->stock,
            ];
        }
    }

    public function updateQty($productId, $qty)
    {
        $qty = (int)$qty;
        if ($qty <= 0) {
            unset($this->posCart[$productId]);
            return;
        }

        $product = Product::findOrFail($productId);
        if ($qty > $product->stock) {
            session()->flash('error', "Stok {$product->name} tersisa {$product->stock}.");
            $this->posCart[$productId]['qty'] = $product->stock;
            return;
        }

        if (isset($this->posCart[$productId])) {
            $this->posCart[$productId]['qty'] = $qty;
        }
    }

    public function removeItem($productId)
    {
        unset($this->posCart[$productId]);
    }

    public function clearCart()
    {
        $this->posCart = [];
        $this->cashReceived = 0;
    }

    public function processTransaction()
    {
        if (empty($this->posCart)) {
            session()->flash('error', 'Keranjang kasir masih kosong.');
            return;
        }

        $total = $this->calculateTotal();

        if ($this->paymentMethod === 'cash') {
            $cash = (float)$this->cashReceived;
            if ($cash < $total) {
                session()->flash('error', 'Jumlah uang tunai yang diterima kurang dari total belanja.');
                return;
            }
        }

        try {
            $order = DB::transaction(function () use ($total) {
                $user = Auth::user();

                // Re-verify stocks and lock
                foreach ($this->posCart as $item) {
                    $prod = Product::where('id', $item['id'])->lockForUpdate()->first();
                    if ($prod->stock < $item['qty']) {
                        throw new \Exception("Stok {$prod->name} tidak mencukupi (sisa {$prod->stock}).");
                    }
                }

                $datePrefix = date('Ymd');
                $todayOrdersCount = Order::whereDate('created_at', today())->count() + 1;
                $orderNumber = 'POS-' . $datePrefix . '-' . str_pad($todayOrdersCount, 4, '0', STR_PAD_LEFT);

                while (Order::where('order_number', $orderNumber)->exists()) {
                    $orderNumber = 'POS-' . $datePrefix . '-' . strtoupper(Str::random(5));
                }

                $order = Order::create([
                    'order_number' => $orderNumber,
                    'user_id' => null, // Pelanggan langsung / umum
                    'cashier_id' => $user->id,
                    'order_type' => 'pos',
                    'total_amount' => $total,
                    'discount_amount' => 0,
                    'final_amount' => $total,
                    'status' => 'completed',
                    'payment_status' => 'paid',
                    'payment_method' => $this->paymentMethod,
                    'notes' => 'Transaksi Kasir POS (' . $this->customerName . ')',
                    'paid_at' => now(),
                    'completed_at' => now(),
                ]);

                foreach ($this->posCart as $item) {
                    $prod = Product::find($item['id']);
                    
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $prod->id,
                        'product_name' => $prod->name,
                        'product_code' => $prod->code,
                        'price' => $item['price'],
                        'quantity' => $item['qty'],
                        'subtotal' => $item['price'] * $item['qty'],
                    ]);

                    $stockBefore = $prod->stock;
                    $stockAfter = $stockBefore - $item['qty'];
                    $prod->update(['stock' => $stockAfter]);

                    StockMovement::create([
                        'product_id' => $prod->id,
                        'user_id' => $user->id,
                        'type' => 'out',
                        'reference_type' => 'order',
                        'reference_id' => $order->id,
                        'quantity' => $item['qty'],
                        'stock_before' => $stockBefore,
                        'stock_after' => $stockAfter,
                        'notes' => "Penjualan langsung POS No. {$order->order_number}",
                    ]);
                }

                Payment::create([
                    'order_id' => $order->id,
                    'user_id' => null,
                    'verified_by' => $user->id,
                    'payment_method' => $this->paymentMethod,
                    'amount' => $total,
                    'status' => 'paid',
                    'verified_at' => now(),
                    'notes' => 'Pembayaran lunas via Kasir POS',
                ]);

                $order->addStatusHistory('completed', 'Transaksi POS kasir selesai dan lunas.', $user->id);

                return $order;
            });

            $this->lastCompletedOrder = Order::with('items')->find($order->id);
            $this->clearCart();
            session()->flash('success', "Transaksi {$order->order_number} berhasil diselesaikan!");

        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function closeReceiptModal()
    {
        $this->lastCompletedOrder = null;
    }

    public function calculateTotal()
    {
        return array_sum(array_map(function ($item) {
            return $item['price'] * $item['qty'];
        }, $this->posCart));
    }

    public function render()
    {
        $categories = Category::where('is_active', true)->get();

        $query = Product::with(['category', 'unit'])->where('is_active', true);

        if (!empty($this->selectedCategory)) {
            $query->where('category_id', $this->selectedCategory);
        }

        if (!empty($this->search)) {
            $query->search($this->search);
        }

        $products = $query->orderBy('name')->take(24)->get();
        $total = $this->calculateTotal();
        $change = max(0, (float)$this->cashReceived - $total);

        return view('livewire.petugas.pos-kasir', [
            'categories' => $categories,
            'products' => $products,
            'total' => $total,
            'change' => $change,
        ])->layout('layouts.petugas');
    }
}

