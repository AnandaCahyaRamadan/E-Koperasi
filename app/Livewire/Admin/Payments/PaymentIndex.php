<?php

namespace App\Livewire\Admin\Payments;

use Livewire\Component;
use Livewire\WithPagination;
use App\Traits\WithDataTable;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;

class PaymentIndex extends Component
{
    use WithPagination, WithDataTable;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $filterStatus = 'all';
    public $previewImageUrl = null;
    public $showImageModal = false;

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function approvePayment($paymentId)
    {
        $payment = Payment::with('order')->findOrFail($paymentId);
        $user = Auth::user();

        $payment->update([
            'status' => 'paid',
            'verified_by' => $user->id,
            'verified_at' => now(),
            'notes' => 'Pembayaran diverifikasi oleh Administrator ' . $user->name,
        ]);

        if ($payment->order) {
            $order = $payment->order;
            $order->update([
                'payment_status' => 'paid',
                'status' => $order->status === 'pending_payment' ? 'confirmed' : $order->status,
                'paid_at' => now(),
            ]);

            $order->addStatusHistory('confirmed', 'Pembayaran diverifikasi oleh Admin ' . $user->name, $user->id);
        }

        session()->flash('success', "Pembayaran untuk pesanan {$payment->order?->order_number} berhasil disetujui.");
    }

    public function viewProofImage($imageUrl)
    {
        $this->previewImageUrl = $imageUrl;
        $this->showImageModal = true;
    }

    public function closeImageModal()
    {
        $this->previewImageUrl = null;
        $this->showImageModal = false;
    }

    public function render()
    {
        $query = Payment::with(['order.user', 'verifier']);

        if ($this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        }

        if (!empty($this->search)) {
            $query->whereHas('order', function ($o) {
                $o->where('order_number', 'like', "%{$this->search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$this->search}%"));
            });
        }

        $this->applySorting($query, ['created_at', 'payment_method', 'amount', 'status']);

        $payments = $query->paginate($this->perPage);

        return view('livewire.admin.payments.payment-index', [
            'payments' => $payments,
        ])->layout('layouts.admin');
    }
}

