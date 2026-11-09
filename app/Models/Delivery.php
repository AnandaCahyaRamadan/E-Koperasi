<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'officer_id',
        'courier_name',
        'recipient_name',
        'recipient_phone',
        'delivery_address',
        'status',
        'delivery_notes',
        'delivered_at',
    ];

    protected $casts = [
        'delivered_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function officer()
    {
        return $this->belongsTo(User::class, 'officer_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'preparing' => 'Disiapkan',
            'on_the_way' => 'Sedang Diantar',
            'delivered' => 'Sudah Diterima',
            'failed' => 'Gagal Terkirim',
            default => ucfirst($this->status),
        };
    }
}

