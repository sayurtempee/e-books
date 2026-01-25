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

            $lastActivity = session('last_activity');

            if ($lastActivity) {
                $inactiveMinutes = Carbon::now()->diffInMinutes($lastActivity);

                if ($inactiveMinutes >= 30) {

                    // SET OFFLINE
                    Auth::user()->update([
                        'isOnline' => false,
                    ]);

                    Auth::logout();
                    session()->invalidate();
                    session()->regenerateToken();

                    return redirect()->route('login')
                        ->withErrors([
                            'login' => 'Anda otomatis logout karena tidak aktif selama 30 menit.'
                        ]);
                }
            }

            // Update waktu aktivitas terakhir
            session(['last_activity' => Carbon::now()]);
        }
        return $next($request);
    }
}
