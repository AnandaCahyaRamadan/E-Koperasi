<?php

namespace App\Livewire\Admin\Orders;

use Livewire\Component;
use Livewire\WithPagination;
use App\Traits\WithDataTable;
use App\Models\Order;

class OrderList extends Component
{
    use WithPagination, WithDataTable;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $filterStatus = 'all';
    public $filterPayment = 'all';
    public $filterType = 'all';
    public $startDate = '';
    public $endDate = '';

    public function updatingFilterStatus() { $this->resetPage(); }
    public function updatingFilterPayment() { $this->resetPage(); }
    public function updatingFilterType() { $this->resetPage(); }
    public function updatingStartDate() { $this->resetPage(); }
    public function updatingEndDate() { $this->resetPage(); }

    public function render()
    {
        $query = Order::with(['user', 'items', 'latestPayment', 'cashier']);

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

        $this->applySorting($query, ['order_number', 'created_at', 'order_type', 'payment_method', 'final_amount', 'payment_status', 'status']);

        $orders = $query->paginate($this->perPage);

        return view('livewire.admin.orders.order-list', [
            'orders' => $orders,
        ])->layout('layouts.admin');
    }
}

