<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            if (Auth::check()) {
                // Mengambil 10 notifikasi terbaru (baik sudah dibaca maupun belum)
                $view->with('notifications', Auth::user()->notifications()->latest()->take(10)->get());
            } else {
                $view->with('notifications', collect());
            }
        });
    }
}
