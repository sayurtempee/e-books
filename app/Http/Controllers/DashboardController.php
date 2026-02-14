<?php

namespace App\Http\Controllers;

use App\Models\Category;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function admin()
    {
        return view('admin.dashboard', ['title' => 'Admin Dashboard']);
    }

    public function seller()
    {
        $categories = Category::all();
        return view('seller.dashboard', ['title' => 'Seller Dashboard'], compact('categories'));
    }

    public function buyer()
    {
        return view('buyer.dashboard', ['title' => 'Buyer Dashboard']);
    }

    public function deletePhoto()
    {
        // Auth::user() akan mengambil data siapa pun yang sedang login (Admin/Seller/Buyer)
        $user = Auth::user();

        if ($user->foto_profile) {
            // Hapus file dari folder storage/app/public
            if (Storage::disk('public')->exists($user->foto_profile)) {
                Storage::disk('public')->delete($user->foto_profile);
            }

            // Update database
            $user->update([
                'foto_profile' => null
            ]);

            return back()->with('success', 'Foto profil berhasil dihapus.');
        }

        return back()->with('error', 'Tidak ada foto untuk dihapus.');
    }
}
