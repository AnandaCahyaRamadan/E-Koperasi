<?php

namespace App\Livewire\Admin\Reports;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\Category;

class InventoryReport extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $category_id = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategoryId()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Product::with(['category', 'unit'])
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('code', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->category_id, function ($q) {
                $q->where('category_id', $this->category_id);
            });

        // Totals across matching products
        $allMatching = (clone $query)->get();
        $totalStock = $allMatching->sum('stock');
        $totalHppValue = $allMatching->sum(fn($p) => $p->stock * $p->purchase_price);
        $totalSellValue = $allMatching->sum(fn($p) => $p->stock * $p->sell_price);
        $potentialProfit = $totalSellValue - $totalHppValue;

        $products = $query->orderBy('name')->paginate(15);
        $categories = Category::orderBy('name')->get();

        return view('livewire.admin.reports.inventory-report', [
            'products' => $products,
            'categories' => $categories,
            'totalStock' => $totalStock,
            'totalHppValue' => $totalHppValue,
            'totalSellValue' => $totalSellValue,
            'potentialProfit' => $potentialProfit,
        ])->layout('layouts.admin');
    }
}

