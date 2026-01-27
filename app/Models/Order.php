<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;


class Order extends Model
{
    use Notifiable;

    protected $fillable = [
        'user_id',
        'total_price',
        'payment_method'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function getStatusAttribute()
    {
        $statuses = $this->items->pluck('status');

        if ($statuses->every(fn($s) => $s === 'refunded')) {
            return 'refunded';
        }

        if ($statuses->contains('shipping')) {
            return 'shipping';
        }

        if ($statuses->contains('approved')) {
            return 'approved';
        }

        if ($statuses->contains('selesai')) {
            return 'selesai';
        }

        return 'pending';
    }
}
