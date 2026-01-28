<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Notifications\GeneralNotification;

class AdminController extends Controller
{
    public function listSellers()
    {
        $users = User::where('role', 'seller')->get();
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
            'no_rek' => 'nullable|string|min:10|max:20|unique:users,no_rek',
            'bank_name' => 'nullable|string|in:BCA,Mandiri,BNI,BRI',
        ]);

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
        ]);

        // $user = User::create([
        //     'nik' => $validated['nik'],
        //     'name' => $validated['name'],
        //     'email' => $validated['email'],
        //     'password' => Hash::make($validated['password']),
        //     'role' => 'seller',
        //     'address' => $validated['address'] ?? 'Address has not been entered',
        // ]);

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
            'no_rek' => 'nullable|string|min:10|max:20|unique:users,no_rek',
            'bank_name' => 'nullable|string|in:BCA,Mandiri,BNI,BRI',
        ]);

        $user->update([
            'nik' => $validated['nik'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'address' => $validated['address'] ?? 'Address has not been entered',
            'no_rek' => $validated['no_rek'] ?? 'Belum Buat Nomor Rekening',
            'bank_name' => $validated['bank_name'] ?? 'Belum Membuat Bank',
        ]);

        // NOTIFIKASI KE SELLER (Info Update)
        $user->notify(new GeneralNotification([
            'title' => 'Data Diperbarui',
            'message' => 'Data profil Anda telah diperbarui oleh Admin.',
            'icon' => '📝',
            'color' => 'bg-blue-100 text-blue-600',
            'url' => '#',
        ]));

        return redirect()->route('admin.sellers')->with('success', 'Seller updated successfully.');
    }

    public function deleteSeller($id)
    {
        $seller = User::findOrFail($id);
        if ($seller->role === 'seller') {
            $seller->delete();
            return redirect()->route('admin.sellers')->with('success', 'Seller deleted successfully.');
        }
        return redirect()->route('admin.sellers')->with('error', 'User is not a seller.');
    }


    public function listBuyers()
    {
        $users = User::where('role', 'buyer')->get();
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
            'no_rek' => 'nullable|string|min:10|max:20|unique:users,no_rek',
            'bank_name' => 'nullable|string|in:BCA,Mandiri,BNI,BRI',
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
            'no_rek' => 'nullable|string|min:10|max:20|unique:users,no_rek',
            'bank_name' => 'nullable|string|in:BCA,Mandiri,BNI,BRI',
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
        if ($buyer->role === 'buyer') {
            $buyer->delete();
            return redirect()->route('admin.buyers')->with('success', 'Buyer deleted successfully.');
        }
        return redirect()->route('admin.buyers')->with('error', 'User is not a buyer.');
    }
}
