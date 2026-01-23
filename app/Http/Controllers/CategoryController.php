<?php

namespace App\Http\Controllers;

use App\Notifications\GeneralNotification;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('admin.category.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'created_at' => 'required|date',
        ]);

        $category = Category::create([
            'title' => $request->title,
            'created_at' => $request->created_at,
        ]);

        auth()->user()->notify(new GeneralNotification([
            'title' => 'Kategori Baru',
            'message' => "Kategori '{$category->title}' telah berhasil ditambahkan ke sistem.",
            'icon' => '📁',
            'color' => 'bg-emerald-100 text-emerald-600',
            'url' => route('admin.categories'),
        ]));

        return back()->with('success', 'Kategori berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        if ($category->books()->exists()) {
            return back()->with('error', 'Mohon maaf, kategori masih memiliki produk buku.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'created_at' => 'required|date',
        ]);

        $oldTitle = $category->title;
        $category->update($request->only('title', 'created_at'));

        // NOTIFIKASI KE ADMIN
        auth()->user()->notify(new GeneralNotification([
            'title' => 'Kategori Diperbarui',
            'message' => "Kategori '{$oldTitle}' telah diubah menjadi '{$category->title}'.",
            'icon' => '✏️',
            'color' => 'bg-blue-100 text-blue-600',
            'url' => route('admin.categories'),
        ]));

        return back()->with('success', 'Kategori berhasil diperbarui');
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        if ($category->books()->exists()) {
            return back()->with('error', 'Mohon maaf, kategori masih memiliki produk buku.');
        }

        $title = $category->title;
        $category->delete();

        // NOTIFIKASI KE ADMIN
        auth()->user()->notify(new GeneralNotification([
            'title' => 'Kategori Dihapus',
            'message' => "Kategori '{$title}' telah dihapus permanen.",
            'icon' => '🗑️',
            'color' => 'bg-red-100 text-red-600',
            'url' => route('admin.categories'),
        ]));

        return back()->with('success', 'Kategori berhasil dihapus.');
    }
}
