<?php
namespace App\Providers;

use App\Constant\CommonConstant;
use App\Services\Message\EmailMessageService;
use App\Services\Message\WechatMessageService;
use Illuminate\Support\ServiceProvider;

class MessageServiceProvider extends ServiceProvider
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
        $this->app->singleton(CommonConstant::MESSAGE_PUSH_TYPE_WECHAT, function () {
            return new WechatMessageService();
        });

        $this->app->singleton(CommonConstant::MESSAGE_PUSH_TYPE_EMAIL, function () {
            return new EmailMessageService();
        });
    }
}
