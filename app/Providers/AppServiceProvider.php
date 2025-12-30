<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Models\Engine;
use App\Observers\EngineObserver;

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
        if (env('APP_ENV') !== 'local') {
            \URL::forceScheme('https');
        }
        Paginator::useBootstrapFive();
        
        // Регистрируем observer для модели Engine
        Engine::observe(EngineObserver::class);
    }
}
