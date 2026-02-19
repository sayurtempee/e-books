<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;


class OrderItem extends Model
{
    use Notifiable;

    protected $fillable = [
        'order_id',
        'book_id',
        'seller_id',
        'qty',
        'price',
        'capital',
        'profit',
        'status',
        'approved_at',
        'refunded_at',
        'tracking_number',
        'expedisi_name',
        'payment_proof',
        'cancel_reason'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    protected $casts = [
        'approved_at' => 'datetime',
    ];
}
