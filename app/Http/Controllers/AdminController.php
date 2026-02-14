<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function listSellers()
    {
        $users = User::where('role', 'seller')
            ->withCount('books') // Menghitung baris di tabel books (Total Buku)
            ->withSum(['orderItems as items_sold' => function ($query) {
                $query->where('status', 'selesai'); // Hanya yang statusnya sudah selesai
            }], 'qty') // Menjumlahkan kolom 'qty' di tabel order_items (Item Terjual)
            ->get();

        return view('admin.seller.index', compact('users'));
    }

    public function createSeller(Request $request)
    {
        $validated = $request->validate([
            'nik' => 'required|string|size:16|unique:users,nik',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'address' => 'nullable|string',
            'no_rek' => 'nullable|string|unique:users,no_rek',
            'bank_name' => 'nullable|string|in:BCA,Mandiri,BNI,BRI',
            'foto_profile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto_profile')) {
            $fotoPath = $request->file('foto_profile')->store('foto_profile', 'public');
        }

        // dd($validated);

        $user = User::create([
            'nik' => $validated['nik'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'seller',
            'address' => $validated['address'] ?? 'Address has not been entered',
            'no_rek' => $validated['no_rek'] ?? 'Belum Buat Nomor Rekening',
            'bank_name' => $validated['bank_name'] ?? 'Belum Membuat Bank',
            'foto_profile' => $fotoPath,
        ]);

        // NOTIFIKASI KE SELLER BARU (Welcome Message)
        $user->notify(new GeneralNotification([
            'title' => 'Selamat Datang!',
            'message' => 'Akun Seller Anda telah berhasil dibuat oleh Admin.',
            'icon' => '🎉',
            'color' => 'bg-teal-100 text-teal-600',
            'url' => route('seller.dashboard'),
        ]));

        return redirect()->route('admin.sellers')->with('success', 'Seller created successfully.');
    }

    public function updateSeller(Request $request, $id)
    {
        $user = User::where('role', 'seller')->findOrFail($id);

        $validated = $request->validate([
            'nik' => 'required|string|size:16|unique:users,nik,' . $user->id,
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'address' => 'nullable|string',
            'no_rek' => 'nullable|string',
            'bank_name' => 'nullable|string|in:BCA,Mandiri,BNI,BRI',
            'foto_profile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle Upload File
        $fotoPath = $user->foto_profile;

        if ($request->hasFile('foto_profile')) {
            // Hapus foto lama jika ada
            if ($user->foto_profile && Storage::disk('public')->exists($user->foto_profile)) {
                Storage::disk('public')->delete($user->foto_profile);
            }
            // Simpan foto baru
            $fotoPath = $request->file('foto_profile')->store('foto_profile', 'public');
        }
        // dd($validated);

        $user->update([
            'nik' => $validated['nik'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'address' => $validated['address'] ?? 'Address has not been entered',
            'no_rek' => $validated['no_rek'] ?? 'Belum Buat Nomor Rekening',
            'bank_name' => $validated['bank_name'] ?? 'Belum Membuat Bank',
            'foto_profile' => $fotoPath,
        ]);

        // NOTIFIKASI KE SELLER (Info Update)
        $user->notify(new GeneralNotification([
            'title' => 'Data Diperbarui',
            'message' => 'Data profil Anda telah diperbarui oleh Admin.',
            'icon' => '📝',
            'color' => 'bg-blue-100 text-blue-600',
            'url' => route('admin.dashboard'),
        ]));

        return redirect()->route('admin.sellers')->with('success', 'Seller updated successfully.');
    }

    public function deleteSeller($id)
    {
        $seller = User::withCount('books')->findOrFail($id);
        if ($seller->role !== 'seller') {
            return redirect()->route('admin.sellers')->with('error', 'User is not a seller.');
        }

        if ($seller->books_count > 0) {
            return redirect()->route('admin.sellers')->with('error', 'Cannot delete seller with existing books.');
        }

        $pendingOrders = $seller->orderItems()->where('status', '!=', 'selesai')->count();
        if ($pendingOrders > 0) {
            return redirect()->route('admin.sellers')->with('error', 'Cannot delete seller with pending orders.');
        }

        $seller->delete();
        return redirect()->route('admin.sellers')->with('success', 'Seller deleted successfully.');
    }

    public function listBuyers()
    {
        $users = User::where('role', 'buyer')
            ->withCount(['boughtItems as active_items_count' => function ($query) {
                $query->whereNotIn('status', ['selesai', 'refunded', 'pending']);
            }])
            ->get();
        return view('admin.buyer.index', compact('users'));
    }

    public function createBuyer(Request $request)
    {
        $validated = $request->validate([
            'nik' => 'required|string|size:16|unique:users,nik',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'address' => 'nullable|string',
            'no_rek' => 'nullable|string|unique:users,no_rek',
            'bank_name' => 'nullable|string|in:BCA,Mandiri,BNI,BRI',
            'foto_profile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = User::create([
            'nik' => $validated['nik'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'buyer',
            'address' => $validated['address'] ?? 'Address has not been entered',
            'no_rek' => $validated['no_rek'] ?? 'Belum Buat Nomor Rekening',
            'bank_name' => $validated['bank_name'] ?? 'Belum Membuat Bank',
            'foto_profile' => $validated['foto_profile'] ?? null,
        ]);

        $user->notify(new GeneralNotification([
            'title' => 'Halo Buyer Baru!',
            'message' => 'Selamat bergabung di Miimoys E-Books. Yuk mulai belanja!',
            'icon' => '🛍️',
            'color' => 'bg-emerald-100 text-emerald-600',
            'url' => route('buyer.dashboard'),
        ]));

        return redirect()->route('admin.buyers')->with('success', 'Buyer created successfully.');
    }

    public function updateBuyer(Request $request, $id)
    {
        $user = User::where('role', 'buyer')->findOrFail($id);

        $validated = $request->validate([
            'nik' => 'required|string|size:16|unique:users,nik,' . $user->id,
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'address' => 'nullable|string',
            'no_rek' => 'nullable|string',
            'bank_name' => 'nullable|string|in:BCA,Mandiri,BNI,BRI',
            'foto_profile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user->update([
            'nik' => $validated['nik'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'address' => $validated['address'] ?? 'Address has not been entered',
            'no_rek' => $validated['no_rek'] ?? 'Belum Buat Nomor Rekening',
            'bank_name' => $validated['bank_name'] ?? 'Belum Membuat Bank',
        ]);

        return redirect()->route('admin.buyers')->with('success', 'Buyer updated successfully.');
    }

    public function deleteBuyer($id)
    {
        $buyer = User::findOrFail($id);

        // Cek yang login harus buyer
        if ($buyer->role !== 'buyer') {
            return redirect()->route('admin.buyers')->with('error', 'User is not a buyer.');
        }

        $activeItemsCount = $buyer->boughtItems()
            ->whereNotIn('status', ['selesai', 'refunded', 'pending'])
            ->count();

        if ($activeItemsCount > 0) {
            return redirect()->route('admin.buyers')->with('error', "Gagal! Buyer memiliki $activeItemsCount transaksi aktif.");
        }

        DB::table('conversations')->where('sender_id', $id)->orWhere('receiver_id', $id)->delete();

        $buyer->delete();
        return redirect()->route('admin.buyers')->with('success', 'Buyer deleted successfully.');
    }

    // Seller melihat Seller
    public function sellerToSeller()
    {
        $sellers = User::where('role', 'seller')->get();
        return view('seller.seller_views', compact('sellers'));
    }
}
