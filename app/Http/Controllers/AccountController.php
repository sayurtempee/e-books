<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\GeneralNotification;

class AccountController extends Controller
{
    public function updateAccount(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email|unique:users,email,' . $user->id,
            'address' => 'nullable|string|max:255',
            'no_rek'  => 'nullable|string|min:10|max:20|unique:users,no_rek,' . $user->id, // Tambahkan ignore ID
            'bank_name' => 'nullable|string|in:BCA,Mandiri,BNI,BRI',
        ]);

        $user->update([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'address'   => $validated['address'] ?? 'Address has not been entered',
            'no_rek'    => $validated['no_rek'] ?? $user->no_rek,
            'bank_name' => $validated['bank_name'] ?? $user->bank_name,
        ]);

        // --- LOGIKA NOTIFIKASI SINGLE ---

        // 1. Hapus notifikasi lama dengan judul yang sama agar tidak menumpuk
        $user->notifications()
            ->where('data->title', 'Profil Diperbarui')
            ->delete();

        // 2. Kirim notifikasi baru
        $user->notify(new GeneralNotification([
            'title'   => 'Profil Diperbarui',
            'message' => 'Data akun Anda telah berhasil diperbarui pada ' . now()->format('H:i d M Y'),
            'icon'    => '👤',
            'color'   => 'bg-indigo-100 text-indigo-600',
            'url'     => route($user->role . '.dashboard'), // Arahkan ke route profil masing-masing role
        ]));

        return back()->with('success', 'Account berhasil diperbarui');
    }
}
