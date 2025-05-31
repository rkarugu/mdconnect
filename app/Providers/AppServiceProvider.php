<?php

namespace App\Providers;

use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\ServiceProvider;
use App\Http\Controllers\Admin\MedicalSpecialtyController;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('admin', function ($app) {
            return new AdminMiddleware();
        });
        $this->app->singleton(MedicalSpecialtyController::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
