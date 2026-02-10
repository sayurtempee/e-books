<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Notifications\GeneralNotification;

class TransactionController extends Controller
{
    public function indexApproval()
    {
        // Ambil data dan kelompokkan berdasarkan order_id
        $groupedItems = OrderItem::where('seller_id', Auth::id())
            ->where('status', '!=', 'refunded')
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
            'status' => 'required|in:pending,approved,shipping,refunded,selesai',
            'expedisi_name' => 'required_if:status,shipping',
            'tracking_number' => [
                'nullable',
                'required_if:status,shipping',
                function ($attribute, $value, $fail) use ($orderId) {
                    if (empty($value)) return;

                    // Cek apakah resi sudah dipakai oleh ORDER lain atau SELLER lain
                    $isUsed = OrderItem::where('tracking_number', $value)
                        ->where(function ($q) use ($orderId) {
                            $q->where('order_id', '!=', $orderId)
                                ->orWhere('seller_id', '!=', Auth::id());
                        })
                        ->exists();

                    if ($isUsed) {
                        $fail('Nomor resi ini sudah digunakan untuk pengiriman lain.');
                    }
                },
            ],
        ]);

        DB::transaction(function () use ($request, $items) {
            $status = $request->status;
            $trackingNumber = $request->tracking_number;

            // Logika Generate Resi Otomatis jika input kosong saat shipping
            if ($status === 'shipping' && empty($trackingNumber)) {
                do {
                    $trackingNumber = 'REG' . strtoupper(Str::random(10));
                } while (OrderItem::where('tracking_number', $trackingNumber)->exists());
            }

            // 3. Eksekusi Update
            foreach ($items as $item) {
                $data = ['status' => $status];

                if ($status === 'approved') {
                    $data['approved_at'] = now();
                }

                if ($status === 'shipping') {
                    $data['tracking_number'] = $trackingNumber;
                    $data['expedisi_name'] = $request->expedisi_name ?? 'Internal Courier';
                }

                if ($status === 'refunded') {
                    $data['refunded_at'] = now();
                    if ($item->book) {
                        $item->book->increment('stock', (int) $item->qty);
                    }
                }

                // Gunakan update pada instance model
                $item->update($data);
            }

            $this->notifyBuyer($items->first());
        });

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }

    private function notifyBuyer(OrderItem $item)
    {
        if (!$item->book || !$item->order) return;

        $map = [
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
