<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Notifications\GeneralNotification;

class TransactionController extends Controller
{
    /**
     * LIST ITEM UNTUK SELLER
     */
    public function indexApproval()
    {
        $items = OrderItem::where('seller_id', Auth::id())
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
        abort_if(
            $item->seller_id !== Auth::id(),
            403,
            'Unauthorized'
        );

        $request->validate([
            'status' => 'required|in:pending,approved,shipping,selesai,refunded',
            'tracking_number' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('order_items', 'tracking_number')->ignore($item->id),
            ],
            'expedisi_name' => 'nullable|string|max:100',
        ]);

        DB::transaction(function () use ($request, $item) {
            $data = ['status' => $request->status];

            if ($request->status === 'approved') {
                $data['approved_at'] = now();
            }

            if ($request->status === 'shipping') {
                // Jika user tidak isi resi, kita generate otomatis
                if (empty($request->tracking_number)) {
                    // Loop untuk memastikan generate resi otomatis pun tidak duplikat
                    do {
                        $newResi = 'REG' . strtoupper(Str::random(10));
                    } while (OrderItem::where('tracking_number', $newResi)->exists());

                    $data['tracking_number'] = $newResi;
                } else {
                    $data['tracking_number'] = $request->tracking_number;
                }

                $data['expedisi_name'] = $request->expedisi_name ?? 'Internal Courier';
            }

            if ($request->status === 'refunded') {
                $data['refunded_at'] = now();
                if ($item->book) {
                    $item->book->increment('stock', (int) $item->qty);
                }
            }

            $item->update($data);
            $this->notifyBuyer($item);
        });

        return back()->with('success', 'Status berhasil diperbarui.');
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
            'selesai' => [
                'title' => 'Item Telah Sampai',
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
