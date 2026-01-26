<?php

namespace App\Http\Controllers;

use App\Notifications\GeneralNotification; // Import di bagian atas
use App\Models\User;
use App\Models\Book;
use App\Models\Order;
use App\Models\Category;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();

        $books = Book::query()

            // 🔍 Search by title
            ->when($request->search, function ($query, $search) {
                $query->where('title', 'like', '%' . $search . '%');
            })

            // 🏷 Filter by category
            ->when($request->category, function ($query, $category) {
                $query->where('category_id', $category);
            })

            ->get();

        return view('buyer.orders.index', compact('books', 'categories'));
    }

    public function uploadPayment(Request $request, Order $order)
    {
        $request->validate([
            'payment_proof' => 'required|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        if ($request->hasFile('payment_proof')) {
            // Hapus file lama jika ada
            if ($order->payment_proof) {
                Storage::delete('public/' . $order->payment_proof);
            }

            $path = $request->file('payment_proof')->store('payments', 'public');

            $order->update([
                'payment_proof' => $path,
                'status' => 'pending' // Tetap pending menunggu verifikasi seller
            ]);

            // Notifikasi
            $sellers = User::whereIn('role', ['seller', 'admin'])->get();

            $notifData = [
                'title' => 'Bukti Pembayaran Baru!',
                'message' => "Buyer " . auth()->user()->name . " telah mengunggah bukti bayar untuk #ORD-{$order->id}.",
                'icon' => '💳',
                'color' => 'bg-blue-100 text-blue-600',
                'url' => route('seller.approval.index'), // Link ke halaman verifikasi seller
            ];

            foreach ($sellers as $seller) {
                $seller->notify(new GeneralNotification($notifData));
            }
        }

        return back()->with('success', 'Bukti pembayaran berhasil diunggah! Seller akan segera memverifikasi.');
    }

    public function downloadInvoice(Order $order)
    {
        // 1. Keamanan: Pastikan hanya pemilik yang bisa akses
        if ($order->user_id !== auth()->id()) {
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
}
