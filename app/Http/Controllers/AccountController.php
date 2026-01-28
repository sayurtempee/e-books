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
        $user = Auth::user();

        $validated = $request->validate([
            'name'  => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'address' => 'nullable|string|max:255',
        ]);

        // $user->update($validated);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'address' => $validated['address'] ?? 'Address has not been entered',
        ]);

        dd($user);

        // KIRIM NOTIFIKASI KE USER SENDIRI
        $user->notify(new GeneralNotification([
            'title' => 'Profil Diperbarui',
            'message' => 'Data akun Anda telah berhasil diperbarui pada ' . now()->format('H:i d M Y'),
            'icon' => '👤',
            'color' => 'bg-indigo-100 text-indigo-600',
            'url' => '#', // Tetap di halaman profil
        ]));

        return back()->with('success', 'Account berhasil diperbarui');
    }
}
