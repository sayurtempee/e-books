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
        ], [
            'nik.required' => 'NIK is required',
            'nik.size' => 'NIK must be exactly 16 digits',
            'nik.unique' => 'This NIK is already registered',
            'name.required' => 'Name is required',
            'email.required' => 'Email is required',
            'email.email' => 'Please enter a valid email address',
            'email.unique' => 'This email is already registered',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 8 characters',
            'password.confirmed' => 'Password confirmation does not match',
            'address.string' => 'Address must be a valid string',
        ]);

        if ($validationData['password'] !== $request->input('password_confirmation')) {
            return back()->withErrors(['password_confirmation' => 'The password confirmation does not match.'])->withInput();
        }

        if (!preg_match('/^\d{16}$/', $validationData['nik'])) {
            return back()->withErrors(['nik' => 'NIK must be exactly 16 digits'])->withInput();
        }

        if (isset($validationData['address'])) {
            $validationData['address'] = $request->input('address');
        }

        // dd($validationData);

        User::create([
            'name' => $validationData['name'],
            'email' => $validationData['email'],
            'password' => Hash::make($validationData['password']),
            'role' => $validationData['role'],
            'nik' => $validationData['nik'],
            'address' => $validationData['address'],
        ]);

        $user->notify(new GeneralNotification([
            'title' => 'Akun Berhasil Dibuat',
            'message' => 'Selamat bergabung! Silakan lengkapi profil Anda untuk mulai bertransaksi.',
            'icon' => '🎊',
            'color' => 'bg-emerald-100 text-emerald-600',
            'url' => route('my-account'), // Jika ada route profil
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

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // 1. UPDATE LAST SEEN SAAT LOGIN
            // Ini memastikan status langsung "Online" begitu masuk dashboard
            $user->update([
                'last_seen' => now()
            ]);

            // 2. KIRIM NOTIFIKASI SAMBUTAN
            $user->notify(new GeneralNotification([
                'title' => 'Selamat Datang Kembali!',
                'message' => "Halo {$user->name}, senang melihat Anda kembali di Miimoys E-Books.",
                'icon' => '👋',
                'color' => 'bg-teal-100 text-teal-600',
                'url' => '#',
            ]));

            // 3. REDIRECT BERDASARKAN ROLE
            return match ($user->role) {
                'admin'  => redirect()->route('admin.dashboard')->with('success', 'Selamat datang, Admin!'),
                'seller' => redirect()->route('seller.dashboard')->with('success', 'Halo Seller, siap berjualan?'),
                'buyer'  => redirect()->route('buyer.dashboard')->with('success', 'Ayo cari buku favoritmu!'),
                default  => redirect()->route('dashboard')->with('success', 'Login berhasil'),
            };
        }

        return back()->withErrors([
            'email' => 'Email atau password salah',
        ]);
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
        $user = auth()->user();

        if ($user) {
            // Menghapus jejak waktu, status akan jadi "Offline" tanpa waktu di Blade
            $user->update([
                'last_seen' => null
            ]);
        }

        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Berhasil keluar.');
    }
}
