<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

class OrderDetailView extends Component
{
    use WithFileUploads;

    public $orderId;
    public $proofImage;
    public $isUploading = false;

    public function mount($id)
    {
        $this->orderId = $id;
    }

    public function uploadProof()
    {
        $this->validate([
            'proofImage' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'], // Max 3MB
        ], [
            'proofImage.required' => 'Silakan pilih file foto/screenshot bukti transfer.',
            'proofImage.image' => 'File harus berupa foto/gambar.',
            'proofImage.max' => 'Ukuran gambar maksimal 3MB.',
        ]);

        $order = Order::where('user_id', Auth::id())->findOrFail($this->orderId);

        if ($order->payment_status === 'paid') {
            session()->flash('error', 'Pembayaran pesanan ini sudah diverifikasi lunas.');
            return;
        }

        // Store proof image
        $path = $this->proofImage->store('payment_proofs', 'public');

        // Create or update payment
        $payment = Payment::firstOrNew([
            'order_id' => $order->id,
        ]);

        $payment->user_id = Auth::id();
        $payment->payment_method = 'qris';
        $payment->amount = $order->final_amount;
        $payment->proof_image = $path;
        $payment->status = 'waiting_verification';
        $payment->rejection_reason = null;
        $payment->notes = 'Bukti pembayaran diunggah oleh pelanggan';
        $payment->save();

        // Update order payment status
        $order->update([
            'payment_status' => 'waiting_verification',
        ]);

        $order->addStatusHistory('waiting_verification', 'Pelanggan telah mengunggah bukti pembayaran QRIS.', Auth::id());

        $this->reset('proofImage');
        session()->flash('success', 'Bukti pembayaran berhasil diunggah! Petugas kasir kami akan segera memeriksa dan memverifikasi pembayaran Anda.');
    }

    public function cancelOrder()
    {
        $order = Order::with('items.product')->where('user_id', Auth::id())->findOrFail($this->orderId);

        if (!in_array($order->status, ['pending_payment']) || $order->payment_status === 'paid') {
            session()->flash('error', 'Pesanan yang telah dibayar atau diproses tidak dapat dibatalkan.');
            return;
        }

        // Return stock
        foreach ($order->items as $item) {
            if ($item->product) {
                $stockBefore = $item->product->stock;
                $stockAfter = $stockBefore + $item->quantity;
                $item->product->update(['stock' => $stockAfter]);

                \App\Models\StockMovement::create([
                    'product_id' => $item->product->id,
                    'user_id' => Auth::id(),
                    'type' => 'in',
                    'reference_type' => 'order',
                    'reference_id' => $order->id,
                    'quantity' => $item->quantity,
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'notes' => "Pengembalian stok dari pembatalan pesanan {$order->order_number}",
                ]);
            }
        }

        $order->update([
            'status' => 'cancelled',
            'cancelled_reason' => 'Dibatalkan oleh pelanggan',
        ]);

        $order->addStatusHistory('cancelled', 'Pesanan dibatalkan oleh pelanggan.', Auth::id());

        session()->flash('success', 'Pesanan berhasil dibatalkan dan stok dikembalikan.');
    }

    public function render()
    {
        $order = Order::with([
            'items.product.unit',
            'payments' => fn($q) => $q->latest(),
            'delivery',
            'statusHistories.user'
        ])->where('user_id', Auth::id())->findOrFail($this->orderId);

        $qrisMerchant = Setting::get('qris_merchant_name', 'KOPERASI PEGAWAI RESMI');
        $qrisNmid = Setting::get('qris_nmid', 'ID1020030040506');
        $qrisImage = Setting::get('qris_image', null);

        return view('livewire.customer.order-detail-view', [
            'order' => $order,
            'latestPayment' => $order->payments->first(),
            'qrisMerchant' => $qrisMerchant,
            'qrisNmid' => $qrisNmid,
            'qrisImage' => $qrisImage,
        ])->layout('layouts.customer');
    }
}

