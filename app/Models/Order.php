<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'cashier_id',
        'order_type',
        'total_amount',
        'discount_amount',
        'final_amount',
        'status',
        'payment_status',
        'payment_method',
        'notes',
        'delivery_address',
        'cancelled_reason',
        'paid_at',
        'completed_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function latestPayment()
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function delivery()
    {
        return $this->hasOne(Delivery::class);
    }

    public function statusHistories()
    {
        return $this->hasMany(OrderStatusHistory::class)->latest();
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending_payment' => 'Menunggu Pembayaran',
            'confirmed' => 'Dikonfirmasi (Lunas)',
            'processing' => 'Sedang Disiapkan',
            'ready_for_pickup' => 'Siap Diambil',
            'on_delivery' => 'Sedang Diantar',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'pending_payment' => 'bg-warning text-dark',
            'confirmed' => 'bg-info text-dark',
            'processing' => 'bg-primary text-white',
            'ready_for_pickup' => 'bg-teal text-white',
            'on_delivery' => 'bg-indigo text-white',
            'completed' => 'bg-success text-white',
            'cancelled' => 'bg-danger text-white',
            default => 'bg-secondary text-white',
        };
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return match($this->payment_status) {
            'pending' => 'Belum Dibayar',
            'waiting_verification' => 'Menunggu Verifikasi',
            'paid' => 'Lunas',
            'rejected' => 'Ditolak',
            'failed' => 'Gagal',
            'refunded' => 'Dikembalikan',
            default => ucfirst($this->payment_status),
        };
    }

    public function getPaymentStatusBadgeClassAttribute(): string
    {
        return match($this->payment_status) {
            'pending' => 'bg-secondary text-white',
            'waiting_verification' => 'bg-warning text-dark',
            'paid' => 'bg-success text-white',
            'rejected' => 'bg-danger text-white',
            'failed' => 'bg-dark text-white',
            'refunded' => 'bg-info text-dark',
            default => 'bg-secondary text-white',
        };
    }

    public function getOrderTypeLabelAttribute(): string
    {
        return match($this->order_type) {
            'pickup' => 'Ambil Sendiri',
            'delivery' => 'Antar ke Meja / Alamat',
            'pos' => 'Transaksi Kasir Langsung',
            default => ucfirst($this->order_type),
        };
    }

    public function addStatusHistory(string $status, ?string $notes = null, ?int $userId = null)
    {
        $this->statusHistories()->create([
            'user_id' => $userId ?? auth()->id(),
            'status' => $status,
            'notes' => $notes,
        ]);
    }
}

