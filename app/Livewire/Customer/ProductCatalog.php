<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Category;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;

class ProductCatalog extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $category = '';
    public $sort = 'latest';
    public $selectedProduct = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'category' => ['except' => ''],
        'sort' => ['except' => 'latest'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategory()
    {
        $this->resetPage();
    }

    public function updatingSort()
    {
        $this->resetPage();
    }

    public function showProductDetail($productId)
    {
        $this->selectedProduct = Product::with(['category', 'unit'])->find($productId);
    }

    public function closeProductDetail()
    {
        $this->selectedProduct = null;
    }

    public function addToCart($productId, $quantity = 1)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan masuk terlebih dahulu untuk belanja.');
        }

        $product = Product::findOrFail($productId);

        if ($product->stock < $quantity) {
            session()->flash('error', "Maaf, stok {$product->name} tidak mencukupi.");
            return;
        }

        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);
        $item = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->first();

        $currentQty = $item ? $item->quantity : 0;
        if ($currentQty + $quantity > $product->stock) {
            session()->flash('error', "Jumlah melebihi stok yang tersedia ({$product->stock} {$product->unit?->symbol}).");
            return;
        }

        if ($item) {
            $item->increment('quantity', $quantity);
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
            ]);
        }

        $this->dispatch('cart-updated');
        session()->flash('success', "{$product->name} berhasil ditambahkan ke keranjang!");
    }

    public function render()
    {
        $categories = Category::where('is_active', true)->get();

        $query = Product::with(['category', 'unit'])->where('is_active', true);

        if (!empty($this->category)) {
            $query->where('category_id', $this->category);
        }

        if (!empty($this->search)) {
            $query->search($this->search);
        }

        switch ($this->sort) {
            case 'price_asc':
                $query->orderBy('sell_price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('sell_price', 'desc');
                break;
            case 'stock_desc':
                $query->orderBy('stock', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(12);

        return view('livewire.customer.product-catalog', [
            'categories' => $categories,
            'products' => $products,
        ])->layout('layouts.customer');
    }
}

