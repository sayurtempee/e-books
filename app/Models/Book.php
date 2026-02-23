<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;


class Book extends Model
{
    use Notifiable, SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'user_id',
        'category_id',
        'photos_product',
        'title',
        'stock',
        'unit',
        'description',
        'capital',
        'price',
        'margin',
        'delete_at'
    ];

    // Belongs To Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function item()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        // Menghubungkan kolom user_id di tabel books ke tabel users
        return $this->belongsTo(User::class, 'user_id');
    }
}
