<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index()
    {
        $sellerId = Auth::id();

        // 1. Ambil OrderItem milik seller ini dengan status yang valid
        $sellerItems = OrderItem::where('seller_id', $sellerId)
            ->whereIn('status', ['approved', 'shipping'])
            ->with(['order', 'book'])
            ->get();

        // 2. Hitung statistik ringkas khusus untuk seller ini
        $totalRevenue = $sellerItems->sum(fn($item) => $item->price * $item->qty);
        $totalProfit  = $sellerItems->sum('profit');
        $totalSold    = $sellerItems->sum('qty');

        // 3. Menyiapkan data untuk Grafik (7 Hari Terakhir)
        $days = collect();
        $profits = collect();

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $days->push(now()->subDays($i)->translatedFormat('l'));

            // Hitung profit harian KHUSUS seller ini
            $dailyProfit = OrderItem::where('seller_id', $sellerId)
                ->whereDate('created_at', $date)
                ->whereIn('status', ['approved', 'shipping'])
                ->sum('profit');

            $profits->push($dailyProfit);
        }

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
        $sellerId = Auth::id();

        // Ambil item milik seller tertentu
        $items = OrderItem::with(['order.user', 'book'])
            ->where('seller_id', $sellerId)
            ->whereIn('status', ['approved', 'shipping'])
            ->get();

        $totalRevenue = $items->sum(fn($i) => $i->price * $i->qty);
        $totalProfit  = $items->sum('profit');

        // Group per order agar tampilan PDF rapi
        $orders = $items->groupBy('order_id');

        $pdf = Pdf::loadView('seller.report.pdf', [
            'orders'       => $orders,
            'totalRevenue' => $totalRevenue,
            'totalProfit'  => $totalProfit,
        ])->setPaper('A4', 'portrait');

        return $pdf->download('laporan-penjualan-' . now()->format('d-m-Y') . '.pdf');
    }
}
