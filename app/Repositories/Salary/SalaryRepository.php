<?php

namespace App\Repositories\Salary;

use App\Models\Salary\SalaryAttendance;
use App\Models\Salary\SalaryForm;
use App\Models\Salary\SalaryRecord;
use App\Models\Salary\SalaryRecordSyncType;
use App\Models\Workflow\EntryData;
use App\Models\Workflow\Flowlink;
use App\Models\Workflow\Proc;
use App\Models\Workflow\Flow;
use App\Models\Workflow\Workflow;
use App\Models\User;
use App\Models\Workflow\Entry;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Mockery\CountValidator\Exception;
use Illuminate\Container\Container as Application;
use App\Services\AuthUserShadowService;
use App\Services\AttendanceApi\CountsService;
use App\Repositories\ParentRepository;
use App\Repositories\SalaryRepository as UserSalaryRepository;
use App\Repositories\SocialSecurityRepository;
use App\Repositories\UsersRepository;
use App\Repositories\Performance\PerformanceTemplateRepository;
use App\Repositories\EntryRepository;
use DB;
use Auth;

class SalaryRepository Extends ParentRepository
{
    private $year;
    private $month;
    private $lastMonth;
    private $lastYear;

    public function model()
    {
        return Entry::class;
    }

    public function __construct(Application $app)
    {
        parent::__construct($app);
    }

    public function refresh(Request $request)
    {
        try {
            $this->initRequest($request);
            $this->checkIsSync(SalaryRecordSyncType::SALARY_GENERATE_STEP_REFRESH);
            $this->initLastMonthAndYear();
            //筛选要发工资的人的列表
            $user     = User::findOrFail(1);
            $formData = [
                'user_id'      => $user->id,
                'employee_num' => $user->employee_num,
                'year'         => $this->year,
                'month'        => $this->month,
            ];

            DB::transaction(function () use ($formData) {
                (new SalaryForm())->fill($formData)->saveOrFail();
                $this->addTypeData(SalaryRecordSyncType::SALARY_GENERATE_STEP_REFRESH);
            });

            $this->data = [
                'thisMonthCount'  => $user->count(),
                'lastMonthRecord' => SalaryRecord::where('year', $this->year)->where('month', $this->month)->count(),
            ];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
        }
        return $this->returnApiJson();
    }

    public function syncAttendance(Request $request)
    {
        try {
            $this->initRequest($request);
            $this->checkIsSync(SalaryRecordSyncType::SALARY_GENERATE_STEP_SYNC_ATTENDANCE);
            $id   = 1791;
            $user = User::findOrFail($id);

            $data = CountsService::oneMonthCountForHr($id, $this->year . '-' . $this->month);
            throw_if(empty($data), new Exception(sprintf('用户名为%s考勤数据不存在', $user->chinese_name)));
            $attendanceData = [
                'user_id'                => $user->id,
                'year'                   => $this->year,
                'month'                  => $this->month,
                'should_attendance_days' => $data['should_arrive'],
                'actual_attendance_days' => $data['reality_arrive'],
                'casual_leave_days'      => $data['leave_of_absence'],
                'casual_leave_minus'     => 0,
                'sick_leave_days'        => $data['leave_sick_leave'],
                'sick_leave_minus'       => 0,
                'overtime_days'          => $data['overtime_nums'],
                'overtime_salary'        => 0,
            ];

            DB::transaction(function () use ($attendanceData) {
                (new SalaryAttendance())->fill($attendanceData)->saveOrFail();
                $this->addTypeData(SalaryRecordSyncType::SALARY_GENERATE_STEP_SYNC_ATTENDANCE);
            });

            $this->data = [
                'attendanceCount' => $user->count(),
            ];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
        }
        return $this->returnApiJson();
    }

    //二期
    public function syncProfitShare()
    {

    }

    public function syncPerformance(Request $request)
    {
        try {
            $this->initRequest($request);
            $result = app()->make(PerformanceTemplateRepository::class)->getSummary('2019-01')->toArray();//获取有绩效员工的绩效薪资

            if (empty($result)) {
                $this->message = '当前同步绩效人数为0';
                return $this->returnApiJson();
            }

            $salaryForm = SalaryForm::where('year', $this->year)
                ->where('month', $this->month)
                ->whereIn('user_id', array_keys($result));
            $count      = 0;
            DB::transaction(function () use ($result, $salaryForm, &$count) {
                $salaryForm->each(function ($item, $key) use ($result, $salaryForm, &$count) {
                    if (isset($result[$item->user_id])) {
                        SalaryForm::find($item->id)->update(['performance' => $result[$item->user_id]]);
                        $count++;
                    }
                });

                $data = [
                    'year'  => $this->year,
                    'month' => $this->month,
                    'type'  => SalaryRecordSyncType::SALARY_GENERATE_STEP_SYNC_PERFORMANCE,
                    'count' => $count
                ];
                (new SalaryRecordSyncType())->fill($data)->saveOrFail();
            });
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
        }
        return $this->returnApiJson();
    }

    public function syncRewordsAndPunishment()
    {

    }

    public function syncFloatSalary()
    {
        try {
            //同步浮动薪资,二期
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
        }
        return $this->returnApiJson();
    }

    public function syncTax()
    {
        try {
            $userId     = 1791;
            $user       = User::findOrFail($userId);
            $this->data = [
                'count' => $user->count(),
            ];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
        }
        return $this->returnApiJson();
    }

    public function syncSocialSecurity()
    {
        try {
            $userId = 1791;
            $user   = User::findOrFail($userId);
            //$userData = app()->make(UserSalaryRepository::class)->getUserSalary($userId);
            $SocialSecurityRepository = app()->make(SocialSecurityRepository::class);
            $socialData               = $SocialSecurityRepository->getSocialSecurity($user->company_id);//获取社保
            $socialUser               = $SocialSecurityRepository->getUserSocialSecurity($user->company_id);//获取社保参与人
            throw_if(empty($socialData), new Exception('社保数据没有配置'));
            throw_if(empty($socialUser), new Exception('社保与人的关系数据没有配置'));
            $this->data = [
                'count' => collect($socialUser)->count(),
            ];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
        }
        return $this->returnApiJson();
    }

    /**
     * 基本薪*工资包比例+补贴+奖金-社保-个税-惩罚+个人分红（总分红*工资包分红比）
     * @param User $user
     */
    public function payrollUser(Request $request)
    {
        try {
            $this->initRequest($request);
            $userId     = 1791;
            $salaryData = app()->make(UserSalaryRepository::class)->getUserSalary($userId);
            $salaryData = [
                '1791' => [
                    'basic_salary'        => 6000,
                    'subsidy_salary'      => 5000,
                    'basic_salary_list'   => [['name' => '技能薪', 'value' => 2000], ['name' => '基础薪', 'value' => 2000]],
                    'subsidy_salary_list' => [['name' => '通讯补贴', 'value' => 1000], ['name' => '车补', 'value' => 2000]],
                ],
                '1792' => [
                    'basic_salary'        => 8000,
                    'subsidy_salary'      => 6000,
                    'basic_salary_list'   => [['name' => '技能薪', 'value' => 3000], ['name' => '基础薪', 'value' => 1000]],
                    'subsidy_salary_list' => [['name' => '通讯补贴', 'value' => 2000], ['name' => '车补', 'value' => 3000]],
                ]
            ];

            $salaryFormRecord = SalaryForm::where('year', $this->year)->where('month', $this->month)->get();
            throw_if(!$salaryFormRecord, new Exception('工资单中没有任何记录'));
            $salaryRecord   = ['year' => $this->year, 'month' => $this->month, 'count' => $salaryFormRecord->count()];
            $userRepository = app()->make(UsersRepository::class);
            $taxRepository  = app()->make(PerformanceTemplateRepository::class);

            $salaryRecord['should_amount']
                = $salaryRecord['actual_amount']
                = $salaryRecord['performance_amount']
                = $salaryRecord['bonus_amount']
                = $salaryRecord['fines_amount']
                = $salaryRecord['overtime_salary_amount']
                = $salaryRecord['single_salary_amount']
                = $salaryRecord['social_company_amount']
                = $salaryRecord['fund_company_amount']
                = $salaryRecord['float_salary_amount']
                = 0;

            DB::transaction(function () use ($salaryFormRecord, $salaryData, &$salaryRecord, $userRepository, $taxRepository) {
                $salaryFormRecord->each(function ($item, $key) use ($salaryData, &$salaryRecord, $userRepository, $taxRepository) {
                    $formData = [
                        'base'         => $salaryData[$item['user_id']]['basic_salary'],
                        'subsidy'      => $salaryData[$item['user_id']]['subsidy_salary'],
                        'base_json'    => json_encode($salaryData[$item['user_id']]['basic_salary_list'], JSON_UNESCAPED_UNICODE),
                        'subsidy_json' => json_encode($salaryData[$item['user_id']]['subsidy_salary_list'], JSON_UNESCAPED_UNICODE),
                        'bonus'        => 2000,
                        'fines'        => 200,
                        'dividend'     => 20000,
                        'float_salary' => 1000,
                    ];

                    $describe = $userRepository->proportion($item['user_id']);
                    throw_if(!$describe, new Exception(sprintf('用户ID为%s没有配置工资包', $item['user_id'])));
                    //$temp = $describe;
                    $formData['should_salary'] = $formData['base'] * $describe[$item['user_id']]['salary_scale'] +
                        $formData['subsidy'] + $formData['bonus'] - $formData['fines'] + $formData['dividend'] * $describe[$item['user_id']]['points_scale'];
                    $socialData                = $this->fetchSocialData($item['user_id'], $formData['base']);

                    $formData['actual_salary'] = $formData['should_salary'] - $socialData['personal']
                        - $taxRepository->getPersonalIncomeTax(($formData['should_salary'] - $socialData['personal']));
                    //个人成本
                    $formData['human_cost'] = $formData['should_salary'] + $socialData['company'] + $socialData['fund'] + $socialData['supplementary_fund'];
                    $formData['is_pass']    = SalaryForm::STATUS_IS_PASS_YES;
                    SalaryForm::where(['user_id' => $item['user_id'], 'year' => $this->year, 'month' => $this->month])->update($formData);
                    //合计
                    $salaryRecord['should_amount'] += $formData['should_salary'];
                    $salaryRecord['actual_amount'] += $formData['actual_salary'];
                    $salaryRecord['performance_amount'] += 0;//绩效暂时写0
                    $salaryRecord['bonus_amount'] += $formData['bonus'];
                    $salaryRecord['fines_amount'] += $formData['fines'];
                    $salaryRecord['overtime_salary_amount'] += 0;//加班工资暂时写0
                    $salaryRecord['single_salary_amount'] += 0;//不知道怎么算;写0
                    $salaryRecord['social_company_amount'] += $socialData['company'];
                    $salaryRecord['fund_company_amount'] += $socialData['fund'] + $socialData['supplementary_fund'];
                    $salaryRecord['float_salary_amount'] += $formData['float_salary'];
                });
                $salaryRecord['status'] = SalaryRecord::SALARY_RECORD_STATUS_FINISH;
                (new SalaryRecord())->fill($salaryRecord)->saveOrFail();
                $this->data = $this->fetchSalaryStatistics($salaryRecord);
            });
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
        }
        return $this->returnApiJson();
    }

    public function fetchTotalSalary()
    {
        try {
            $start  = Carbon::now()->subYear()->startOfMonth()->toDateTimeString();
            $result = SalaryRecord::where('created_at', '>', $start)->get(['year', 'month', 'total_amount'])->all();
            $temp   = $finalData = [];
            collect($result)->each(function ($item, $key) use ($temp, &$finalData, $result) {
                if ($key > 0) {
                    $temp['year']                    = $item['year'];
                    $temp['month']                   = $item['month'];
                    $temp['total_amount']            = $item['total_amount'];
                    $temp['last_month_total_amount'] = isset($result[$key - 1]) ? $result[$key - 1]['total_amount'] : 0;
                    $finalData[]                     = $temp;
                }
            });
            $this->data = [
                'MonthData' => $finalData,
            ];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
        }
        return $this->returnApiJson();
    }

    public function fetchSalaryMonths()
    {
        try {
            $start  = Carbon::now()->subYear()->startOfMonth()->toDateTimeString();
            $result = SalaryRecord::where('created_at', '>', $start)->get(['year', 'month', 'status'])->all();
            $temp   = $finalData = [];
            collect($result)->each(function ($item, $key) use ($temp, &$finalData, $result) {
                if ($key > 0) {
                    $temp['year']  = $item['year'];
                    $temp['month'] = $item['month'];
                    $finalData[]   = $temp;
                }
            });
            $this->data = [
                'MonthData' => $finalData,
            ];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
        }
        return $this->returnApiJson();
    }

    public function fetchSalaryStatus()
    {
        try {
            $start      = Carbon::now()->subYear()->startOfMonth()->toDateTimeString();
            $result     = SalaryRecord::where('created_at', '>', $start)->get(['year', 'month', 'status'])->count();
            $this->data = [
                'has_record' => $result ? 1 : 0,
            ];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
        }
        return $this->returnApiJson();
    }

    public function fetchHumanCostList(Request $request)
    {
        try {
            $this->initRequest($request);
            $salaryRecord = SalaryRecord::where('year', $this->year)->where('month', $this->month)->get(['count', 'total_amount'])->first();
            $salaryForm   = SalaryForm::where('year', $this->year)->where('month', $this->month)->get()->all();
            throw_if(empty($salaryForm) || empty($salaryRecord), new Exception('没有对应的记录'));
            $this->data = [
                'averageCost' => round($salaryRecord['total_amount'] / $salaryRecord['count'], 2),
                'list'        => $salaryForm
            ];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
        }
        return $this->returnApiJson();
    }

    public function fetchPersonalSalaryForm(Request $request)
    {
        try {
            $id         = $request->get('id', 0);
            $salaryForm = SalaryForm::find($id);
            throw_if(empty($salaryForm), new Exception('没有对应的记录'));
            $this->data = [
                'list' => $salaryForm
            ];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
        }
        return $this->returnApiJson();
    }

    public function createSalaryRecordApply(Request $request)
    {
        try {
            $flow = Flow::findByFlowNo(Entry::WORK_FLOW_NO_SALARY_STATISTICS);
            throw_if(empty($flow), new Exception('没有对应的流程'));
            $id           = $request->get('id', 0);
            $salaryRecord = SalaryRecord::findOrFail($id);
            throw_if(empty($salaryRecord), new Exception('没有对应的记录'));
            $insertData = [
                'flow_id' => $flow->id,
                'title'   => '薪资统计审批流程',
                'tpl'     => [
                    'total_amount'           => $salaryRecord->total_amount,
                    'should_amount'          => $salaryRecord->should_amount,
                    'actual_amount'          => $salaryRecord->actual_amount,
                    'performance_amount'     => $salaryRecord->performance_amount,
                    'bonus_amount'           => $salaryRecord->bonus_amount,
                    'fines_amount'           => $salaryRecord->fines_amount,
                    'overtime_salary_amount' => $salaryRecord->overtime_salary_amount,
                    'single_salary_amount'   => $salaryRecord->single_salary_amount,
                    'social_company_amount'  => $salaryRecord->social_company_amount,
                    'fund_company_amount'    => $salaryRecord->fund_company_amount,
                    'float_salary_amount'    => $salaryRecord->float_salary_amount,
                ]];

            $entryId = $this->storeWorkflow($insertData);
            $salaryRecord->update(['entry_id' => $entryId]);
            $this->data = [
                'entry_id' => $entryId,
            ];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
        }
        return $this->returnApiJson();
    }

    public function goToPassedSalaryGroup(Request $request)
    {
        try {
            $this->initRequest($request);
            $salaryRecord = $this->fetchPassedSalaryRecord($this->year, $this->month);
            throw_if(empty($salaryRecord), new Exception('没有对应的记录'));
            $salaryForm = SalaryForm::with('user')
                ->where('year', $this->year)
                ->where('month', $this->month)->get();

            throw_if($salaryForm->isEmpty(), new Exception('没有对应的工资条'));
            $result = [];
            $salaryForm->each(function ($item, $key) use (&$result) {
                $result[$key]['user_id']      = $item->user_id;
                $result[$key]['name']         = $item['user']['chinese_name'];
                $result[$key]['employee_num'] = $item->employee_num;
                $result[$key]['join_at']      = $item->user->join_at;
            });

            $this->data = $result;
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
        }
        return $this->returnApiJson();
    }

    public function sendSalaryForm(Request $request)
    {
        try {
            $id         = $request->get('id', 0);
            $salaryForm = SalaryForm::find($id);
            throw_if(empty($salaryForm), new Exception('没有对应的工资条'));
            $salaryRecord = $this->fetchPassedSalaryRecord($salaryForm->year, $salaryForm->month);
            throw_if(empty($salaryRecord), new Exception('没有对应的薪资审批记录'));
            $salaryForm->update(['is_send' => SalaryForm::STATUS_IS_SEND_YES]);
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
        }
        return $this->returnApiJson();
    }

    public function fetchSalarySyncList(Request $request)
    {
        try {
            $this->initRequest($request);
            $salaryRecord = $this->fetchPassedSalaryRecord($this->year, $this->month);
            throw_if(empty($salaryRecord), new Exception('没有对应的记录'));
            $this->data = [
                'personal_cost' => round($salaryRecord->total_amount, $salaryRecord->count),
                'count'         => $salaryRecord->count,
                'list'          => SalaryRecordSyncType::where('year', $this->year)->where('month', $this->month)->get(['type', 'count'])->toArray(),
                'type_list'     => SalaryRecordSyncType::$typeList,
            ];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
        }
        return $this->returnApiJson();

    }

    public function fetchSalaryFormStatusCount(Request $request)
    {
        try {
            $this->initRequest($request);
            $salaryRecord = $this->fetchPassedSalaryRecord($this->year, $this->month);
            throw_if(empty($salaryRecord), new Exception('没有对应的记录'));
            $salaryFormNoSend    = SalaryForm::where('year', $this->year)
                ->where('month', $this->month)
                ->where('is_pass', SalaryForm::STATUS_IS_PASS_NO)
                ->where('is_withdraw', SalaryForm::STATUS_IS_WITHDRAW_NO)
                ->count();
            $salaryFormNoView    = SalaryForm::where('year', $this->year)
                ->where('month', $this->month)
                ->where('is_view', SalaryForm::STATUS_IS_VIEW_NO)->count();
            $salaryFormNoConfirm = SalaryForm::where('year', $this->year)
                ->where('month', $this->month)
                ->where('is_confirm', SalaryForm::STATUS_IS_CONFIRM_YES)->count();

            $this->data = [
                'no_send_count'    => $salaryFormNoSend,
                'no_view_count'    => $salaryFormNoView,
                'no_confirm_count' => $salaryFormNoConfirm,
            ];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
        }
        return $this->returnApiJson();

    }

    public function fetchSalaryFormStatusList(Request $request)
    {
        try {
            $this->initRequest($request);
            $status     = $request->get('status');
            $statusList = $this->fetchStatusList();
            throw_if(!array_key_exists($status, $statusList), new Exception('非法操作'));
            $salaryRecord = $this->fetchPassedSalaryRecord($this->year, $this->month);
            throw_if(empty($salaryRecord), new Exception('没有对应的记录'));
            $salaryFormList = SalaryForm::with('user', 'user.fetchPrimaryDepartment')->where('year', $this->year)
                ->where('month', $this->month)
                ->where('is_pass', SalaryForm::STATUS_IS_PASS_YES);

            switch ($status) {
                case 'no_send':
                    $salaryFormList->where('is_send', SalaryForm::STATUS_IS_SEND_NO);
                    break;
                case 'no_view':
                    $salaryFormList->where('is_send', SalaryForm::STATUS_IS_SEND_YES)->where('is_view', SalaryForm::STATUS_IS_VIEW_NO);
                    break;
                case 'no_confirm':
                    $salaryFormList->where('is_send', SalaryForm::STATUS_IS_SEND_YES)
                        ->where('is_view', SalaryForm::STATUS_IS_VIEW_YES)
                        ->where('is_confirm', SalaryForm::STATUS_IS_CONFIRM_NO);
                    break;
                case 'is_completed':
                    $salaryFormList->where('is_confirm', SalaryForm::STATUS_IS_CONFIRM_YES);
                    break;
                case 'is_withdraw':
                    $salaryFormList->where('is_withdraw', SalaryForm::STATUS_IS_WITHDRAW_YES);
                    break;
                default:
                    break;
            }

            $result = $temp = [];
            $temp   = $salaryFormList->get();
            $temp->each(function ($item, $key) use (&$result) {
                $dept                         = $item->user->fetchPrimaryDepartment->toArray();
                $result[$key]['user_id']      = $item['user_id'];
                $result[$key]['employee_num'] = $item['employee_num'];
                $result[$key]['human_cost']   = $item['human_cost'];
                $result[$key]['name']         = $item->user->chinese_name;
                $result[$key]['position']     = $item->user->position;
                $result[$key]['department']   = isset($dept['0']['name']) ? $dept['0']['name'] : '暂无部门';
                $result[$key]['join_at']      = $item->user->join_at;
            });
            $this->data = $result;
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
        }
        return $this->returnApiJson();

    }

    public function salaryFormSend(Request $request)
    {
        try {
            $id = $request->get('id', 0);
            throw_if(!$id, new Exception('缺少必要的参数'));
            $salaryForm = SalaryForm::findOrFail($id);
            throw_if(!$salaryForm->is_pass, new Exception('非法操作'));
            $result = $salaryForm->update(['is_send' => SalaryForm::STATUS_IS_SEND_YES]);
            throw_if(!$result, new Exception('操作失败'));
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
        }
        return $this->returnApiJson();
    }

    public function salaryFormWithdraw(Request $request)
    {
        try {
            $id = $request->get('id', 0);
            throw_if(!$id, new Exception('缺少必要的参数'));
            $salaryForm = SalaryForm::findOrFail($id);
            throw_if(!$salaryForm->is_pass || !$salaryForm->is_send || $salaryForm->is_view, new Exception('非法操作'));
            $result = $salaryForm->update(['is_send' => SalaryForm::STATUS_IS_SEND_NO, 'is_confirm' => SalaryForm::STATUS_IS_CONFIRM_NO, 'is_view' => SalaryForm::STATUS_IS_VIEW_NO, 'is_withdraw' => SalaryForm::STATUS_IS_WITHDRAW_YES]);
            throw_if(!$result, new Exception('操作失败'));
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
        }
        return $this->returnApiJson();
    }

    //个人视角
    public function viewPersonalSalaryFormList(Request $request)
    {
        try {
            $this->initRequest($request);
            $this->data = SalaryForm::where('user_id', Auth::id())
                ->where('is_send', SalaryForm::STATUS_IS_SEND_YES)
                ->where('is_confirm', SalaryForm::STATUS_IS_CONFIRM_NO)
                ->orderBy('year', 'asc')
                ->orderBy('month', 'asc')
                ->get(['year', 'month', 'actual_salary', 'id']);
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
        }
        return $this->returnApiJson();
    }

    public function viewPersonalSalaryForm(Request $request)
    {
        try {
            $id         = $request->get('id');
            $this->data = SalaryForm::where('id', $id)
                ->where('user_id', Auth::id())
                ->where('is_send', SalaryForm::STATUS_IS_SEND_YES)
                ->where('is_confirm', SalaryForm::STATUS_IS_CONFIRM_NO)
                ->first();
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
        }
        return $this->returnApiJson();
    }

    public function salaryFormView(Request $request)
    {
        try {
            $id = $request->get('id', 0);
            throw_if(!$id, new Exception('缺少必要的参数'));
            $salaryForm = SalaryForm::findOrFail($id);
            throw_if((Auth::id() != $salaryForm->user_id) || !$salaryForm->is_pass || !$salaryForm->is_send, new Exception('非法操作'));
            $result = $salaryForm->update(['is_view' => SalaryForm::STATUS_IS_VIEW_YES]);
            throw_if(!$result, new Exception('操作失败'));
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
        }
        return $this->returnApiJson();
    }

    public function salaryFormConfirm(Request $request)
    {
        try {
            $id = $request->get('id', 0);
            throw_if(!$id, new Exception('缺少必要的参数'));
            $salaryForm = SalaryForm::findOrFail($id);
            throw_if((Auth::id() != $salaryForm->user_id) || !$salaryForm->is_pass || !$salaryForm->is_send, new Exception('非法操作'));
            $result = $salaryForm->update(['is_confirm' => SalaryForm::STATUS_IS_CONFIRM_YES]);
            throw_if(!$result, new Exception('操作失败'));
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
        }
        return $this->returnApiJson();
    }

    //流程处理-开始
    public function storeWorkflow($data)
    {
        return $this->updateFlow($data, 0);
    }

    public function updateFlow($data, $id)
    {
        try {
            DB::beginTransaction();

            $flow_id = $data['flow_id'];
            $flow    = Flow::findById($flow_id);
            $flow->checkCanApply(); // 校验是否可以提交该流程的申请,不可提交申请的话,创建或者修改申请单,也没有意义
            $entry = $this->updateOrCreateEntry($data, $id); // 创建或更新申请单

            $receiver_ids    = Proc::query()
                ->where('entry_id', '=', $entry->id)
                ->where('status', '!=', Entry::STATUS_FINISHED)
                ->pluck('user_id')->toArray();
            $entryRepository = app()->make(EntryRepository::class);
            $entryRepository->createTaskByEntry($entry, $receiver_ids);

            if ($entry->isInHand()) {
                $flow_link = Flowlink::firstStepLink($entry->flow_id);
                //进程初始化
                (new Workflow())->setFirstProcessAuditor($entry, $flow_link);
            }
            $entry->save();
            DB::commit();

            $this->data = ['entry' => $entry->toArray()];
        } catch (Exception $e) {
            DB::rollback();
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
            Workflow::errLog('EntryUpdate', $e->getMessage() . $e->getTraceAsString());
        }
        return $entry->id;
    }

    public function updateOrCreateEntry($data, $id = 0)
    {
        $data['file_source_type'] = 'workflow';
        $data['file_source']      = 'entry_apply';
        $data['is_draft']         = null;
        $data['entry_id']         = null;

        if (!$id) {
            $authApplyer = new AuthUserShadowService(); // 以影子用户作为申请人
            $entry       = Entry::create([
                'title'            => $data['title'],
                'flow_id'          => $data['flow_id'],
                'user_id'          => $authApplyer->id(),
                'circle'           => 1,
                'status'           => Entry::STATUS_IN_HAND,
                'origin_auth_id'   => Auth::id(),
                'origin_auth_name' => Auth::user()->name,
            ]);
        } else {
            $entry = Entry::findOrFail($id);
            $entry->checkEntryCanUpdate(); // 校验申请单是否可以修改
            $entry->update($data);
        }
        if (!empty($data['is_draft'])) {
            $entry->status = Entry::STATUS_DRAFT;
        } else {
            $entry->status = Entry::STATUS_IN_HAND;
        }
        $this->updateTpl($entry, $data['tpl'] ?? []);

        return $entry;
    }

    public function updateTpl(Entry $entry, $tpl = [])
    {
        foreach ($tpl as $k => $v) {
            $val = is_array($v) ? json_encode($v) : $v;
            $val = $val === null ? '' : $val;
            if ('password' == $k) {
                $val = Hash::make($val);
            }

            EntryData::updateOrCreate(['entry_id' => $entry->id, 'field_name' => $k], [
                'flow_id'     => $entry->flow_id,
                'field_value' => $val,
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ]);
        }
    }

    //流程处理-结束
    private function initRequest(Request $request)
    {
        $this->year  = $request->get('year');
        $this->month = $request->get('month');
    }

    private function initLastMonthAndYear()
    {
        $this->lastMonth = $this->month == 1 ? 12 : $this->month - 1;
        $this->lastYear  = $this->month == 1 ? $this->year - 1 : $this->year;
    }

    private function addTypeData($type)
    {
        $typeData = [
            'year'  => $this->year,
            'month' => $this->month,
            'type'  => $type,
        ];
        return (new SalaryRecordSyncType())->fill($typeData)->saveOrFail();
    }

    private function checkIsSync($type, $year = null, $month = null)
    {
        $year  = $year??$this->year;
        $month = $month??$this->month;
        $re    = SalaryRecordSyncType::where('year', $year)->where('month', $month)->where('type', $type)->count();
        throw_if(!$re, new Exception('本月数据已经同步过了'));
    }

    //计算个人社保,企业社保,企业公积金和企业补充公积金
    private function fetchSocialData($userId, $salaryBase)
    {
        $user                     = User::findOrFail($userId);
        $SocialSecurityRepository = app()->make(SocialSecurityRepository::class);
        $socialData               = $SocialSecurityRepository->getSocialSecurity($user->company_id);//获取社保
        $socialUser               = $SocialSecurityRepository->getUserSocialSecurity($user->company_id);//获取社保参与人
        throw_if(empty($socialData) || !isset($socialData['medical']) || !isset($socialData['aged']) || !isset($socialData['lost']) || !isset($socialData['social']) || !isset($socialData['fund']) || !isset($socialData['supplementary_fund']), new Exception('社保数据没有配置'));
        $socialPersonal = $socialCompany = $fundCompany = $supplementaryFundCompany = 0;
        if (in_array($userId, $socialUser)) {
            $socialPersonal           = $salaryBase * collect($socialData)->sum('personal_proportion');
            $socialCompany            = $salaryBase * ($socialData['medical']['company_proportion'] + $socialData['aged']['company_proportion'] + $socialData['lost']['company_proportion'] + $socialData['social']['company_proportion']);
            $fundCompany              = $salaryBase * $socialData['fund']['company_proportion'];
            $supplementaryFundCompany = $salaryBase * $socialData['supplementary_fund']['company_proportion'];
        }
        return ['personal' => $socialPersonal, 'company' => $socialCompany, 'fund' => $fundCompany, 'supplementary_fund' => $supplementaryFundCompany];
    }

    private function fetchSalaryStatistics($salaryRecord)
    {
        $salaryRecord['human_cost'] = round(($salaryRecord['should_amount'] + $salaryRecord['social_company_amount'] + $salaryRecord['fund_company_amount']) / $salaryRecord['count'], 2);
        return $salaryRecord;
    }

    private function fetchPassedSalaryRecord($year, $month)
    {
        $salaryRecord = SalaryRecord::where('year', $year)
            ->where('month', $month)
            ->where('status', SalaryRecord::SALARY_RECORD_STATUS_FINISH)
            ->where('status_entry', SalaryRecord::STATUS_PASS)
            ->first();
        return $salaryRecord;
    }

    private function fetchStatusList()
    {
        return [
            'all'          => SalaryForm::STATUS_IS_PASS_YES,
            'no_send'      => SalaryForm::STATUS_IS_SEND_NO,
            'no_view'      => SalaryForm::STATUS_IS_VIEW_NO,
            'no_confirm'   => SalaryForm::STATUS_IS_CONFIRM_NO,
            'is_completed' => SalaryForm::STATUS_IS_CONFIRM_YES,
            'is_withdraw'  => SalaryForm::STATUS_IS_WITHDRAW_YES,
        ];
    }
}
