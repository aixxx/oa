<?php

namespace App\Models\PAS\Warehouse;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PAS\Warehouse\VacationRule
 *
 * @mixin \Eloquent
 * @property int $id
 * @property int|null $unit 最小请假单位1、半天 2、一天 3、1小时
 * @property int|null $leave_type 请假类型1、工作日计算 2、自然日计算
 * @property int|null $is_balance 是否开启余额1、是 0否
 * @property int|null $balance_type 余额发放类型 1、每年自动固定发放天数 2、按照入职时间自动发放 3、加班时长自动计入余额
 * @property int|null $balance_value 余额发放数
 * @property int $expire_rule 有效期规则1、按自然年(1月1日 - 12月31日) 2、按入职日期12月
 * @property int|null $is_add_expire 是否可以延长时间
 * @property int|null $add_expire_value 延长时间
 * @property int|null $newer_start_type 新员工请假类型1、入职当天 2、转正
 * @property int|null $salary_percent 薪资比例
 * @property int $company_id 公司
 * @property int $cursor 操作人
 * @property int|null $status 状态
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\VacationRule whereAddExpireValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\VacationRule whereBalanceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\VacationRule whereBalanceValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\VacationRule whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\VacationRule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\VacationRule whereCursor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\VacationRule whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\VacationRule whereExpireRule($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\VacationRule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\VacationRule whereIsAddExpire($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\VacationRule whereIsBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\VacationRule whereLeaveType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\VacationRule whereNewerStartType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\VacationRule whereSalaryPercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\VacationRule whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\VacationRule whereUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\VacationRule whereUpdatedAt($value)
 */
class VacationRule extends Model
{
    //
    protected $table = 'vacation_rule';

    const UNIT_HAF_DAY = 1;
    const UNIT_DAY = 2;
    const UNIT_HOUR = 3;

    public static $_units = [
        self::UNIT_HAF_DAY => '半天',
        self::UNIT_DAY => '一天',
        self::UNIT_HOUR => '一小时',
    ];

    const LEAVE_TYPE_WORK =1;
    const LEAVE_TYPE_UN_WORK =2;
    public static $_leave_types = [
        self::LEAVE_TYPE_WORK => '工作日计算',
        self::LEAVE_TYPE_UN_WORK => '非工作日计算',
    ];

    const IS_BALANCE_YES = 1;
    const IS_BALANCE_NO = 0;
    public static $_is_balances = [
        self::IS_BALANCE_NO => '否',
        self::IS_BALANCE_YES => '是',
    ];

    const BALANCE_TYPE_SOLID = 1;
    const BALANCE_TYPE_EMPLOYER = 2;
    const BALANCE_TYPE_EXTRA = 3;
    public static $_balance_types = [
        self::BALANCE_TYPE_SOLID => '每年自动固定发放天数',
        self::BALANCE_TYPE_EMPLOYER => '按照入职时间自动发放',
        self::BALANCE_TYPE_EXTRA => '加班时长自动计入余额',
    ];

    const  EXPIRE_RULE_NORMAL = 1;
    const  EXPIRE_RULE_EMPLOYER = 2;
    public static $_expire_rules=[
        self::EXPIRE_RULE_NORMAL => '按自然年(1月1日 - 12月31日)',
        self::EXPIRE_RULE_EMPLOYER => '按入职日期12月',
    ];

    const NEWER_START_TYPE_CURRENT = 1;
    const NEWER_START_TYPE_OFFICIAL = 2;
    public static $_newer_start_types = [
        self::NEWER_START_TYPE_CURRENT => '入职当天',
        self::NEWER_START_TYPE_OFFICIAL => '转正',
    ];

    protected $fillable = [
        'unit',
        'leave_type',
        'is_balance',
        'balance_type',
        'balance_value',
        'expire_rule',
        'is_add_expire',
        'add_expire_value',
        'newer_start_type',
        'salary_percent',
        'company_id',
        'cursor',
        'status',
        ];
}
