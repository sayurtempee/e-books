<?php

use Illuminate\Foundation\Application;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\EnsureUserExists;
use App\Http\Middleware\UpdateLastSeen;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(\App\Http\Middleware\UpdateUserOnlineStatus::class);
        $middleware->web(append: [
            \Illuminate\Session\Middleware\AuthenticateSession::class,
            \App\Http\Middleware\AutoLogout::class,
        ]);

        $middleware->alias([
            'role' => RoleMiddleware::class,
            'user.exists' => EnsureUserExists::class,
            'last_seen' => UpdateLastSeen::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->respond(function ($response, $e, $request) {
            // Jika user "ditendang" karena sesi tidak valid (Authentication Exception)
            if ($response->getStatusCode() === 401 || ($e instanceof \Illuminate\Auth\AuthenticationException)) {
                return redirect()->route('login')->with('status', 'session_expired');
            }
            return $response;
        });
    })->create();
