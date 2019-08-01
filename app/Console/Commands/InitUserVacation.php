<?php

namespace App\Console\Commands;

use App\Models\Attendance\AnnualRule;
use App\Models\Company;
use App\Models\CompanyAnnualRule;
use App\Models\User;
use App\Models\Vacations\UserVacation;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Validation\Rule;

class InitUserVacation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user_vacation:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '初始化所有用户的假期信息';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $users = User::query()->limit(100)->get();

        foreach($users as $key => $user){
            //用户公司采用的规则
            /** @var Company $company */
            $company = $user->company;
            if(empty($company)){
                continue;
            }
            $rules = AnnualRule::query()->where('type', '=', 1)->get();
            foreach ($rules as $rule){
                $cnt = CompanyAnnualRule::query()->where(['company_id'=>$company->id, 'rule_id'=> $rule->id])->count();
                if($cnt <= 0){
                    CompanyAnnualRule::create(['company_id'=>$company->id, 'rule_id'=> $rule->id]);
                }
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
                        $user->join_at = strtotime($user->join_at) < 0 ? Carbon::now() : $user->join_at;
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
            $menstrual_time = $user->gender == 2 ? 1 : 0;  //女有例假
            $cnt = UserVacation::query()->where('user_id', '=', $user->id)->count();
            if($cnt > 0){
                continue;
            }
            $data = [
                'user_id' => $user->id,
                'annual_time' => $ruleValue,
                'rest_time' => 0,
                'menstrual_time' => $menstrual_time,
                'maternity_cnt' => 0,
                'paternity_cnt' => 0,
                'marital_cnt' => 0,
                'breastfeeding_cnt' => 0,
                'updated_at' => Carbon::now(),
                'created_at' => Carbon::now(),
            ];
            UserVacation::query()->create($data);

            echo 'user:'.$user->id . " has init \n\t";
        }

    }
}
