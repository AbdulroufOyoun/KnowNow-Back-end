<?php

namespace App\Providers;

use App\Interfaces\PublicRepositoryInterface;
use App\Repositories\PublicRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PublicRepositoryInterface::class, PublicRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
