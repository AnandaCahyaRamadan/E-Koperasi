<?php

namespace App\Livewire\Petugas;

use Livewire\Component;
use Livewire\WithPagination;
use App\Traits\WithDataTable;
use App\Models\Product;
use App\Models\Category;

class ProductCatalog extends Component
{
    use WithPagination, WithDataTable;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $selectedCategory = '';

    public function mount()
    {
        $this->sortField = 'name';
        $this->sortDirection = 'asc';
    }

    public function updatingSelectedCategory()
    {
        $this->resetPage();
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

        $this->applySorting($query, ['code', 'name', 'sell_price', 'stock'], 'name', 'asc');

        $products = $query->paginate($this->perPage);

        return view('livewire.petugas.product-catalog', [
            'categories' => $categories,
            'products' => $products,
        ])->layout('layouts.petugas');
    }
}

