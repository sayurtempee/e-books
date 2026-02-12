<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
        $month = $request->input('month'); // Bisa berisi 'all' atau angka 1-12
        $year = $request->input('year');
        $chartImage = $request->input('chart_image');

        $query = OrderItem::with(['order.user', 'book'])
            ->where('seller_id', $sellerId)
            ->whereIn('status', ['shipping', 'selesai']);

        // Filter Tahun
        $query->whereYear('created_at', $year);

        // Filter Bulan (Jika bukan 'all')
        if ($month && $month !== 'all') {
            $query->whereMonth('created_at', $month);
            $namaBulan = Carbon::createFromDate($year, $month, 1)->locale('id')->translatedFormat('F');
            $periode = $namaBulan . ' ' . $year;
        } else {
            $periode = "Semua Bulan di Tahun " . $year;
        }

        $items = $query->get();

        // Hitung statistik untuk dikirim ke PDF
        $totalRevenue = $items->sum(fn($i) => $i->price * $i->qty);
        $totalProfit  = $items->sum('profit');
        $orders = $items->groupBy('order_id');

        $pdf = Pdf::loadView('seller.report.pdf', [
            'orders'       => $orders,
            'totalRevenue' => $totalRevenue,
            'totalProfit'  => $totalProfit,
            'periode'      => $periode,
            'chartImage'   => $chartImage
        ])->setPaper('A4', 'portrait');

        $filename = 'laporan-penjualan-' . str_replace(' ', '-', strtolower($periode)) . '.pdf';
        return $pdf->download($filename);
    }
}
