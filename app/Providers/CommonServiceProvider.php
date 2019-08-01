<?php

namespace App\Providers;

use View;
use Illuminate\Support\ServiceProvider;

class CommonServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer(
            ['layouts.partials.sidebar', 'layouts.partials.toolbar'],
            'App\Http\ViewComposers\AdminComposer'
        );
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
