<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Book;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Notifications\GeneralNotification;

class CheckoutController extends Controller
{
    public function checkout(Request $request)
    {
        $cart = session('cart');
        if (!$cart) {
            return back()->with('error', 'Keranjang belanja kosong.');
        }

        $request->validate([
            'address' => 'required|string|max:500',
            'payment_method' => 'required|in:qris,gopay,ovo,transfer',
        ]);

        try {
            DB::transaction(function () use ($request, $cart, &$order) {

                // 1️⃣ Update profil user
                $user = $request->user();
                $user->update([
                    'address' => $request->address,
                ]);

                // 2️⃣ Buat order
                $order = Order::create([
                    'user_id' => $user->id,
                    'total_price' => collect($cart)->sum(fn($i) => $i['price'] * $i['qty']),
                    'status' => 'pending',
                    'address' => $request->address,
                    'payment_method' => $request->payment_method,
                ]);

                // 3️⃣ Simpan item & kelola stok
                foreach ($cart as $item) {
                    $book = Book::lockForUpdate()->findOrFail($item['id']);

                    if ($book->stock < $item['qty']) {
                        throw new \Exception("Stok {$book->title} tidak mencukupi.");
                    }

                    $order->items()->create([
                        'book_id' => $book->id,
                        'qty' => $item['qty'],
                        'price' => $item['price'],
                        'capital' => $book->capital,
                        'profit' => ($item['price'] - $book->capital) * $item['qty'],
                    ]);

                    $book->decrement('stock', $item['qty']);

                    // 4️⃣ Trigger notifikasi stok habis
                    if ($book->fresh()->stock === 0) {
                        $this->notifySellerStockOut($book);
                    }
                }
            });

            // 5️⃣ Notifikasi ke BUYER
            auth()->user()->notify(new GeneralNotification([
                'title' => 'Checkout Berhasil 🛒',
                'message' => "Pesanan #ORD-{$order->id} berhasil dibuat. Silakan lanjutkan pembayaran.",
                'icon' => '🧾',
                'color' => 'bg-teal-100 text-teal-700',
                'url' => route('buyer.orders.index'),
            ]));

            // 6️⃣ Notifikasi ke SELLER & ADMIN (order masuk)
            User::whereIn('role', ['admin', 'seller'])->each(function ($user) use ($order) {
                $user->notify(new GeneralNotification([
                    'title' => 'Pesanan Baru 📦',
                    'message' => "Order #ORD-{$order->id} dari {$order->user->name}",
                    'icon' => '📦',
                    'color' => 'bg-blue-100 text-blue-700',
                    'url' => route('seller.approval.index'),
                ]));
            });

            // 7️⃣ Bersihkan cart
            session()->forget('cart');

            return redirect()
                ->route('buyer.carts.index')
                ->with([
                    'checkout_success' => true,
                    'order_id' => $order->id,
                ]);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Notifikasi stok habis
     */
    private function notifySellerStockOut(Book $book)
    {
        User::whereIn('role', ['admin', 'seller'])->each(function ($user) use ($book) {
            $user->notify(new GeneralNotification([
                'title' => 'STOK HABIS 🚫',
                'message' => "Buku '{$book->title}' telah habis terjual.",
                'icon' => '🚫',
                'color' => 'bg-red-100 text-red-600',
                'url' => route('seller.book.index'),
            ]));
        });
    }
}
