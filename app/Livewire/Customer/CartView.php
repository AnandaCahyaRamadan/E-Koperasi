<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;

class CartView extends Component
{
    public function incrementQuantity($itemId)
    {
        $item = CartItem::with('product')->whereHas('cart', function ($q) {
            $q->where('user_id', Auth::id());
        })->findOrFail($itemId);

        if ($item->quantity + 1 > $item->product->stock) {
            session()->flash('error', "Stok {$item->product->name} tidak mencukupi (tersedia {$item->product->stock}).");
            return;
        }

        $item->increment('quantity');
        $this->dispatch('cart-updated');
    }

    public function decrementQuantity($itemId)
    {
        $item = CartItem::whereHas('cart', function ($q) {
            $q->where('user_id', Auth::id());
        })->findOrFail($itemId);

        if ($item->quantity > 1) {
            $item->decrement('quantity');
        } else {
            $item->delete();
        }

        $this->dispatch('cart-updated');
    }

    public function removeItem($itemId)
    {
        $item = CartItem::whereHas('cart', function ($q) {
            $q->where('user_id', Auth::id());
        })->findOrFail($itemId);

        $item->delete();
        $this->dispatch('cart-updated');
        session()->flash('success', 'Produk berhasil dihapus dari keranjang.');
    }

    public function clearCart()
    {
        $cart = Cart::where('user_id', Auth::id())->first();
        if ($cart) {
            $cart->items()->delete();
        }
        $this->dispatch('cart-updated');
        session()->flash('success', 'Keranjang berhasil dikosongkan.');
    }

    public function render()
    {
        $cart = Cart::with(['items.product.unit'])->where('user_id', Auth::id())->first();
        $items = $cart ? $cart->items : collect();

        $subtotal = $items->sum(fn($i) => $i->product ? $i->quantity * $i->product->sell_price : 0);
        $totalItems = $items->sum('quantity');

        // Check if any product exceeds available stock
        $hasStockError = false;
        foreach ($items as $item) {
            if ($item->product && $item->quantity > $item->product->stock) {
                $hasStockError = true;
                break;
            }
        }

        return view('livewire.customer.cart-view', [
            'items' => $items,
            'subtotal' => $subtotal,
            'totalItems' => $totalItems,
            'hasStockError' => $hasStockError,
        ])->layout('layouts.customer');
    }
}

