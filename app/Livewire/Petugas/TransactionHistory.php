<?php

namespace App\Livewire\Petugas;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class TransactionHistory extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $dateFilter = '';

    public function mount()
    {
        $this->dateFilter = date('Y-m-d');
    }

    public function render()
    {
        $query = Order::with(['user', 'items', 'cashier'])
            ->where(function ($q) {
                $q->where('cashier_id', Auth::id())
                  ->orWhereHas('payments', function ($p) {
                      $p->where('verified_by', Auth::id());
                  });
            })
            ->latest();

        if (!empty($this->dateFilter)) {
            $query->whereDate('created_at', $this->dateFilter);
        }

        $orders = $query->paginate(10);
        $totalAmount = $query->sum('final_amount');

        return view('livewire.petugas.transaction-history', [
            'orders' => $orders,
            'totalAmount' => $totalAmount,
        ])->layout('layouts.petugas');
    }
}

