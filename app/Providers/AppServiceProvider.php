<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use DB;
use Log;
use Socialite;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Workflow\Flow;
use App\Models\Workflow\Entry;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        
        Schema::defaultStringLength(191);
         //驱动
       app('Dingo\Api\Auth\Auth')->extend('jwt', function ($app) {
           return new \Dingo\Api\Auth\Provider\JWT($app['Tymon\JWTAuth\JWTAuth']);
        });



        Collection::macro('paginate', function ($perPage, $total = null, $page = null, $pageName = 'page') {
            $page = $page ?: LengthAwarePaginator::resolveCurrentPage($pageName);
            return new LengthAwarePaginator(
                $this->forPage($page, $perPage),
                $total ?: $this->count(),
                $perPage,
                $page,
                [
                    'path'     => LengthAwarePaginator::resolveCurrentPath(),
                    'pageName' => $pageName,
                ]
            );
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (config('app.debug')) {
            DB::connection()->enableQueryLog();
            DB::listen(function ($query) {
                Log::info($query->sql . ';(' . $query->time . 'ms)', $query->bindings);
            });
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
            $this->app->register(\Orangehill\Iseed\IseedServiceProvider::class);
            //$this->app->register(\Way\Generators\GeneratorsServiceProvider::class);
            //$this->app->register(\Xethron\MigrationsGenerator\MigrationsGeneratorServiceProvider::class);
            //数据库生成seed
        }
    }
}
