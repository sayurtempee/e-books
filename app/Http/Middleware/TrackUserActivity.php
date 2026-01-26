<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class TrackUserActivity
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            // update DB
            $user->update([
                'last_activity_at' => now()
            ]);

            // cache realtime
            Cache::put(
                'user-is-online-' . $user->id,
                true,
                now()->addMinutes(2)
            );
        }

        return $next($request);
    }
}
