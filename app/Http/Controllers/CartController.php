<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use App\Notifications\GeneralNotification;

class CartController extends Controller
{
    public function index()
    {
        return view('buyer.carts.index');
    }

    public function store(Request $request)
    {
        // Ambil data buku beserta relasi user (seller)
        $book = Book::with('user')->find($request->book_id);

        if (!$book) return back()->with('error', 'Produk tidak ditemukan');

        $cart = session()->get('cart', []);
        $id = $book->id;

        if (isset($cart[$id])) {
            if ($book->stock <= $cart[$id]['qty']) {
                return back()->with('error', 'Stok tidak mencukupi!');
            }
            $cart[$id]['qty']++;
        } else {
            $cart[$id] = [
                "id" => $id,
                "title" => $book->title,
                "qty" => 1,
                "price" => $book->price,
                "photos_product" => $book->photos_product,
                "seller_id" => $book->user_id, // Simpan ID Seller
                "seller_name" => $book->user->name // Simpan Nama Seller untuk tampilan
            ];
        }

        session()->put('cart', $cart);
        return redirect()->back()->with('success', 'Keranjang diperbarui!');
    }

    public function destroy($id)
    {
        $cart = session()->get('cart');

        if (isset($cart[$id])) {
            if ($cart[$id]['qty'] > 1) {
                $cart[$id]['qty']--;
            } else {
                unset($cart[$id]);
            }
            session()->put('cart', $cart);
        }

        return redirect()->back()->with('success', 'Keranjang diperbarui');
    }
}
