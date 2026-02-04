<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\ServiceRepository;

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
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
