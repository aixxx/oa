<?php
namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use DevFixException;
use UserFixException;
/**
 * Created by PhpStorm.
 * User: aike
 * Date: 2018/7/27
 * Time: 下午2:24
 */

class AuthUserShadowService
{
    const SHADOW_USER_ID_KEY_PRIFIX = 'grant_user_';

    /**
     * 获取影子用户的用户id
     * @return \Illuminate\Session\SessionManager|\Illuminate\Session\Store|mixed
     * @throws \Exception
     */
    public function id()
    {
        $originId = Auth::id(); // 原始登录用户id
        if (empty($originId)) {
            throw new DevFixException('auth ID 为空,无效的id');
        }

        $shadowId = session(self::getShadowUserIdKey());
        if (empty($shadowId)) {
            $shadowId = $originId; // 没有影子授权,则使用真实登录用户id
        }
        return $shadowId;
    }

    /**
     * 获取影子用户的用户信息
     */
    public function user()
    {
        $shadowUserId = self::id();
        return User::findById($shadowUserId);
    }

    public static function getShadowUserIdKey()
    {
        return self::SHADOW_USER_ID_KEY_PRIFIX.Auth::id();
    }
}
