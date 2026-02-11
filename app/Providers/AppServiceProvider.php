<?php

namespace App\Providers;

use App\Models\Service;
use Inertia\Inertia;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        // Forzar HTTPS en producción
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Inertia::share([
            'servicioActual' => function () {
                $id = session('servicio_actual_id');
                
                if (!$id) {
                    return null;
                }

                return Service::with('sede')->find($id);
            },
        ]);
    }
}
