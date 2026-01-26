<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index()
    {
        // 1. Ambil semua pesanan yang statusnya sudah valid (Approved atau Shipping)
        // Kita pusingkan (eager load) relasi items dan book untuk menghitung profit
        $orders = Order::whereHas('items', function ($q) {
            $q->whereIn('status', ['approved', 'shipping']);
        })
            ->with(['items' => function ($q) {
                $q->whereIn('status', ['approved', 'shipping'])
                    ->with('book');
            }])
            ->get();

        // 2. Hitung statistik ringkas untuk Card di dashboard
        $totalRevenue = $orders->sum('total_price');
        $totalProfit = $orders->flatMap->items->sum('profit');
        $totalSold = $orders->flatMap->items->sum('qty');

        // 3. Menyiapkan data untuk Grafik (7 Hari Terakhir)
        // Kita ambil data harian agar grafik Chart.js tidak statis
        $days = collect();
        $profits = collect();

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $days->push(now()->subDays($i)->translatedFormat('l')); // Nama hari (Senin, Selasa, dst)

            // Hitung total profit pada tanggal tersebut
            $dailyProfit = Order::whereDate('created_at', $date)
                ->whereHas('items', function ($q) {
                    $q->whereIn('status', ['approved', 'shipping']);
                })
                ->with(['items' => function ($q) {
                    $q->whereIn('status', ['approved', 'shipping']);
                }])
                ->get()
                ->flatMap->items
                ->sum('profit');

            $profits->push($dailyProfit);
        }

        // 4. Kirim semua variabel ke view
        return view('seller.report.index', compact(
            'totalRevenue',
            'totalProfit',
            'totalSold',
            'days',
            'profits'
        ));
    }

    public function download()
    {
        $sellerId = auth()->id();

        // Ambil item yang valid untuk laporan
        $items = OrderItem::with(['order.user', 'book'])
            ->where('seller_id', $sellerId)
            ->whereIn('status', ['approved', 'shipping'])
            ->get();

        // ================= STATISTIK =================
        $totalRevenue = $items->sum(fn($i) => $i->price * $i->qty);
        $totalProfit  = $items->sum('profit');

        // Group per order (untuk tabel utama PDF)
        $orders = $items->groupBy('order_id')->map(function ($items) {
            return (object) [
                'id' => $items->first()->order->id,
                'user' => $items->first()->order->user,
                'created_at' => $items->first()->order->created_at,
                'items' => $items,
                'subtotal' => $items->sum(fn($i) => $i->price * $i->qty),
            ];
        });

        $pdf = Pdf::loadView('seller.report.pdf', [
            'orders'        => $orders,
            'totalRevenue' => $totalRevenue,
            'totalProfit'  => $totalProfit,
        ])->setPaper('A4', 'portrait');

        return $pdf->download('laporan-penjualan-' . now()->format('Y-m') . '.pdf');
    }
}
