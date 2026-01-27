<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Notifications\GeneralNotification;
// Tambahkan Facade Notification sebagai alternatif jika Auth::user()->notify() masih merah
use Illuminate\Support\Facades\Notification;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::where('user_id', Auth::id())
            ->with('category')
            ->latest()
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
        ]);

        $validated['user_id'] = Auth::id();

        if ($request->hasFile('photos_product')) {
            $validated['photos_product'] = $request->file('photos_product')->store('books', 'public');
        }

        // AUTO HITUNG MARGIN
        $validated['margin'] = $validated['capital'] > 0
            ? round((($validated['price'] - $validated['capital']) / $validated['capital']) * 100, 2)
            : 0;

        $book = Book::create($validated);

        $this->notifyBook($book, [
            'title'   => 'Produk Ditambahkan',
            'message' => "Buku '{$book->title}' berhasil diterbitkan dengan stok {$book->stock} {$book->unit}.",
            'icon'    => '📚',
            'color'   => 'bg-emerald-100 text-emerald-600',
        ]);

        return redirect()->route('seller.book.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $book = Book::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'category_id'    => 'required|exists:categories,id',
            'photos_product' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'stock'          => 'required|integer|min:0',
            'unit'           => 'required|in:pcs,pack,box',
            'capital'        => 'required|numeric|min:0',
            'price'          => 'required|numeric|min:0|gte:capital',
        ]);

        if ($request->hasFile('photos_product')) {
            if ($book->photos_product) {
                Storage::disk('public')->delete($book->photos_product);
            }
            $validated['photos_product'] = $request->file('photos_product')->store('books', 'public');
        }

        $validated['margin'] = $validated['capital'] > 0
            ? round((($validated['price'] - $validated['capital']) / $validated['capital']) * 100, 2)
            : 0;

        $book->update($validated);

        if ($book->stock <= 5 && $book->stock > 0) {
            $this->notifyBook($book, [
                'title'   => 'Peringatan Stok Rendah!',
                'message' => "Stok buku '{$book->title}' sisa {$book->stock}. Segera restock!",
                'icon'    => '⚠️',
                'color'   => 'bg-yellow-100 text-yellow-600',
            ]);
        }

        return redirect()->route('seller.book.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $book = Book::where('user_id', Auth::id())->findOrFail($id);

        if ($book->stock > 0) {
            return back()->with('error', 'Produk tidak bisa dihapus karena masih memiliki stok.');
        }

        $title = $book->title;

        if ($book->photos_product) {
            Storage::disk('public')->delete($book->photos_product);
        }

        $book->delete();

        // Menggunakan Facade Notification untuk menghindari "Undefined Method"
        Notification::send(Auth::user(), new GeneralNotification([
            'title'   => 'Produk Dihapus',
            'message' => "Produk '{$title}' telah dihapus dari sistem.",
            'icon'    => '🗑️',
            'color'   => 'bg-red-100 text-red-600',
            'url'     => route('seller.book.index'),
        ]));

        return back()->with('success', 'Produk berhasil dihapus.');
    }

    private function notifyBook(Book $book, array $data)
    {
        $user = Auth::user();

        if ($user) {
            // Menggunakan Facade Notification agar editor tidak protes
            Notification::send($user, new GeneralNotification([
                'title'   => $data['title'],
                'message' => $data['message'],
                'icon'    => $data['icon'],
                'color'   => $data['color'],
                'url'     => route('seller.book.index'),
            ]));
        }
    }
}
