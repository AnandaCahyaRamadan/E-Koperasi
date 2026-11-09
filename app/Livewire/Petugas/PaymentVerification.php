<?php

namespace App\Livewire\Petugas;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Payment;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class PaymentVerification extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $filterMethod = 'all'; // 'all', 'qris', 'cash'
    public $filterStatus = 'waiting_verification'; // 'waiting_verification', 'paid', 'rejected', 'all'
    public $selectedPayment = null;
    public $rejectionReason = '';
    public $showRejectModal = false;
    public $showImageModal = false;
    public $previewImageUrl = null;

    public function approvePayment($paymentId)
    {
        $payment = Payment::with('order')->findOrFail($paymentId);

        if ($payment->status === 'paid') {
            session()->flash('error', 'Pembayaran ini sudah pernah disetujui sebelumnya.');
            return;
        }

        $user = Auth::user();

        $payment->update([
            'status' => 'paid',
            'verified_by' => $user->id,
            'verified_at' => now(),
            'notes' => 'Pembayaran disetujui oleh kasir/petugas ' . $user->name,
        ]);

        if ($payment->order) {
            $order = $payment->order;
            $order->update([
                'payment_status' => 'paid',
                'status' => 'confirmed',
                'paid_at' => now(),
            ]);

            $order->addStatusHistory('confirmed', 'Pembayaran ' . strtoupper($payment->payment_method) . ' diverifikasi lunas oleh ' . $user->name, $user->id);
        }

        session()->flash('success', "Pembayaran untuk pesanan {$payment->order?->order_number} berhasil disetujui (ACC)!");
    }

    public function openRejectModal($paymentId)
    {
        $this->selectedPayment = Payment::with('order')->findOrFail($paymentId);
        $this->rejectionReason = '';
        $this->showRejectModal = true;
    }

    public function closeRejectModal()
    {
        $this->selectedPayment = null;
        $this->rejectionReason = '';
        $this->showRejectModal = false;
    }

    public function rejectPayment()
    {
        $this->validate([
            'rejectionReason' => ['required', 'string', 'min:3', 'max:255'],
        ], [
            'rejectionReason.required' => 'Wajib menyertakan alasan penolakan pembayaran.',
        ]);

        if (!$this->selectedPayment) return;

        $user = Auth::user();

        $this->selectedPayment->update([
            'status' => 'rejected',
            'verified_by' => $user->id,
            'verified_at' => now(),
            'rejection_reason' => $this->rejectionReason,
        ]);

        if ($this->selectedPayment->order) {
            $order = $this->selectedPayment->order;
            $order->update([
                'payment_status' => 'rejected',
            ]);

            $order->addStatusHistory('payment_rejected', "Pembayaran ditolak: {$this->rejectionReason} (oleh {$user->name})", $user->id);
        }

        session()->flash('success', "Pembayaran untuk pesanan {$this->selectedPayment->order?->order_number} telah ditolak.");
        $this->closeRejectModal();
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
        $query = Payment::with(['order.user', 'verifier'])->latest();

        if ($this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterMethod !== 'all') {
            $query->where('payment_method', $this->filterMethod);
        }

        $payments = $query->paginate(10);

        return view('livewire.petugas.payment-verification', [
            'payments' => $payments,
        ])->layout('layouts.petugas');
    }
}

