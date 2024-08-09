<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Clinic;

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
         // Using a closure
         View::composer('layouts.sidebar', function ($view) {
            $clinic = Clinic::first(); // Assuming you only have one clinic record
            $view->with('clinic', $clinic);
        });

        View::composer('auth.login', function ($view) {
            $clinic = Clinic::first(); // Assuming you only have one clinic record
            $view->with('clinic', $clinic);
        });
    }
}
