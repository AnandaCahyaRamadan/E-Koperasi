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
    public $perPage = 10;
    public $sortField = 'id';
    public $sortDirection = 'desc';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedCategory()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function deleteProduct($id)
    {
        $product = Product::findOrFail($id);
        
        // Prevent deletion if order items exist
        if ($product->orderItems()->exists()) {
            $product->update(['is_active' => false]);
            session()->flash('success', "Produk {$product->name} memiliki riwayat transaksi, status diubah menjadi Non-Aktif.");
            return;
        }

        $name = $product->name;
        $product->delete();
        session()->flash('success', "Produk {$name} berhasil dihapus.");
        $this->dispatch('toast', ['type' => 'success', 'message' => "Produk {$name} berhasil dihapus."]);
    }

    public function toggleActive($id)
    {
        $product = Product::findOrFail($id);
        $product->update(['is_active' => !$product->is_active]);
        $statusStr = $product->is_active ? 'diaktifkan' : 'dinonaktifkan';
        session()->flash('success', "Produk {$product->name} berhasil {$statusStr}.");
        $this->dispatch('toast', ['type' => 'success', 'message' => "Produk {$product->name} {$statusStr}."]);
    }

    public function render()
    {
        $categories = Category::all();

        $query = Product::with(['category', 'unit']);

        if (!empty($this->selectedCategory)) {
            $query->where('category_id', $this->selectedCategory);
        }

        if (!empty($this->search)) {
            $query->search($this->search);
        }

        if (in_array($this->sortField, ['code', 'name', 'purchase_price', 'sell_price', 'stock', 'is_active'])) {
            $query->orderBy($this->sortField, $this->sortDirection);
        } else {
            $query->latest();
        }

        $products = $query->paginate($this->perPage);

        return view('livewire.admin.products.product-list', [
            'categories' => $categories,
            'products' => $products,
        ])->layout('layouts.admin');
    }
}

