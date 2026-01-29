<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\OrderItem;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use Notifiable, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nik',
        'name',
        'email',
        'password',
        'role',
        'foto_profile',
        'address',
        'isOnline',
        'no_rek',
        'bank_name',
        'last_activity_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_activity_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function books()
    {
        // Asumsi: Di tabel 'books' ada kolom 'user_id' untuk menandai pemilik buku
        return $this->hasMany(Book::class, 'user_id');
    }

    public function orderItems()
    {
        // Relasi User ke OrderItems (berdasarkan kolom seller_id di image database kamu)
        return $this->hasMany(OrderItem::class, 'seller_id');
    }

    public function getSoldCountAttribute()
    {
        // Mengambil semua order_items melalui buku yang dimiliki user
        // Hanya menghitung jika status order adalah 'approved' atau 'completed'
        return OrderItem::whereHas('book', function ($query) {
            $query->where('user_id', $this->id);
        })->whereHas('order', function ($query) {
            $query->whereNotNull('approved_at'); // Mengasumsikan jika sudah di-approve = terjual
        })->sum('qty');
    }

    public function getBooksCountAttribute()
    {
        return $this->books()->count();
    }

    public function isOnline(): bool
    {
        return $this->last_activity_at &&
            $this->last_activity_at->gt(now()->subMinutes(2));
    }

    public function conversations()
    {
        // Mengambil semua percakapan di mana user terlibat sebagai pengirim atau penerima
        return $this->hasMany(Conversation::class, 'sender_id')
            ->orWhere('receiver_id', $this->id);
    }

    public function getTotalProfitAttribute()
    {
        return OrderItem::where('seller_id', $this->id)
            ->whereIn('status', ['approved', 'shipping', 'selesai'])
            ->sum('profit'); // Menggunakan kolom 'profit' yang ada di tabel order_items Anda
    }
}
