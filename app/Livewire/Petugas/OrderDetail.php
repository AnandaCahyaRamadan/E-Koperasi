<?php

namespace App\Livewire\Petugas;

use Livewire\Component;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;

class OrderDetail extends Component
{
    public $orderId;
    public $courierName = '';
    public $deliveryNotes = '';

    public function mount($id)
    {
        $this->orderId = $id;
        $order = Order::with('delivery')->findOrFail($id);
        if ($order->delivery) {
            $this->courierName = $order->delivery->courier_name ?? Auth::user()->name;
            $this->deliveryNotes = $order->delivery->delivery_notes ?? '';
        }
    }

    public function updateDeliveryInfo()
    {
        $order = Order::with('delivery')->findOrFail($this->orderId);
        if ($order->delivery) {
            $order->delivery->update([
                'courier_name' => $this->courierName,
                'delivery_notes' => $this->deliveryNotes,
                'officer_id' => Auth::id(),
            ]);
            session()->flash('success', 'Informasi pengantaran berhasil diperbarui.');
        }
    }

    public function confirmCashPayment()
    {
        $order = Order::with('payments')->findOrFail($this->orderId);
        $user = Auth::user();

        $payment = $order->latestPayment;
        if (!$payment) {
            $payment = Payment::create([
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'payment_method' => 'cash',
                'amount' => $order->final_amount,
                'status' => 'paid',
                'verified_by' => $user->id,
                'verified_at' => now(),
            ]);
        } else {
            $payment->update([
                'status' => 'paid',
                'verified_by' => $user->id,
                'verified_at' => now(),
                'notes' => 'Pembayaran tunai diterima langsung oleh ' . $user->name,
            ]);
        }

        $order->update([
            'payment_status' => 'paid',
            'status' => $order->status === 'pending_payment' ? 'confirmed' : $order->status,
            'paid_at' => now(),
        ]);

        $order->addStatusHistory('paid', 'Pembayaran tunai dikonfirmasi oleh petugas ' . $user->name, $user->id);

        session()->flash('success', 'Pembayaran tunai berhasil dikonfirmasi lunas.');
    }

    public function updateStatus($newStatus)
    {
        $order = Order::with('delivery')->findOrFail($this->orderId);
        $user = Auth::user();

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

        $order->addStatusHistory($newStatus, "Status pesanan diubah menjadi {$order->status_label} oleh {$user->name}", $user->id);

        session()->flash('success', 'Status pesanan berhasil diperbarui.');
    }

    public function render()
    {
        $order = Order::with(['user', 'items.product.unit', 'payments.verifier', 'delivery', 'statusHistories.user'])->findOrFail($this->orderId);

        return view('livewire.petugas.order-detail', [
            'order' => $order,
        ])->layout('layouts.petugas');
    }
}

