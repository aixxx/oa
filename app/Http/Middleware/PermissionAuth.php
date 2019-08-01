<?php

namespace App\Http\Middleware;

use Closure;
use Silber\Bouncer\Bouncer;
use Route;
use Session;
use Redirect;
use Auth;
use App\Models\DepartUser;
use App\Services\Common\MessageService;

class PermissionAuth
{
    /**
     * The Bouncer instance.
     *
     * @var \Silber\Bouncer\Bouncer
     */
    protected $bouncer;

    /**
     * Constructor.
     *
     * @param \Silber\Bouncer\Bouncer $bouncer
     */
    public function __construct(Bouncer $bouncer)
    {
        $this->bouncer = $bouncer;
    }

    /**
     * Set the proper Bouncer scope for the incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //如果用户没有登录就直接跳到登录界面
        if (!(Auth::check())) {
            //登录前访问地址写入session
            session(['pre_login_url' => url()->current()]);
            session::save();
            return Redirect::action('Auth\LoginController@login');
        }

        //$this->checkAbilities();
        return $next($request);
    }

    //后期优化，用户的权限存到缓存
    private function checkAbilities()
    {
        $route     = Route::currentRouteName();
        $abilities = auth()->user()->getAbilities()->toArray();
        if (empty($abilities)) {
            MessageService::generateResponse('您没有任何权限');
        }

        $actions  = [];
        $constant = config('constant');

        foreach ($abilities as $a) {
            $infoArray = explode('_', $a['name']);
            $count     = count($infoArray);

            if ($count < 2) {
                MessageService::generateResponse('权限错误，请联系管理员');
            }

            $prefix = $infoArray['0'];
            $last   = $infoArray['1'];
            if ($count > 2) {
                $last   = array_pop($infoArray);
                $prefix = join('_', $infoArray);
            }

            if (isset($constant['menu'][$a['root_code']]['children'][$a['level2_no']]['children'][$prefix]['abilities'][$last])) {
                $actions = array_merge(
                    $actions,
                    $constant['menu'][$a['root_code']]['children'][$a['level2_no']]['children'][$prefix]['abilities'][$last]
                );
            }
        }

        if (!in_array($route, $actions) || ('attendance.attendance.department_leader' == $route) && !DepartUser::checkUserIsLeader(Auth::id())) {
            MessageService::generateResponse('权限不足');
        }
    }
}
