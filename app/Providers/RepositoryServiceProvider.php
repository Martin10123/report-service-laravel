<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\ServiceRepository;
use App\Repositories\PrimerConteoRepository;
use App\Repositories\ConteoA1Repository;
use App\Repositories\ConteoA2Repository;
use App\Repositories\ConteoA3Repository;
use App\Repositories\ConteoA4Repository;
use App\Repositories\ConteoDeSobresRepository;

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

        $this->app->singleton(ConteoA1Repository::class, function ($app) {
            return new ConteoA1Repository();
        });

        $this->app->singleton(ConteoA2Repository::class, function ($app) {
            return new ConteoA2Repository();
        });

        $this->app->singleton(ConteoA3Repository::class, function ($app) {
            return new ConteoA3Repository();
        });

        $this->app->singleton(ConteoA4Repository::class, function ($app) {
            return new ConteoA4Repository();
        });

        $this->app->singleton(ConteoDeSobresRepository::class, function ($app) {
            return new ConteoDeSobresRepository();
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
