<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportRuleUser extends Model
{

    /*
     * 获取规则选中的员工
     * */
    public static function getRuleSelectUser($user){
//        return self::whereIn('report_rule_users.id', $user)
//            ->leftJoin('users', 'users.id', '=', 'report_rule_users.user_id')->select('users.chinese_name', 'users.position', 'users.avatar')->get();

        return User::whereIn('id', $user)->pluck('chinese_name');
    }
}
