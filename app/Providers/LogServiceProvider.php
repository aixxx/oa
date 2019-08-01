<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\UserLogService;
use App\Services\OperateLogService;
use App\Services\FixedAssetLogService;
use App\Contracts\LogContract;

class LogServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->singleton('userlog', function () {
            return new UserLogService();
        });

        $this->app->singleton('operatelog', function () {
            return new OperateLogService();
        });

        $this->app->bind('App\Contracts\LogContract', function () {
            return new FixedAssetLogService();
        });


//        $this->app->bind('App\Contracts\LogContract',function(){
//            return new UserLogService();
//        });
    }
}
