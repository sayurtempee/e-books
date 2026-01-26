<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function uploadPayment(Request $request, Order $order)
    {
        // 1. Validasi
        $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'seller_id' => 'required|exists:users,id'
        ]);

        try {
            // 2. Simpan File
            if ($request->hasFile('payment_proof')) {
                $file = $request->file('payment_proof');
                $filename = 'proof_' . $order->id . '_' . $request->seller_id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('payment_proofs', $filename, 'public');

                // 3. Update OrderItem milik seller terkait
                // Pastikan tabel order_items kamu punya kolom 'payment_proof' dan 'status'
                $order->items()->where('seller_id', $request->seller_id)->update([
                    'payment_proof' => $path,
                    'status' => 'approved'
                ]);

                return back()
                    ->with('checkout_success', true) // Agar modal tetap terbuka
                    ->with('order_id', $order->id)
                    ->with('success', 'Bukti transfer untuk penjual berhasil dikirim!');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengupload bukti: ' . $e->getMessage());
        }
    }
}
