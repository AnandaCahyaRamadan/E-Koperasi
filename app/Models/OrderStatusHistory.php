<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'status',
        'notes',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending_payment' => 'Menunggu Pembayaran',
            'waiting_verification' => 'Menunggu Verifikasi Pembayaran',
            'paid' => 'Pembayaran Lunas',
            'confirmed' => 'Pembayaran Dikonfirmasi',
            'payment_rejected' => 'Pembayaran Ditolak',
            'processing' => 'Sedang Disiapkan',
            'ready_for_pickup' => 'Siap Diambil di Kasir',
            'on_delivery' => 'Sedang Diantar',
            'completed' => 'Pesanan Selesai',
            'cancelled' => 'Pesanan Dibatalkan',
            'pending' => 'Menunggu',
            'rejected' => 'Ditolak',
            'failed' => 'Gagal',
            'refunded' => 'Dikembalikan',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'pending_payment', 'waiting_verification', 'pending' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
            'paid', 'confirmed', 'ready_for_pickup', 'completed' => 'bg-success-subtle text-success border border-success-subtle',
            'processing', 'on_delivery' => 'bg-primary-subtle text-primary border border-primary-subtle',
            'payment_rejected', 'cancelled', 'rejected', 'failed' => 'bg-danger-subtle text-danger border border-danger-subtle',
            default => 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle',
        };
    }
}

