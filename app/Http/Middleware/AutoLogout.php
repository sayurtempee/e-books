<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AutoLogout
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Ambil session aktivitas terakhir
            // Pastikan key-nya sama (sebelumnya Anda pakai 'last_activity' dan 'last_activity_at')
            $lastActivity = session('last_activity');

            if ($lastActivity) {
                // Hitung durasi tidak aktif (30 menit)
                if (now()->diffInMinutes($lastActivity) >= 30) {

                    // Update DB ke Offline sebelum logout
                    $user->update([
                        'isOnline' => false,
                    ]);

                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return redirect()->route('login')->withErrors([
                        'login' => 'Sesi berakhir karena Anda tidak aktif selama 30 menit.'
                    ]);
                }
            }

            // JIKA MASIH AKTIF:
            // 1. Update session untuk pengecekan berikutnya (Gunakan key yang konsisten)
            session(['last_activity' => now()]);

            // 2. Heartbeat: Update database agar status tetap online dan waktu terpantau
            // Hanya update jika sudah lewat 1 menit untuk menghemat beban database (opsional)
            if (!$user->last_activity_at || now()->diffInSeconds($user->last_activity_at) > 60) {
                $user->update([
                    'isOnline' => true,
                    'last_activity_at' => now(),
                ]);
            }
        }

        return $next($request);
    }
}
