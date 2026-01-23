<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;


class Book extends Model
{
    use Notifiable;

    protected $fillable = [
        'category_id',
        'photos_product',
        'title',
        'stock',
        'unit',
        'description',
        'capital',
        'price',
        'margin',
    ];

    // Belongs To Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
