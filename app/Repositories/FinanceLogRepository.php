<?php

namespace App\Repositories;

use App\Constant\ConstFile;
use App\Models\CaiWu\CaiWuFlow;
use App\Models\CaiWu\FlowClass;
use App\Models\Department;
use App\Models\DepartUser;
use App\Models\Financial;
use App\Models\FinancialCustomer;
use App\Models\FinancialDetail;
use App\Models\FinancialLog;
use App\Models\FinancialOrder;
use App\Models\FinancialPic;
use App\Models\TransactionLog;
use App\Models\UserAccount;
use App\Models\UsersDetailInfo;
use App\Models\Workflow\WorkflowRole;
use App\Models\Workflow\WorkflowRoleUser;
use App\Repositories\PAS\SaleOrderRepository;
use App\Repositories\PAS\SupplierRepository;
use Mockery\Exception;
use App\Models\Workflow\Entry;
use App\Models\Workflow\EntryData;
use App\Models\Workflow\Flowlink;
use App\Models\Workflow\FlowType;
use App\Models\Workflow\Proc;
use App\Models\Workflow\Workflow;
use App\Services\AuthUserShadowService;
use App\Services\WorkflowUserService;
use Illuminate\Http\Request;
use App\Models\Workflow\Flow;
use Illuminate\Support\Carbon;
use Auth;
use Hash;
use DB;
use App\Models\User;
use JWTAuth;
use Hprose\Http\Client;
use App\Repositories\RpcRepository;

class FinanceLogRepository extends ParentRepository
{
    /**
     * @var UsersRepository
     */
    protected $users;
    protected $userRepository;
    /**
     * @var RpcRepository
     */
    protected $rpcRepository;
    /**
     * @var FinanceRepository
     */
    protected $financeRepository;

    public function model()
    {
        return Entry::class;
    }

    public function __construct()
    {
        $this->userRepository = app()->make(UsersRepository::class);
        $this->rpcRepository = app()->make(RpcRepository::class);
        $this->saleRepository = app()->make(SaleOrderRepository::class);
        $this->financeRepository = app()->make(FinanceRepository::class);
        $this->users = Auth::user();
    }

    public function searchData($searchData)
    {
        $data = [];
        $data['create_begin'] = isset($searchData['time1']) ? $searchData['time1'] . ' 00:00:00' : '';
        $data['create_end'] = isset($searchData['time2']) ? $searchData['time2'] . ' 23:59:59' : '';
        //type 1:经营交易 2：账户流水 3：利润排名 4：经营计划 5：应收应付
        $isJoin = isset($searchData['is_join']) ? $searchData['is_join'] : 0;//0 表示分 1：合

        if (empty($searchData['selectdepts'])) {
            $searchData['selectdepts'] = Q(Auth::user(), 'primaryDepartUser', 'department_id');
        }

        $data['selectdepts'] = [];
        if ($isJoin == 1) {
            //合，查子集
            $deptIds = $this->userRepository->getChild($searchData['selectdepts']);
            $data['selectdepts'] = explode(',', $deptIds);
        } else {
            //分，查当前部
            $data['selectdepts'][] = $searchData['selectdepts'];
        }
        return $data;
    }

    public $types = [
        1 => '对内交易（收)',
        2 => '对内交易（支)',
        3 => '对外交易（收)',
        4 => '对外交易（支）',
        5 => '分红支出',
        6 => '资产',
    ];


    public function financeDepartmentList(Request $request)
    {
        $search = $request->all();
        $searchData = $this->searchData($search);//dd($search['type']);
        $type = $search['type'];//经营交易的8种类型
        $list = [];
        $is_index = 2;//1:表示首页 2表示列表
        $is_deptment = 2;//部门财务
        $list = $this->deptData($searchData, $type, $is_index = 2);
        $data = $this->financeRepository->fetchFinancials($list, $is_deptment);

        $this->data = $data;
        return $this->returnApiJson();
        //dd($list);
        // dd($this->$types);

    }

    //收
    public function flowReceivedData()
    {
        //( 收：（还款和收款）
        $flowReceivedIds = Flow::getFlowIds([
            Entry::WORK_FLOW_NO_FINANCE_REPAYMENT,
            Entry::WORK_FLOW_NO_FINANCE_RECEIVABLES
        ]);
        return $flowReceivedIds;
    }

    //支
    public function flowBranchIds()
    {
        //支：（报销，借款，支付）
        $flowBranchIds = Flow::getFlowIds([
            Entry::WORK_FLOW_NO_FINANCE_LOAN,
            Entry::WORK_FLOW_NO_CAPITAL_FEE_EXPENSE,
            Entry::WORK_FLOW_NO_FINANCE_PAYMENT
        ]);
        return $flowBranchIds;
    }


    //部门数据
    public function deptData($data, $type, $is_index)
    {
        //( 收：（还款和收款）
        $flowReceivedIds = $this->flowReceivedData();
        //支：（报销，借款，支付）
        $flowBranchIds = $this->flowBranchIds();
        $builder = Financial::whereIn('primary_dept', $data['selectdepts'])->where('status','>',0);
        if (isset($data['create_begin']) && $data['create_begin']) {
            $builder->where('created_at', '>=', $data['create_begin']);
        }
        if (isset($data['create_end']) && $data['create_end']) {
            $builder->where('created_at', '<=', $data['create_end']);
        }


        switch ($type) {
            case TransactionLog::FINANCIALS_DEPARTMENT_DNJYS_TYPE :
                $builder->where('transaction', trim('对内交易'))->whereIn('flow_id', $flowReceivedIds);
                break;
            case TransactionLog::FINANCIALS_DEPARTMENT_DNJYZ_TYPE :
                $builder->where('transaction', trim('对内交易'))->whereIn('flow_id', $flowBranchIds);
                break;
            case TransactionLog::FINANCIALS_DEPARTMENT_DWJYS_TYPE :
                $builder->where('transaction', trim('对外交易'))->whereIn('flow_id', $flowReceivedIds);
                break;
            case TransactionLog::FINANCIALS_DEPARTMENT_DWJYZ_TYPE :
                $builder->where('transaction', trim('对外交易'))->whereIn('flow_id', $flowBranchIds);
                break;
            case TransactionLog::FINANCIALS_DEPARTMENT_FEE_BOOTH_TYPE :

                $builder->where('fee_booth', Financial::FINANCIALS_FEE_BOOTH_YES);// 平摊
                break;
            /* case TransactionLog::FINANCIALS_DEPARTMENT_ZCJZ_TYPE :
                 // 资产价值
                 break;*/
            case TransactionLog::FINANCIALS_DEPARTMENT_DJZT_TYPE :// 单据状态
                $builder->whereHas('financePic');
                break;
            default :
                break;
            // 分红支出
        }
        if ($is_index == 2) {//列表
            $list = $builder->with('getFlow', 'getEntry', 'financeDetail', 'loan_bill.getFinanceInfo', 'financePic')
                ->select('id', 'flow_id', 'user_id', 'status', 'expense_amount', 'transaction', 'fee_booth',
                    'created_at', 'title', 'entry_id', 'status', 'account_period', 'sum_money', 'loan_bill_id')
                ->orderBy('id', 'desc')->paginate(20);
        } else {//统计
            $count = $builder->count('id');
            $amount = $builder->sum('expense_amount');
            $list = [
                'count' => $count,
                'type' => $type,
                'amount' => sprintf("%.2f", $amount)
            ];

        }

        return $list;

    }

    //部门财务的头部统计
    public function departmentStatistics($search,$selectdepts=[])
    {

        $builder = CaiWuFlow::where(['status'=> 1,'audit_status'=>1,'type' => 1]);
        $builder1 = CaiWuFlow::where(['status'=> 1,'audit_status'=>1,'type' => 2]);

        if(!$selectdepts){
            $builder->whereIn('department_id', $search['selectdepts']);
            $builder1->whereIn('department_id', $search['selectdepts']);
        }else{
            $builder->whereIn('department_id', $selectdepts);
            $builder1->whereIn('department_id', $selectdepts);
        }
        if (isset($search['create_begin']) && $search['create_begin']) {
            $create_begin = strtotime($search['create_begin']);
            $builder->where('create_time', '>=', $create_begin);
            $builder1->where('create_time', '>=', $create_begin);
        }
        if (isset($search['create_end']) && $search['create_end']) {
            $create_end = strtotime($search['create_end']);
            $builder->where('create_time', '<=', $create_end);
            $builder1->where('create_time', '<=', $create_end);
        }
        $profit = $caiwuFlowClassReceived = $caiwuFlowClassBranch = "0";
        //经营收入

        $caiwuFlowClassReceivedIds = FlowClass::where(['status' => 1, 'type' => 1])->pluck('id')->toArray();//收入类别ids
        $caiwuFlowClassReceived = $builder->whereIn('class_id',$caiwuFlowClassReceivedIds)->sum('money');//收

        //经营支出
        $caiwuFlowClassBranchIds = FlowClass::where(['status' => 1, 'type' => 2])->pluck('id')->toArray();//支出类别ids
        $caiwuFlowClassBranch = $builder1->whereIn('class_id',$caiwuFlowClassBranchIds)->sum('money');
        //利润
        $profit = $caiwuFlowClassReceived - $caiwuFlowClassBranch;
        $data = [
            'caiwuFlowClassReceived' => $caiwuFlowClassReceived,//经营收入
            'caiwuFlowClassBranch' => $caiwuFlowClassBranch,//经营成本
            'accumulatedIncome' => $caiwuFlowClassReceived,//累计收入
            'profit' => $profit,//经营利润
            'accountBalances' => $profit //账户余额
        ];
        return $data;


    }

    //应收应付
    public function accountReceivablePayable($search)
    {
        //( 收：（还款和收款）
        $flowReceivedIds = $this->flowReceivedData();
        //支：（报销，借款，支付）
        $flowBranchIds = $this->flowBranchIds();
        $builder = Financial::whereIn('primary_dept', $search['selectdepts'])->where('status', '>', 0);
        $builder1 = Financial::whereIn('primary_dept', $search['selectdepts'])->where('status', '>', 0);
        if (isset($search['create_begin']) && $search['create_begin']) {
            $builder->where('created_at', '>=', $search['create_begin']);
            $builder1->where('created_at', '>=', $search['create_begin']);
        }
        if (isset($search['create_end']) && $search['create_end']) {
            $builder->where('created_at', '<=', $search['create_end']);
            $builder1->where('created_at', '<=', $search['create_end']);
        }
        $in = $out = "0.00";
        $in = $builder->whereIn('flow_id', $flowReceivedIds)->sum('expense_amount');//收
        $out = $builder1->whereIn('flow_id', $flowBranchIds)->sum('expense_amount');//支
        $data = [
            'in' => sprintf("%.2f", $in),
            'out' => sprintf("%.2f", $out)
        ];
        return $data;

    }

    //应收应付列表
    public function accountReceivablePayableList($search)
    {
        //( 收：（还款和收款）
        $flowReceivedIds = $this->flowReceivedData();
        //支：（报销，借款，支付）
        $flowBranchIds = $this->flowBranchIds();
        $builder = Financial::whereIn('primary_dept', $search['selectdepts'])->where('status', '>', 0);
        if (isset($search['create_begin']) && $search['create_begin']) {
            $builder->where('created_at', '>=', $search['create_begin']);
        }
        if (isset($search['create_end']) && $search['create_end']) {
            $builder->where('created_at', '<=', $search['create_end']);
        }
        $list = $builder->with('financeOrder', 'users')
            ->select('id', 'flow_id', 'user_id', 'status', 'expense_amount', 'entry_id',
                'created_at', 'status', 'account_period', 'sum_money', 'code', 'end_period_at')
            ->orderBy('id', 'desc')->get();
        $nowTime = strtotime(date("Y-m-d"));//
        if ($list) {

            foreach ($list as &$val) {
                $uncollectedMoney = "0.00";
                $primary_dept_path = WorkflowUserService::fetchUserPrimaryDeptPath($val->user_id);
                if (in_array($val->flow_id, $flowReceivedIds)) {
                    $flow_name = "收";
                } else {
                    $flow_name = "支";
                }
                $val->flow_name = $flow_name;
                $val->primary_dept_path = $primary_dept_path;//部门层级
                $val->created_ats = date("Y-m-d", strtotime($val->created_at));
                $val->expense_amount = sprintf("%.2f", $val->expense_amount);//总金额
                $uncollectedMoney = (($val->expense_amount) - ($val->sum_money));//未收支
                $val->uncollectedMoney = sprintf("%.2f", $uncollectedMoney);//未收
                $end_period_at = strtotime(date("Y-m-d", strtotime($val->end_period_at)));//账期最后截止时间
                $days = round(($nowTime - $end_period_at) / 3600 / 24);//逾期天数
                $val->days = $days;//逾期天数，大于0逾期
                $val->linked_order_name = $val->order_id = $val->order_type = '';
                if (Q($val, 'financeOrder')) {
                    $financeOrder = Q($val, 'financeOrder');
                    if (isset($financeOrder[0]) && $financeOrder[0]->order_type == 1) {
                        $val->linked_order_name = '销售订单';
                        $val->order_type = 'sale';
                        $val->order_id = $financeOrder[0]->title;
                    } elseif (isset($financeOrder[0]) && $financeOrder[0]->order_type == 1) {
                        $val->linked_order_name = '采购订单';
                        $val->order_type = 'purchase';
                        $val->order_id = $financeOrder[0]->title;
                    }
                    unset($val->financeOrder);
                }
                $val->user_name = Q($val, 'users', 'chinese_name');
                unset($val->users);

            }
            $data = $list->toArray();
        }


        return $data;

    }

    //最新收益
    public function caiWuFlowLastest($search)
    {
        $latestRevenue = '0.00';
        $builder = CaiWuFlow::whereIn('department_id', $search['selectdepts'])
            ->where(['status' => 1, 'type' => 1, 'audit_status' => 1]);
        if (isset($search['create_begin']) && $search['create_begin']) {
            $create_begin = strtotime($search['create_begin']);
            $builder->where('create_time', '>=', $create_begin);
        }
        if (isset($search['create_end']) && $search['create_end']) {
            $create_end = strtotime($search['create_end']);
            $builder->where('create_time', '<=', $create_end);
        }
        $caiWuFlowLastest = $builder->orderBy('id', 'desc')->select('money')->first();
        if ($caiWuFlowLastest) {
            $latestRevenue = $caiWuFlowLastest->money;
        }
        return $latestRevenue;
    }

    public function financeDepartmentIndex(Request $request)
    {
        $search = $request->all();
        $is_join = $request->input('is_join');
        $time1 = $request->input('time1');
        $time2 = $request->input('time2');
        $selectdepts = $request->input('selectdepts', '0');
        $is_join == 1 ? 1 : 0;
        $user = Auth::user();
        // 获取部门
        $repository = app()->make(UsersRepository::class);
        $deps = $repository->getCurrentDept($user);
        $dep = $deps['id'];
        $selectdep = $selectdepts ? $selectdepts : $dep;
        $searchData = $this->searchData($search);
        $selectdepts=$selectdepts?$selectdepts:$dep;
        //账户流水
        $deptAccountFlows = $this->DeptAccountState($searchData);
        //应收应付
        $accountReceivablePayable = $this->accountReceivablePayable($searchData);
        //应收应付列表
        $accountReceivablePayableList = $this->accountReceivablePayableList($searchData);
        //财务头部统计
        $departmentStatistics = $this->departmentStatistics($searchData);
        //最新收入
        $caiWuFlowLastest = $this->caiWuFlowLastest($searchData);
        //获取利润排名
        $profitRanking=$this->profitRanking($searchData, $is_join, $selectdepts);


        $selfdept = Department::where('id', $selectdep)->select('id', 'name')->first();
        if ($is_join == 1) {
            $deptIds = $this->userRepository->getChild($selectdep);
            $deptIds = explode(',', $deptIds);
        } else {
            $deptIds = array($selectdep);
        }
        $thisdep = $this->userRepository->getChild($dep);
        $thisdep = explode(',', $thisdep);

        // 获取部门有问题，不能解决不了
        if (is_array($thisdep)) {
            $departments = Department::whereIn('id', $thisdep)->select('id', 'name')->get();
        } else {
            $departments = Department::where('id', $thisdep)->select('id', 'name')->get();
        }


        $depts = [];
        foreach ($departments as $department) {
            $depts[] = [
                'name' => $department['name'],
                'value' => $department['id'],
            ];
        }

        // 判断统计时间
        if ($time1 == '' && $time2 == '') {
            $dates = [];
        } elseif ($time1 != '' || $time2 == '') {
            $dates['time1'] = $time1;
        } else {
            $dates['time1'] = $time1;
            $dates['time2'] = $time2;
        }
        // 对内交易（收) => dnjjs type:1
        // 对内交易（支) => dnjyz type:2
        // 对外交易（收) => dwjys type:3
        // 对外交易（支）=> dwjyz type:4
        // 费用摊派        fytp  type:8
        // 资产价值        zcjz type:6
        // 分红支出        fhzc type:5
        // 单据状态        djzt type:7

        $dnjjs = $this->deptData($searchData, TransactionLog::FINANCIALS_DEPARTMENT_DNJYS_TYPE, $is_index = 1);// 对内交易（收)
        $dnjyz = $this->deptData($searchData, TransactionLog::FINANCIALS_DEPARTMENT_DNJYZ_TYPE, $is_index = 1);// 对内交易（支),
        $dwjys = $this->deptData($searchData, TransactionLog::FINANCIALS_DEPARTMENT_DWJYS_TYPE, $is_index = 1);// 对外交易（收)
        $dwjyz = $this->deptData($searchData, TransactionLog::FINANCIALS_DEPARTMENT_DWJYZ_TYPE, $is_index = 1);// 对外交易（支）
        $fytp = $this->deptData($searchData, TransactionLog::FINANCIALS_DEPARTMENT_FEE_BOOTH_TYPE, $is_index = 1);// 平摊
        $djzt = $this->deptData($searchData, TransactionLog::FINANCIALS_DEPARTMENT_DJZT_TYPE, $is_index = 1);// 单据状态

        $this->data = [
            'selfdept' => $selfdept,
            'lrpm' => $profitRanking,//利润排名
            'zfls' => $deptAccountFlows,//账户流水
            'deptIds' => $deptIds,
            'index' => [
                'dnjjs' => $dnjjs,// 对内交易（收)
                'dnjyz' => $dnjyz,// 对内交易（支),
                'dwjys' => $dwjys,// 对外交易（收)
                'dwjyz' => $dwjyz,// 对外交易（支）
                'fytp' => $fytp,// 平摊
                'zcjz' => $this->transactionLogTg([
                    'date' => $dates,
                    'type' => TransactionLog::FINANCIALS_DEPARTMENT_ZCJZ_TYPE,//资产价值
                    'dep' => $deptIds
                ]),
                'fhzc' => $this->transactionLogTg([
                    'date' => $dates,
                    'type' => TransactionLog::FINANCIALS_DEPARTMENT_FHZC_TYPE,// 分红支出
                    'dep' => $deptIds
                ]),
                'djzt' => $djzt,// 单据状态
                //经营收入
                'jysr' => sprintf("%.2f", $departmentStatistics['caiwuFlowClassReceived']),
                //经营成本
                'jycb' => sprintf("%.2f", $departmentStatistics['caiwuFlowClassBranch']),
                //累计收入
                'ljsy' => sprintf("%.2f", $departmentStatistics['accumulatedIncome']),
                //账户余额
                'zfye'=>sprintf("%.2f", $departmentStatistics['accountBalances']),

                //最新收入
                'zxsr' => $caiWuFlowLastest
            ],
            'departments' => $depts,
            'ysyf' => $accountReceivablePayable,

            'ysyflist' => $accountReceivablePayableList,
        ];
        return $this->returnApiJson();
    }

    public function transactionLoglrpmList($map)
    {
        $depts = $map['depts'];
        $model = new TransactionLog();

        $model->where('status_end_time', '!=', '');
        $d = [];
        $ds = [];
        // 循环部门
        // 获取所有部门
        $depgoIds = [];
        foreach ($depts as $data) {
            $deps = $this->userRepository->getChild($data->id);
            $deps = explode(',', $deps);
            $depgoIds = array_merge($depgoIds, $deps);
        }
        foreach ($depts as $data) {
            $modelTemp2 = $model;
            $modelTemp3 = $model;
            $deptIds = $this->userRepository->getChild($data->id);
            $deptIds = explode(',', $deptIds);


            $one = $modelTemp2->whereIn('department_id', $deptIds)->sum('amount');
            $all = $modelTemp3->whereIn('department_id', $depgoIds)->sum('amount');

            $d['dept_name'] = $data->name;
            $d['amount'] = sprintf("%.2f", $one / 100);
            if ($all != 0) {
                $d['percent'] = sprintf("%.2f", (int)($one / $all * 100));
            } else {
                $d['percent'] = 0;
            }

            $ds[] = $d;
        }
        usort($ds, [$this, 'arraySort']);
        return $ds;
    }

    public function arraySort($array1, $array2)
    {
        return $array1['amount'] < $array2['amount'];
    }

    public function transactionLogList($map)
    {
        $model = new TransactionLog();
        // has select time
        if (count($map['date']) == 2) {
            $data['count'] = 123;
            $dates = $map['date'];
            // time start
            $firstday = date("Y-m-01", strtotime($dates['time1']));
            // time end
            $lastdayT = date("Y-m-01", strtotime($dates['time2']));
            $lastday = date("Y-m-d", strtotime("$lastdayT +1 month -1 day"));

            $model = $model->where('status_start_time', '>=', $firstday)->where('status_start_time', '<', $lastday);
        } elseif (count($map['date']) == 1) {
            $d = strtotime($map['date']['time1']);
            $firstday = date("Y-m-01", $d);
            $lastday = date("Y-m-d", strtotime("$firstday +1 month -1 day"));
            $model = $model->where('status_start_time', '>=', $firstday)->where('status_start_time', '<', $lastday);
        } else {
            $d = time();
            $firstday = date("Y-m-01", $d);
            $lastday = date("Y-m-d", strtotime("$firstday +1 month -1 day"));
            $model = $model->where('status_start_time', '>=', $firstday)->where('status_start_time', '<', $lastday);
        }

        // 判断部门
        if (!is_array($map['dep'])) {
            $map['dep'] = (array)$map['dep'];
        }
        $model = $model->whereIn('department_id', $map['dep']);

        if (isset($map['is_more_department'])) {
            $where = ['is_more_department' => $map['is_more_department']];
            $model = $model->where($where);
        }

        if (isset($map['is_bill'])) {
            $where = ['is_bill' => $map['is_bill']];
            $model = $model->where($where);
        }
        $model = $model->where('status_end_time', '!=', '');
        $datas = $model->where('type', '!=', 0)->get();

        $all = $model->sum('amount');
        $d = [];
        $ds = [];
        foreach ($datas as &$data) {
            $one = $model->where('type', $data->type)->sum('amount');
            $d['type_name'] = $this->types[$data->type];
            $d['type'] = $data->type;
            $d['in_out'] = $data->in_out;
            $d['amount'] = sprintf("%.2f", $data->amount / 100);
            $d['percent'] = sprintf("%.2f", (int)($one / $all * 100));
            $ds[] = $d;
        }

        return $ds;
    }


    public function transactionLogTg($map)
    {
        $model = new TransactionLog();
        // has select time
        if (count($map['date']) == 2) {
            $data['count'] = 123;
            $dates = $map['date'];
            // time start
            $firstday = date("Y-m-01", strtotime($dates['time1']));
            // time end
            $lastdayT = date("Y-m-01", strtotime($dates['time2']));
            $lastday = date("Y-m-d", strtotime("$lastdayT +1 month -1 day"));

            $model = $model->where('status_start_time', '>=', $firstday)->where('status_start_time', '<', $lastday);
        } elseif (count($map['date']) == 1) {
            $d = strtotime($map['date']['time1']);
            $firstday = date("Y-m-01", $d);
            $lastday = date("Y-m-d", strtotime("$firstday +1 month -1 day"));
            $model = $model->where('status_start_time', '>=', $firstday)->where('status_start_time', '<', $lastday);
        } else {
            $d = time();
            $firstday = date("Y-m-01", $d);
            $lastday = date("Y-m-d", strtotime("$firstday +1 month -1 day"));
            $model = $model->where('status_start_time', '>=', $firstday)->where('status_start_time', '<', $lastday);
        }


        if (isset($map['type'])) {
            $where = ['type' => $map['type']];
            $model = $model->where($where);
        }
        // 判断部门
        if (!is_array($map['dep'])) {
            $map['dep'] = (array)$map['dep'];
        }
        $model->whereIn('department_id', $map['dep']);

        if (isset($map['is_more_department'])) {
            $where = ['is_more_department' => $map['is_more_department']];
            $model = $model->where($where);
        }

        if (isset($map['is_bill'])) {
            $where = ['is_bill' => $map['is_bill']];
            $model = $model->where($where);
        }

        $data['count'] = $model->count();
        $data['amount'] = sprintf("%.2f", $model->sum('amount') / 100);

        return $data;
    }

    public function transactionLogTotal($map)
    {
        $model = new TransactionLog();
        // has select time
        if (count($map['date']) == 2) {
            $data['count'] = 123;
            $dates = $map['date'];
            // time start
            $firstday = date("Y-m-01", strtotime($dates['time1']));
            // time end
            $lastdayT = date("Y-m-01", strtotime($dates['time2']));
            $lastday = date("Y-m-d", strtotime("$lastdayT +1 month -1 day"));

            $model = $model->where('status_start_time', '>=', $firstday)->where('status_start_time', '<', $lastday);
        } elseif (count($map['date']) == 1) {
            $d = strtotime($map['date']['time1']);
            $firstday = date("Y-m-01", $d);
            $lastday = date("Y-m-d", strtotime("$firstday +1 month -1 day"));
            $model = $model->where('status_start_time', '>=', $firstday)->where('status_start_time', '<', $lastday);
        } else {
            $d = time();
            $firstday = date("Y-m-01", $d);
            $lastday = date("Y-m-d", strtotime("$firstday +1 month -1 day"));
            $model = $model->where('status_start_time', '>=', $firstday)->where('status_start_time', '<', $lastday);
        }
        // 判断部门
        if (!is_array($map['dep'])) {
            $map['dep'] = (array)$map['dep'];
        }
        $model = $model->whereIn('department_id', $map['dep']);

        if (isset($map['jysr'])) {
            $model = $model->where('in_out', 1);
        }

        if (isset($map['jycb'])) {
            $model = $model->where('in_out', 2);
        }

        // 判断部门
        if (!is_array($map['dep'])) {
            $map['dep'] = (array)$map['dep'];
        }
        $model = $model->whereIn('department_id', $map['dep']);
        return sprintf("%.2f", $model->sum('amount') / 100);
    }

    public function transactionLogTotalAll($map)
    {
        $model = new TransactionLog();
        $in = $model->where('in_out', 1)->whereIn('department_id', $map['dep'])->sum('amount');

        $out = $model->where('in_out', 2)->whereIn('department_id', $map['dep'])->sum('amount');

        return sprintf("%.2f", ($in - $out) / 100);
    }

    public function transactionLogTotalysyf($map)
    {
        $model = new TransactionLog();
        // has select time
        if (count($map['date']) == 2) {
            $data['count'] = 123;
            $dates = $map['date'];
            // time start
            $firstday = date("Y-m-01", strtotime($dates['time1']));
            // time end
            $lastdayT = date("Y-m-01", strtotime($dates['time2']));
            $lastday = date("Y-m-d", strtotime("$lastdayT +1 month -1 day"));

            $model = $model->where('status_start_time', '>=', $firstday)->where('status_start_time', '<', $lastday);
        } elseif (count($map['date']) == 1) {
            $d = strtotime($map['date']['time1']);
            $firstday = date("Y-m-01", $d);
            $lastday = date("Y-m-d", strtotime("$firstday +1 month -1 day"));
            $model = $model->where('status_start_time', '>=', $firstday)->where('status_start_time', '<', $lastday);
        } else {
            $d = time();
            $firstday = date("Y-m-01", $d);
            $lastday = date("Y-m-d", strtotime("$firstday +1 month -1 day"));
            $model = $model->where('status_start_time', '>=', $firstday)->where('status_start_time', '<', $lastday);
        }

        // 判断部门
        if (!is_array($map['dep'])) {
            $map['dep'] = (array)$map['dep'];
        }

        $in = $model->whereIn('department_id', $map['dep'])->where('status_end_time', '!=', '')->where('in_out', 1)->sum('amount');
        $out = $model->whereIn('department_id', $map['dep'])->where('status_end_time', '!=', '')->where('in_out', 2)->sum('amount');

        return [
            'in' => sprintf("%.2f", ($in) / 100),
            'out' => sprintf("%.2f", ($out) / 100),
        ];
    }

    public function transactionLogTotalzhye($map)
    {
        $model = new TransactionLog();
        // has select time
        if (count($map['date']) == 2) {
            $data['count'] = 123;
            $dates = $map['date'];
            // time start
            $firstday = date("Y-m-01", strtotime($dates['time1']));
            // time end
            $lastdayT = date("Y-m-01", strtotime($dates['time2']));
            $lastday = date("Y-m-d", strtotime("$lastdayT +1 month -1 day"));

            $model->where('status_start_time', '>=', $firstday)->where('status_start_time', '<', $lastday);
        } elseif (count($map['date']) == 1) {
            $d = strtotime($map['date']['time1']);
            $firstday = date("Y-m-01", $d);
            $lastday = date("Y-m-d", strtotime("$firstday +1 month -1 day"));
            $model = $model->where('status_start_time', '>=', $firstday)->where('status_start_time', '<', $lastday);
        } else {
            $d = time();
            $firstday = date("Y-m-01", $d);
            $lastday = date("Y-m-d", strtotime("$firstday +1 month -1 day"));
            $model->where('status_start_time', '>=', $firstday)->where('status_start_time', '<', $lastday);
        }
        $users = $this->userRepository->getChildUsers($map['selectdep']);
        $data = [];
        foreach ($users as $user) {
            $modelTemp2 = clone $model;
            $modelTemp3 = clone $model;
            $in = $modelTemp2->where(['user_id' => $user['user_id'], 'in_out' => 1])->sum('amount');
            $out = $modelTemp3->where(['user_id' => $user['user_id'], 'in_out' => 2])->sum('amount');
//            $ua = UserAccount::where('user_id', $user['user_id'])->first();
            $data[] = [
                'user_name' => $user['chinese_name'],
                'in' => sprintf("%.2f", $in / 100),
                'out' => sprintf("%.2f", $out / 100),
                'account' => sprintf("%.2f", 0)
            ];
        }
        return $data;
    }

    public function transactionLogTotalysyflist($map)
    {
        $model = new TransactionLog();
        // has select time
        if (count($map['date']) == 2) {
            $data['count'] = 123;
            $dates = $map['date'];
            // time start
            $firstday = date("Y-m-01", strtotime($dates['time1']));
            // time end
            $lastdayT = date("Y-m-01", strtotime($dates['time2']));
            $lastday = date("Y-m-d", strtotime("$lastdayT +1 month -1 day"));

            $model->where('status_start_time', '>=', $firstday)->where('status_start_time', '<', $lastday);
        } elseif (count($map['date']) == 1) {
            $d = strtotime($map['date']['time1']);
            $firstday = date("Y-m-01", $d);
            $lastday = date("Y-m-d", strtotime("$firstday +1 month -1 day"));
            $model = $model->where('status_start_time', '>=', $firstday)->where('status_start_time', '<', $lastday);
        } else {
            $d = time();
            $firstday = date("Y-m-01", $d);
            $lastday = date("Y-m-d", strtotime("$firstday +1 month -1 day"));
            $model->where('status_start_time', '>=', $firstday)->where('status_start_time', '<', $lastday);
        }
        $users = $this->userRepository->getChildUsers($map['selectdep']);
        $data = [];
        foreach ($users as $user) {
            $modelTemp2 = clone $model;
            $modelTemp3 = clone $model;
            $in = $modelTemp2->where(['user_id' => $user['user_id'], 'in_out' => 1])->sum('amount');
            $out = $modelTemp3->where(['user_id' => $user['user_id'], 'in_out' => 2])->sum('amount');
//            $ua = UserAccount::where('user_id', $user['user_id'])->first();
            $data[] = [
                'user_name' => $user['chinese_name'],
                'in' => sprintf("%.2f", $in / 100),
                'out' => sprintf("%.2f", $out / 100),
                'account' => sprintf("%.2f", 0)
            ];
        }
        return $data;
    }

    public function transactionLogTotalzxsr($map)
    {
        $model = new TransactionLog();
        $d = time();
        if (isset($map['date'])) {
            $d = strtotime($map['date']);
        }

        // 判断部门
        if (!is_array($map['dep'])) {
            $map['dep'] = (array)$map['dep'];
        }
        $model = $model->whereIn('department_id', $map['dep']);
        // 判断部门
        if (!is_array($map['dep'])) {
            $map['dep'] = (array)$map['dep'];
        }

        $d = time();
        $firstday = date("Y-m-01", $d);
        $lastday = date("Y-m-d", strtotime("$firstday +1 month -1 day"));

        $in = TransactionLog::whereIn('type', [1, 3])->whereIn('department_id', $map['dep'])->where('status_start_time', '>=', $firstday)->where('status_start_time', '<', $lastday)->sum('amount');
        $out = TransactionLog::whereIn('type', [2, 4])->whereIn('department_id', $map['dep'])->where('status_start_time', '>=', $firstday)->where('status_start_time', '<', $lastday)->sum('amount');

        return sprintf("%.2f", ($in - $out) / 100);
    }

    /*
     * 获取部门财务-账号流水
     */
    public function DeptAccountState($search)
    {
        $data = [];
        //$search['selectdepts']=[]
        $builder = CaiWuFlow::whereIn('department_id', $search['selectdepts'])->where(['status' => 1, 'audit_status' => 1]);
        if (isset($search['create_begin']) && $search['create_begin']) {
            $create_begin = strtotime($search['create_begin']);
            $builder->where('create_time', '>=', $create_begin);
        }
        if (isset($search['create_end']) && $search['create_end']) {
            $create_end = strtotime($search['create_end']);
            $builder->where('create_time', '<=', $create_end);
        }
        $caiWuFlowLastest = $builder->with('getFlowClass')
            ->selectRaw('class_id,type,sum(money) as total_money')
            ->groupBy('class_id')->orderBy('id', 'desc')->get();
        if ($caiWuFlowLastest->toArray()) {
            $data = $caiWuFlowLastest->toArray();
        }

        return $data;
    }

    /**
     * 利润排名
     */
    public function profitRanking($search, $is_join, $selectdepts)
    {
        $depts=[];
        if ($is_join == 1) {//合
            $deptObj=Department::find($selectdepts);
            $deptIds = $this->userRepository->getChild($deptObj->parent_id);
            $totalDept = explode(',', $deptIds);//父级部门下面的所有部门，做统计百分比
            if($totalDept){
                unset($totalDept[0]);//去除父级本身
            }
            $depts=Department::where('parent_id',$deptObj->parent_id)->select('id','name as dept_name','parent_id')->get();
        }else{//分
            $depts=Department::where('parent_id',$selectdepts)->select('id','name as dept_name','parent_id')->get();
            $deptIds = $this->userRepository->getChild($selectdepts);
            $totalDept = explode(',', $deptIds);//父级部门下面的所有部门，做统计百分比
            if($totalDept){
                unset($totalDept[0]);//去除父级本身
            }

        }
        if($totalDept){
            $totalDept=$this->departmentStatistics($search,$totalDept);//获取总部门的利润
        }
        $depts=$depts->toArray();

        if($depts){
            foreach ($depts as $key=>&$val){
                    $val['profit']="0";
                    $dept_ids = $this->userRepository->getChild($val['id']);
                    $dept_ids = explode(',', $dept_ids);//获取当前部门id下的子集以级本身id
                    $profits=$this->departmentStatistics($search,$dept_ids);//获取当前部门id下的子集以级本身id的利润
                    $val['amount']=sprintf("%.2f", $profits['profit']);//dd($totalDept);
                    $val['percent']=$profits['profit']?intval($val['amount']/$totalDept['profit']*100):0.00;
                    $amount[$key]=$val['amount'];

            }
            array_multisort($amount,SORT_DESC,$depts);
        }

       return $depts;

    }





}
