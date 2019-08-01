<?php

namespace App\Providers;


use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        //Passport::tokensExpireIn(now()->addDays(15));
        //Passport::routes();

        /*Passport::tokensCan([
            'user_basic' => '获得你的公开信息（姓名、头像等）',
            'user_write' => '编辑、修改、禁用你的账号信息',
        ]);*/
    }
}
