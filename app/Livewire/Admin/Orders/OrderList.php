<?php

namespace App\Livewire\Admin\Orders;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Order;

class OrderList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $filterStatus = 'all';
    public $filterPayment = 'all';
    public $filterType = 'all';
    public $startDate = '';
    public $endDate = '';

    public function render()
    {
        $query = Order::with(['user', 'items', 'latestPayment', 'cashier'])->latest();

        if ($this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterPayment !== 'all') {
            $query->where('payment_status', $this->filterPayment);
        }

        if ($this->filterType !== 'all') {
            $query->where('order_type', $this->filterType);
        }

        if (!empty($this->startDate)) {
            $query->whereDate('created_at', '>=', $this->startDate);
        }

        if (!empty($this->endDate)) {
            $query->whereDate('created_at', '<=', $this->endDate);
        }

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('order_number', 'like', "%{$this->search}%")
                  ->orWhereHas('user', function ($u) {
                      $u->where('name', 'like', "%{$this->search}%")
                        ->orWhere('employee_id', 'like', "%{$this->search}%");
                  });
            });
        }

        $orders = $query->paginate(12);

        return view('livewire.admin.orders.order-list', [
            'orders' => $orders,
        ])->layout('layouts.admin');
    }
}

