<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/30
 * Time: 13:30
 */

namespace App\Http\Middleware;

use App\Models\Power\Routes;
use App\Services\Common\MessageService;
use Closure;
use Auth;
use App\Models\Power\RolesUsers;
use Cache;

class TotalMiddleware
{
    public function __construct()
    {

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
        $this->checkAbilities();
        return $next($request);
    }

    //用户的权限存到缓存
    private function checkAbilities()
    {
        $route = request()->route()->getName();
        $getRoutes = Routes::where('path', $route)->first();
        //判断是否有缓存
        $keys = Auth::id() . "_action";
        $cacheRoutes = Cache::get($keys);
        if ($getRoutes) {
            if ($cacheRoutes) {
                if (!in_array($route, $cacheRoutes)) {
                    MessageService::generateResponse('权限不足');
                }
            }
            //根据user获取角色id
            $apiRoutes = RolesUsers::join('api_routes_roles', 'api_roles_users.role_id', '=', 'api_routes_roles.role_id')
                ->join('api_vue_action', 'api_routes_roles.action_id', '=', 'api_vue_action.id')
                ->join('api_vue_routes', 'api_vue_routes.action_id', '=', 'api_vue_action.id')
                ->join('api_routes', 'api_vue_routes.route_id', '=', 'api_routes.id')
                ->where(['api_roles_users.user_id' => Auth::id()])
                ->whereNull('api_routes.deleted_at')
                ->select(['api_routes.path'])->distinct('api_routes.path')->pluck('api_routes.path');
            if ($apiRoutes) {
                $routes = $apiRoutes->toArray();
                if (!in_array($route, $routes)) {
                    MessageService::generateResponse('权限不足');
                }
                //存缓存
                Cache::put($keys, $routes, 10);
            }
        }
    }
}