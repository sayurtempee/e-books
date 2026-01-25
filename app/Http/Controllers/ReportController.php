<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index()
    {
        // 1. Ambil semua pesanan yang statusnya sudah valid (Approved atau Shipping)
        // Kita pusingkan (eager load) relasi items dan book untuk menghitung profit
        $orders = Order::whereIn('status', ['approved', 'shipping'])
            ->with('items.book')
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
            $dailyProfit = Order::whereIn('status', ['approved', 'shipping'])
                ->whereDate('created_at', $date)
                ->with('items')
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
        // 1. Ambil data pesanan yang sukses saja
        $orders = Order::whereIn('status', ['approved', 'shipping'])
            ->with(['user', 'items.book'])
            ->latest()
            ->get();

        // 2. Hitung total untuk ringkasan di PDF
        $totalRevenue = $orders->sum('total_price');
        $totalProfit = $orders->flatMap->items->sum('profit');

        // 3. Load view khusus untuk PDF
        $pdf = Pdf::loadView('seller.report.pdf', compact('orders', 'totalRevenue', 'totalProfit'))
            ->setPaper('a4', 'portrait');

        // 4. Download file
        return $pdf->download('Laporan-Penjualan-' . now()->format('Y-m-d') . '.pdf');
    }
}
