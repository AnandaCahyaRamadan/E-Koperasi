<?php

namespace App\Livewire\Admin\StockMovements;

use Livewire\Component;
use Livewire\WithPagination;
use App\Traits\WithDataTable;
use App\Models\StockMovement;
use App\Models\Product;

class StockMovementList extends Component
{
    use WithPagination, WithDataTable;

    protected $paginationTheme = 'bootstrap';

    public $filterType = 'all'; // 'all', 'in', 'out', 'adjustment'
    public $filterReference = 'all'; // 'all', 'purchase', 'order', 'opname', 'manual', 'initial'
    public $searchProduct = '';

    public function mount()
    {
        $this->perPage = 15;
    }

    public function updatingSearchProduct()
    {
        $this->resetPage();
    }

    public function updatingFilterType()
    {
        $this->resetPage();
    }

    public function updatingFilterReference()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = StockMovement::with(['product.unit', 'user']);

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

        $this->applySorting($query, ['created_at', 'type', 'reference_type', 'quantity', 'stock_after']);

        $movements = $query->paginate($this->perPage);

        return view('livewire.admin.stock-movements.stock-movement-list', [
            'movements' => $movements,
        ])->layout('layouts.admin');
    }
}

