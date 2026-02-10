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
        // --- PENAMBAHAN: Force Default Redirect ---
        // Jika user mengakses /seller/reports tanpa parameter, arahkan ke all & 2026
        if (!$request->has('month')) {
            return redirect()->route('seller.reports.index', [
                'month' => 'all',
                'year' => '2026'
            ]);
        }

        $sellerId = Auth::id();

        // Ambil input filter
        $selectedMonth = $request->get('month');
        $selectedYear = $request->get('year', 2026);

        // --- 1. Data Berdasarkan Filter ---
        $query = OrderItem::where('seller_id', $sellerId)
            ->whereIn('status', ['shipping', 'selesai']);

        // Ringkasan Seluruh Waktu (Tidak terpengaruh filter)
        $grandTotalQuery = clone $query;
        $grandTotalRevenue = $grandTotalQuery->get()->sum(fn($item) => $item->price * $item->qty);
        $grandTotalProfit  = $grandTotalQuery->sum('profit');

        // Filter berdasarkan Tahun
        $query->whereYear('created_at', $selectedYear);

        // Filter berdasarkan Bulan (Hanya jika bukan 'all')
        if ($selectedMonth !== 'all') {
            $query->whereMonth('created_at', $selectedMonth);
        }

        $sellerItems = $query->get();

        $totalRevenue = $sellerItems->sum(fn($item) => $item->price * $item->qty);
        $totalProfit  = $sellerItems->sum('profit');
        $totalSold    = $sellerItems->sum('qty');

        // --- 2. Menyiapkan data untuk Grafik ---
        $days = collect();
        $profits = collect();
        $revenues = collect();

        if ($selectedMonth === 'all') {
            // Jika SEMUA BULAN, tampilkan grafik Jan - Des
            for ($m = 1; $m <= 12; $m++) {
                $monthName = \Carbon\Carbon::createFromDate(null, $m, 1)->locale('id')->translatedFormat('M');
                $days->push($monthName);

                $monthlyData = OrderItem::where('seller_id', $sellerId)
                    ->whereYear('created_at', $selectedYear)
                    ->whereMonth('created_at', $m)
                    ->whereIn('status', ['shipping', 'selesai'])
                    ->get();

                $revenues->push($monthlyData->sum(fn($item) => $item->price * $item->qty));
                $profits->push($monthlyData->sum('profit'));
            }
        } else {
            // Jika BULAN TERTENTU, tampilkan grafik harian
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, (int)$selectedMonth, (int)$selectedYear);
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $date = sprintf('%04d-%02d-%02d', $selectedYear, $selectedMonth, $d);
                $days->push($d);

                $dailyItems = OrderItem::where('seller_id', $sellerId)
                    ->whereDate('created_at', $date)
                    ->whereIn('status', ['shipping', 'selesai'])
                    ->get();

                $revenues->push($dailyItems->sum(fn($item) => $item->price * $item->qty));
                $profits->push($dailyItems->sum('profit'));
            }
        }

        $yearRange = range(2026, 2021); // Tahun tetap ke 2026 sebagai start

        return view('seller.report.index', compact(
            'totalRevenue',
            'totalProfit',
            'totalSold',
            'grandTotalRevenue',
            'grandTotalProfit',
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

    public function downloadAll()
    {
        $sellerId = Auth::id();

        // Ambil semua data tanpa filter bulan/tahun
        $items = OrderItem::with(['order.user', 'book'])
            ->where('seller_id', $sellerId)
            ->whereIn('status', ['shipping', 'selesai'])
            ->get();

        // Hitung total
        $totalRevenue = $items->sum(fn($i) => $i->price * $i->qty);
        $totalProfit  = $items->sum('profit');

        // Kelompokkan berdasarkan order_id untuk tampilan tabel di PDF
        $orders = $items->groupBy('order_id');

        $periode = "Semua Waktu";

        // Load view PDF yang sama
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('seller.report.pdf', [
            'orders'       => $orders,
            'totalRevenue' => $totalRevenue,
            'totalProfit'  => $totalProfit,
            'periode'      => $periode,
        ])->setPaper('A4', 'portrait');

        $filename = 'laporan-penjualan-keseluruhan-' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }
}
