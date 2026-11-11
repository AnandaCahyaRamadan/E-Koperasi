<?php

namespace App\Livewire\Admin\StockOpname;

use Livewire\Component;
use Livewire\WithPagination;
use App\Traits\WithDataTable;
use App\Models\StockOpname;

class OpnameList extends Component
{
    use WithPagination, WithDataTable;

    protected $paginationTheme = 'bootstrap';

    public $search = '';

    public function render()
    {
        $query = StockOpname::with(['creator', 'approver', 'details.product']);

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('opname_number', 'like', "%{$this->search}%")
                  ->orWhereHas('creator', fn($u) => $u->where('name', 'like', "%{$this->search}%"));
            });
        }

        $this->applySorting($query, ['opname_number', 'created_at', 'status', 'approved_at']);

        $opnames = $query->paginate($this->perPage);

        return view('livewire.admin.stock-opname.opname-list', [
            'opnames' => $opnames,
        ])->layout('layouts.admin');
    }
}

