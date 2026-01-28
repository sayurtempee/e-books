<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $sellerId = Auth::id();

        // Ambil input filter (default ke bulan & tahun sekarang)
        $selectedMonth = $request->get('month', now()->month);
        $selectedYear = $request->get('year', now()->year);

        // 1. Ambil data berdasarkan filter untuk Statistik Ringkas
        $query = OrderItem::where('seller_id', $sellerId)
            ->whereIn('status', ['shipping', 'selesai'])
            ->whereMonth('created_at', $selectedMonth)
            ->whereYear('created_at', $selectedYear);

        $sellerItems = $query->get();

        $totalRevenue = $sellerItems->sum(fn($item) => $item->price * $item->qty);
        $totalProfit  = $sellerItems->sum('profit');
        $totalSold    = $sellerItems->sum('qty');

        // 2. Menyiapkan data untuk Grafik (Berdasarkan jumlah hari dalam bulan terpilih)
        $days = collect();
        $profits = collect();
        $revenues = collect();

        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $selectedMonth, $selectedYear);

        for ($d = 1; $d <= $daysInMonth; $d++) {
            $date = sprintf('%04d-%02d-%02d', $selectedYear, $selectedMonth, $d);
            $days->push($d); // Label sumbu X cukup tanggalnya saja (1-31)

            $dailyItems = OrderItem::where('seller_id', $sellerId)
                ->whereDate('created_at', $date)
                ->whereIn('status', ['shipping', 'selesai'])
                ->get();

            $revenues->push($dailyItems->sum(fn($item) => $item->price * $item->qty));
            $profits->push($dailyItems->sum('profit'));
        }

        // Untuk dropdown tahun (5 tahun terakhir)
        $yearRange = range(now()->year, now()->year - 5);

        return view('seller.report.index', compact(
            'totalRevenue',
            'totalProfit',
            'totalSold',
            'days',
            'profits',
            'revenues',
            'selectedMonth',
            'selectedYear',
            'yearRange'
        ));
    }

    public function download(Request $request)
    {
        $sellerId = Auth::id();
        $month = $request->get('month');
        $year = $request->get('year');

        // Inisialisasi query
        $query = OrderItem::with(['order.user', 'book'])
            ->where('seller_id', $sellerId)
            ->whereIn('status', ['shipping', 'selesai']);

        // Filter berdasarkan Tahun jika ada
        $query->when($year, function ($q) use ($year) {
            return $q->whereYear('created_at', $year);
        });

        // Filter berdasarkan Bulan jika ada
        $query->when($month, function ($q) use ($month) {
            return $q->whereMonth('created_at', $month);
        });

        $items = $query->get();

        $totalRevenue = $items->sum(fn($i) => $i->price * $i->qty);
        $totalProfit  = $items->sum('profit');
        $orders = $items->groupBy('order_id');

        // Menyiapkan teks periode untuk ditampilkan di PDF
        if ($month && $year) {
            // Buat objek carbon dari bulan & tahun, lalu ubah ke bahasa Indonesia
            $namaBulan = \Carbon\Carbon::createFromDate($year, $month, 1)
                ->locale('id')
                ->translatedFormat('F');
            $periode = $namaBulan . ' ' . $year;
        } elseif ($year) {
            $periode = "Tahun " . $year;
        } else {
            $periode = "Semua Waktu";
        }

        $pdf = Pdf::loadView('seller.report.pdf', [
            'orders'       => $orders,
            'totalRevenue' => $totalRevenue,
            'totalProfit'  => $totalProfit,
            'periode'      => $periode, // Kirim variabel periode
        ])->setPaper('A4', 'portrait');

        $filename = 'laporan-penjualan-' . str_replace(' ', '-', strtolower($periode)) . '.pdf';
        return $pdf->download($filename);
    }
}
