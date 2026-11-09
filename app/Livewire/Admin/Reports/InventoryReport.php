<?php

namespace App\Livewire\Admin\Reports;

use Livewire\Component;
use App\Models\Product;
use App\Models\Category;

class InventoryReport extends Component
{
    public function render()
    {
        $products = Product::with(['category', 'unit'])->orderBy('category_id')->get();
        $categories = Category::withCount('products')->get();

        $totalStock = $products->sum('stock');
        $totalHppValue = $products->sum(fn($p) => $p->stock * $p->purchase_price);
        $totalSellValue = $products->sum(fn($p) => $p->stock * $p->sell_price);
        $potentialProfit = $totalSellValue - $totalHppValue;

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

