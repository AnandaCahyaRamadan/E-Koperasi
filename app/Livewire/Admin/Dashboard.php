<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\StockOpname;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public function render()
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // Statistics
        $monthRevenue = Order::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->where('payment_status', 'paid')
            ->sum('final_amount');

        $monthOrdersCount = Order::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();

        $totalProducts = Product::count();
        $totalUsers = User::whereHas('role', fn($q) => $q->where('slug', 'pelanggan'))->count();
        $lowStockCount = Product::lowStock()->count();
        $pendingOpnameCount = StockOpname::where('status', 'draft')->orWhere('status', 'reviewed')->count();

        // Recent Orders
        $recentOrders = Order::with(['user', 'latestPayment'])
            ->latest()
            ->take(6)
            ->get();

        // Low stock products alert
        $lowStockProducts = Product::with(['category', 'unit'])->lowStock()->take(5)->get();

        // Last 7 days sales data for Chart.js
        $salesLabels = [];
        $salesData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $salesLabels[] = $date->format('d M');
            $salesData[] = (float) Order::whereDate('created_at', $date->toDateString())
                ->where('payment_status', 'paid')
                ->sum('final_amount');
        }

        return view('livewire.admin.dashboard', [
            'monthRevenue' => $monthRevenue,
            'monthOrdersCount' => $monthOrdersCount,
            'totalProducts' => $totalProducts,
            'totalUsers' => $totalUsers,
            'lowStockCount' => $lowStockCount,
            'pendingOpnameCount' => $pendingOpnameCount,
            'recentOrders' => $recentOrders,
            'lowStockProducts' => $lowStockProducts,
            'salesLabels' => json_encode($salesLabels),
            'salesData' => json_encode($salesData),
        ])->layout('layouts.admin');
    }
}

