<?php
/**
 * Created by PhpStorm.
 * User: lizhennan
 * Date: 2020/8/14
 * Time: 上午11:27
 */

namespace App\Models\Attendance;


use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Attendance\AttendanceVacationConversion
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $annual_balance 法定年假结余（小时）
 * @property string|null $company_benefits_balance 公司福利年假结余（小时）
 * @property string|null $sum_amount 总结余
 * @property string|null $state 结算状态：0未处理，1结算成功
 * @property string|null $date_year 结算年份
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceVacationConversion whereAnnualBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceVacationConversion whereCompanyBenefitsBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceVacationConversion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceVacationConversion whereDateYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceVacationConversion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceVacationConversion whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceVacationConversion whereSumAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceVacationConversion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceVacationConversion whereUserId($value)
 * @mixin \Eloquent
 */
class AttendanceVacationConversion extends Model
{
    //处理状态
    const CONVERSION_STATE_UNTREATED = 0; //未处理
    const CONVERSION_STATE_FINISH = 1; //已折算工资处理
    protected $fillable = [
        'user_id',
        'annual_balance',//法定年假结余（小时）
        'company_benefits_balance',//公司福利年假结余（小时）
        'sum_amount',//总结余
        'state',
        'date_year',//结算日期
    ];

    /**
     * 新建假期折算记录
     * @param $userId
     * @param $annual_balance
     * @param $company_benefits_balance
     * @param $date
     * @param int $state
     */
    static public function createVacationConversion($userId, $annual_balance, $company_benefits_balance, $date, $state = self::CONVERSION_STATE_UNTREATED)
    {
        $data = [
            'user_id'                  => $userId,
            'annual_balance'           => $annual_balance,
            'company_benefits_balance' => $company_benefits_balance,
            'sum_amount'               => $annual_balance + $company_benefits_balance,
            'state'                    => $state,
            'date_year'                => $date,
        ];
        $vacationConversion = new AttendanceVacationConversion();
        $vacationConversion->fill($data);
        $vacationConversion->save();
    }

}