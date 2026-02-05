<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\ServiceRepository;
use App\Repositories\PrimerConteoRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(ServiceRepository::class, function ($app) {
            return new ServiceRepository();
        });

        $this->app->singleton(PrimerConteoRepository::class, function ($app) {
            return new PrimerConteoRepository();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
