<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Notifications\GeneralNotification;

class AccountController extends Controller
{
    public function updateAccount(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'name'         => 'required|string|max:100',
            'email'        => 'required|email|unique:users,email,' . $user->id,
            'address'      => 'nullable|string|max:255',
            'no_rek'       => 'nullable|string|min:10|max:20|unique:users,no_rek,' . $user->id,
            'bank_name'    => 'nullable|string|in:BCA,Mandiri,BNI,BRI',
            'foto_profile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $photoPath = $user->foto_profile;

        if ($request->hasFile('foto_profile')) {
            // Hapus foto lama dari storage jika ada
            if ($user->foto_profile && Storage::disk('public')->exists($user->foto_profile)) {
                Storage::disk('public')->delete($user->foto_profile);
            }

            // Simpan foto baru
            $photoPath = $request->file('foto_profile')->store('profile_photos', 'public');
        }

        $user->update([
            'name'         => $validated['name'],
            'email'        => $validated['email'],
            'address'      => $validated['address'] ?? $user->address,
            'no_rek'       => $validated['no_rek'] ?? $user->no_rek,
            'bank_name'    => $validated['bank_name'] ?? $user->bank_name,
            'foto_profile' => $photoPath,
        ]);

        // --- LOGIKA NOTIFIKASI ---
        $user->notifications()->where('data->title', 'Profil Diperbarui')->delete();

        $user->notify(new GeneralNotification([
            'title'   => 'Profil Diperbarui',
            'message' => 'Data akun Anda telah berhasil diperbarui pada ' . now()->format('H:i d M Y'),
            'icon'    => '👤',
            'color'   => 'bg-indigo-100 text-indigo-600',
            'url'     => route($user->role . '.dashboard'),
        ]));

        return back()->with('success', 'Profil berhasil diperbarui');
    }

    /**
     * Fungsi Universal untuk Hapus Foto Profil
     */
    public function deletePhoto()
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->foto_profile) {
            // Hapus file fisik
            if (Storage::disk('public')->exists($user->foto_profile)) {
                Storage::disk('public')->delete($user->foto_profile);
            }

            // Update database
            $user->update(['foto_profile' => null]);

            return back()->with('success', 'Foto profil berhasil dihapus');
        }

        return back()->with('error', 'Tidak ada foto untuk dihapus');
    }
}
