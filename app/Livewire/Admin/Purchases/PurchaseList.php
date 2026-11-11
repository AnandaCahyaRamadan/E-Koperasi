<?php

namespace App\Livewire\Admin\Purchases;

use Livewire\Component;
use Livewire\WithPagination;
use App\Traits\WithDataTable;
use App\Models\Purchase;

class PurchaseList extends Component
{
    use WithPagination, WithDataTable;

    protected $paginationTheme = 'bootstrap';

    public $search = '';

    public function render()
    {
        $query = Purchase::with(['supplier', 'user', 'items.product']);

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('purchase_number', 'like', "%{$this->search}%")
                  ->orWhere('invoice_number', 'like', "%{$this->search}%")
                  ->orWhereHas('supplier', fn($s) => $s->where('name', 'like', "%{$this->search}%"));
            });
        }

        $this->applySorting($query, ['purchase_number', 'purchase_date', 'invoice_number', 'total_amount'], 'purchase_date', 'desc');

        $purchases = $query->paginate($this->perPage);

        return view('livewire.admin.purchases.purchase-list', [
            'purchases' => $purchases,
        ])->layout('layouts.admin');
    }
}

