<?php
/**
 * Created by PhpStorm.
 * User: lizhennan
 * Date: 2018/7/25
 * Time: 下午5:15
 */

namespace App\Models\Attendance;


use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use App\Services\VacationManageService;
use UserFixException;
use DevFixException;

/**
 * App\Models\Attendance\AttendanceVacation
 *
 * @mixin \Eloquent
 * @property int $vacation_id
 * @property int $user_id 员工编号
 * @property string $annual 法定年假 （小时）
 * @property string $company_benefits 公司福利年假（小时）
 * @property string $marriage 婚假（天）
 * @property string $funeral 丧假（天）
 * @property string $maternity 产假（天）
 * @property string $paternity 陪产假（天）
 * @property string $check_up 产检假（天）
 * @property string $breastfeeding 哺乳假（小时）
 * @property string $working_injury 工伤假（天）
 * @property string $full_pay_sick 全薪病假（小时）
 * @property string $sick 病假（小时）
 * @property string $extra_day_off 调休（小时）
 * @property string $spring_festival 春节假（天）
 * @property string $business_trip 出差（天）
 * @property \Carbon\Carbon|null $created_at 创建时间
 * @property \Carbon\Carbon|null $updated_at 修改时间
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AttendanceVacation whereAnnual($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AttendanceVacation whereBreastfeeding($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AttendanceVacation whereBusinessTrip($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AttendanceVacation whereCheckUp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AttendanceVacation whereCompanyBenefits($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AttendanceVacation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AttendanceVacation whereExtraDayOff($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AttendanceVacation whereFullPaySick($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AttendanceVacation whereFuneral($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AttendanceVacation whereMarriage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AttendanceVacation whereMaternity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AttendanceVacation wherePaternity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AttendanceVacation whereSick($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AttendanceVacation whereSpringFestival($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AttendanceVacation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AttendanceVacation whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AttendanceVacation whereVacationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AttendanceVacation whereWorkingInjury($value)
 * @property int $annual_stashes 法定年假发放缓存区
 * @property int $company_benefits_stashes 公司福利年假发放缓存区
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceVacation whereAnnualStashes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceVacation whereCompanyBenefitsStashes($value)
 * @property float $actual_annual 实际发放法定年假
 * @property float $remain_annual 剩余法定年假
 * @property float $actual_company_benefits 实际发放福利年假
 * @property float $remain_company_benefits 剩余福利年假
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceVacation whereActualAnnual($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceVacation whereActualCompanyBenefits($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceVacation whereRemainAnnual($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceVacation whereRemainCompanyBenefits($value)
 * @property int $actual_full_pay_sick 实际发放全薪病假（小时）
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceVacation whereActualFullPaySick($value)
 */
class AttendanceVacation extends Model
{
    protected $primaryKey = 'vacation_id';
    //假期结算单位
    const VACATION_UNIT_DAY = 'day';
    const VACATION_UNIT_HOUR = 'hour';
    //法定年假
    const ANNUAL_COUNT_ZERO_DAYS = 0;//社会工龄不满 1 年：0天
    const ANNUAL_COUNT_FIVE_DAYS = 5;//社会工龄已满 1 年不满 10 年：5天
    const ANNUAL_COUNT_TEN_DAYS = 10;//社会工龄已满 10 年不满 20 年：10天
    const ANNUAL_COUNT_FIFTY_DAYS = 15;//社会工龄已满 20 年：15天
    //remark
    const REMARK_PER_DAILY_SETTLE = '每天自动分配假期';
    const REMARK_PER_MONTH_SETTLE = '每月自动分配假期';
    const LEAVE_APPLY_CONSUME = '请假消耗';
    const WORK_OVERTIME_APPLY_ADD = '加班申请后增加调休';
    const LAST_YEAR_VACATION = '假期结余';
    const REMAKE_HR_IMPORT_EXCEL = 'hr导入员工假期数据';
    const CONSOLE_VACATION_RECOVERY = '销假后恢复假期数据';

    const SUM_ANNUAL_AND_COMPANY_BENEFITS = 15;//法定年假和公司福利年假合计不得超过15天，合计数超过15天，需减少公司福利年假。

    const VACATION_USER_PER_FOUR_HOURS = 4;//请假基本单位为4小时
    const VACATION_SEND_PER_EIGHT_HOURS = 8;//福利和法定年假最小释放单位为1天（8小时）

    protected $fillable = [
        'user_id',
        'annual',
        'company_benefits',
        'full_pay_sick',
        'extra_day_off',
        'actual_full_pay_sick',
        //'marriage',
        //'funeral',
        // 'maternity',
    ];

    public static $holiday = [
        "actual_annual" => "0小时",
        "actual_company_benefits" => "0小时",
        "actual_full_pay_sick" => "0小时",
        "annual" => "0小时",
        "breastfeeding" => "0小时",
        "business_trip" => "0小时",
        "check_up" => "0小时",
        "company_benefits" => "0小时",
        "extra_day_off" => "0小时",
        "full_pay_sick" => "0小时",
        "funeral" => "0小时",
        "marriage" => "0小时",
        "maternity" => "0小时",
        "paternity" => "0小时",
        "sick" => "0小时",
        "spring_festival" => "0小时",
        "working_injury"=>"0小时"
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public $timestamps = true;

    public function user()
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'user_id');
    }

    public static function createNewAttendanceVacation($data)
    {
        $attendanceVacation = new AttendanceVacation();
        $attendanceVacation->fill($data);
        $attendanceVacation->save();
        return $attendanceVacation;
    }

    /**
     * 获取员工假期信息
     * @param $userId
     * @return Model|null|object|static
     */
    public static function getVacation($userId)
    {
        return self::where('user_id', $userId)->first();
    }

    /**
     * 请假后修改法定年假
     * @param $userId
     * @param $amount
     * @param $entryId流程编号
     * @param $unit （单位 :小时）
     */
    public static function reduceAnnual($userId, $amount, $entryId, $unit = AttendanceVacation::VACATION_UNIT_HOUR)
    {
        if ($unit != AttendanceVacation::VACATION_UNIT_HOUR) {
            throw new UserFixException('单位转换不正确！');
        }
        $vacation = self::getVacation($userId);
        if ($amount > $vacation->annual) {
            throw new UserFixException('余额不足');
        }
        if ($vacation->decrement('annual', $amount)) {
            AttendanceVacationChange::createVacationChangeLog($userId, AttendanceVacationChange::CHANGE_TYPE_ANNUAL, $vacation->annual + $amount,
                $vacation->annual, $amount, AttendanceVacation::LEAVE_APPLY_CONSUME, AttendanceVacation::VACATION_UNIT_HOUR, $entryId);
        } else {
            throw new DevFixException('请假后修改法定年假保存失败！');
        }

    }

    /**
     * 流程申请后添加法定年假
     * @param $userId
     * @param $amount
     * @param $entryId
     * @param string $remark
     * @param string $unit
     * @throws \Exception
     */
    public static function addAnnual($userId, $amount, $entryId, $remark, $unit = AttendanceVacation::VACATION_UNIT_HOUR)
    {
        if ($unit != AttendanceVacation::VACATION_UNIT_HOUR) {
            throw new UserFixException('单位转换不正确！');
        }
        $vacation = self::getVacation($userId);
        if ($vacation->increment('annual', $amount)) {
            AttendanceVacationChange::createVacationChangeLog($userId, AttendanceVacationChange::CHANGE_TYPE_ANNUAL, $vacation->annual,
                $vacation->annual + $amount, $amount, $remark, AttendanceVacation::VACATION_UNIT_HOUR, $entryId);
        } else {
            throw new DevFixException('销假后修改法定年假保存失败！');
        }

    }

    /**
     * 请假后修改公司福利年假
     * @param $userId
     * @param $amount
     * @param $unit （单位 :天）
     */
    public static function reduceCompanyBenefits($userId, $amount, $entryId, $unit = AttendanceVacation::VACATION_UNIT_DAY)
    {
        if ($unit != AttendanceVacation::VACATION_UNIT_DAY) {
            throw new UserFixException('单位转换不正确！');
        }
        $vacation = self::getVacation($userId);
        if ($amount > $vacation->company_benefits) {
            throw new UserFixException('余额不足');
        }
        if ($vacation->decrement('company_benefits', $amount)) {
            AttendanceVacationChange::createVacationChangeLog($userId, AttendanceVacationChange::CHANGE_TYPE_COMPANY_BENEFITS,
                $vacation->company_benefits + $amount,
                $vacation->company_benefits, $amount, AttendanceVacation::LEAVE_APPLY_CONSUME, AttendanceVacation::VACATION_UNIT_HOUR, $entryId);
        } else {
            throw new DevFixException('请假后修改公司福利年假保存失败！');
        }
    }

    /**
     * 流程申请后增加公司福利年假
     * @param $userId
     * @param $amount
     * @param $entryId
     * @param $remake
     * @param string $unit
     * @throws \Exception
     */
    public static function addCompanyBenefits($userId, $amount, $entryId, $remake, $unit = AttendanceVacation::VACATION_UNIT_DAY)
    {
        if ($unit != AttendanceVacation::VACATION_UNIT_DAY) {
            throw new UserFixException('单位转换不正确！');
        }
        $vacation = self::getVacation($userId);
        if ($vacation->increment('company_benefits', $amount)) {
            AttendanceVacationChange::createVacationChangeLog($userId, AttendanceVacationChange::CHANGE_TYPE_COMPANY_BENEFITS, $vacation->company_benefits,
                $vacation->company_benefits + $amount, $amount, $remake, AttendanceVacation::VACATION_UNIT_HOUR, $entryId);
        } else {
            throw new UserFixException('修改公司福利年假保存失败！');
        }
    }

    /**
     * 请假后修改全薪病假
     * @param $userId
     * @param $amount
     * @param $unit （单位 :小时）
     */
    public static function reduceFullPaySick($userId, $amount, $entryId, $unit = AttendanceVacation::VACATION_UNIT_HOUR)
    {
        if ($unit != AttendanceVacation::VACATION_UNIT_HOUR) {
            throw new UserFixException('单位转换不正确！');
        }
        $vacation = self::getVacation($userId);
        if ($amount > $vacation->full_pay_sick) {
            throw new UserFixException('余额不足');
        }
        if ($vacation->decrement('full_pay_sick', $amount)) {
            AttendanceVacationChange::createVacationChangeLog($userId, AttendanceVacationChange::CHANGE_TYPE_FULL_PAY_SICK, $vacation->full_pay_sick + $amount,
                $vacation->full_pay_sick, $amount, AttendanceVacation::LEAVE_APPLY_CONSUME, AttendanceVacation::VACATION_UNIT_HOUR, $entryId);
        } else {
            throw new DevFixException('请假后修改全薪病假保存失败！');
        }
    }

    /**
     * 增加全薪病假
     * @param $userId
     * @param $amount
     * @param $entryId
     * @param string $unit
     * @throws \Exception
     */
    public static function addFullPaySick($userId, $amount, $entryId, $remake, $unit = AttendanceVacation::VACATION_UNIT_HOUR)
    {
        if ($unit != AttendanceVacation::VACATION_UNIT_HOUR) {
            throw new UserFixException('单位转换不正确！');
        }
        $vacation = self::getVacation($userId);
        if ($vacation->increment('full_pay_sick', $amount)) {
            AttendanceVacationChange::createVacationChangeLog($userId, AttendanceVacationChange::CHANGE_TYPE_FULL_PAY_SICK, $vacation->full_pay_sick,
                $vacation->full_pay_sick + $amount, $amount, $remake, AttendanceVacation::VACATION_UNIT_HOUR, $entryId);
        } else {
            throw new DevFixException('增加全薪病假保存失败！');
        }
    }

    /**
     * 流程申请后修改调休
     * @param $userId
     * @param $amount
     * @param $unit （单位 :小时）
     */
    public static function addExtraDayOff(
        $userId,
        $amount,
        $entryId,
        $remark = AttendanceVacation::WORK_OVERTIME_APPLY_ADD,
        $unit = AttendanceVacation::VACATION_UNIT_HOUR
    )
    {
        if ($unit != AttendanceVacation::VACATION_UNIT_HOUR) {
            throw new UserFixException('单位转换不正确！');
        }
        $vacation = self::getVacation($userId);
        $oldExtra = $vacation->extra_day_off;

        if ($vacation->increment('extra_day_off', $amount)) {
            AttendanceVacationChange::createVacationChangeLog($userId, AttendanceVacationChange::CHANGE_TYPE_EXTRA_DAY_OFF, $oldExtra,
                intval($oldExtra) + $amount, $amount, $remark, AttendanceVacation::VACATION_UNIT_HOUR, $entryId);
        } else {
            throw new DevFixException('加班申请后修改调休保存失败！');
        }
    }

    /**
     * 请假后修改调休
     * @param $userId
     * @param $amount
     * @param $unit （单位 :小时）
     */
    public static function reduceExtraDayOff($userId, $amount, $entryId, $unit = AttendanceVacation::VACATION_UNIT_HOUR)
    {
        if ($unit != AttendanceVacation::VACATION_UNIT_HOUR) {
            throw new UserFixException('单位转换不正确！');
        }
        $vacation = self::getVacation($userId);
        $oldExtra = $vacation->extra_day_off;

        if ($vacation->decrement('extra_day_off', $amount)) {
            AttendanceVacationChange::createVacationChangeLog($userId, AttendanceVacationChange::CHANGE_TYPE_EXTRA_DAY_OFF, $oldExtra,
                $oldExtra - $amount, $amount, '请假消耗', AttendanceVacation::VACATION_UNIT_HOUR, $entryId);
        } else {
            throw new UserFixException('请假后修改调休保存失败！');
        }
    }

    /**
     * 假期取余操作
     * @param $vacation
     * @param $num
     */
    public static function formatResetVacation($vacation, $num)
    {
        $vacation = $vacation - fmod($vacation, $num);
        return $vacation;
    }

    public function getLastYearAnnual()
    {
        return max(floor(($this->annual - $this->actual_annual) / self::VACATION_USER_PER_FOUR_HOURS) * self::VACATION_USER_PER_FOUR_HOURS, 0);
    }

    public function getLastYearBenefit()
    {
        return max(
            floor(($this->company_benefits - $this->actual_company_benefits) / self::VACATION_USER_PER_FOUR_HOURS) * self::VACATION_USER_PER_FOUR_HOURS, 0
        );
    }

    public static function getVacationByUserNumOrName($userName = '', $userNum = '')
    {
        return AttendanceVacation::with('user')->whereHas('user', function ($query) use ($userName, $userNum) {
            $query->where('status', User::STATUS_JOIN);
            if ($userName) {
                $query->where('chinese_name', $userName);
            }
            if ($userNum) {
                $query->where('employee_num', $userNum);
            }
        })->orderBy('user_id')->paginate(15);
    }

    /**
     * 将福利和法定年假取余后的数据加入缓存区
     * @param AttendanceVacation $attendanceVacation
     */
    public static function vacationMoveStashes(AttendanceVacation $attendanceVacation)
    {
        //将福利和法定年假取余4的数量加到缓存区
        //缓存区作用：发放的假期满8小时才发放给用户，所以缓存区不满8小时，每月假期发放先放在缓存区
        $attendanceVacation->annual_stashes = fmod($attendanceVacation->annual, AttendanceVacation::VACATION_USER_PER_FOUR_HOURS); //初始化数据将4的取余移到缓存区
        $attendanceVacation->company_benefits_stashes = fmod($attendanceVacation->company_benefits, AttendanceVacation::VACATION_USER_PER_FOUR_HOURS);
        $attendanceVacation->annual -= $attendanceVacation->annual_stashes;//将转移到缓存区的数据剪掉
        $attendanceVacation->company_benefits -= $attendanceVacation->company_benefits_stashes;
        $attendanceVacation->save();
    }
}