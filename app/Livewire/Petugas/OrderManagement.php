<?php

namespace App\Livewire\Petugas;

use Livewire\Component;
use Livewire\WithPagination;
use App\Traits\WithDataTable;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderManagement extends Component
{
    use WithPagination, WithDataTable;

    protected $paginationTheme = 'bootstrap';

    public $filterStatus = 'all';
    public $search = '';

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updateOrderStatus($orderId, $newStatus)
    {
        $order = Order::with('delivery')->findOrFail($orderId);
        $user = Auth::user();

        // Validations
        if ($newStatus === 'processing' && $order->payment_status !== 'paid') {
            session()->flash('error', 'Pesanan belum dapat disiapkan karena pembayaran belum lunas/diverifikasi.');
            return;
        }

        $order->status = $newStatus;

        if ($newStatus === 'completed') {
            $order->completed_at = now();
            if ($order->delivery) {
                $order->delivery->update([
                    'status' => 'delivered',
                    'delivered_at' => now(),
                    'officer_id' => $user->id,
                ]);
            }
        } elseif ($newStatus === 'on_delivery' && $order->delivery) {
            $order->delivery->update([
                'status' => 'on_the_way',
                'officer_id' => $user->id,
            ]);
        }

        $order->save();

        $statusNotes = match($newStatus) {
            'processing' => 'Pesanan sedang disiapkan oleh petugas ' . $user->name,
            'ready_for_pickup' => 'Pesanan selesai disiapkan dan siap diambil di kasir koperasi.',
            'on_delivery' => 'Pesanan sedang diantar oleh petugas ' . $user->name,
            'completed' => 'Pesanan telah selesai diserahkan ke pelanggan.',
            default => 'Status diperbarui ke ' . $newStatus,
        };

        $order->addStatusHistory($newStatus, $statusNotes, $user->id);

        $msg = "Status pesanan {$order->order_number} berhasil diperbarui ke: " . $order->status_label;
        session()->flash('success', $msg);
        $this->dispatch('toast', ['type' => 'success', 'message' => $msg, 'title' => 'Update Status']);
    }

    public function render()
    {
        $query = Order::with(['user', 'items', 'delivery', 'latestPayment']);

        if ($this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
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

        $this->applySorting($query, ['order_number', 'created_at', 'order_type', 'final_amount', 'payment_status', 'status']);

        $orders = $query->paginate($this->perPage);

        return view('livewire.petugas.order-management', [
            'orders' => $orders,
        ])->layout('layouts.petugas');
    }
}

