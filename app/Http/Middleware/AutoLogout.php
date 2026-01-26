<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AutoLogout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
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
            // 1. Update session untuk pengecekan berikutnya
            session(['last_activity_at' => now()]);

            // 2. Update database (Heartbeat) agar loginSubmit tahu user ini masih ada
            $user->update([
                'isOnline' => true,
                'last_activity_at' => now(),
            ]);
        }
        return $next($request);
    }
}
