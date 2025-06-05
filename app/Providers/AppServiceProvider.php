<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        // Use Bootstrap 5 pagination views
        Paginator::useBootstrapFive();
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register RouteServiceProvider explicitly (optional if already loaded by Laravel)
        $this->app->register(\App\Providers\RouteServiceProvider::class);
    }
}
