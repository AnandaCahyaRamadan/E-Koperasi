<?php

namespace App\Livewire\Admin\Inventory;

use Livewire\Component;
use Livewire\WithPagination;
use App\Traits\WithDataTable;
use App\Models\Product;
use App\Models\Category;

class InventoryIndex extends Component
{
    use WithPagination, WithDataTable;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $filterStock = 'all'; // 'all', 'low', 'out'

    public function updatingFilterStock()
    {
        $this->resetPage();
    }

    public function render()
    {
        $totalStockUnits = Product::sum('stock');
        $totalStockAssetValue = Product::selectRaw('SUM(stock * purchase_price) as total_val')->value('total_val') ?? 0;
        $totalStockSellValue = Product::selectRaw('SUM(stock * sell_price) as total_val')->value('total_val') ?? 0;
        $lowStockCount = Product::lowStock()->count();
        $outOfStockCount = Product::where('stock', '<=', 0)->count();

        $query = Product::with(['category', 'unit']);

        if ($this->filterStock === 'low') {
            $query->lowStock();
        } elseif ($this->filterStock === 'out') {
            $query->where('stock', '<=', 0);
        }

        if (!empty($this->search)) {
            $query->search($this->search);
        }

        $this->applySorting($query, ['code', 'name', 'purchase_price', 'sell_price', 'stock', 'min_stock']);

        $products = $query->paginate($this->perPage);

        return view('livewire.admin.inventory.inventory-index', [
            'products' => $products,
            'totalStockUnits' => $totalStockUnits,
            'totalStockAssetValue' => $totalStockAssetValue,
            'totalStockSellValue' => $totalStockSellValue,
            'lowStockCount' => $lowStockCount,
            'outOfStockCount' => $outOfStockCount,
        ])->layout('layouts.admin');
    }
}

