<?php

namespace App\Http\Controllers;

use App\Notifications\GeneralNotification;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    // Seller
    public function index()
    {
        // Mengambil buku dengan kategori dan menjumlahkan profit dari relasi orderItems
        $books = Book::with('category')
            ->withSum('items as total_real_profit', 'profit')
            ->get();

        $categories = Category::all();

        return view('seller.book.index', compact('books', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id'    => 'required|exists:categories,id',
            'photos_product' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'stock'          => 'required|integer|min:0',
            'unit'           => 'required|in:pcs,pack,box',
            'capital'        => 'required|numeric|min:0',
            'price'          => 'required|numeric|min:0|gte:capital',
            'margin'         => 'nullable|numeric',
        ]);

        $validated['photos_product'] =
            $request->file('photos_product')->store('books', 'public');

        // 🔥 AUTO HITUNG MARGIN (ANTI MANIPULASI)
        $validated['margin'] = round(
            (($validated['price'] - $validated['capital']) / $validated['capital']) * 100,
            2
        );

        $book = Book::create($validated);

        // Notifikasi ke Seller (Diri sendiri)
        auth()->user()->notify(new GeneralNotification([
            'title' => 'Produk Ditambahkan',
            'message' => "Buku '{$book->title}' berhasil diterbitkan dengan stok {$book->stock} {$book->unit}.",
            'icon' => '📚',
            'color' => 'bg-emerald-100 text-emerald-600',
            'url' => route('seller.book.index'),
        ]));

        return redirect()
            ->route('seller.book.index')
            ->with('success', 'Book created successfully.');
    }

    public function update(Request $request, $id)
    {
        $book = Book::findOrFail($id);

        $validated = $request->validate([
            'category_id'    => 'required|exists:categories,id',
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'stock'          => 'required|integer|min:0',
            'unit'           => 'required|in:pcs,pack,box',
            'capital'        => 'required|numeric|min:0',
            'price'          => 'required|numeric|min:0|gte:capital',
            'margin'         => 'nullable|numeric',
            'photos_product' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('photos_product')) {
            if ($book->photos_product) {
                Storage::disk('public')->delete($book->photos_product);
            }

            $validated['photos_product'] =
                $request->file('photos_product')->store('books', 'public');
        }

        // 🔥 AUTO HITUNG ULANG
        $validated['margin'] = round(
            (($validated['price'] - $validated['capital']) / $validated['capital']) * 100,
            2
        );

        $book->update($validated);

        // Notifikasi stok kritis otomatis
        if ($book->stock <= 5 && $book->stock > 0) {
            auth()->user()->notify(new GeneralNotification([
                'title' => 'Peringatan Stok Rendah!',
                'message' => "Stok buku '{$book->title}' sisa {$book->stock}. Segera restock!",
                'icon' => '⚠️',
                'color' => 'bg-yellow-100 text-yellow-600',
                'url' => route('seller.book.index'),
            ]));
        }

        return redirect()
            ->route('seller.book.index')
            ->with('success', 'Book updated successfully.');
    }

    public function destroy($id)
    {
        $book = Book::findOrFail($id);

        if ($book->stock > 0) {
            return back()->with('error', 'Mohon maaf, Produk masih mempunyai stok.');
        }

        $title = $book->title; // Simpan judul sebelum dihapus
        $book->delete();

        // Notifikasi penghapusan
        auth()->user()->notify(new GeneralNotification([
            'title' => 'Produk Dihapus',
            'message' => "Produk '{$title}' telah dihapus permanen dari sistem.",
            'icon' => '🗑️',
            'color' => 'bg-red-100 text-red-600',
            'url' => route('seller.book.index'),
        ]));

        return back()->with('success', 'Produk berhasil dihapus.');
    }
}
