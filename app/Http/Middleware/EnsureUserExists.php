<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserExists
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            /**
             * User sudah dihapus oleh admin
             */
            if (!$user || !$user->exists) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('welcome')
                    ->with('error', 'Akun Anda sudah tidak tersedia.');
            }
        }

        return $next($request);
    }
}
