<?php
/**
 * Created by PhpStorm.
 * User: lizhennan
 * Date: 2018/8/1
 * Time: 上午11:03
 */

namespace App\Models\Attendance;


use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AttendanceVacationChange
 *
 * @mixin \Eloquent
 * @property int $change_id
 * @property int|null $change_user_id 被修改人id
 * @property string $change_type 调整假日类型 1:法定年假 2：公司福利年假 3：全薪假 4：调休
 * @property string $change_before_amount 调整前假期余额
 * @property string $change_after_amount 调整后假期余额
 * @property string $change_amount 调整的数量
 * @property string $change_remark 调整原因备注
 * @property string $change_unit 节假日结算单位(hour/day)
 * @property \Carbon\Carbon|null $created_at 创建时间
 * @property \Carbon\Carbon|null $updated_at 修改时间
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AttendanceVacationChange whereChangeAfterAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AttendanceVacationChange whereChangeAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AttendanceVacationChange whereChangeBeforeAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AttendanceVacationChange whereChangeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AttendanceVacationChange whereChangeRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AttendanceVacationChange whereChangeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AttendanceVacationChange whereChangeUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AttendanceVacationChange whereChangeUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AttendanceVacationChange whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AttendanceVacationChange whereUpdatedAt($value)
 * @property string $change_entry_id 流程编号
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceVacationChange whereChangeEntryId($value)
 * @property string|null $update_user_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceVacationChange whereUpdateUserId($value)
 */
class AttendanceVacationChange extends Model
{
    protected $primaryKey = 'change_id';
    //调整假日类型
    const CHANGE_TYPE_ANNUAL                  = '1'; //法定年假
    const CHANGE_TYPE_COMPANY_BENEFITS        = '2'; //公司福利年假
    const CHANGE_TYPE_FULL_PAY_SICK           = '3'; //全薪病假
    const CHANGE_TYPE_EXTRA_DAY_OFF           = '4'; //调休
    const CHANGE_TYPE_ACTUAL_ANNUAL           = '实际发放法定年假'; //实际发放法定年假
    const CHANGE_TYPE_ACTUAL_COMPANY_BENEFITS = '实际发放福利年假'; //实际发放福利年假
    //调整假日类型（字符,用于流程类型判断）
    const CHANGE_TYPE_ANNUAL_STR           = '法定年假';
    const CHANGE_TYPE_COMPANY_BENEFITS_STR = '公司福利年假';
    const CHANGE_TYPE_FULL_PAY_SICK_STR    = '全薪病假';
    const CHANGE_TYPE_EXTRA_DAY_OFF_STR    = '调休'; //调休（小时）
    protected $fillable = [
        'change_user_id',
        'change_type',
        'change_before_amount',
        'change_after_amount',
        'change_amount',
        'change_remark',
        'change_unit',//结算单位
        'change_entry_id',
        'update_user_id',
    ];

    public static function createAnnualChangeLog($user_id, AttendanceVacation $vacation, $des)
    {
        if (!$des) {
            return null;
        }
        return self::createVacationChangeLog(
            $user_id,
            AttendanceVacationChange::CHANGE_TYPE_ANNUAL,
            $vacation->annual - $des,
            $vacation->annual,
            $des,
            AttendanceVacation::REMARK_PER_DAILY_SETTLE,
            AttendanceVacation::VACATION_UNIT_HOUR
        );
    }

    public static function createBenefitChangeLog($user_id, AttendanceVacation $vacation, $des)
    {
        if (!$des) {
            return null;
        }
        return self::createVacationChangeLog(
            $user_id,
            AttendanceVacationChange::CHANGE_TYPE_COMPANY_BENEFITS,
            $vacation->company_benefits - $des,
            $vacation->company_benefits,
            $des,
            AttendanceVacation::REMARK_PER_DAILY_SETTLE,
            AttendanceVacation::VACATION_UNIT_HOUR
        );
    }

    public static function createFullPaySickChangeLog($user_id, AttendanceVacation $vacation, $des)
    {
        if (!$des) {
            return null;
        }
        return self::createVacationChangeLog(
            $user_id,
            AttendanceVacationChange::CHANGE_TYPE_FULL_PAY_SICK,
            $vacation->full_pay_sick - $des,
            $vacation->full_pay_sick,
            $des,
            AttendanceVacation::REMARK_PER_DAILY_SETTLE,
            AttendanceVacation::VACATION_UNIT_HOUR
        );
    }

    public static function createVacationChangeLog(
        $user_id,
        $change_type,
        $change_before_amount,
        $change_after_amount,
        $change_amount,
        $change_remark,
        $change_unit = AttendanceVacation::VACATION_UNIT_HOUR,
        $change_entry_id = '',
        $update_user_id = ''
    ) {
        $data           = [
            'change_user_id'       => $user_id,
            'change_type'          => $change_type,
            'change_before_amount' => $change_before_amount,
            'change_after_amount'  => $change_after_amount,
            'change_amount'        => $change_amount,
            'change_remark'        => $change_remark,
            'change_unit'          => $change_unit,
            'change_entry_id'      => $change_entry_id,
            'update_user_id'       => $update_user_id,
        ];
        $vacationChange = new AttendanceVacationChange();
        $vacationChange->fill($data);
        $vacationChange->save();
        return $vacationChange;
    }

    public static function getChangeAmount($a, $b)
    {
        $diff   = intval($a) - intval($b);
        $result = abs($diff);
        return sprintf('%.2f', $result);
    }

    /**
     * 查找全新病假使用记录
     * @param $userId
     * @param $type
     * @param $date
     */
    static public function findFullPaySickRecord($userId, $type, $date)
    {
        $record = self::where('change_user_id', '=', $userId)
            ->where('change_type', '=', $type)
            ->where('created_at', '>=', $date)
            ->where('change_amount', '>', 0)
            ->where('change_remark', '=', '请假消耗')
            ->get();
        return $record;
    }

    /**
     * 获取上年结余假期
     * @param $userId
     * @param $remark
     * @param $type
     */
    static public function findLastYearResidue($userId, $remark, $type)
    {
        $record = self::where('change_user_id', '=', $userId)
            ->where('change_remark', '=', $remark)
            ->where('change_type', '=', $type)->first();
        return $record;
    }

    static public function getSumAnnualAndCompanyBenefits($userId, $remark, $type, $date)
    {
        $record = self::where('change_user_id', '=', $userId)
            ->where('change_remark', '=', $remark)
            ->whereIn('change_type', $type)
            ->where('created_at', '>=', $date)
            ->get();
        return $record;
    }

}