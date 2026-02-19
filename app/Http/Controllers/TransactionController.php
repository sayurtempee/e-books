<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Notifications\GeneralNotification;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function indexApproval()
    {
        // Ambil data dan kelompokkan berdasarkan order_id
        $groupedItems = OrderItem::where('seller_id', Auth::id())
            // ->where('status', '!=', 'refunded') // ini itu untuk menghilangkan refunded status di views nya.
            ->with(['order.user', 'book'])
            ->latest()
            ->get()
            ->groupBy('order_id');

        return view('seller.approval.index', compact('groupedItems'));
    }

    public function updateApproval(Request $request, $orderId)
    {
        // 1. Ambil data items terlebih dahulu agar bisa digunakan di validasi
        $items = OrderItem::where('order_id', $orderId)
            ->where('seller_id', Auth::id())
            ->get();

        if ($items->isEmpty()) {
            return back()->with('error', 'Pesanan tidak ditemukan.');
        }

        // 2. Validasi
        $request->validate([
            'status' => 'required|in:tolak,pending,approved,shipping,refunded,selesai',
            'cancel_reason' => 'required_if:status,tolak|required_if:status,refunded|nullable|string|max:500',
            'expedisi_name' => 'required_if:status,shipping',
            'tracking_number' => [
                'nullable',
                'required_if:status,shipping',
                function ($attribute, $value, $fail) use ($orderId) {
                    if (empty($value)) return;
                    $isUsed = OrderItem::where('tracking_number', $value)
                        ->where('order_id', '!=', $orderId)
                        ->exists();
                    if ($isUsed) $fail('Nomor resi ini sudah digunakan untuk order lain.');
                },
            ],
        ]);

        DB::transaction(function () use ($request, $items) {
            $status = $request->status;

            foreach ($items as $item) {
                $updateData = ['status' => $status];

                // Logika Approved
                if ($status === 'approved') {
                    $updateData['approved_at'] = now();
                }

                // Logika Shipping
                if ($status === 'shipping') {
                    $updateData['expedisi_name'] = $request->expedisi_name;
                    $updateData['tracking_number'] = $request->tracking_number;
                }

                // Logika Tolak & Refund (Tambah Alasan)
                if (in_array($status, ['tolak', 'refunded'])) {
                    $updateData['cancel_reason'] = $request->cancel_reason;

                    if ($status === 'refunded') {
                        $updateData['refunded_at'] = now();
                        if ($item->book) {
                            $item->book->increment('stock', (int) $item->qty);
                        }
                    }
                }

                $item->update($updateData);
            }

            $this->notifyBuyer($items->first());
        });

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }

    public function purchaseHistory(Request $request)
    {
        // 1. Inisialisasi query dari OrderItem agar bisa grouping per seller
        $query = OrderItem::with(['order', 'book.user'])
            ->whereHas('order', function ($q) {
                $q->where('user_id', Auth::id());
            });

        // 2. Filter berdasarkan Judul Buku
        if ($request->filled('title')) {
            $query->whereHas('book', function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->title . '%');
            });
        }

        // 3. Filter berdasarkan Status Pesanan
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 4. Filter berdasarkan Rentang Tanggal
        if ($request->filled('start_date') || $request->filled('end_date')) {
            $query->whereHas('order', function ($q) use ($request) {
                if ($request->filled('start_date') && $request->filled('end_date')) {
                    $start = Carbon::parse($request->start_date)->startOfDay();
                    $end = Carbon::parse($request->end_date)->endOfDay();
                    $q->whereBetween('created_at', [$start, $end]);
                } elseif ($request->filled('start_date')) {
                    $q->whereDate('created_at', '>=', $request->start_date);
                } else {
                    $q->whereDate('created_at', '<=', $request->end_date);
                }
            });
        }

        // 5. Eksekusi Query dan Grouping secara Manual
        // Grouping berdasarkan kombinasi order_id dan seller_id
        $items = $query->latest()->get();

        $groupedData = $items->groupBy(function ($item) {
            return $item->order_id . '-' . $item->seller_id;
        });

        // 6. Logika Manual Pagination (Agar bisa pakai ->links() di Blade)
        $currentPage = Paginator::resolveCurrentPage() ?: 1;
        $perPage = 10; // Jumlah kartu pesanan per halaman

        // Memotong collection sesuai halaman yang sedang dibuka
        $currentItems = $groupedData->slice(($currentPage - 1) * $perPage, $perPage)->all();

        // Membuat objek Paginator
        $purchases = new LengthAwarePaginator(
            $currentItems,
            $groupedData->count(),
            $perPage,
            $currentPage,
            [
                'path' => Paginator::resolveCurrentPath(),
                'query' => $request->query(), // Menjaga filter tetap ada di URL saat ganti halaman
            ]
        );

        return view('buyer.history', compact('purchases'));
    }

    private function notifyBuyer(OrderItem $item)
    {
        if (!$item->book || !$item->order) return;

        $map = [
            'tolak' => ['title' => 'Pesanan Ditolak', 'message' => "Pesanan #ORD-{$item->order_id} ditolak", 'icon' => '❌'],
            'pending'  => [
                'title' => 'Pesanan Menunggu',
                'message' => "Pesanan #ORD-{$item->order_id} sedang menunggu konfirmasi penjual.",
                'icon' => '⏳'
            ],
            'approved' => ['title' => 'Pesanan Diproses', 'message' => "Pesanan #ORD-{$item->order_id} sedang diproses.", 'icon' => '✅'],
            'shipping' => ['title' => 'Pesanan Dikirim', 'message' => "Pesanan #ORD-{$item->order_id} dalam pengiriman. Resi: {$item->tracking_number}", 'icon' => '🚚'],
            'selesai'  => ['title' => 'Pesanan Selesai', 'message' => "Pesanan #ORD-{$item->order_id} telah diterima.", 'icon' => '📦'],
            'refunded' => ['title' => 'Pesanan Dibatalkan', 'message' => "Pesanan #ORD-{$item->order_id} dibatalkan.", 'icon' => '❌'],
        ];

        if (!isset($map[$item->status])) return;

        $item->order->user->notify(new GeneralNotification([
            ...$map[$item->status],
            'color' => 'bg-teal-100 text-teal-600',
            'url' => route('buyer.orders.tracking'),
        ]));
    }
}
