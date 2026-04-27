<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Forzamos HTTPS siempre para evitar errores de Mixed Content con Cloudflare
        \Illuminate\Support\Facades\URL::forceScheme('https');
    }
}
