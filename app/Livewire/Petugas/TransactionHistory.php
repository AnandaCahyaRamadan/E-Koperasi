<?php

namespace App\Livewire\Petugas;

use Livewire\Component;
use Livewire\WithPagination;
use App\Traits\WithDataTable;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class TransactionHistory extends Component
{
    use WithPagination, WithDataTable;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $dateFilter = '';

    public function mount()
    {
        $this->dateFilter = date('Y-m-d');
    }

    public function updatingDateFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Order::with(['user', 'items', 'cashier'])
            ->where(function ($q) {
                $q->where('cashier_id', Auth::id())
                  ->orWhereHas('payments', function ($p) {
                      $p->where('verified_by', Auth::id());
                  });
            });

        if (!empty($this->dateFilter)) {
            $query->whereDate('created_at', $this->dateFilter);
        }

        $totalAmount = (clone $query)->sum('final_amount');

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('order_number', 'like', "%{$this->search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$this->search}%"));
            });
        }

        $this->applySorting($query, ['order_number', 'created_at', 'order_type', 'payment_method', 'final_amount', 'status']);

        $orders = $query->paginate($this->perPage);

        return view('livewire.petugas.transaction-history', [
            'orders' => $orders,
            'totalAmount' => $totalAmount,
        ])->layout('layouts.petugas');
    }
}

