<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

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
        Route::macro('neo', function ($uri, $component) {
            return Route::get($uri, $component)
                ->middleware(['auth', 'admin'])
                ->name('neo.' . str_replace('/', '.', $uri));
        });
    }
}
