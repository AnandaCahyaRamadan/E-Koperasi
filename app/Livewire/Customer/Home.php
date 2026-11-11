<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use App\Models\Category;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;

class Home extends Component
{
    public $search = '';
    public $selectedCategory = null;

    public function selectCategory($categoryId)
    {
        $this->selectedCategory = $this->selectedCategory === $categoryId ? null : $categoryId;
    }

    public function addToCart($productId)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan masuk terlebih dahulu untuk belanja.');
        }

        $product = Product::findOrFail($productId);

        if ($product->stock < 1) {
            session()->flash('error', "Maaf, stok {$product->name} sedang habis.");
            return;
        }

        $user = Auth::user();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        $item = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->first();

        $currentQty = $item ? $item->quantity : 0;
        if ($currentQty + 1 > $product->stock) {
            $msg = "Jumlah melebihi stok yang tersedia ({$product->stock} {$product->unit?->symbol}).";
            session()->flash('error', $msg);
            $this->dispatch('toast', ['type' => 'error', 'message' => $msg, 'title' => 'Stok Terbatas']);
            return;
        }

        if ($item) {
            $item->increment('quantity');
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => 1,
            ]);
        }

        $this->dispatch('cart-updated', count: Cart::countForUser($user->id));
        $successMsg = "{$product->name} berhasil ditambahkan ke keranjang!";
        session()->flash('success', $successMsg);
        $this->dispatch('toast', ['type' => 'success', 'message' => $successMsg, 'title' => 'Keranjang']);
    }

    public function render()
    {
        $categories = Category::where('is_active', true)->get();

        $query = Product::with(['category', 'unit'])->where('is_active', true);

        if ($this->selectedCategory) {
            $query->where('category_id', $this->selectedCategory);
        }

        if (!empty($this->search)) {
            $query->search($this->search);
        }

        $products = $query->latest()->take(12)->get();
        $popularProducts = Product::with(['category', 'unit'])
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->inRandomOrder()
            ->take(4)
            ->get();

        return view('livewire.customer.home', [
            'categories' => $categories,
            'products' => $products,
            'popularProducts' => $popularProducts,
        ])->layout('layouts.customer');
    }
}

