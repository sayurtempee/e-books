<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;


class Category extends Model
{
    use Notifiable;

    protected $fillable = [
        'title',
        'created_at',
    ];

    // Hash Many Book
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Hash Many Category
    public function books()
    {
        return $this->hasMany(Book::class);
    }

    // Get Icons
    public function getIconClass(): string
    {
        return match (strtolower($this->title)) {
            'fiksi' => 'book-fill',
            'non fiksi' => 'book',
            'akademik' => 'mortarboard',
            'komik' => 'bluesky',
            default => 'collection',
        };
    }
}
