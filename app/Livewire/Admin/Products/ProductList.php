<?php

namespace App\Livewire\Admin\Products;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\Category;

class ProductList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $selectedCategory = '';

    public function deleteProduct($id)
    {
        $product = Product::findOrFail($id);
        
        // Prevent deletion if order items exist
        if ($product->orderItems()->exists()) {
            $product->update(['is_active' => false]);
            session()->flash('success', "Produk {$product->name} memiliki riwayat transaksi, status diubah menjadi Non-Aktif.");
            return;
        }

        $product->delete();
        session()->flash('success', 'Produk berhasil dihapus.');
    }

    public function toggleActive($id)
    {
        $product = Product::findOrFail($id);
        $product->update(['is_active' => !$product->is_active]);
        session()->flash('success', "Status produk {$product->name} diperbarui.");
    }

    public function render()
    {
        $categories = Category::all();

        $query = Product::with(['category', 'unit'])->latest();

        if (!empty($this->selectedCategory)) {
            $query->where('category_id', $this->selectedCategory);
        }

        if (!empty($this->search)) {
            $query->search($this->search);
        }

        $products = $query->paginate(12);

        return view('livewire.admin.products.product-list', [
            'categories' => $categories,
            'products' => $products,
        ])->layout('layouts.admin');
    }
}

