<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\User;
use App\Models\Order;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Jobs\DeleteRefundedOrderJob;
use App\Notifications\GeneralNotification;

class TransactionController extends Controller
{
    public function indexApproval()
    {
        // Mengambil semua order milik seller (atau semua order) beserta item dan bukunya
        $orders = Order::with(['items.book', 'user'])->get();

        return view('seller.approval.index', compact('orders'));
    }

    public function updateApproval(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,shipping,refunded',
            'tracking_number' => 'nullable|string|max:100|unique:orders,tracking_number,' . $order->id,
        ]);

        $data = ['status' => $request->status];

        // Persiapkan variabel untuk isi notifikasi
        $notifDetails = [
            'title' => '',
            'message' => '',
            'icon' => '🔔',
            'color' => 'bg-teal-100 text-teal-600',
            'url' => route('buyer.orders.index'), // Arahkan buyer ke riwayat pesanan
        ];

        if ($request->status === 'approved') {
            // Logika jika pesanan disetujui (sedang disiapkan)
            if ($order->approved_at === null) $data['approved_at'] = now();
            $data['tracking_number'] = null; // Belum ada resi saat disiapkan

            $notifDetails['title'] = 'Pesanan Disetujui';
            $notifDetails['message'] = "Pembayaran Anda valid. Pesanan #ORD-{$order->id} sedang kami siapkan.";
            $notifDetails['icon'] = '✅';
            $notifDetails['color'] = 'bg-teal-100 text-teal-600';
        } elseif ($request->status === 'shipping') {
            // Logika jika pesanan dikirim
            $data['tracking_number'] = $request->tracking_number ?? 'JNE' . strtoupper(Str::random(10));

            $notifDetails['title'] = 'Pesanan Dalam Pengiriman';
            $notifDetails['message'] = "Hore! Pesanan #ORD-{$order->id} telah dikirim dengan resi: {$data['tracking_number']}.";
            $notifDetails['icon'] = '🚚';
            $notifDetails['color'] = 'bg-blue-100 text-blue-600';
        } elseif ($request->status === 'refunded') {
            $data['refunded_at'] = now();

            $notifDetails['title'] = 'Pesanan Dibatalkan/Refund';
            $notifDetails['message'] = "Maaf, pesanan #ORD-{$order->id} telah dibatalkan dan sedang diproses refund.";
            $notifDetails['icon'] = '❌';
            $notifDetails['color'] = 'bg-red-100 text-red-600';
        }

        $order->update($data);

        // KIRIM NOTIFIKASI KE BUYER
        if ($notifDetails['title'] !== '') {
            $order->user->notify(new GeneralNotification($notifDetails));
        }

        // Logika Job Refund (Tetap sama)
        if ($request->status === 'refunded') {
            DeleteRefundedOrderJob::dispatch($order)->delay(now()->addSeconds(20));
            return redirect()->route('seller.approval.index')->with('success', 'Status Refunded. Notifikasi telah dikirim ke buyer.');
        }

        return redirect()->route('seller.approval.index')->with('success', 'Order diperbarui dan buyer telah dinotifikasi.');
    }

    public function deleteRefundedOrder(Order $order)
    {
        if ($order->status !== 'refunded') abort(403);

        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                if ($item->book) $item->book->increment('stock', (int) $item->qty);
            }
            $order->delete();
        });

        return redirect()->route('seller.approval.index')->with('success', 'Order dihapus permanen.');
    }
}
