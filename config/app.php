<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'name' => env('APP_NAME', 'OA'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services your application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'domain_env'             => env('APP_DOMAIN_ENV', 'production'),
    'super_admin_check_code' => env('SUPER_ADMIN_CHECK_CODE', 5),
	'rpc_domain' => env('RPC_DOMAIN', 'erpproduct.yuns.net'),
//	'rpc_local_domain' => env('RPC_LOCAL_DOMAIN', 'http://erpcaiwu.org'),
//	'rpc_cus_local_domain' => env('RPC_CUS_LOCAL_DOMAIN', 'http://customer.org/'),
//	'rpc_mission_local_domain' => env('RPC_MISSION_LOCAL_DOMAIN', 'http://project.org/'),
//    'rpc_local_domain' => env('RPC_LOCAL_DOMAIN', 'http://erpcaiwu.liyunnetwork.com/'),
//    'rpc_cus_local_domain' => env('RPC_CUS_LOCAL_DOMAIN', 'http://customer.liyunnetwork.com/'),
//    'rpc_mission_local_domain' => env('RPC_MISSION_LOCAL_DOMAIN', 'http://mission.liyunnetwork.com/'),
	'rpc_local_domain' => env('RPC_LOCAL_DOMAIN', 'http://erpcaiwuproduct.yuns.net'),
    'rpc_cus_local_domain' => env('RPC_CUS_LOCAL_DOMAIN', 'http://customerproduct.yuns.net'),
	'rpc_mission_local_domain' => env('RPC_MISSION_LOCAL_DOMAIN', 'http://missionproduct.yuns.net'),

    'debug' => env('APP_DEBUG', true),

    'use_oauth' => env('KUAINIU_USE_OAUTH', false) && env('KUAINIU_DOMAIN', false) && isset($_SERVER['HTTP_HOST'])
        && explode("//", env('KUAINIU_DOMAIN', false))[1] !== $_SERVER['HTTP_HOST'],
    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),


    'cdn_domain' => env('CDN_DOMAIN', null),
    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => 'Asia/Shanghai',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => 'zh_cn',

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => [

        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,
		Tymon\JWTAuth\Providers\LaravelServiceProvider::class,

        //Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class,

        /*
         * Package Service Providers...
         */
        Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class,
        Barryvdh\Debugbar\ServiceProvider::class,
        Overtrue\LaravelWeChat\ServiceProvider::class,
		Mrgoon\AliSms\ServiceProvider::class,

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,

        //员工日志服务
        App\Providers\LogServiceProvider::class,

        //汉子拼音
        Overtrue\LaravelPinyin\ServiceProvider::class,
        Freyo\Flysystem\QcloudCOSv5\ServiceProvider::class,

        //数据库生成seed
        Orangehill\Iseed\IseedServiceProvider::class,
        //sentry
        Sentry\SentryLaravel\SentryLaravelServiceProvider::class,
        //公用数据
        App\Providers\CommonServiceProvider::class,
        App\Providers\MessageServiceProvider::class,
		
		Rap2hpoutre\LaravelLogViewer\LaravelLogViewerServiceProvider::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => [

        'App'          => Illuminate\Support\Facades\App::class,
        'Artisan'      => Illuminate\Support\Facades\Artisan::class,
        'Auth'         => Illuminate\Support\Facades\Auth::class,
        'Blade'        => Illuminate\Support\Facades\Blade::class,
        'Broadcast'    => Illuminate\Support\Facades\Broadcast::class,
        'Bus'          => Illuminate\Support\Facades\Bus::class,
        'Cache'        => Illuminate\Support\Facades\Cache::class,
        'Config'       => Illuminate\Support\Facades\Config::class,
        'Cookie'       => Illuminate\Support\Facades\Cookie::class,
        'Crypt'        => Illuminate\Support\Facades\Crypt::class,
        'Debugbar'     => Barryvdh\Debugbar\Facade::class,
        'DB'           => Illuminate\Support\Facades\DB::class,
        'Eloquent'     => Illuminate\Database\Eloquent\Model::class,
        'Event'        => Illuminate\Support\Facades\Event::class,
        'File'         => Illuminate\Support\Facades\File::class,
        'Gate'         => Illuminate\Support\Facades\Gate::class,
        'Hash'         => Illuminate\Support\Facades\Hash::class,
        'Lang'         => Illuminate\Support\Facades\Lang::class,
        'Log'          => Illuminate\Support\Facades\Log::class,
        'Mail'         => Illuminate\Support\Facades\Mail::class,
        'Notification' => Illuminate\Support\Facades\Notification::class,
        'Password'     => Illuminate\Support\Facades\Password::class,
        'Queue'        => Illuminate\Support\Facades\Queue::class,
        'Redirect'     => Illuminate\Support\Facades\Redirect::class,
        'Redis'        => Illuminate\Support\Facades\Redis::class,
        'Request'      => Illuminate\Support\Facades\Request::class,
        'Response'     => Illuminate\Support\Facades\Response::class,
        'Route'        => Illuminate\Support\Facades\Route::class,
        'Schema'       => Illuminate\Support\Facades\Schema::class,
        'Session'      => Illuminate\Support\Facades\Session::class,
        'Storage'      => Illuminate\Support\Facades\Storage::class,
        'URL'          => Illuminate\Support\Facades\URL::class,
        'Validator'    => Illuminate\Support\Facades\Validator::class,
        'View'         => Illuminate\Support\Facades\View::class,
        'WeChat'       => Overtrue\LaravelWeChat\Facade::class,
        'Pinyin'       => Overtrue\LaravelPinyin\Facades\Pinyin::class,
        'Input'        => Illuminate\Support\Facades\Input::class,
        'Sentry'       => Sentry\SentryLaravel\SentryFacade::class,
		'AliSms'=>Mrgoon\AliSms\ServiceProvider::class,
		'JWTAuth' => Tymon\JWTAuth\Facades\JWTAuth::class
    ],


    /*
     * 客户端请求地址
     * */
    'customer_url' => 'http://customer.liyunnetwork.com/hprose/customer/start',
];
