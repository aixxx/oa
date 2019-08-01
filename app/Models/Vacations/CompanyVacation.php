<?php

namespace App\Models\Vacations;

use Illuminate\Database\Eloquent\Model;



/**
 * App\Models\Vacations\Vacation
 *
 * @mixin \Eloquent
 * @property int $id
 * @property int $company_id 公司id
 * @property string $vacation_name 假期名
 * @property int|null $cost_unit_type 最小请假单位1、按1小时2、按半天3、按一天4、一次请完
 * @property int|null $leave_type 请假时长方式1、按工作日计算请假时长2、按自然日计算请假时长
 * @property int|null $is_balance 是否启用余额1、开0、关
 * @property int|null $balance_type 余额发放形式1、每年自动固定发放天数2、按照入职时间自动发放3、加班时长自动计入余额
 * @property int|null $per_count 每人发放天数
 * @property int|null $expire_time 规则有效期1、按自然年(1月1日 - 12月31日)2、按入职日期12月
 * @property int|null $is_add_expire 是否支持延长有效期1、是0、否
 * @property int|null $add_time 可以延长的天数
 * @property int|null $leave_start_type 新员工何时可以请假1、入职当天2、转正
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class CompanyVacation extends Model
{
    const LEAVE_START_TYPE_ENTRY_DAY = 1;
    const LEAVE_START_TYPE_POSITIVE = 2;
    public static  $_leave_start_type =[
        self::LEAVE_START_TYPE_ENTRY_DAY => '入职当天',
        self::LEAVE_START_TYPE_POSITIVE => '转正',
    ];

    const IS_ADD_EXPIRE_ON =1;
    const IS_ADD_EXPIRE_OFF =0;


    const EXPIRE_TIME_NORM_YEAR = 1;
    const EXPIRE_TIME_ENTRY_YEAR = 2;
    public static $_expire_time_type = [
        self::EXPIRE_TIME_NORM_YEAR => '按自然年(1月1日 - 12月31日)',
        self::EXPIRE_TIME_ENTRY_YEAR => '按入职日期12月',
    ];

    const BALANCE_TYPE_SOLID_DAYS = 1;
    const BALANCE_TYPE_ENTRY_TIME = 2;
    const BALANCE_TYPE_WORK_ADD = 3;
    public static $_balance_type = [
        self::BALANCE_TYPE_SOLID_DAYS => '每年自动固定发放天数',
        self::BALANCE_TYPE_ENTRY_TIME => '按照入职时间自动发放',
        self::BALANCE_TYPE_WORK_ADD => '加班时长自动计入余额',
    ];

    const IS_BALANCE_ON = 1;
    const IS_BALANCE_Off = 0;

    const LEAVE_TYPE_WORK_DAY = 1;
    const LEAVE_TYPE_NORM_DAY = 2;
    public static $_leave_type = [
        self::LEAVE_TYPE_WORK_DAY => '按工作日计算请假时长',
        self::LEAVE_TYPE_NORM_DAY => '按自然日计算请假时长',
    ];

    const COST_UNIT_TYPE_HOUR = 1;
    const COST_UNIT_TYPE_HUF_DAY = 2;
    const COST_UNIT_TYPE_DAY = 3;
    const COST_UNIT_TYPE_ONCE = 4;
    public static $_cost_unit_type = [
        self::COST_UNIT_TYPE_HOUR => '按1小时',
        self::COST_UNIT_TYPE_HUF_DAY => '按半天',
        self::COST_UNIT_TYPE_DAY => '按一天',
        self::COST_UNIT_TYPE_ONCE => '一次请完',
    ];

    //
    protected $table = 'company_vacation';

    protected $fillable = [
        'company_id',
        'vacation_name',
        'cost_unit_type',
        'leave_type',
        'is_balance',
        'balance_type',
        'per_count',
        'expire_time',
        'is_add_expire',
        'add_time',
        'leave_start_type',
        'discount_salary',
    ];

}
