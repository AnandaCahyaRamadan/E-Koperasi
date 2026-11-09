<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Delivery;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutView extends Component
{
    public $order_type = 'pickup'; // 'pickup' or 'delivery'
    public $payment_method = 'qris'; // 'qris' or 'cash'
    public $delivery_address = '';
    public $recipient_name = '';
    public $recipient_phone = '';
    public $notes = '';

    public function mount()
    {
        $user = Auth::user();
        $this->recipient_name = $user->name;
        $this->recipient_phone = $user->phone ?? '';
    }

    public function rules()
    {
        $rules = [
            'order_type' => ['required', 'in:pickup,delivery'],
            'payment_method' => ['required', 'in:qris,cash'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];

        if ($this->order_type === 'delivery') {
            $rules['delivery_address'] = ['required', 'string', 'min:5', 'max:500'];
            $rules['recipient_name'] = ['required', 'string', 'max:100'];
            $rules['recipient_phone'] = ['required', 'string', 'max:20'];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'delivery_address.required' => 'Mohon sebutkan lokasi pengantaran (misal: Lantai 3, Ruang Keuangan / Meja Budi).',
            'recipient_name.required' => 'Nama penerima wajib diisi.',
            'recipient_phone.required' => 'Nomor WhatsApp / HP penerima wajib diisi.',
        ];
    }

    public function processCheckout()
    {
        $this->validate();

        $user = Auth::user();
        $cart = Cart::with(['items.product'])->where('user_id', $user->id)->first();

        if (!$cart || $cart->items->isEmpty()) {
            session()->flash('error', 'Keranjang belanja Anda kosong.');
            return redirect()->route('customer.cart');
        }

        try {
            $order = DB::transaction(function () use ($cart, $user) {
                // 1. Lock each product for update and re-verify stock
                $subtotal = 0;
                $itemsToOrder = [];

                foreach ($cart->items as $cartItem) {
                    $product = Product::where('id', $cartItem->product_id)->lockForUpdate()->first();

                    if (!$product || !$product->is_active) {
                        throw new \Exception("Produk {$cartItem->product->name} tidak lagi tersedia.");
                    }

                    if ($product->stock < $cartItem->quantity) {
                        throw new \Exception("Maaf, stok {$product->name} tersisa {$product->stock}, tidak mencukupi untuk kuantitas yang dipesan ({$cartItem->quantity}).");
                    }

                    $itemSubtotal = $product->sell_price * $cartItem->quantity;
                    $subtotal += $itemSubtotal;

                    $itemsToOrder[] = [
                        'product' => $product,
                        'quantity' => $cartItem->quantity,
                        'price' => $product->sell_price,
                        'subtotal' => $itemSubtotal,
                    ];
                }

                // 2. Generate unique order number: ORD-YYYYMMDD-XXXX
                $datePrefix = date('Ymd');
                $todayOrdersCount = Order::whereDate('created_at', today())->count() + 1;
                $orderNumber = 'ORD-' . $datePrefix . '-' . str_pad($todayOrdersCount, 4, '0', STR_PAD_LEFT);

                // Check collision just in case
                while (Order::where('order_number', $orderNumber)->exists()) {
                    $orderNumber = 'ORD-' . $datePrefix . '-' . strtoupper(Str::random(5));
                }

                // 3. Create Order
                $order = Order::create([
                    'order_number' => $orderNumber,
                    'user_id' => $user->id,
                    'order_type' => $this->order_type,
                    'total_amount' => $subtotal,
                    'discount_amount' => 0,
                    'final_amount' => $subtotal,
                    'status' => 'pending_payment',
                    'payment_status' => 'pending',
                    'payment_method' => $this->payment_method,
                    'notes' => $this->notes,
                    'delivery_address' => $this->order_type === 'delivery' ? $this->delivery_address : null,
                ]);

                // 4. Create Order Items & Deduct Stock with StockMovement record
                foreach ($itemsToOrder as $item) {
                    $prod = $item['product'];
                    $qty = $item['quantity'];

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $prod->id,
                        'product_name' => $prod->name,
                        'product_code' => $prod->code,
                        'price' => $item['price'],
                        'quantity' => $qty,
                        'subtotal' => $item['subtotal'],
                    ]);

                    $stockBefore = $prod->stock;
                    $stockAfter = $stockBefore - $qty;

                    // Deduct product stock
                    $prod->update(['stock' => $stockAfter]);

                    // Record stock movement (Rule: Integrity & Audit)
                    StockMovement::create([
                        'product_id' => $prod->id,
                        'user_id' => $user->id,
                        'type' => 'out',
                        'reference_type' => 'order',
                        'reference_id' => $order->id,
                        'quantity' => $qty,
                        'stock_before' => $stockBefore,
                        'stock_after' => $stockAfter,
                        'notes' => "Pengurangan stok untuk pesanan {$order->order_number}",
                    ]);
                }

                // 5. Create Payment Record
                Payment::create([
                    'order_id' => $order->id,
                    'user_id' => $user->id,
                    'payment_method' => $this->payment_method,
                    'amount' => $order->final_amount,
                    'status' => 'pending',
                    'notes' => 'Menunggu penyelesaian pembayaran oleh pelanggan',
                ]);

                // 6. Create Delivery record if order_type is delivery
                if ($this->order_type === 'delivery') {
                    Delivery::create([
                        'order_id' => $order->id,
                        'recipient_name' => $this->recipient_name,
                        'recipient_phone' => $this->recipient_phone,
                        'delivery_address' => $this->delivery_address,
                        'status' => 'preparing',
                    ]);
                }

                // 7. Record Status History
                $order->addStatusHistory('pending_payment', 'Pesanan berhasil dibuat, menunggu pembayaran pelanggan.', $user->id);

                // 8. Clear Cart
                $cart->items()->delete();

                return $order;
            });

            session()->flash('success', "Pesanan {$order->order_number} berhasil dibuat!");
            return redirect()->route('customer.orders.detail', $order->id);

        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            return;
        }
    }

    public function render()
    {
        $cart = Cart::with(['items.product.unit'])->where('user_id', Auth::id())->first();
        $items = $cart ? $cart->items : collect();
        $subtotal = $items->sum(fn($i) => $i->product ? $i->quantity * $i->product->sell_price : 0);

        return view('livewire.customer.checkout-view', [
            'items' => $items,
            'subtotal' => $subtotal,
        ])->layout('layouts.customer');
    }
}

