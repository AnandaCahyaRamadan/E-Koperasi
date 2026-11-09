<?php

namespace App\Livewire\Admin\Purchases;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Purchase;

class PurchaseList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';

    public function render()
    {
        $query = Purchase::with(['supplier', 'user', 'items.product'])->latest();

        if (!empty($this->search)) {
            $query->where('purchase_number', 'like', "%{$this->search}%")
                  ->orWhere('invoice_number', 'like', "%{$this->search}%")
                  ->orWhereHas('supplier', fn($s) => $s->where('name', 'like', "%{$this->search}%"));
        }

        $purchases = $query->paginate(10);

        return view('livewire.admin.purchases.purchase-list', [
            'purchases' => $purchases,
        ])->layout('layouts.admin');
    }
}

