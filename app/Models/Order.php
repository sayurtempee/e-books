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
        'status',
        'tracking_number',
        'approved_at',
        'payment_proof',
        'refunded_at',
        'payment_method'
    ];

    protected $casts = [
        'approved_at' => 'datetime'
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
