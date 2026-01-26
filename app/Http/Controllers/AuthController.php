<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Notifications\GeneralNotification;

class AuthController extends Controller
{
    public function showFormRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validationData = $request->validate([
            'nik' => 'required|string|size:16|unique:users,nik',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:admin,seller,buyer',
            'address' => 'nullable|string',
            'no_rek' => 'nullable|string|min:10|max:20|unique:users,no_rek',
            'bank_name' => 'nullable|string|in:BCA,Mandiri,BNI,BRI',
        ], [
            'nik.required' => 'NIK wajib diisi',
            'nik.size' => 'NIK harus tepat 16 digit',
            'no_rek.unique' => 'Nomor rekening ini sudah terdaftar',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        // Simpan User Baru
        $user = User::create([
            'name' => $validationData['name'],
            'email' => $validationData['email'],
            'password' => Hash::make($validationData['password']),
            'role' => $validationData['role'],
            'nik' => $validationData['nik'],
            'address' => $validationData['address'] ?? null,
            'no_rek' => $validationData['no_rek'] ?? null,
            'bank_name' => $validationData['bank_name'] ?? null,
            'isOnline' => false, // Default awal saat register
        ]);

        // Notifikasi pendaftaran
        $user->notify(new GeneralNotification([
            'title' => 'Akun Berhasil Dibuat',
            'message' => 'Selamat bergabung! Silakan login untuk mulai bertransaksi.',
            'icon' => '🎊',
            'color' => 'bg-emerald-100 text-emerald-600',
            'url' => route('login'),
        ]));

        return redirect()->route('login')->with('success', 'Registration successful! Please login.');
    }

    public function loginPage()
    {
        return view('auth.login');
    }

    public function loginSubmit(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {

            // LOGIKA SINGLE LOGIN
            // Cek jika isOnline true DAN aktivitas terakhir masih di bawah 30 menit
            $isReallyOnline = $user->isOnline &&
                $user->last_activity_at &&
                $user->last_activity_at->diffInMinutes(now()) < 30;

            if ($isReallyOnline) {
                return back()->withErrors([
                    'login' => 'Akun ini sedang aktif di perangkat lain. Silakan logout terlebih dahulu.'
                ]);
            }

            // Jalankan Login jika lolos pengecekan
            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();

                $user = Auth::user();
                $user->update([
                    'isOnline' => true,
                    'last_activity_at' => now(),
                ]);

                // Set session awal agar middleware langsung mengenalinya
                session(['last_activity' => now()]);

                // Notifikasi (Opsional)
                $user->notify(new GeneralNotification([
                    'title' => 'Selamat Datang!',
                    'message' => "Halo {$user->name}, Anda berhasil masuk.",
                    'icon' => '👋',
                    'color' => 'bg-teal-100 text-teal-600',
                    'url' => '#',
                ]));

                return match ($user->role) {
                    'admin'  => redirect()->route('admin.dashboard'),
                    'seller' => redirect()->route('seller.dashboard'),
                    'buyer'  => redirect()->route('buyer.dashboard'),
                    default  => redirect()->route('dashboard'),
                };
            }
        }

        return back()->withErrors(['email' => 'Email atau password salah']);
    }

    public function showForgotPassword()
    {
        return view('auth.forgot');
    }

    public function forgotPasswordSubmit(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', 'Link reset password telah dikirim ke email.')
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetForm(Request $request, $token)
    {
        return view('auth.reset', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function resetPasswordUpdate(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->update([
                    'password' => Hash::make($password),
                ]);

                // KIRIM NOTIFIKASI KEAMANAN
                $user->notify(new GeneralNotification([
                    'title' => 'Password Berhasil Direset',
                    'message' => 'Keamanan Akun: Password Anda baru saja diperbarui melalui fitur reset password.',
                    'icon' => '🔐',
                    'color' => 'bg-red-100 text-red-600',
                    'url' => '#',
                ]));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login.page')->with('success', 'Password berhasil direset.')
            : back()->withErrors(['email' => __($status)]);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $user->update([
                'isOnline' => false,
                'last_activity_at' => null,
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
