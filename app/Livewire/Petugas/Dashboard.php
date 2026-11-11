<?php

namespace App\Livewire\Petugas;

use Livewire\Component;
use Livewire\WithPagination;
use App\Traits\WithDataTable;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;

class Dashboard extends Component
{
    use WithPagination, WithDataTable;

    protected $paginationTheme = 'bootstrap';

    public $search = '';

    public function mount()
    {
        $this->perPage = 8;
    }

    public function render()
    {
        $todayOrders = Order::whereDate('created_at', today())->count();
        $todayRevenue = Order::whereDate('created_at', today())->where('payment_status', 'paid')->sum('final_amount');
        
        $waitingVerificationCount = Payment::where('status', 'waiting_verification')->count();
        $processingCount = Order::whereIn('status', ['confirmed', 'processing'])->count();
        $readyPickupCount = Order::where('status', 'ready_for_pickup')->count();
        $onDeliveryCount = Order::where('status', 'on_delivery')->count();

        $ordersQuery = Order::with(['user', 'latestPayment']);
        if (!empty($this->search)) {
            $ordersQuery->where(function ($q) {
                $q->where('order_number', 'like', "%{$this->search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$this->search}%"));
            });
        }
        $this->applySorting($ordersQuery, ['order_number', 'created_at', 'order_type', 'final_amount', 'payment_status', 'status']);
        $recentOrders = $ordersQuery->paginate($this->perPage);

        $lowStockProducts = Product::with('unit')->lowStock()->take(5)->get();

        return view('livewire.petugas.dashboard', [
            'todayOrders' => $todayOrders,
            'todayRevenue' => $todayRevenue,
            'waitingVerificationCount' => $waitingVerificationCount,
            'processingCount' => $processingCount,
            'readyPickupCount' => $readyPickupCount,
            'onDeliveryCount' => $onDeliveryCount,
            'recentOrders' => $recentOrders,
            'lowStockProducts' => $lowStockProducts,
        ])->layout('layouts.petugas');
    }
}

