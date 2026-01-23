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

        // 1. Validasi input
        $request->validate([
            'address' => 'required|string|max:500',
            'payment_method' => 'required|in:gopay,ovo,qris,transfer',
        ]);

        try {
            $newOrder = null;

            DB::transaction(function () use ($cart, $request, &$newOrder) {
                // 2. Update alamat di profil User agar permanen
                $request->user()->update([
                    'address' => $request->address
                ]);

                // 3. Simpan data Order utama
                $newOrder = Order::create([
                    'user_id'        => auth()->id(),
                    'total_price'    => array_sum(array_map(fn($item) => $item['price'] * $item['qty'], $cart)),
                    'status'         => 'pending',
                    'address'        => $request->address,
                    'payment_method' => $request->payment_method,
                ]);

                foreach ($cart as $id => $item) {
                    $bookId = $item['id'] ?? $id;
                    $book = Book::findOrFail($bookId);

                    // 4. Simpan Detail Item Pesanan
                    $newOrder->items()->create([
                        'book_id' => $bookId,
                        'qty'     => $item['qty'],
                        'price'   => $item['price'],
                        'capital' => $book->capital,
                        'profit'  => ($item['price'] - $book->capital) * $item['qty'],
                    ]);

                    // 5. Kurangi Stok Buku
                    $book->decrement('stock', $item['qty']);

                    // 6. Cek jika stok habis setelah pembelian untuk notifikasi Seller
                    if ($book->fresh()->stock <= 0) {
                        $this->notifySellerStockOut($book);
                    }
                }
            });

            // 7. Kirim Notifikasi ke Buyer (Konfirmasi Pesanan)
            auth()->user()->notify(new GeneralNotification([
                'title' => 'Pesanan Berhasil!',
                'message' => "Pesanan #ORD-{$newOrder->id} telah dibuat. Segera lakukan pembayaran via " . strtoupper($request->payment_method),
                'icon' => '🛒',
                'color' => 'bg-teal-100 text-teal-600',
                'url' => route('buyer.orders.index'),
            ]));

            // 8. Kirim Notifikasi ke Seller & Admin (Info Pesanan Masuk)
            $admins = User::whereIn('role', ['admin', 'seller'])->get();
            foreach ($admins as $admin) {
                $admin->notify(new GeneralNotification([
                    'title' => 'Ada Pesanan Baru 📦',
                    'message' => "Buyer " . auth()->user()->name . " memesan buku senilai Rp " . number_format($newOrder->total_price, 0, ',', '.'),
                    'icon' => '📦',
                    'color' => 'bg-blue-100 text-blue-600',
                    'url' => route('seller.approval.index'),
                ]));
            }

            // 9. Bersihkan Keranjang
            session()->forget('cart');

            return redirect()->route('buyer.carts.index')->with([
                'success' => 'Pesanan berhasil dibuat!',
                'checkout_success' => true,
                'order_id' => $newOrder->id
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses pesanan: ' . $e->getMessage());
        }
    }

    /**
     * Helper untuk kirim notifikasi stok habis
     */
    private function notifySellerStockOut($book)
    {
        $sellers = User::whereIn('role', ['admin', 'seller'])->get();
        foreach ($sellers as $seller) {
            $seller->notify(new GeneralNotification([
                'title' => 'STOK HABIS!',
                'message' => "Buku '{$book->title}' baru saja terjual habis melalui checkout terbaru.",
                'icon' => '🚫',
                'color' => 'bg-red-100 text-red-600',
                'url' => route('seller.book.index'),
            ]));
        }
    }
}
