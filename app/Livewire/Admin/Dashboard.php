<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Traits\WithDataTable;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\StockOpname;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    use WithPagination, WithDataTable;

    protected $paginationTheme = 'bootstrap';

    public $search = '';

    public function mount()
    {
        $this->perPage = 6;
    }
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

        // Recent Orders (DataTable with search, sort, pagination)
        $ordersQuery = Order::with(['user', 'latestPayment']);
        if (!empty($this->search)) {
            $ordersQuery->where(function ($q) {
                $q->where('order_number', 'like', "%{$this->search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$this->search}%"));
            });
        }
        $this->applySorting($ordersQuery, ['order_number', 'created_at', 'order_type', 'payment_method', 'final_amount', 'status']);
        $recentOrders = $ordersQuery->paginate($this->perPage);

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

