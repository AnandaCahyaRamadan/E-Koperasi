<?php

namespace App\Livewire\Admin\Reports;

use Livewire\Component;
use Livewire\WithPagination;
use App\Traits\WithDataTable;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class SalesReport extends Component
{
    use WithPagination, WithDataTable;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $startDate;
    public $endDate;

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->toDateString();
        $this->endDate = now()->toDateString();
    }

    public function updatedStartDate()
    {
        $this->resetPage();
    }

    public function updatedEndDate()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Order::with(['user', 'items'])
            ->whereDate('created_at', '>=', $this->startDate)
            ->whereDate('created_at', '<=', $this->endDate)
            ->where('payment_status', 'paid');

        $totalRevenue = (clone $query)->sum('final_amount');
        $totalOrders = (clone $query)->count();
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // Top selling products in this date range (paginated as DataTable)
        $topProducts = OrderItem::select('product_name', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_sales'))
            ->whereHas('order', function ($q) {
                $q->whereDate('created_at', '>=', $this->startDate)
                  ->whereDate('created_at', '<=', $this->endDate)
                  ->where('payment_status', 'paid');
            })
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->paginate(5, ['*'], 'topPage');

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('order_number', 'like', "%{$this->search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$this->search}%"));
            });
        }

        $this->applySorting($query, ['order_number', 'created_at', 'order_type', 'payment_method', 'final_amount']);

        $orders = $query->paginate($this->perPage);

        return view('livewire.admin.reports.sales-report', [
            'orders' => $orders,
            'totalRevenue' => $totalRevenue,
            'totalOrders' => $totalOrders,
            'avgOrderValue' => $avgOrderValue,
            'topProducts' => $topProducts,
        ])->layout('layouts.admin');
    }
}

