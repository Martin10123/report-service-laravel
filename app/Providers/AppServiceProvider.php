<?php

namespace App\Providers;

use App\Models\Service;
use Inertia\Inertia;
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

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Limpiar servicio actual si se solicita explícitamente
        if (request()->boolean('clear_servicio')) {
            session()->forget('servicio_actual_id');
        }

        // Persistir servicio actual si viene en la URL
        if (request()->has('servicio_id')) {
            session(['servicio_actual_id' => request('servicio_id')]);
        }

        Inertia::share([
            'servicioActual' => function () {
                $id = session('servicio_actual_id');
                return $id ? Service::with('sede')->find($id) : null;
            },
        ]);
    }
}
