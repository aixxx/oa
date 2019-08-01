<?php

namespace App\Console\Commands;

use App\Models\Attendance\AnnualRule;
use App\Models\Attendance\AttendanceVacation;
use App\Models\Attendance\AttendanceVacationChange;
use App\Models\Company;
use App\Models\User;
use App\Models\User\HasCrond;
use App\Repositories\UsersRepository;
use Illuminate\Console\Command;

class AttendanceVacationForAnnual extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vacation:annual';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '定时更新员工的剩余年假';

    protected $repository;

    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->repository=app()->make(UsersRepository::class);
    }

    public function handle()
    {
        $users = User::query()->leftJoin('testoa_yuns_net.attendance_vacation_has_croned', function ($join){
                                $join->on('users.id', '=', 'attendance_vacation_has_croned.uid');
                        })
            ->where('attendance_vacation_has_croned.cron_date', '<', date('Ym'))
            ->orWhereNull('attendance_vacation_has_croned.cron_date')
            ->limit(1000)
            ->get(['users.*']);

        /** @var User $user */
        foreach($users as $key => $user){
            //用户公司采用的规则
            /** @var Company $company */
            $company = $user->company;
            if(empty($company)){
                continue;
            }
            $rules = $company->annualRules;
            $ruleValue = 0;
            $overMonthLen = 0;
            /** @var AnnualRule $rule */
            foreach ($rules as $rule){
                $yearLength = -1;
                $moreMonthLen = 0;
                switch ($rule->type){
                    case '1'://按入职时长计算
                        $dateDiff = date_diff(new \DateTime(), new \DateTime($user->join_at));
                        $yearLength = $dateDiff->y;
                        $moreMonthLen = $dateDiff->m;
                        break;
                    case '2': //按累计工龄计算  累计工龄： 1、当前-入职时间+累计工龄    2、累计工龄（更新）
                        $yearLength = floor($user->cumulative_length/12);  //单位月
                        $moreMonthLen = $user->cumulative_length%12;
                        break;
                    default:
                        $yearLength = -1;
                        $moreMonthLen  = 0;
                        break;
                }
                if($yearLength >= $rule->min && $yearLength < $rule->max){
                    $ruleValue = $rule->value * 8;
                    $overMonthLen = $moreMonthLen;
                    break;
                }

            }
            //  有需要更新年假的用户
            if($ruleValue > 0){
                try{
                    $this->updateUserAnnual($user, $ruleValue);
                    echo 'user:'.$user->id . "annual updated \n\t";
                }catch (\Exception $exception){
                    throw $exception;
                }
            }
            $this->modifyNextCronDate($overMonthLen, $user);

            echo 'user:'.$user->id . "cron_date has modified \n\t";
        }
        return true;
    }

    /**
     * @param $overMonthLen
     * @param $user
     */
    public function modifyNextCronDate($overMonthLen, $user)
    {
        //更新下一次需要执行的时间
        $mDiff = 12 - $overMonthLen - 1;
        $nextCronMonth = date('Ym', strtotime("+" . $mDiff . "month"));
        //是否存在记录
        $crond = HasCrond::query()->where('uid', '=', $user->id)
            ->where('type', '=', HasCrond::TYPE_ANNUAL)->first();
        if ($crond) {
            $crond->cron_date = $nextCronMonth;
            $crond->save();
        } else {
            $data = [
                'cron_date' => $nextCronMonth,
                'uid' => $user->id,
                'type' => HasCrond::TYPE_ANNUAL,
            ];
            HasCrond::query()->insert($data);
        }
    }

    /**
     * @param $user
     * @param $ruleValue
     */
    public function updateUserAnnual($user, $ruleValue)
    {
        /** @var AttendanceVacation $attendanceVacation */
        $attendanceVacation = AttendanceVacation::query()->where('user_id', '=', $user->id)->first();
        $attendanceVacation->annual+=$ruleValue;
        $attendanceVacation->save();

        $data = [
            'change_user_id' => $user->id,
            'change_type' => AttendanceVacationChange::CHANGE_TYPE_ANNUAL,
            'change_before_amount' => $attendanceVacation->annual,
            'change_after_amount' => intval($attendanceVacation->annual) + $ruleValue,
            'change_amount' => $ruleValue,
            'change_remark' => '脚本自动更新年假',
            'change_unit' => AttendanceVacation::VACATION_UNIT_HOUR,
        ];
        $vacationChange = new AttendanceVacationChange();
        $vacationChange->fill($data);
        $vacationChange->save();
    }
}
