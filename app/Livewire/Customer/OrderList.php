<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $filterStatus = 'all';

    public function setFilter($status)
    {
        $this->filterStatus = $status;
        $this->resetPage();
    }

    public function render()
    {
        $query = Order::with(['items', 'delivery'])
            ->where('user_id', Auth::id())
            ->latest();

        if ($this->filterStatus === 'pending_payment') {
            $query->where('status', 'pending_payment');
        } elseif ($this->filterStatus === 'processing') {
            $query->whereIn('status', ['confirmed', 'processing', 'ready_for_pickup', 'on_delivery']);
        } elseif ($this->filterStatus === 'completed') {
            $query->where('status', 'completed');
        } elseif ($this->filterStatus === 'cancelled') {
            $query->where('status', 'cancelled');
        }

        $orders = $query->paginate(8);

        return view('livewire.customer.order-list', [
            'orders' => $orders,
        ])->layout('layouts.customer');
    }
}

