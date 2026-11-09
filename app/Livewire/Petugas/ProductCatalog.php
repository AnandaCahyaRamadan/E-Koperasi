<?php

namespace App\Livewire\Petugas;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\Category;

class ProductCatalog extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $selectedCategory = '';

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

        $products = $query->orderBy('name')->paginate(12);

        return view('livewire.petugas.product-catalog', [
            'categories' => $categories,
            'products' => $products,
        ])->layout('layouts.petugas');
    }
}

