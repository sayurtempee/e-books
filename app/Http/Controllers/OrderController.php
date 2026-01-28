<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Order;
use App\Models\Category;
use App\Models\OrderItem;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Notifications\GeneralNotification; // Import di bagian atas

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();

        $books = Book::query()
            ->when($request->search, function ($query, $search) {
                $query->where('title', 'like', '%' . $search . '%');
        })
            ->when($request->category, function ($query, $category) {
                $query->where('category_id', $category);
        })
            ->get();

        $items = OrderItem::all();

        return view('buyer.orders.index', compact('books', 'categories', 'items'));
    }

    public function downloadInvoice(Order $order)
    {
        // 1. Keamanan: Pastikan hanya pemilik yang bisa akses
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        // 2. Load relasi agar data buku dan user muncul di PDF
        // Gunakan items.book untuk mendapatkan judul buku dari relasi
        $order->load(['items.book', 'user']);

        // 3. Generate PDF menggunakan view khusus
        $pdf = Pdf::loadView('buyer.orders.invoice_pdf', compact('order'));

        // 4. Return download dengan nama file yang unik
        return $pdf->download('Invoice-ORD-' . $order->id . '-' . now()->format('Ymd') . '.pdf');
    }

    private function notifyBuyer(OrderItem $item)
    {
        // Pastikan relasi book, order, dan user tersedia
        if (!$item->book || !$item->order || !$item->order->user) return;

        $map = [
            'approved' => [
                'title' => 'Item Disetujui',
                'message' => "Item \"{$item->book->title}\" sedang disiapkan oleh seller.",
                'icon' => '✅',
                'color' => 'bg-teal-100 text-teal-600',
            ],
            'shipping' => [
                'title' => 'Item Dalam Pengiriman',
                // Menampilkan Ekspedisi dan Resi yang baru diupdate
                'message' => "Item \"{$item->book->title}\" telah dikirim via {$item->expedisi_name}. Resi: {$item->tracking_number}",
                'icon' => '🚚',
                'color' => 'bg-blue-100 text-blue-600',
            ],
            'selesai' => [ // Tambahkan status selesai
                'title' => 'Pesanan Selesai',
                'message' => "Terima kasih! Item \"{$item->book->title}\" telah diterima.",
                'icon' => '🏠',
                'color' => 'bg-emerald-100 text-emerald-600',
            ],
            'refunded' => [
                'title' => 'Item Dibatalkan',
                'message' => "Item \"{$item->book->title}\" telah dibatalkan & stok dikembalikan.",
                'icon' => '❌',
                'color' => 'bg-red-100 text-red-600',
            ],
        ];

        // Jika status tidak ada di map (misal: pending), jangan kirim notif
        if (!isset($map[$item->status])) return;

        $item->order->user->notify(
            new GeneralNotification([
                ...$map[$item->status],
                'url' => route('buyer.orders.tracking'),
            ])
        );
    }
}
