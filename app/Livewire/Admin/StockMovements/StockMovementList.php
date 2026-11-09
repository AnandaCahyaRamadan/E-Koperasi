<?php

namespace App\Livewire\Admin\StockMovements;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\StockMovement;
use App\Models\Product;

class StockMovementList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $filterType = 'all'; // 'all', 'in', 'out', 'adjustment'
    public $filterReference = 'all'; // 'all', 'purchase', 'order', 'opname', 'manual', 'initial'
    public $searchProduct = '';

    public function render()
    {
        $query = StockMovement::with(['product.unit', 'user'])->latest();

        if ($this->filterType !== 'all') {
            $query->where('type', $this->filterType);
        }

        if ($this->filterReference !== 'all') {
            $query->where('reference_type', $this->filterReference);
        }

        if (!empty($this->searchProduct)) {
            $query->whereHas('product', function ($p) {
                $p->where('name', 'like', "%{$this->searchProduct}%")
                  ->orWhere('code', 'like', "%{$this->searchProduct}%");
            });
        }

        $movements = $query->paginate(15);

        return view('livewire.admin.stock-movements.stock-movement-list', [
            'movements' => $movements,
        ])->layout('layouts.admin');
    }
}

