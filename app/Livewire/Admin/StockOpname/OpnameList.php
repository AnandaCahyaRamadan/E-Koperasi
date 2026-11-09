<?php

namespace App\Livewire\Admin\StockOpname;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\StockOpname;

class OpnameList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        $opnames = StockOpname::with(['creator', 'approver', 'details.product'])->latest()->paginate(10);

        return view('livewire.admin.stock-opname.opname-list', [
            'opnames' => $opnames,
        ])->layout('layouts.admin');
    }
}

