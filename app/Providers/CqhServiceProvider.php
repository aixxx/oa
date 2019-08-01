<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class CqhServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('layouts.partials.toolbar', function ($view) {
            $view->with('zhang', 'hahahahahaha');
        });
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
