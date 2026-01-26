<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Notifications\GeneralNotification;

class TransactionController extends Controller
{
    /**
     * LIST ITEM UNTUK SELLER
     */
    public function indexApproval()
    {
        $items = OrderItem::where('seller_id', auth()->id())
            ->where('status', '!=', 'refunded')
            ->with([
            'order.user',
            'book',
            ])
            ->latest()
            ->get();

        return view('seller.approval.index', compact('items'));
    }

    /**
     * UPDATE STATUS ITEM (APPROVE / SHIPPING / REFUND)
     */
    public function updateApproval(Request $request, OrderItem $item)
    {
        // 🔐 VALIDASI KEPEMILIKAN (SUMBER KEBENARAN = seller_id)
        abort_if(
            $item->seller_id !== auth()->id(),
            403,
            'Unauthorized'
        );

        $request->validate([
            'status' => 'required|in:pending,approved,shipping,refunded',
            'tracking_number' => 'nullable|string|max:100|unique:order_items,tracking_number,' . $item->id,
        ]);

        DB::transaction(function () use ($request, $item) {

            $data = [
                'status' => $request->status,
            ];

            // ================= APPROVED =================
            if ($request->status === 'approved') {
                $data['approved_at'] = now();
                $data['tracking_number'] = null;
            }

            // ================= SHIPPING =================
            if ($request->status === 'shipping') {
                $data['tracking_number'] =
                    $request->tracking_number
                    ?? 'JNE' . strtoupper(Str::random(10));
            }

            // ================= REFUNDED =================
            if ($request->status === 'refunded') {
                $data['refunded_at'] = now();

                // ⬆️ KEMBALIKAN STOK (AMAN)
                if ($item->book) {
                    $item->book->increment('stock', (int) $item->qty);
                }
            }

            $item->update($data);

            // 🔔 NOTIFIKASI BUYER (PER ITEM)
            $this->notifyBuyer($item);
        });

        return back()->with('success', 'Status item berhasil diperbarui.');
    }

    /**
     * NOTIFIKASI BUYER
     */
    private function notifyBuyer(OrderItem $item)
    {
        if (!$item->book || !$item->order) return;

        $map = [
            'approved' => [
                'title' => 'Item Disetujui',
                'message' => "Item \"{$item->book->title}\" sedang diproses oleh seller.",
                'icon' => '✅',
                'color' => 'bg-teal-100 text-teal-600',
            ],
            'shipping' => [
                'title' => 'Item Dikirim',
                'message' => "Item \"{$item->book->title}\" dikirim. Resi: {$item->tracking_number}",
                'icon' => '🚚',
                'color' => 'bg-blue-100 text-blue-600',
            ],
            'refunded' => [
                'title' => 'Item Direfund',
                'message' => "Item \"{$item->book->title}\" dibatalkan & refund diproses.",
                'icon' => '❌',
                'color' => 'bg-red-100 text-red-600',
            ],
        ];

        if (!isset($map[$item->status])) return;

        $item->order->user->notify(
            new GeneralNotification([
                ...$map[$item->status],
                'url' => route('buyer.orders.index'),
            ])
        );
    }
}
