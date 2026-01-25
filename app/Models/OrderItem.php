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
        'qty',
        'price',
        'capital',
        'profit'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id');
    }
}
