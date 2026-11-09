<?php

namespace App\Livewire\Petugas;

use Livewire\Component;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;

class Dashboard extends Component
{
    public function render()
    {
        $todayOrders = Order::whereDate('created_at', today())->count();
        $todayRevenue = Order::whereDate('created_at', today())->where('payment_status', 'paid')->sum('final_amount');
        
        $waitingVerificationCount = Payment::where('status', 'waiting_verification')->count();
        $processingCount = Order::whereIn('status', ['confirmed', 'processing'])->count();
        $readyPickupCount = Order::where('status', 'ready_for_pickup')->count();
        $onDeliveryCount = Order::where('status', 'on_delivery')->count();

        $recentOrders = Order::with(['user', 'latestPayment'])
            ->latest()
            ->take(8)
            ->get();

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

