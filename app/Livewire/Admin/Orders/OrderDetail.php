<?php

namespace App\Livewire\Admin\Orders;

use Livewire\Component;
use App\Models\Order;

class OrderDetail extends Component
{
    public $orderId;

    public function mount($id)
    {
        $this->orderId = $id;
    }

    public function render()
    {
        $order = Order::with(['user', 'cashier', 'items.product.unit', 'payments.verifier', 'delivery', 'statusHistories.user'])->findOrFail($this->orderId);

        return view('livewire.admin.orders.order-detail', [
            'order' => $order,
        ])->layout('layouts.admin');
    }
}

