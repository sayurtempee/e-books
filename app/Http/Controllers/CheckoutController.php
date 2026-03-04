<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
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

                // 1. Update alamat pembeli
                $user->update(['address' => $request->address]);

                // 2. Buat record Order utama
                $order = Order::create([
                    'user_id' => $user->id,
                    'total_price' => collect($cart)->sum(fn($i) => $i['price'] * $i['qty']),
                    'payment_method' => $request->payment_method,
                    'address' => $request->address,
                ]);

                // Array untuk melacak penjual mana saja yang sudah dikirimi notifikasi
                $notificationSellers = [];

                foreach ($cart as $item) {
                    // Ambil data buku dan kunci (lock) untuk mencegah race condition stok
                    $book = Book::lockForUpdate()->findOrFail($item['id'] ?? $item['book_id']);

                    if ($book->stock < $item['qty']) {
                        throw new \Exception("Stok produk '{$book->title}' tidak mencukupi.");
                    }

                    // 3. Simpan item pesanan
                    $order->items()->create([
                        'book_id'   => $book->id,
                        'seller_id' => $book->user_id,
                        'qty'       => $item['qty'],
                        'price'     => $item['price'],
                        'capital'   => $book->capital,
                        'profit'    => ($item['price'] - $book->capital) * $item['qty'],
                        'status'    => 'pending',
                    ]);

                    // 4. Kurangi stok buku
                    $book->decrement('stock', $item['qty']);

                    // 5. LOGIKA NOTIFIKASI UNTUK PENJUAL
                    // Cek apakah penjual ini sudah masuk daftar notifikasi di transaksi ini
                    if (!in_array($book->user_id, $notificationSellers)) {
                        $seller = $book->user; // Pastikan relasi 'user' ada di model Book

                        if ($seller) {
                            $seller->notify(new GeneralNotification([
                                'title' => 'Pesanan Baru Masuk! 📦',
                                'message' => "Hore! Produk Anda dipesan dalam order #ORD-{$order->id}.",
                                'icon' => '🛍️',
                                'color' => 'bg-blue-100 text-blue-700',
                                'url' => route('seller.approval.index'), // Sesuaikan dengan route dashboard penjual Anda
                            ]));
                        }

                        // Masukkan ke array agar tidak dikirim double jika ada buku lain dari penjual yg sama
                        $notificationSellers[] = $book->user_id;
                    }
                }
            });

            // 6. NOTIFIKASI UNTUK PEMBELI (Berhasil Checkout)
            Auth::user()->notify(new GeneralNotification([
                'title' => 'Checkout Berhasil 🛒',
                'message' => "Pesanan #ORD-{$order->id} berhasil dibuat. Silakan selesaikan pembayaran.",
                'icon' => '🧾',
                'color' => 'bg-teal-100 text-teal-700',
                'url' => route('buyer.orders.index'),
            ]));

            // Bersihkan session
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

    public function uploadProof(Request $request)
    {
        $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'order_id' => 'required',
            'seller_id' => 'required' // Pastikan seller_id dikirim dari form
        ]);

        // 1. Filter hanya item yang sesuai dengan ORDER ID dan SELLER ID tersebut
        $items = OrderItem::where('order_id', $request->order_id)
            ->where('seller_id', $request->seller_id)
            ->get();

        if ($items->isEmpty()) {
            return back()->with('error', 'Pesanan untuk toko ini tidak ditemukan.');
        }

        if ($request->hasFile('payment_proof')) {
            $path = $request->file('payment_proof')->store('payment_proofs', 'public');

            foreach ($items as $item) {
                $item->update([
                    'payment_proof' => $path,
                    'status' => 'approved'
                ]);
            }

            return back()->with('success', 'Bukti berhasil diunggah untuk toko ini. Status: APPROVED');
        }

        return back()->with('error', 'Gagal mengunggah file.');
    }
}
