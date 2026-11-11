<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function getTotalAttribute(): float
    {
        return $this->items->sum(function ($item) {
            return $item->product ? $item->quantity * $item->product->sell_price : 0;
        });
    }

    public function getTotalQuantityAttribute(): int
    {
        return $this->items->sum('quantity');
    }

    public static function countForUser(?int $userId = null): int
    {
        $uid = $userId ?? auth()->id();
        if (!$uid) {
            return 0;
        }

        return (int) CartItem::whereHas('cart', fn ($q) => $q->where('user_id', $uid))->sum('quantity');
    }
}

