<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Notifications\GeneralNotification;

class CheckoutController extends Controller
{
    public function checkout(Request $request)
    {
        $cart = session('cart');

        if (!$cart || count($cart) === 0) {
            return back()->with('error', 'Keranjang belanja kosong.');
        }

        $request->validate([
            'address' => 'required|string|max:500',
            'payment_method' => 'required|in:qris,gopay,ovo,transfer',
        ]);

        try {
            DB::transaction(function () use ($request, $cart, &$order) {

                $user = $request->user();

                // SIMPAN ALAMAT TERAKHIR
                $user->update([
                    'address' => $request->address,
                ]);

                // ================= CREATE ORDER =================
                $order = Order::create([
                    'user_id' => $user->id,
                    'total_price' => collect($cart)->sum(
                        fn($i) => $i['price'] * $i['qty']
                    ),
                    'payment_method' => $request->payment_method,
                    'address' => $request->address,
                ]);

                // ================= CREATE ORDER ITEMS =================
                foreach ($cart as $item) {
                    $book = Book::lockForUpdate()->findOrFail($item['id']);

                    if ($book->stock < $item['qty']) {
                        throw new \Exception("Stok {$book->title} tidak mencukupi.");
                    }

                    $order->items()->create([
                        'book_id'   => $book->id,
                        'seller_id' => $book->user_id,
                        'qty'       => $item['qty'],
                        'price'     => $item['price'],
                        'capital'   => $book->capital,
                        'profit'    => ($item['price'] - $book->capital) * $item['qty'],
                        'status'    => 'pending',
                    ]);

                    // KURANGI STOK
                    $book->decrement('stock', $item['qty']);
                }
            });

            // SIMPAN ORDER KE SESSION
            session()->put('order_id', $order->id);

            // NOTIFIKASI BUYER
            auth()->user()->notify(new GeneralNotification([
                'title' => 'Checkout Berhasil 🛒',
                'message' => "Pesanan #ORD-{$order->id} berhasil dibuat.",
                'icon' => '🧾',
                'color' => 'bg-teal-100 text-teal-700',
                'url' => route('buyer.payment'),
            ]));

            session()->forget('cart');

            return redirect()
                ->route('buyer.carts.index')
                ->with('checkout_success', true);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}


