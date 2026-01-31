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
    public function confirmPage()
    {
        $cart = session('cart');
        if (!$cart || count($cart) === 0) {
            return redirect()->route('buyer.carts.index')->with('error', 'Keranjang belanja kosong.');
        }

        $sellerIds = collect($cart)->pluck('seller_id')->unique();
        $sellers = User::whereIn('id', $sellerIds)->get();

        return view('buyer.checkout.confirm', compact('cart', 'sellers'));
    }

    public function checkout(Request $request)
    {
        $cart = session('cart');
        if (!$cart || count($cart) === 0) {
            return redirect()->route('buyer.carts.index')->with('error', 'Keranjang belanja kosong.');
        }

        $request->validate([
            'address' => 'required|string|max:500',
            'payment_method' => 'required|in:qris,gopay,ovo,transfer',
        ]);

        try {
            $order = null;
            DB::transaction(function () use ($request, $cart, &$order) {
                $user = $request->user();
                $user->update(['address' => $request->address]);

                $order = Order::create([
                    'user_id' => $user->id,
                    'total_price' => collect($cart)->sum(fn($i) => $i['price'] * $i['qty']),
                    'payment_method' => $request->payment_method,
                    'address' => $request->address,
                ]);

                foreach ($cart as $item) {
                    $book = Book::lockForUpdate()->findOrFail($item['id'] ?? $item['book_id']);
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
                    $book->decrement('stock', $item['qty']);
                }
            });

            session()->put('order_id', $order->id);
            auth()->user()->notify(new GeneralNotification([
                'title' => 'Checkout Berhasil 🛒',
                'message' => "Pesanan #ORD-{$order->id} berhasil dibuat.",
                'icon' => '🧾',
                'color' => 'bg-teal-100 text-teal-700',
                'url' => route('buyer.orders.index'),
            ]));

            session()->forget('cart');
            session()->put('order_id', $order->id);
            return redirect()->route('buyer.carts.index')->with('checkout_success', true);
        } catch (\Exception $e) {
            return redirect()->route('buyer.carts.index')->with('error', $e->getMessage());
        }
    }

    public function payPage(Order $order)
    {
        // Eager load untuk mempercepat query
        $order->load('items.book.user');

        // Ambil penjual unik
        $sellers = $order->items->groupBy('seller_id');

        return view('buyer.payment.pay', compact('order', 'sellers'));
    }
}
