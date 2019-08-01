<?php

namespace App\Repositories;

use App\Constant\ConstFile;
use App\Models\Department;
use App\Models\DepartUser;
use App\Models\Financial;
use App\Models\FinancialCustomer;
use App\Models\FinancialDetail;
use App\Models\FinancialLoanBill;
use App\Models\FinancialLog;
use App\Models\FinancialOrder;
use App\Models\FinancialPic;
use App\Models\Message\Message;
use App\Models\TransactionLog;
use App\Models\UserAccountRecord;
use App\Models\UsersDetailInfo;
use App\Models\Workflow\WorkflowRole;
use App\Models\Workflow\WorkflowRoleUser;
use App\Repositories\PAS\SaleOrderRepository;
use App\Repositories\PAS\SupplierRepository;
use App\UserAccount\AccountLog;
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

class FinanceRepository extends ParentRepository
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

    public function model()
    {
        return Entry::class;
    }

    public function __construct()
    {
        $this->userRepository = app()->make(UsersRepository::class);
        $this->rpcRepository = app()->make(RpcRepository::class);
        $this->saleRepository = app()->make(SaleOrderRepository::class);
        $this->users = Auth::user();
    }

    //我的财务首页
    public function financeIndex()
    {
        $user_id = Auth::id();
        $data1 = $this->getFinanceStatusData(Financial::FINANCIALS_STARUS_SUBMIT, $user_id);
        $data2 = $this->getFinanceStatusData(Financial::FINANCIALS_STARUS_CHECKING, $user_id);
        $data3 = $this->getFinanceStatusData(Financial::FINANCIALS_STARUS_CHECK_FINISH, $user_id);
        $data4 = $this->getFinanceStatusData(Financial::FINANCIALS_STARUS_PEDING, $user_id);
        $data5 = $this->getFinanceStatusData(Financial::FINANCIALS_STARUS_PENDING_BUDGET, $user_id);
        $data6 = $this->getFinanceStatusData(Financial::FINANCIALS_STARUS_RECEIVED_BUDGET, $user_id);
        $data7 = $this->getFinanceStatusData(Financial::FINANCIALS_STARUS_WAITING_INVOICE, $user_id);
        $data8 = $this->getFinanceStatusData(Financial::FINANCIALS_STARUS_FINISH, $user_id);
        $this->data = [
            'data1' => $data1,
            'data2' => $data2,
            'data3' => $data3,
            'data4' => $data4,
            'data5' => $data5,
            'data6' => $data6,
            'data7' => $data7,
            'data8' => $data8
        ];

        return $this->returnApiJson();

    }

    //详情明细
    public function companyDetail($id)
    {
        $id = (int)$id;
        if ($id < 0) {
            throw new Exception(sprintf('无效的流程ID:%s', $id));
        }

        $obj = [];
        $finance = Financial::find($id);
        if ($finance) {
            $primary_dept_path = WorkflowUserService::fetchUserPrimaryDeptPath(Q($finance, 'user_id'));
            $path = explode('/', $primary_dept_path);
            $finance->company_name = $path[0];
            array_shift($path);
            $finance->dept_name = join('/', $path);

            if ($finance->primary_dept) {
                $depts = Department::find($finance->primary_dept);
                $finance->primary_dept = Q($depts, 'name');
            }
            /*if ($finance->company_name) {
                $company = Department::find($finance->company_name);
                $finance->company_name = Q($company, 'name');
            }*/
            $finance->flow_name = Q($finance, 'getFlow', 'flow_name');
            $finance->flow_no = Q($finance, 'getFlow', 'flow_no');
            if ($finance->status == Financial::FINANCIALS_STARUS_SUBMIT) {
                $finance->status_name = '待审批';
            } elseif ($finance->status == Financial::FINANCIALS_STARUS_CHECKING) {
                $finance->status_name = '审批中';
            } elseif ($finance->status == Financial::FINANCIALS_STARUS_CHECK_FINISH) {
                $finance->status_name = '待入账';
            } elseif ($finance->status == Financial::FINANCIALS_STARUS_PENDING_BUDGET) {
                $finance->status_name = '待收支';
            } elseif ($finance->status == Financial::FINANCIALS_STARUS_RECEIVED_BUDGET) {
                $finance->status_name = '已收支';
            } elseif ($finance->status == Financial::FINANCIALS_STARUS_WAITING_INVOICE) {
                $finance->status_name = '待发票';
            } elseif ($finance->status == Financial::FINANCIALS_STARUS_FINISH) {
                $finance->status_name = '已完成';
            } elseif ($finance->status == Financial::FINANCIALS_STARUS_REJUEST) {
                $finance->status_name = '拒绝';
            } elseif ($finance->status == Financial::FINANCIALS_STARUS_CANCEL) {
                $finance->status_name = '撤销';
            }

            $finance->child_status = Q($finance, 'child_status') ? Q($finance, 'child_status') : 0;
            unset($finance->company);
            unset($finance->dept);
            $finance->applicant_chinese_name = Q($finance, 'users', 'chinese_name');
            $finance->current_unit_id='';
            //客户
            if($finance->unittype=='内部单位'){
                $unitUsers=User::findById($finance->current_unit);
                $finance->current_unit = Q($unitUsers,'chinese_name');
                $finance->current_unit_id = Q($unitUsers,'id');
            }else{
                if ($finance->current_unit) {
                    $customer = $this->rpcRepository->getCustomerById($finance->current_unit);
                    if ($customer) {
                        $finance->current_unit = $customer['cusname'];
                        $finance->current_unit_id = $customer['id'];
                    }
                }
            }

            //项目
            if ($finance->projects_id) {
                $miss = $this->rpcRepository->getProjectById($finance->projects_id);
                if ($miss) {
                    $finance->projects_id = $miss['title'];

                }
            }
            //关联订单
            $finance->order_id=$finance->linked_order_name='';
            if ($finance->linked_order != Financial::FINANCIALS_LINKED_ORDER_NO) {
                $finance->linked_order_name = '';
            }
            if(Q($finance,'financeOrder')->toArray()){
                $finances=Q($finance,'financeOrder');
                if($finances[0]->order_type==1){
                    $finance->linked_order_name='销售订单';

                    $finance->order_id=$finances[0]->saleOrder->order_sn;
                }

                unset($finance->financeOrder);
            }

            //借款单
            $finance->loan_bills=[];
            if ($finance->loan_bill) {
                $bills = FinancialLoanBill::where('financial_id',$finance->id)->with('getFinanceInfo')->get();
                if ($bills) {
                    $bills=$bills->toArray();
                    $finance->loan_bills=$bills;
                }
            } else {
                $finance->loan_bill_id = '';
            }

            $finance->expense_amount = sprintf("%.2f", $finance->expense_amount);
            $budget = $this->rpcRepository->getBudgetsById($finance->budget_id);//dd($budget);
            $finance->budget_title=isset($budget['title']) ? $budget['title'] : '';
            if ($finance->financeDetail) {
                foreach ($finance->financeDetail as &$val) {

                    if (is_numeric($val->projects_id)) {
                        $projects = $this->rpcRepository->getFlowCateName($val->projects_id);
                        if ($projects) {
                            $val->projects_id = isset($projects['name']) ? $projects['name'] : '';
                        }else{
                            $val->projects_id ='';
                        }
                    }
                }
                $finance->detail = $finance->financeDetail->toArray();
                unset($finance->financeDetail);

            }
            if ($finance->financePic) {
                $finance->pics = $finance->financePic->toArray();
                unset($finance->financePic);
            }

            $finance->processes = $this->fetchEntryProcess(Q($finance, 'getEntry'));
            unset($finance->financeDetail);
            unset($finance->getEntry);
            unset($finance->getFlow);
            unset($finance->users);
            $obj = $finance->toArray();
        }
        return $obj;

    }

    //改变财务状态
    public function changeStatus($id, $status)
    {
        $id = (int)$id;
        if ($id < 0) {
            throw new Exception(sprintf('无效的流程ID:%s', $id));
        }
        // return $status;
        try {
            DB::beginTransaction();

            $rt = Financial::where('id', $id)->update(['status' => $status]);
            if ($status == Financial::FINANCIALS_STARUS_PENDING_BUDGET) {
                $remarks = '财务主管点击待入账到待收支的操作';
            } elseif ($status == Financial::FINANCIALS_STARUS_RECEIVED_BUDGET) {
                $remarks = '会计点击待收支到已收支的操作';
                $this->insertAccountRecord($id);
            } elseif ($status == Financial::FINANCIALS_STARUS_WAITING_INVOICE) {
                $remarks = '出纳点击已收支到待发票的操作';
            } elseif ($status == Financial::FINANCIALS_STARUS_FINISH) {
                $remarks = '统计点击待发票到已完成的操作';
            } elseif ($status == Financial::FINANCIALS_STARUS_CANCEL) {
                $remarks = '撤销';
            }
            $financeLog = [
                'financial_id' => $id,
                'operator' => Auth::id(),
                'remarks' => $remarks
            ];
            FinancialLog::insertFinanceLog($financeLog);
            DB::commit();
            return [
                'code' => 200,
                'message' => '修改成功'
            ];


        } catch (\Exception $e) {
            DB::rollback();
            return [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ];

        }

    }
    //我的财务-交易记录
    public function insertAccountRecord($financial_id){
        $finance=Financial::find($financial_id);
        $flow_no = Q($finance, 'getFlow', 'flow_no');
        $balance= $finance->expense_amount;
        $title= $finance->title;
        switch ($flow_no) {
            case Entry::WORK_FLOW_NO_CAPITAL_FEE_EXPENSE:
                $account_type =UserAccountRecord::ACCOUNT_EXPENSE_ACCOUNT;//报销
                $type=1;
                break;
            case Entry::WORK_FLOW_NO_FINANCE_LOAN:
                $account_type =UserAccountRecord::ACCOUNT_BORROWING;//借款
                $type=1;
                break;
            case Entry::WORK_FLOW_NO_FINANCE_REPAYMENT:
                $account_type = UserAccountRecord::ACCOUNT_REIMBURSEMENT;//还款
                $type=0;
                break;
            case Entry::WORK_FLOW_NO_FINANCE_RECEIVABLES:
                $account_type = UserAccountRecord::ACCOUNT_COLLECTION;//收款
                break;
            default :
                $account_type = UserAccountRecord::ACCOUNT_PAY;//支付
                $type=0;
                $type=1;
                break;
        }
        $ret=
        $ret=app()->make(AccountRepository::class)->insertInfo($title,$type,$account_type,$balance);
        return $ret->toArray();

    }

    //数组改变财务状态
    public function changeStatusByIds($data)
    {
        // return $status;
        try {
            DB::beginTransaction();
            if($data){
                foreach ($data as $val){
                    unset($sql_data);
                    if(isset($val['status']) && $val['status']){
                        $sql_data['status'] = $val['status'];
                    }
                    if(isset($val['sum_money'])){
                        $sql_data['sum_money'] = $val['sum_money'];
                    }
                    if(isset($val['child_status'])){
                        $sql_data['child_status'] = $val['child_status'];
                    }
                    $rt = Financial::where('id', $val['financial_id'])->update($sql_data);

                }
            }

            DB::commit();
            return [
                'code' => 200,
                'message' => '修改成功'
            ];


        } catch (\Exception $e) {
            DB::rollback();
            return [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ];

        }

    }



    //通过状态获取不同的数量和金额
    public function getFinanceStatusData($status, $user_id)
    {
        $data = [];

        switch ($status) {
            case Financial::FINANCIALS_STARUS_CHECKING :
                $finaCount2 = Financial::where(['user_id' => $user_id, 'status' => $status])->count('id');
                $finaMoney2 = Financial::where(['user_id' => $user_id, 'status' => $status])->sum('expense_amount');
                $data = [
                    'finaTotalcount' => $finaCount2,
                    'finaTotalMoney' => sprintf("%.2f", $finaMoney2),
                    'status' => $status,
                    'statusName' => '审批中'
                ];
                break;

            case Financial::FINANCIALS_STARUS_CHECK_FINISH :
                $proc_entry = Proc::where(['user_id' => $user_id])
                    ->where(["status" => Proc::STATUS_PASSED])
                    ->whereHas('process', function ($q) {
                        // 第一步为申请步骤,所以此处不加载
                        $q->where('position', '<>', 0);
                    })
                    ->pluck('entry_id')->toArray();
                $procCount3 = Financial::whereIn('entry_id', $proc_entry)
                    ->count('id');
                $procMoney3 = Financial::whereIn('entry_id', $proc_entry)->sum('expense_amount');
                $finaCount3 = Financial::where(['user_id' => $user_id, 'status' => $status])->count('id');
                $finaMoney3 = Financial::where(['user_id' => $user_id, 'status' => $status])->sum('expense_amount');
                $finaTotalCount3 = $procCount3 + $finaCount3;
                $finaTotalMoney3 = $procMoney3 + $finaMoney3;
                $data = [
                    'finaTotalcount' => $finaTotalCount3,
                    'finaTotalMoney' => sprintf("%.2f", $finaTotalMoney3),
                    'status' => $status,
                    'statusName' => '审批完成'
                ];
                break;
            case Financial::FINANCIALS_STARUS_PEDING ://待入账-
                $finaCount4 = Financial::where(['user_id' => $user_id, 'status' => 3])->count('id');
                $finaMoney4 = Financial::where(['user_id' => $user_id, 'status' => 3])->sum('expense_amount');
                $finaTotalCount4 = $finaCount4;
                $finaTotalMoney4 = $finaMoney4;
                $data = [
                    'finaTotalcount' => $finaTotalCount4,
                    'finaTotalMoney' => sprintf("%.2f", $finaTotalMoney4),
                    'status' => $status,
                    'statusName' => '待入账'
                ];
                break;
            case Financial::FINANCIALS_STARUS_PENDING_BUDGET ://待收支

                $finaTotalCount5 = Financial::where(['user_id' => $user_id, 'status' => $status])->count('id');
                $finaTotalMoney5 = Financial::where(['user_id' => $user_id, 'status' => $status])->sum('expense_amount');

                $data = [
                    'finaTotalcount' => $finaTotalCount5,
                    'finaTotalMoney' => sprintf("%.2f", $finaTotalMoney5),
                    'status' => $status,
                    'statusName' => '待收支'
                ];
                break;
            case Financial::FINANCIALS_STARUS_RECEIVED_BUDGET ://已收支

                $finaTotalCount6 = Financial::where(['user_id' => $user_id, 'status' => $status])->count('id');
                $finaTotalMoney6 = Financial::where(['user_id' => $user_id, 'status' => $status])->sum('expense_amount');


                $data = [
                    'finaTotalcount' => $finaTotalCount6,
                    'finaTotalMoney' => sprintf("%.2f", $finaTotalMoney6),
                    'status' => $status,
                    'statusName' => '已收支'
                ];
                break;
            case Financial::FINANCIALS_STARUS_WAITING_INVOICE ://待发票
                $finaTotalCount7 = Financial::where(['user_id' => $user_id, 'status' => $status])->count('id');
                $finaTotalMoney7 = Financial::where(['user_id' => $user_id, 'status' => $status])->sum('expense_amount');
                $data = [
                    'finaTotalcount' => $finaTotalCount7,
                    'finaTotalMoney' => sprintf("%.2f", $finaTotalMoney7),
                    'status' => $status,
                    'statusName' => '待发票'
                ];
                break;
            case Financial::FINANCIALS_STARUS_FINISH ://已完成
                $finaTotalCount8 = Financial::where(['user_id' => $user_id, 'status' => $status])->count('id');
                $finaTotalMoney8 = Financial::where(['user_id' => $user_id, 'status' => $status])->sum('expense_amount');
                $data = [
                    'finaTotalcount' => $finaTotalCount8,
                    'finaTotalMoney' => sprintf("%.2f", $finaTotalMoney8),
                    'status' => $status,
                    'statusName' => '已完成'
                ];
                break;
            default :

                $proc_entry = Proc::where(['user_id' => $user_id])
                    ->where(["status" => Proc::STATUS_IN_HAND])->pluck('entry_id')->toArray();
                $procCount1 = Financial::whereIn('entry_id', $proc_entry)
                    ->count('id');
                $procMoney1 = Financial::whereIn('entry_id', $proc_entry)->sum('expense_amount');
                $finaCount1 = Financial::where(['user_id' => $user_id])
                    ->where(function ($query) use($status){
                        $query->where('status' , $status)->orWhere('status', -1);
                    })
                    ->count('id');
                $finaMoney1 = Financial::where(['user_id' => $user_id])
                    ->where(function ($query) use($status){
                        $query->where('status' , $status)->orWhere('status', -1);
                    })
                    ->sum('expense_amount');
                $finaTotalCount1 = $procCount1 + $finaCount1;
                $finaTotalMoney1 = $procMoney1 + $finaMoney1;
                $data = [
                    'finaTotalcount' => $finaTotalCount1,
                    'finaTotalMoney' => sprintf("%.2f", $finaTotalMoney1),
                    'status' => Financial::FINANCIALS_STARUS_SUBMIT,
                    'statusName' => '待审批'
                ];
        }
        return $data;

    }

    //处理我的财务列表数据
    public function fetchFinancials($financials,$is_deptment=1)
    {
        $data = [];
        $period_over_count = 0;//过期数
        $totalsMoney = 0;
        foreach ($financials as &$financial) {
            //流程名称
            $financial->flow_name = Q($financial, 'getFlow', 'flow_name');
            //流程编号
            $financial->flow_no = Q($financial, 'getFlow', 'flow_no');
            //用户中文名
            $financial->chinese_name = Q($financial, 'users', 'chinese_name');
            $nowTime = date('Y-m-d H:i:s');
            //$end_period_day = ($nowTime - $end_period) / 86400;
            //逾期数
            $period_over = Financial::where('entry_id', $financial->entry_id)
                ->where('end_period_at', '<', $nowTime)
                ->count();
            $period_over_count += $period_over;
            $totalsMoney += Q($financial, 'expense_amount');
            //计算总金额
            $projects['money'] = Q($financial, 'expense_amount');
            $projects['money'] = sprintf("%.2f", $projects['money']);

            if (Q($financial, 'financeDetail')) {
                $financeDetail = $financial->financeDetail->toArray();
                //报销类别
                if ($financeDetail[0]['projects_id']) {//
                    $projec_name = $this->rpcRepository->getFlowCateName($financeDetail[0]['projects_id']);
                    $projects['project_name'] = isset($projec_name['name']) ? $projec_name['name'] : '';
                }

                unset($financial->getEntry);
            }
            $financial->projects = $projects;
            // STATUS_IN_HAND
            $auditor = $financial->getEntry->getCurrentStepProcs()->toArray();
            $auditor = array_values($auditor);
            $auditors = Proc::where('entry_id', $financial->entry_id)
                ->where('status', Proc::STATUS_PASSED)
                ->where('user_id', Auth::id())
                ->orderBy('id', 'name')
                ->select('user_name', 'id', 'status')
                ->first();
                if ($financial->user_id == Auth::id() || $is_deptment==2) {
                    $financial->is_auditor = 0;
                    $auditor_name = isset($auditor[0]['user_name']) ? $auditor[0]['user_name'] : Q($auditors, 'user_name');
                    if ($financial->status == Financial::FINANCIALS_STARUS_SUBMIT || $financial->status == Financial::FINANCIALS_STARUS_CHECKING) {
                        $financial->status_name = '等待' . $auditor_name . '审批';
                    } elseif ($financial->status == Financial::FINANCIALS_STARUS_CHECK_FINISH) {
                        $financial->status_name = '批复结束';
                    } elseif ($financial->status == Financial::FINANCIALS_STARUS_PEDING) {

                        $financial->status_name = '待入账';
                    } elseif ($financial->status == Financial::FINANCIALS_STARUS_PENDING_BUDGET) {

                        $financial->status_name = '待收支';
                    } elseif ($financial->status == Financial::FINANCIALS_STARUS_RECEIVED_BUDGET) {
                        $financial->status_name = '待已收支';
                    } elseif ($financial->status == Financial::FINANCIALS_STARUS_WAITING_INVOICE) {

                        $financial->status_name = '待发票';
                    } elseif($financial->status == Financial::FINANCIALS_STARUS_FINISH) {
                        $financial->status_name = '已完成';
                    }elseif($financial->status == Financial::FINANCIALS_STARUS_REJUEST) {
                        $financial->status_name = '拒绝';
                    }

                    $financial->url = Q($financial, 'entry_id');

                } else {

                    $financial->is_auditor = 1;
                    if ($financial->status == Financial::FINANCIALS_STARUS_SUBMIT) {
                        $financial->status_name = '待处理';
                        if ($auditor) {
                            $auditor_name = $auditor[0]['user_name'];
                            $url_id = $auditor[0]['id'];
                        } else {
                            $financial->status_name = '已处理';
                            $auditor_name = Q($auditors, 'user_name');
                            $url_id = Q($auditors, 'id');
                        }

                    } else {
                        if ($auditors) {
                            $financial->status_name = '已批复';
                            $auditor_name = Q($auditors, 'user_name');
                            $url_id = Q($auditors, 'id');
                        } else {
                            $financial->status_name = '待处理';
                            $auditor_name =$auditor? $auditor[0]['user_name']:'';
                            $url_id = $auditor?$auditor[0]['id']:'';
                        }

                        //$auditor_content=$auditor->content;
                    }

                    $financial->auditor_name = $auditor_name;
                    $financial->url = $url_id;
                }


            unset($financial->getFlow);
            unset($financial->users);
            unset($financial->getEntry);
            unset($financial->financeDetail);

        }

        if ($financials) {
            $data = $financials->toArray();
            $data['period_over_count'] = $period_over_count;
            $data['totalsMoney'] = sprintf("%.2f", $totalsMoney);
        } else {
            $data = [];
        }

        return $data;
    }

    // 我的财务列表
    public function fetchToDo()
    {
        try {
            $user_id = Auth::id();
            $searchData = \Request::all();
            $list = $this->getStatusList($searchData, $user_id);//不同状态获取不同的列表
            $data = $this->fetchFinancials($list);//对数据进一步处理
            $this->data = ['entries' => $data];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
        }

        return $this->returnApiJson();
    }
    //不同状态获取不同的列表
    public function getStatusList($data, $user_id)
    {
        $list = [];

        switch ($data['status']) {
            case  Financial::FINANCIALS_STARUS_CHECKING :
                $builder = Financial::where(['user_id' => $user_id, 'status' => $data['status']]);
                break;
            case Financial::FINANCIALS_STARUS_CHECK_FINISH :
                $proc_entry = Proc::where(['user_id' => $user_id])
                    ->where(["status" => Proc::STATUS_PASSED])
                    ->whereHas('process', function ($q) {
                        // 第一步为申请步骤,所以此处不加载
                        $q->where('position', '<>', 0);
                    })
                    ->pluck('entry_id')->toArray();

                $entry_ids = Financial::where(['user_id' => $user_id, 'status' => $data['status']])->pluck('entry_id')->toArray();
                $entry_ids = array_merge($entry_ids, $proc_entry);
                $builder = Financial::whereIn('entry_id', $entry_ids);

                break;
            case Financial::FINANCIALS_STARUS_PEDING ://待入账

                //普通员工
                $builder = Financial::where(['user_id' => $user_id, 'status' => Financial::FINANCIALS_STARUS_CHECK_FINISH]);

                break;
            case Financial::FINANCIALS_STARUS_PENDING_BUDGET ://待收支
                $builder = Financial::where(['user_id' => $user_id, 'status' => $data['status']]);
                break;
            case Financial::FINANCIALS_STARUS_RECEIVED_BUDGET ://已收支

                $builder = Financial::where(['user_id' => $user_id, 'status' => $data['status']]);

                break;
            case Financial::FINANCIALS_STARUS_WAITING_INVOICE ://待发票

                $builder = Financial::where(['user_id' => $user_id, 'status' => $data['status']]);

                break;
            case Financial::FINANCIALS_STARUS_FINISH ://已完成
                $builder = Financial::where(['user_id' => $user_id, 'status' => $data['status']]);
                break;
            default :
                $proc_entry = Proc::where(['user_id' => $user_id])
                    ->where(["status" => Proc::STATUS_IN_HAND])->pluck('entry_id')->toArray();
                $entry_ids = Financial::where(['user_id' => $user_id])
                    ->where(function ($query) use($data){
                        $query->where('status' , $data['status'])->orWhere('status', -1);
                    })->pluck('entry_id')->toArray();
                $entry_ids = array_merge($entry_ids, $proc_entry);
                $builder = Financial::whereIn('entry_id', $entry_ids)
                    ->with('getFlow', 'getEntry', 'financeDetail');
        }
        if (isset($data['create_begin']) && $data['create_begin']) {
            $builder->where('created_at', '>=', $data['create_begin'] . " 00:00:00");
        }
        if (isset($data['create_end']) && $data['create_end']) {
            $builder->where('created_at', '<=', $data['create_end'] . " 23:59:59");
        }
        $list = $builder->with('getFlow', 'getEntry', 'financeDetail','loan_bill.getFinanceInfo')
            ->select('id', 'flow_id', 'user_id', 'status','transaction','fee_booth', 'expense_amount', 'created_at', 'title', 'entry_id', 'status', 'account_period','sum_money','loan_bill_id')
            ->where('status','>=',-1)->orderBy('id', 'desc')->paginate(10);
        return $list;

    }

    //考勤相关列表
    public function fetchWorkflowList(Request $request)
    {
        $type = $request->input('type', '');

        try {
            $canSeeFlowIds = WorkflowUserService::fetchUserCanSeeWorkflowIds(Auth::id());
            $canSeeFlowIds = $canSeeFlowIds->toArray();
            if ($type) {
                $types = FlowType::with('publish_flow')->where('type_name', '考勤相关')->get();
            } else {
                $types = FlowType::with('publish_flow')->where('type_name', '财务相关')->get();
            }


            if (empty($types) || empty($canSeeFlowIds)) {
                return $this->returnApiJson();
            }

            $this->data = $temp = [];
            foreach ($types as $type) {
                foreach ($type->valid_flow as $flow) {
                    if (in_array($flow->id, $canSeeFlowIds)) {
                        $temp['flow_no'] = $flow->flow_no;
                        $temp['id'] = $flow->id;
                        $temp['name'] = $flow->flow_name;
                        $temp['url'] = route('api.finance.flow.create', ['flow_id' => $flow->id]);
                        $this->data[] = $temp;
                    }
                }
            }
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
        }
        return $this->returnApiJson();
    }
    //通过预算单id获取类别名称
    public function projectsByBudgetsId(Request $request)
    {
        $domain = config('app.rpc_local_domain') . '/hprose/cost/start';
        $client = new Client($domain, false);
        try {
            $budgetsId = $request->get('id', '');
            $budgetsId = (int)$budgetsId;
            if ($budgetsId < 0) {
                throw new Exception('预算单id不能为空');
            }
            $projects = [];
            $project = [];
            $projects = $client->getBudgetsItemsByBudgetsId($budgetsId);
            if ($projects) {
                foreach ($projects as $key => $val) {
                    unset($val['setting']);
                    $project[$key]['key'] = $val['fe_flow_class_id'];
                    $project[$key]['value'] = $val['categort_title'];
                }
            }
            $this->data = [
                'projects' => $project,
            ];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
        }
        return $this->returnApiJson();
    }
    //通过类别id获取显示条件
    public function getBudgetsItemId(Request $request)
    {

        $domain = config('app.rpc_local_domain') . '/hprose/cost/start';
        $client = new Client($domain, false);
        $items = [];
        try {
            $projectId = $request->get('id', '');
            $projectId = (int)$projectId;
            if ($projectId < 0) {
                throw new Exception('项目id不能为空');
            }
            $projectsItems = [];
            $conItems = [];
            $projectsItems = $client->getBudgetsItemById($projectId);
            $is_control=0;
            if ($projectsItems) {
                if (isset($projectsItems['setting']) && $projectsItems['setting']) {
                    $conItems = $projectsItems['setting'];

                    if ($conItems) {
                        foreach ($conItems as &$val) {
                            $val['title'] = isset($val['con_info'][0]) ? $val['con_info'][0] : '';
                            $is_control=$val['is_control'];
                        }

                    }
                }
            }

            $this->data = [
                'projectsItems' => $conItems,
                'is_control'=>$is_control
            ];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
        }
        return $this->returnApiJson();
    }

    //创建
    public function createWorkflow(Request $request)
    {
        try {
            $canSeeFlowIds = WorkflowUserService::fetchUserCanSeeWorkflowIds(Auth::id());
            $flow_id = $request->get('flow_id', 0);
            $flow_id = (int)$flow_id;
            if ($flow_id < 0) {
                throw new Exception(sprintf('无效的流程ID:%s', $flow_id));
            }

            if (!$canSeeFlowIds->contains($flow_id)) {
                throw new Exception('当前流程不可用');
            }

            $flow = Flow::publish()->findOrFail($flow_id);
            $users = Auth::user();
            $user_id=$users->id;
            $apply_basic_info=[];
            Workflow::generateHtml($flow->template, null, null, $user_id);

            $apply_basic_info=$this->deptPlan($users);
            $domain = config('app.rpc_local_domain') . '/hprose/cost/start';
            $client = new Client($domain, false);
            $departBudets = $client->getBudgetsByDeptId($apply_basic_info['user_primary_dept_id']);
            if (!$departBudets) {
                throw new Exception('部门预算单不能为空');
            }
            $departBudet = [];
            if ($departBudets) {
                foreach ($departBudets as $k1 => $v1) {
                    $exp=$v1['exp']?$v1['exp']:"0.00";
                    $departBudet[$k1]['key'] = $v1['id'];
                    $departBudet[$k1]['value'] = $v1['title'];
                    $departBudet[$k1]['total'] =sprintf("%.2f",($v1['inc']-$v1['exp'])) ;
                    $departBudet[$k1]['is_over'] = $v1['is_over'];
                }
            }
            $projects = [];
            $project = [];
            $projectsItems = [];
            $tempItem = [];
            $is_control=0;
            if (count($departBudets) > 0) {
                $projects = $client->getBudgetsItemsByBudgetsId($departBudets[0]['id']);
                /* if (!$projects) {
                     throw new Exception('报销项目不能为空');
                 }*/
                $is_control=0;
                if (count($projects) > 0) {
                    foreach ($projects as $key => $val) {
                        $project[$key]['key'] = $val['fe_flow_class_id'];
                        $project[$key]['value'] = isset($val['categort_title']) ? $val['categort_title'] : '';
                    }

                    $projectsItems = $client->getBudgetsItemById($projects[0]['id']);
                    if (isset($projectsItems['setting']) && $projectsItems['setting']) {

                        $tempItem = $projectsItems['setting'];
                        if ($tempItem) {
                            foreach ($tempItem as &$val) {
                                $val['title'] = isset($val['con_info'][0]) ? $val['con_info'][0] : '';
                                $is_control=$val['is_control'];
                            }

                        }

                    }

                }

            }
            $apply_basic_info['departBudet'] = $departBudet;//预算单列表
            $apply_basic_info['projectsItems'] = $tempItem;//类别条件
            $apply_basic_info['projects'] = $project;//类别名称列表
            $apply_basic_info['is_control']=$is_control;

            $flow_loan_id = Flow::findByFlowNo('finance_loan')->id;
            $loan_bill_list = Financial::where([
                'status' => Financial::FINANCIALS_STARUS_FINISH,
                'user_id' => $users->id,
                'flow_id' => $flow_loan_id
            ])->where('expense_amount','>',0)->select('id', 'title')->get()->toArray();
            $loan_bill_Money = 0;//借款单金额
            $loan_bill_Money = Financial::where([
                'status' => Financial::FINANCIALS_STARUS_FINISH,
                'user_id' => $users->id,
                'flow_id' => $flow_loan_id
            ])->sum('expense_amount');
            $apply_basic_info['loan_bill_Money'] = $loan_bill_Money;
            $apply_basic_info['loan_bill_list'] = $loan_bill_list;//借款单列表
            $apply_basic_info['pay_list'] = [
                '支付宝',
                '微信',
                '银行卡'
            ];

            $userinfo=[];
            if($users->detail){
                $userinfo = $users->detail->toArray();
            }

            $userinfos['branch_bank'] = isset($userinfo['bank_card']) ? decrypt($userinfo['bank_card']) : '';
            $userinfos['alipay_account'] = isset($userinfo['alipay_account']) ? decrypt($userinfo['alipay_account']) : '';
            $userinfos['wechat_account'] = isset($userinfo['wechat_account']) ? decrypt($userinfo['alipay_account']) : '';
            $userinfos['bank'] = isset($userinfo['bank']) ? $userinfo['bank'] : '';

            $organize = $this->userRepository->getAllDept($dept_id = '', $users);//付款组织列表
            $apply_basic_info['pay_account'] = $userinfos;
            if (count($organize) <= 3) {
                $organize = [$organize];
            }
            $apply_basic_info['organize'] = $organize;

            $defaultTitle = $flow->flow_name . (new Carbon())->toDateString();
            $templates = $flow->template->toArray();
            foreach ($templates['template_form'] as $key => &$val) {
                if ($val['field'] == 'budget') {
                    $val['field_value'] = isset($departBudet) ? $departBudet : '';
                }
                if ($val['field'] == 'expense_reimburse_details') {
                    $val['field_type'] = 'expense_reimburse_details';
                    $val['field_value'] = 'tpl[expense_reimburse_details][money],tpl[expense_reimburse_details][projects],tpl[expense_reimburse_details][reason],tpl[expense_reimburse_details][other_num],tpl[expense_reimburse_details][nums]';
                }
            }
            $flowData = [
                'flow_no' => Q($flow, 'flow_no'),
                'flow_name' => Q($flow, 'flow_name'),
            ];

            $this->data = [
                'flow' => $templates,
                'user_id' => $user_id,
                'apply_basic_info' => $apply_basic_info,
                'default_title' => $defaultTitle
            ];
        } catch (Exception $e) {

            $this->message = $e->getMessage();
            $this->code = $e->getCode();
        }
        return $this->returnApiJson();
    }

    /**
     * @param $dept_id
     * @return Department[]|array
     */
    public function getFinanceDept($dept_id)
    {
        $departs = $this->userRepository->getChild($dept_id);
        $departs = explode(',', $departs);
        unset($departs[0]);
        $depatlist = Department::whereIn('id', $departs)->select('id', 'name')->get();
        if ($depatlist) {
            $depatlist = $depatlist->toArray();
        } else {
            $depatlist = 0;
        }
        return $depatlist;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function storeWorkflow(Request $request)
    {

        return $this->updateFlow($request, 0);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param                          $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateFlow(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $flow_id = $request->get('flow_id');
            $flow = Flow::findById($flow_id);
            $flow->checkCanApply(); // 校验是否可以提交该流程的申请,不可提交申请的话,创建或者修改申请单,也没有意义

            $entry = $this->updateOrCreateEntry($request, $id); // 创建或更新申请单

            if ($entry->isInHand()) {
                $flow_link = Flowlink::firstStepLink($entry->flow_id);
                //进程初始化
                (new Workflow())->setFirstProcessAuditor($entry, $flow_link);
            }
            $entry->save();
            DB::commit();
            unset($entry->flow);
            unset($entry->user);
            unset($entry->procs);

            $this->data = ['entry' => $entry->toArray()];
        } catch (Exception $e) {
            DB::rollback();
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
            Workflow::errLog('EntryUpdate', $e->getMessage() . $e->getTraceAsString());
        }
        return $this->returnApiJson();
    }

    /**
     * 更新或插入申请单
     *
     * @param Request $request
     * @param         $id
     *
     * @return Entry
     * @author hurs
     */
    private function updateOrCreateEntry(Request $request, $id = 0)
    {
        $data = $request->all();
        $data['tpl']['expense_reimburse_details'] = json_encode($data['tpl']['expense_reimburse_details']);
        if (isset($data['tpl']['file_upload']) && $data['tpl']['file_upload']) {
            $data['tpl']['file_upload'] = json_encode($data['tpl']['file_upload']);
        } else {
            $data['tpl']['file_upload'] = '';
        }
        //dd($data['flow_id']);
        $type_code = '';
        $flow = Flow::findById($data['flow_id']);
        if (Q($flow, 'flow_no') == Entry::WORK_FLOW_NO_CAPITAL_FEE_EXPENSE) {
            $type_code = 'BX';
        } elseif (Q($flow, 'flow_no') == Entry::WORK_FLOW_NO_FINANCE_LOAN) {
            $type_code = 'JK';
        } elseif (Q($flow, 'flow_no') == Entry::WORK_FLOW_NO_FINANCE_PAYMENT) {
            $type_code = 'ZF';
        } elseif (Q($flow, 'flow_no') == Entry::WORK_FLOW_NO_FINANCE_REPAYMENT) {
            $type_code = 'HK';
        } elseif (Q($flow, 'flow_no') == Entry::WORK_FLOW_NO_FINANCE_RECEIVABLES) {
            $type_code = 'SK';
        }

        $data['tpl']['code'] = getCode($type_code);
        if (isset($data['id']) && $data['id']) {
            $id = $data['id'];
        }
        if (!$id) {
            $authApplyer = new AuthUserShadowService(); // 以影子用户作为申请人
            $entry = Entry::create([
                'title' => $data['title'],
                'flow_id' => $data['flow_id'],
                'user_id' => $authApplyer->id(),
                'circle' => 1,
                'status' => Entry::STATUS_IN_HAND,
                'origin_auth_id' => Auth::id(),
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

        $data['entry_id'] = $entry->id;
        $data['user_id'] = $entry->user_id;
        $data['applicant_chinese_name'] = Auth::user()->chinese_name;
        //$data['primary_dept'] = Q(Auth::user(), 'primaryDepartUser', 'department_id');
        $this->updateFinance($data);

        return $entry;
    }


    private function updateTpl(Entry $entry, $tpl = [])
    {
        foreach ($tpl as $k => $v) {
            $val = is_array($v) ? json_encode($v) : $v;
            $val = $val === null ? '' : $val;
            EntryData::updateOrCreate(['entry_id' => $entry->id, 'field_name' => $k], [
                'flow_id' => $entry->flow_id,
                'field_value' => $val,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ]);
        }
    }

    //初次提交报销单时插入到财务表
    private function updateFinance($data = [])
    {
        try {
            DB::transaction(function () use ($data) {
                $company_account = '';
                $end_period_at = date("Y-m-d H:i:s", strtotime("+" . $data['tpl']['account_period'] . ' days'));
                if (strpos($data['tpl']['account_number'], '-') === false) {
                    $account_number = $data['tpl']['account_number'];
                } else {
                    $account_number = explode('-', $data['tpl']['account_number']);
                    $company_account = $account_number[0];
                    $account_number = $account_number[1];
                }

                $financeData = [
                    'code' => $data['tpl']['code'],
                    'flow_id' => $data['flow_id'],
                    'title' => $data['title'],
                    'user_id' => $data['user_id'],
                    'entry_id' => $data['entry_id'],
                    'endtime' => isset($data['tpl']['endtime']) ? $data['tpl']['endtime'] : '',
                    'company_id' => isset($data['tpl']['company_id']) ? $data['tpl']['company_id'] : '',
                    'loan_bill_id' => isset($data['tpl']['loan_bill_id']) ? $data['tpl']['loan_bill_id'] : '',
                    'projects_id' => isset($data['tpl']['projects_id']) ? $data['tpl']['projects_id'] : '',
                    'primary_dept' => $data['tpl']['primary_dept'],
                    'status' => Financial::FINANCIALS_STARUS_SUBMIT,
                    'budget_id' => isset($data['tpl']['budget']) ? $data['tpl']['budget'] : '',
                    'expense_amount' => $data['tpl']['expense_amount'],
                    'account_type' => $data['tpl']['account_type'],
                    'account_number' => $account_number,
                    'account_period' => $data['tpl']['account_period'],
                    'end_period_at' => $end_period_at,
                    'applicant_chinese_name' => $data['applicant_chinese_name'],
                    'unittype' => isset($data['tpl']['unittype']) ? $data['tpl']['unittype'] : '',
                    'current_unit' => isset($data['tpl']['current_unit']) ? $data['tpl']['current_unit'] : '',
                    'transaction' => isset($data['tpl']['transaction']) ? $data['tpl']['transaction'] : '',
                    'loan_bill' => isset($data['tpl']['loan_bill']) ? $data['tpl']['loan_bill'] : 2,
                    'fee_booth' => isset($data['tpl']['fee_booth']) ? $data['tpl']['fee_booth'] : 2,
                    'associated_projects' => isset($data['tpl']['associated_projects'])?$data['tpl']['associated_projects']:2,
                    'linked_order' => isset($data['tpl']['linked_order']) ? $data['tpl']['linked_order'] : 2,
                    'bank' => isset($data['tpl']['bank']) ? $data['tpl']['bank'] : '',
                    'bank_name' => isset($data['tpl']['bank_name']) ? $data['tpl']['bank_name'] : '',
                    'bank_address' => isset($data['tpl']['bank_address']) ? $data['tpl']['bank_address'] : '',
                    'company_account' => isset($data['tpl']['company_account']) ? $data['tpl']['company_account'] : $company_account,
                ];

                $fileDatas = json_decode($data['tpl']['file_upload'], true);
                $financeDetails = json_decode($data['tpl']['expense_reimburse_details'], true);
                if(isset($data['financial_id']) && $data['financial_id']){//更新
                    $financial_id=$data['financial_id'];
                    Financial::where('id',$financial_id)->update($financeData);//更新财务
                    FinancialOrder::where('financial_id',$financial_id)->delete();//删除财务订单
                    FinancialLoanBill::where('financial_id',$financial_id)->delete();//删除关联借款单
                    FinancialDetail::where('financial_id',$financial_id)->delete();//删除财务明细
                    FinancialPic::where('financial_id',$financial_id)->delete();//删除财务订单

                }else{//新建
                    $res = Financial::create($financeData);
                    $financial_id=$res->id;
                }

                if (isset($data['tpl']['order_type']) && isset($data['tpl']['order_id']) && $data['tpl']['order_id']) {
                    $orderData = [
                        'financial_id' => $financial_id,
                        'order_type' => isset($data['tpl']['order_type']) ? $data['tpl']['order_type'] : '',
                        'title' => isset($data['tpl']['order_id']) ? $data['tpl']['order_id'] : '',
                    ];
                    FinancialOrder::create($orderData);
                }
                //抵充多个借款单
                if (isset($data['tpl']['bill_ids']) &&  $data['tpl']['bill_ids']) {
                    foreach ($data['tpl']['bill_ids'] as $v5){
                        $billData = [
                            'financial_id' => $financial_id,
                            'loan_bill_id' => $v5,
                        ];
                        FinancialLoanBill::create($billData);
                    }

                }
                //明细
                $detailDeatail = Collect($financeDetails)->each(function ($item, $key) use ($financial_id) {

                    FinancialDetail::create([
                            'financial_id' => $financial_id,
                            'limit_price'=>isset($item['limit_price']) ? $item['limit_price'] : '',
                            'is_control'=>isset($item['is_control']) ? $item['is_control'] : '',
                            'money' => $item['money'],
                            'money' => $item['money'],
                            'repayment_date' => isset($item['repayment_date']) ? $item['repayment_date'] : '',
                            'projects_id' => $item['projects'],
                            'projects_condition' => isset($item['projectsItems']) ? json_encode($item['projectsItems']) : '',
                            'reason' => $item['reason'],
                        ]
                    );

                });
                if ($fileDatas) {
                    if (isset($fileDatas['pic1']) && $fileDatas['pic1']) {
                        foreach ($fileDatas['pic1'] as $v1) {
                            FinancialPic::create([
                                    'financial_id' => $financial_id,
                                    'pic_type' => FinancialPic::FINANCIALS_PIC_TYPE_PIC,
                                    'pic_url' => $v1,
                                ]
                            );
                        }

                    }

                    if (isset($fileDatas['pic2']) && $fileDatas['pic2']) {
                        foreach ($fileDatas['pic2'] as $v2) {
                            FinancialPic::create([
                                    'financial_id' => $financial_id,
                                    'pic_type' => FinancialPic::FINANCIALS_PIC_TYPE_FILE,
                                    'pic_url' => $v2,
                                ]
                            );
                        }
                    }
                    if (isset($fileDatas['pic3']) && $fileDatas['pic3']) {
                        foreach ($fileDatas['pic3'] as $v3) {
                            FinancialPic::create([
                                    'financial_id' => $financial_id,
                                    'pic_type' => FinancialPic::FINANCIALS_PIC_TYPE_BILL,
                                    'pic_url' => $v3,
                                ]
                            );
                        }
                    }
                }
            });
        } catch (Exception $e) {
            return returnJson($e->getMessage(), $e->getCode());
        }


    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function editWorkflow(Request $request)
    {
        $domain = config('app.rpc_local_domain') . '/hprose/cost/start';
        $client = new Client($domain, false);
        try {
            $id = $request->get('id');
            $authAuditor = new AuthUserShadowService();
            $entry = Entry::findUserEntry($authAuditor->id(), $id);
            $entryData = $entry->entry_data->toArray();
            unset($entry->entry_data);
            $entryData = array_values($entryData);
            foreach ($entryData as $key => $val) {
                if (in_array($val['field_name'], ['account_type', 'unittype', 'current_unit', 'file_upload', 'expense_reimburse_details'])) {
                    $entryData[$key]['field_value'] = json_decode($val['field_value'], true);
                }
            }
            $this->data = [
                'entry' => $entry,
                'entryData' => $entryData,
            ];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
        }
        return $this->returnApiJson();
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function workflowShow(Request $request)
    {
        try {
            $id = $request->get('id');
            $authAuditor = new AuthUserShadowService();
            $apply_basic_info = Workflow::getApplyerBasicInfo(null, null);//dd($apply_basic_info);
            $entry = Entry::findUserEntry($authAuditor->id(), $id);
            $procs_id = '';
            $procs = Proc::where(['entry_id' =>Q($entry,'id'), 'user_id' => $authAuditor->id()])->first();
            $procs_id = Q($procs, 'id');

            if (!$entry) {
                $this->message = '您没有访问权限!';
                $this->code = ConstFile::API_RESPONSE_FAIL;
                return $this->returnApiJson();
            }

            $receiver_id = '';

            $flow_no = Q($entry, 'flow', 'flow_no');
            $flow_name = Q($entry, 'flow', 'flow_name');
            $finance = Q($entry, 'finance');
            $entryData = [];
            if ($finance) {
                $entryData = $this->companyDetail(Q($finance, 'id'));
            }

            $is_cancel = Proc::where('entry_id', $id)->where(["status" => Proc::STATUS_PASSED])
                ->whereHas('process', function ($q) {
                    // 第一步为申请步骤,所以此处不加载
                    $q->where('position', '<>', 0);
                })->count();
            //$is_cancel

            $this->data = [
                'is_auditor' => 0,
                'is_cancel' => $is_cancel,
                'procs_id' => $procs_id,
                'flow_no' => $flow_no,
                'flow_name' => $flow_name,
                'entry' => $entryData

            ];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
        }
        return $this->returnApiJson();
    }


    private
    function fetchEntryProcess(Entry $entry)
    {
        $processes = (new Workflow())->getProcs($entry);

        if (empty($processes)) {
            throw new Exception('流程没有配置审批节点');
        }
        $processAuditors = $temp = [];
        foreach ($processes as $process) {
            $temp['process_name'] = $process->process_name;
            $temp['auditor_name'] = '';
            $temp['approval_content'] = $process->proc ? $process->proc->content : '';
            $temp['approval_finish_at'] = Q($process, 'proc', 'finish_at');
            if ($process->proc && $process->proc->auditor_name) {
                $temp['auditor_name'] = $process->proc->auditor_name;
            } elseif ($process->proc && $process->proc->user_name) {
                $temp['auditor_name'] = $process->proc->user_name;
            } else {
                $temp['auditor_name'] = $process->auditors;
            }

            $temp['status'] = $process->proc ? $process->proc->status : '';
            $temp['id'] = $process->proc ? $process->proc->id : '';
            $temp['status_name'] = '';
            if ($temp['id']) {
                if (Q($process, 'proc', 'totalComments')) {
                    $temp['totalComment'] = $process->proc->totalComments->each(function ($item, $key) {
                        if ($item['comment_img']) {
                            $item['comment_img'] = json_decode($item['comment_img'], true);
                        }
                    })->toArray();
                }
            } else {
                $temp['totalComment'] = [];
            }

            if ($process->proc && $process->proc->status == Proc::STATUS_REJECTED) {
                $temp['status_name'] = '驳回';
            } elseif ($process->proc && $process->proc->status == Proc::STATUS_PASSED) {
                $temp['status_name'] = '完成';
            } else {
                $temp['status_name'] = '待处理';
            }
            $processAuditors[] = $temp;
        }

        return $processAuditors;
    }

    /**
     * 我的申请单
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return $this
     */
    public
    function myApply(Request $request)
    {
        // 获取有审批权限的
        $authAuditor = new AuthUserShadowService();
        //我的申请
        $entries = Entry::getApplyEntries($authAuditor->id(), $request->all());

        $flows = Flow::getFlowsOfNo(); // 流程列表
        $entryStatusMap = Entry::STATUS_MAP; // 申请单状态map
        $searchData = $request->all();

        return view('workflow.entry.my_apply')->with(compact('entries', 'flows', 'entryStatusMap', 'searchData'));
    }

    /**
     * 审批中的
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return $this
     */
    public
    function myProcsTest(Request $request)
    {
        // 获取有审批权限的
        try {
            $authAuditor = new AuthUserShadowService();

            $process = Proc::getUserFinanceProcByPage($authAuditor->id(), $request->all(), 10);
            $processList = [];
            $total1sMoney = 0;
            if (!empty($process)) {
                foreach ($process as $k2 => $p) {
                    $auditor = '';
                    $entryData1 = $p->entry->entry_data->toArray();

                    $project1s = [];
                    $title = '';
                    foreach ($entryData1 as $v1) {
                        if ($v1['field_name'] == 'expense_reimburse_details') {
                            $arr1 = json_decode($v1['field_value'], true);
                            $project1s['money'] = $arr1[0]['money'];

                        }
                        if ($v1['field_name'] == 'expense_amount') {
                            $total1 = intval($v1['field_value']);
                        }
                    }
                    $total1sMoney += $total1;
                    $processList[$k2]['projects'] = $project1s;

                    $auditor = $p->entry->getCurrentStepProcs()->pluck('user_name')->toArray();
                    if (count($auditor) > 0) {
                        if (count($auditor) > 1) {
                            $auditor = join(',', $auditor);
                        } else {
                            $auditor = $auditor[0];
                        }
                    }

                    if (Q($p, 'status') == Entry::STATUS_IN_HAND) {
                        $status_names = '待处理';
                    } elseif (Q($p, 'status') == Entry::STATUS_FINISHED) {
                        if ($auditor) {
                            $status_names = '等待' . $auditor . '处理';
                        } else {
                            $status_names = Entry::STATUS_MAP[Entry::STATUS_FINISHED];
                        }

                    } elseif (Q($p, 'status') == Entry::STATUS_REJECTED) {
                        $status_names = Entry::STATUS_MAP[Entry::STATUS_FINISHED];
                    }

                    $time = Q($p, 'created_at')->toArray();
                    $processList[$k2]['auditor_name'] = $auditor;
                    $processList[$k2]['is_auditor'] = 1;
                    $processList[$k2]['id'] = $p['id'];
                    $processList[$k2]['status'] = Q($p, 'status');
                    $processList[$k2]['status_name'] = $status_names;
                    $processList[$k2]['created_at'] = $time['formatted'];
                    $processList[$k2]['flow_name'] = Q($p, 'flow', 'flow_name');
                    $processList[$k2]['title'] = Q($p, 'entry', 'title');
                    $processList[$k2]['url'] = route('api.finance.auditor_flow.show') . "?id=" . $p['id'];

                }
            }
            $processList = array_values($processList);//dd($processList);
            $totalsMoney = $total1sMoney;


            $count = count($processList);
            $this->data = ['entries' => $processList, 'totalsMoney' => $totalsMoney, 'count' => $count];
        } catch (Exception $e) {

            $this->message = $e->getMessage();
            $this->code = $e->getCode();
        }
        return $this->returnApiJson();

    }

//

    /**
     * 我审批过的
     */
    public
    function myAudited(Request $request)
    {

        //dd($procs->toArray());
        try {
            $authAuditor = new AuthUserShadowService();
            $process = Proc::getUserFinanceAuditedByPage($authAuditor->id(), $request->all(), 20);
            $processList = [];
            $total1sMoney = 0;
            if (!empty($process)) {
                foreach ($process as $k2 => $p) {
                    $auditor = '';
                    $entryData1 = $p->entry->entry_data->toArray();

                    $project1s = [];
                    $title = '';
                    foreach ($entryData1 as $v1) {
                        if ($v1['field_name'] == 'expense_reimburse_details') {
                            $arr1 = json_decode($v1['field_value'], true);
                            $project1s['money'] = $arr1[0]['money'];

                        }
                        if ($v1['field_name'] == 'expense_amount') {
                            $total1 = intval($v1['field_value']);
                        }
                    }
                    $total1sMoney += $total1;
                    $processList[$k2]['projects'] = $project1s;

                    $auditor = $p->entry->getCurrentStepProcs()->pluck('user_name')->toArray();
                    if (count($auditor) > 0) {
                        if (count($auditor) > 1) {
                            $auditor = join(',', $auditor);
                        } else {
                            $auditor = $auditor[0];
                        }
                    }

                    if (Q($p, 'status') == Entry::STATUS_IN_HAND) {
                        $status_names = '待处理';
                    } elseif (Q($p, 'status') == Entry::STATUS_FINISHED) {
                        if ($auditor) {
                            $status_names = '等待' . $auditor . '处理';
                        } else {
                            $status_names = Entry::STATUS_MAP[Entry::STATUS_FINISHED];
                        }

                    } elseif (Q($p, 'status') == Entry::STATUS_REJECTED) {
                        $status_names = Entry::STATUS_MAP[Entry::STATUS_FINISHED];
                    }

                    $time = Q($p, 'created_at')->toArray();
                    $processList[$k2]['auditor_name'] = $auditor;
                    $processList[$k2]['is_auditor'] = 1;
                    $processList[$k2]['id'] = $p['id'];
                    $processList[$k2]['status'] = Q($p, 'status');
                    $processList[$k2]['status_name'] = $status_names;
                    $processList[$k2]['created_at'] = $time['formatted'];
                    $processList[$k2]['flow_name'] = Q($p, 'flow', 'flow_name');
                    $processList[$k2]['title'] = Q($p, 'entry', 'title');
                    $processList[$k2]['url'] = route('api.finance.auditor_flow.show') . "?id=" . $p['id'];

                }
            }
            $processList = array_values($processList);//dd($processList);
            $totalsMoney = $total1sMoney;


            $count = count($processList);
            $this->data = ['entries' => $processList, 'totalsMoney' => $totalsMoney, 'count' => $count];
        } catch (Exception $e) {

            $this->message = $e->getMessage();
            $this->code = $e->getCode();
        }
        return $this->returnApiJson();
    }


    /**
     * 审批人视角
     */

    public
    function processQuery(Request $request)
    {

        $myAuditedSearchData = [];
        $myProcsSearchData = [];
        $myApplySearchData = [];
        $authAuditor = new AuthUserShadowService();


        $flows = Flow::getFlowsOfNo(); // 流程列表
        //我审批过的

        $procsAudited = Proc::getUserAuditedByPage($authAuditor->id(), $myAuditedSearchData, 10);
        // 我审批过的只需要处理中、结束、拒绝三种过滤状态
        $entryAuditorStatusMap = [
            Entry::STATUS_IN_HAND => Entry::STATUS_MAP[Entry::STATUS_IN_HAND],
            Entry::STATUS_FINISHED => Entry::STATUS_MAP[Entry::STATUS_FINISHED],
            Entry::STATUS_REJECTED => Entry::STATUS_MAP[Entry::STATUS_REJECTED],
        ];

        //待我审批
        $procsProc = Proc::getUserFinanceProcByPage($authAuditor->id(), $myProcsSearchData, 10);//dd($procsProc);

        $entryProcStatusMap = Entry::STATUS_MAP; // 申请单状态map

        //我提交过的
        $entries = Entry::getApplyEntries($authAuditor->id(), $myApplySearchData, 10);

        $entryEntriesStatusMap = Entry::STATUS_MAP; // 申请单状态map
        $data = [
            'flows' => $flows,//流程列表
            'procsAudited' => $procsAudited->toArray(),//我审批过的
            'entryAuditorStatusMap' => $procsAudited->toArray(),//我审批过的状态
            'procsProc' => $procsProc->toArray(),//待我审批的
            'entryProcStatusMap' => $entryProcStatusMap,//待我审批的状态列表
            'entries' => $entries->toArray(),//我提交过的
            'entryEntriesStatusMap' => $entryEntriesStatusMap//我申请的状态
        ];
        return $data;

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     */
    public
    function workflowAuthorityShow(Request $request)
    {
        try {
            $id = $request->get('id');
            $authAuditor = new AuthUserShadowService();
            $process = Proc::findUserProcAllStatus($authAuditor->id(), $id);
            $entry = Entry::findOrFail($process->entry_id);
            $flow_no = Q($entry, 'flow', 'flow_no');
            $flow_name = Q($entry, 'flow', 'flow_name');
            $entryData = [];
            $entryData = [];
            $finance = Q($entry, 'finance');
            if ($finance) {
                $entryData = $this->companyDetail(Q($finance, 'id'));
            }
            $procs_id = '';
            $procs_id = $process->id ? $process->id : '';
            $this->data = [
                'is_auditor' => 1,
                'is_cancel' => 1,
                'procs_id' => $procs_id,
                'entry' => $entryData,
                'flow_no' => $flow_no,
                'flow_name' => $flow_name,
                'proc' => $process->toArray()
            ];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
        }
        return $this->returnApiJson();
    }

    public
    function passWorkflow(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {

                (new Workflow())->passWithNotify($request->get('id'));
                $pro = Proc::find($request->get('id'));
                $financils = Financial::where('entry_id', $pro->entry_id)->first();
                $oldStatus = Q($financils, 'status');
                if ($pro) {//判断是否是最后一步，是则更新福利的表的状态为已完成
                    if (Q($pro, 'entry', 'status') == Entry::STATUS_FINISHED) {
                        $this->updataFinancial($pro->entry_id, $oldStatus, Financial::FINANCIALS_STARUS_CHECK_FINISH);//审批完成
                    }
                    $flowlink = Flowlink::where(['flow_id' => $pro->flow_id, 'process_id' => $pro->process_id])->first();

                    if (Q($flowlink, 'next_process_id') == Flowlink::LAST_FLOW_LINK) {
                        $this->updataFinancial($pro->entry_id, $oldStatus, Financial::FINANCIALS_STARUS_CHECK_FINISH);//审批完成
                    } else {
                        $this->updataFinancial($pro->entry_id, $oldStatus, Financial::FINANCIALS_STARUS_CHECKING);//审批中
                    }
                }

            });
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
        }
        return $this->returnApiJson();
    }

    public
    function updataFinancial($entry_id, $oldStatus, $status)
    {
        if ($status == Financial::FINANCIALS_STARUS_CHECK_FINISH) {
            $financils = Financial::where('entry_id', $entry_id)->first();
            $this->insertAccountLog($financils);
        }
        $res = Financial::where(['entry_id' => $entry_id, 'status' => $oldStatus])->update(['status' => $status]);
        return $res;
    }

    public
    function rejectWorkflow(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                (new Workflow())->reject($request->get('id'), $request->input('content', ''));
                $pro = Proc::find($request->get('id'));
                $financils = Financial::where('entry_id', $pro->entry_id)->first();
                //拒绝
                Financial::where(['entry_id' => $pro->entry_id])->update([
                    'status' => -1, 'reasons' => $request->input('content', '')
                ]);
                /**************审批驳回， 发送通知******************/
                Message::addProc(Proc::find($request->get('id')), Message::MESSAGE_TYPE_WORKFLOW_REJECT);

            });
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
        }
        return $this->returnApiJson();
    }

    /**
     * @deprecated 流程
     * @param $id
     *
     * @return \Illuminate\View\View
     */
    public
    function entryShow($id)
    {

        try {

            $entry = Entry::findOrFail($id);
            if ($entry->pid > 0) {
                $templateForm = $this->fetchEntryTemplate($entry->parent_entry)->template_form;
                $showData = $this->fetchShowData($entry->parent_entry->entry_data, $templateForm);

            } else {
                $templateForm = $this->fetchEntryTemplate($entry)->template_form;
                $showData = $this->fetchShowData($entry->entry_data, $templateForm);
            }
            $this->data = [
                'show_data' => $showData,//申请内容
                'processes' => $this->fetchEntryProcess($entry),//审批记录
            ];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
        }
        return $this->returnApiJson();
    }

    private
    function fetchEntryTemplate(Entry $entry)
    {
        return $entry->pid > 0 ? $entry->parent_entry->flow->template : $entry->flow->template;
    }

    public
    function myProcs()
    {
        try {
            // entry_status 是审批流中的 状态
            $entryArr = $this->getEntryIds([
                'entry_status' => [0, 9],
            ]);
            $user_id = Auth::id();

            // 这里是status Financial中的状态
            $map = ['status' => 2];
            $first = Financial::whereIn('entry_id', $entryArr['apply'])->where($map);
            $financials = Financial::whereIn('entry_id', $entryArr['process'])->where($map)
                ->select()
                ->union($first)
                ->simplePaginate(10);

            $count = Financial::where(['user_id' => $user_id])->where($map)
                ->select()
                ->count();
            $totalsMoney = Financial::where(['user_id' => $user_id])->where($map)
                ->select()
                ->sum('expense_amount');
            $data = $this->fetchFinancials($financials);

            $this->data = ['entries' => $data, 'totalsMoney' => $totalsMoney, 'count' => $count];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
        }
        return $this->returnApiJson();
    }

    private function getEntryIds($params)
    {
        $entries = Entry::getApplyEntryIdsByUser(Auth::id(), $params);
        $data['apply'] = $entries;

        $process = Proc::getProcEntryIdsByUser(Auth::id(), $params);
        $data['process'] = $process;
        return $data;
    }

    //待生成凭证的5个流的入口
    public function voucher(Request $request)
    {
        $items = [];
        $items = Flow::getFlowFinance(['fee_expense', 'finance_loan', 'finance_repayment', 'finance_receivables', 'finance_payment']);
        $this->data = $items;
        return $this->returnApiJson();

    }

    //公司财务首页
    public function companyIndex($searchData)
    {
        $list = [];
        $page = $searchData['page'];
         if ($searchData['dept_id'] == 1) {
             $companyIds =$this->userRepository->getChild($searchData['dept_id']);
             $companyIds=explode(',',$companyIds);
         } else {
             $companyIds = $searchData['dept_id'];
         }

        $builder = Financial::where('status', '>', Financial::FINANCIALS_STARUS_CHECKING)
        ->where('status','<>',Financial::FINANCIALS_STARUS_FINISH);
        if (isset($searchData['flow_id']) && $searchData['flow_id']) {
            $builder->where('flow_id', $searchData['flow_id']);
        }
        if (is_array($companyIds)) {
            $builder->whereIn('primary_dept', $companyIds);
        } else {
            $builder->where('primary_dept', $companyIds);
        }

        if (isset($searchData['create_begin']) && $searchData['create_begin']) {
            $searchData['create_begin'] = $searchData['create_begin'] . ' 00:00:00';
            $builder->where('created_at', '>=', $searchData['create_begin']);
        }
        if (isset($searchData['create_end']) && $searchData['create_end']) {
            $searchData['create_end'] = $searchData['create_end'] . ' 23:59:59';
            $builder->where('created_at', '<=', $searchData['create_end']);
        }
        if (isset($searchData['status']) && $searchData['status'] >0) {
            $builder->where('status', $searchData['status']);
        }

        $list = $builder->with('getFlow', 'getEntry', 'financeDetail', 'loan_bill.getFinanceInfo')
            ->select('id', 'flow_id', 'user_id', 'expense_amount','budget_id', 'transaction','fee_booth','sof_id','primary_dept',
                'created_at', 'title', 'entry_id', 'status', 'account_period', 'sum_money', 'cur_money', 'child_status', 'loan_bill_id')
            ->orderBy('id', 'desc')->paginate($perPage = 20, $columns = ['*'], $pageName = 'page', $page);
        if ($list) {
            foreach ($list as &$financial) {
                $financial = $this->companyIndexData($financial);
            }
            $list = $list->toArray();
        }
        return $list;

    }

    //公司财务待办数据
    public function companyIndexData($financial)
    {
        $financial->flow_name = Q($financial, 'getFlow', 'flow_name');
        $financial->flow_no = Q($financial, 'getFlow', 'flow_no');
        $financial->chinese_name = Q($financial, 'users', 'chinese_name');
        $financial->child_status = $financial->child_status ? $financial->child_status : 0;
        $projects['money'] = Q($financial, 'expense_amount');
        if(Q($financial, 'fee_booth')==1){
            $financial['fee_booth_name'] = '费用摊派';
        }else{
            $financial['fee_booth_name'] = '';
        }

        $projects['money'] = sprintf("%.2f", $projects['money']);
        $projects['project_name'] = '';
        $financial->pic_count = count(Q($financial, 'financePic'));

        if (Q($financial, 'financeDetail')) {
            $financeDetail = $financial->financeDetail->toArray();
            if ($financeDetail[0]['projects_id']) {//
                $projec_name = $this->rpcRepository->getFlowCateName($financeDetail[0]['projects_id']);
                $projects['project_name'] = isset($projec_name['name']) ? $projec_name['name'] : '';
            }

            unset($financial->getEntry);
        }
        $financial->projects = $projects;

        if ($financial->status == Financial::FINANCIALS_STARUS_CHECK_FINISH) {
            $financial->status_name = '待入账';
        } elseif ($financial->status == Financial::FINANCIALS_STARUS_PENDING_BUDGET) {

            $financial->status_name = '待收支';
        } elseif ($financial->status == Financial::FINANCIALS_STARUS_RECEIVED_BUDGET) {
            $financial->status_name = '已收支';
        } elseif ($financial->status == Financial::FINANCIALS_STARUS_WAITING_INVOICE) {
            $financial->status_name = '待发票';
        } elseif ($financial->status == Financial::FINANCIALS_STARUS_FINISH) {
            $financial->status_name = '已完成';
        }
        unset($financial->getFlow);
        unset($financial->users);
        unset($financial->getEntry);
        unset($financial->financeDetail);
        return $financial;
    }

    //更新财务凭证数据
    public function updateCompanyFinancial($id, $data = [])
    {
        $financial = Financial::find($id);
        try {
            if ($financial) {
                $financial = $financial->toArray();
                if (isset($data['cur_money']) && $data['cur_money']) {
                    $data['cur_money'] = $data['cur_money'];
                }
                if (isset($data['expense_amount']) && $data['expense_amount']) {
                    $data['expense_amount'] = $data['expense_amount'];
                }
                if (isset($data['status']) && $data['status']) {
                    $data['status'] = $data['status'];
                }
                if (isset($data['reasons']) && $data['reasons']) {
                    $data['reasons'] = $data['reasons'];
                }
                if (isset($data['sum_money']) && $data['sum_money']) {
                    $sum_money = ($financial['sum_money']) + $data['sum_money'];
                    $data['sum_money'] = $sum_money;
                }

                $res = Financial::where('id', $id)->update($data);
                if ($res) {
                    return [
                        'code' => 200,
                        'message' => '保存成功'
                    ];
                }
            } else {
                return [
                    'code' => 400,
                    'message' => '找不到数据'
                ];
            }
        } catch (\Exception $e) {
            return [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ];
        }
    }

    //更新抵充借款单的数据
    public function updateBillFinancial($id, $data = [])
    {
        $financial = Financial::find($id);
        try {
            if ($financial) {
                $financial = $financial->toArray();
                $data['expense_amount'] = $data['expense_amount'];
                $res = Financial::where('id', $id)->update($data);
                if ($res) {
                    return [
                        'code' => 200,
                        'message' => '保存成功'
                    ];
                }
            } else {
                return [
                    'code' => 400,
                    'message' => '找不到数据'
                ];
            }
        } catch (\Exception $e) {
            return [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ];
        }
    }

    //插入账户账号表 $finance是个对象
    public function insertAccountLog($finance)
    {
        $flow_no = Q($finance, 'getFlow', 'flow_no');
        $flow_name = Q($finance, 'getFlow', 'flow_name');
        $first = TransactionLog::where('outer_id', Q($finance, 'id'))->count();
        if ($first) {
            return true;
        }
        $flowBranch = [
            Entry::WORK_FLOW_NO_FINANCE_LOAN,
            Entry::WORK_FLOW_NO_CAPITAL_FEE_EXPENSE,
            Entry::WORK_FLOW_NO_FINANCE_PAYMENT
        ];
        //
        if (in_array($flow_no, $flowBranch)) {//支
            $in_out = TransactionLog::FINANCIALS_DEPARTMENT_INOUTF;
            if (Q($finance, 'transaction') == '对内交易') {
                $type = TransactionLog::FINANCIALS_DEPARTMENT_DNJYZ_TYPE;// 对内交易（支)
            } else {
                $type = TransactionLog::FINANCIALS_DEPARTMENT_DWJYZ_TYPE;// 对外交易（支）
            }
        } else {//收
            $in_out = TransactionLog::FINANCIALS_DEPARTMENT_INOUTS;
            if (Q($finance, 'transaction') == '对内交易') {
                $type = TransactionLog::FINANCIALS_DEPARTMENT_DNJYS_TYPE;// 对内交易（收)
            } else {
                $type = TransactionLog::FINANCIALS_DEPARTMENT_DWJYS_TYPE;// 对外交易（收)
            }
        }
        //报销类型
        switch ($flow_no) {
            case Entry::WORK_FLOW_NO_CAPITAL_FEE_EXPENSE:
                $category = TransactionLog::FINANCIALS_DEPARTMENT_CATEGORY_BX;//报销
                break;
            case Entry::WORK_FLOW_NO_FINANCE_LOAN:
                $category = TransactionLog::FINANCIALS_DEPARTMENT_CATEGORY_JK;//借款
                break;
            case Entry::WORK_FLOW_NO_FINANCE_REPAYMENT:
                $category = TransactionLog::FINANCIALS_DEPARTMENT_CATEGORY_HK;//还款
                break;
            case Entry::WORK_FLOW_NO_FINANCE_RECEIVABLES:
                $category = TransactionLog::FINANCIALS_DEPARTMENT_CATEGORY_SK;//收款
                break;
            default :
                $category = TransactionLog::FINANCIALS_DEPARTMENT_CATEGORY_ZF;//支付
                break;
        }
        $financePic = $finance->financePic->where('pic_type', FinancialPic::FINANCIALS_PIC_TYPE_BILL);//发票
        if ($financePic) {//是否发票
            $is_bill = 1;
        } else {
            $is_bill = 0;
        }
        if (Q($finance, 'fee_booth') == Financial::FINANCIALS_FEE_BOOTH_YES) {//是否平摊
            $is_more_department = 1;
        } else {
            $is_more_department = 0;
        }


        $data = [
            "user_id" => Q($finance, 'user_id'),
            "department_id" => Q($finance, 'primary_dept'),
            "outer_id" => Q($finance, 'id'),
            'company_id' => Q($finance, 'company_id'),
            "model_name" => get_class(new Financial()),
            "amount" => Q($finance, 'expense_amount'),
            "category" => $category,
            "type" => $type,
            "is_bill" => $is_bill,
            "status_start_time" => date("Y-m-d H:i:s", strtotime($finance->created_at)),
            "is_more_department" => $is_more_department,
            'status' => 1,
            'is_jysr' => 0,
            'in_out' => $in_out,
            'is_rpc' => 0,
            'title' => $flow_name
        ];
        $res = TransactionLog::create($data);

        return $res;

    }

    //获取发票信息
    public function getPicInfo($finance_ids)
    {
        $items = [];
        $items=FinancialPic::whereIn('financial_id',$finance_ids)
            ->select('id','pic_type','pic_url','created_at')
            ->get();
        if($items){
            $items=$items->toArray();
        }

        return $items;

    }
    /**
     * @param $type 1 为客户 2 为项目 3 订单
     * @param $id  客户或者项目id
     */
    //根据参数类型查询审批单id
    public function getCustProList($data)
    {
        $items = [];
        $page = $data['page'] ? $data['page'] : 1;
        $builder = Financial::where('status', '>', 0);
        if ($data['type'] == Financial::FINANCIALS_CUSTOM_TYPE) {
            $builder->where('current_unit', $data['id']);
        } elseif ($data['type'] == Financial::FINANCIALS_PROJECT_TYPE) {
            $builder->where('projects_id', $data['id']);
        } elseif ($data['type'] == Financial::FINANCIALS_OEDER_TYPE) {
            $financeIds = FinancialOrder::where('title', $data['id'])->pluck('financial_id')->toArray();
            $builder->whereIn('id', $financeIds);
        }
        $items = $builder->with(['getFlow' => function ($query) {
            $query->select(['id', 'flow_no', 'flow_name']);
        }])->select('id', 'flow_id', 'status', 'expense_amount',
            'created_at', 'title', 'status')
            ->orderBy('id', 'desc')->paginate($perPage = 2, $columns = ['*'], $pageName = 'page', $page);
        if ($items) {
            $items = $items->toArray();
        }
        return $items;

    }

    public function getLimitPrice($data)
    {
        $res = 0;
        $res = $this->rpcRepository->getPriceByConditions($data);
        $list['limit_price'] = $res;

        return returnJson($message = 'ok', $code = 200, $data = $list);
        //return $list;
    }
    //获取账户类型下的银行或现金列表
    public function getFlowAccount($type_name)
    {
        $data = [];
        if (!$type_name) {
            $type_name = '现金账户';
        }
        $data = $this->rpcRepository->getFlowAccount($type_name);
        return returnJson($message = 'ok', $code = 200, $data);
    }

    //部长计划-创建计划类目
    public  function deptPlan($users){
        $apply_basic_info=[];
        //$apply_basic_info = Workflow::getApplyerBasicInfo(null, null);
        $apply_basic_info['primary_dept_path'] = WorkflowUserService::fetchUserPrimaryDeptPath($users->id);
        $apply_basic_info['applicant_chinese_name']=Q($users,'chinese_name');
        $user_company_id = '';
        $user_company_name = '';
        if ($apply_basic_info['primary_dept_path']) {
            $deptPrimarys = explode('/', $apply_basic_info['primary_dept_path']);
            $companys = Department::findByName($deptPrimarys[0]);
            $user_company_id = Q($companys, 'id');
            $user_company_name = $deptPrimarys[0];

        }
        $apply_basic_info['user_company_name'] = $user_company_name;
        $user_primary_dept_id = Q($users, 'primaryDepartUser', 'department_id');
        $user_primary_dept_name = Q($users, 'primaryDepartUser', 'department','name');
        $apply_basic_info['user_primary_dept_id'] = $user_primary_dept_id;
        $apply_basic_info['user_company_id'] = $user_company_id;
        // if (in_array($flow->flow_no, ['finance_payment', 'fee_expense'])) {
        $apply_basic_info['user_primary_dept_name']=$user_primary_dept_name;

        //unset($apply_basic_info['company_name_list']);
        $apply_basic_info['unit_type_list'] = [
            '客户',
            '供应商',
            '内部单位'
        ];

        $departs = $this->userRepository->getChild($user_company_id);
        $departs = explode(',', $departs);
        unset($departs[0]);

        $depatlist = Department::whereIn('id', $departs)->select('id', 'name')->get();
        if ($depatlist) {
            $apply_basic_info['depatlist'] = $depatlist->toArray();
        } else {
            $apply_basic_info['depatlist'] = [];
        }

        $mission = $this->rpcRepository->getProjectList($users->id);

        $apply_basic_info['mission'] = $mission;//关联项目列表
        $custList = $this->rpcRepository->getCustomerListByCompanyId(Q($users, 'company', 'id'));
        $supplierList = $this->rpcRepository->getCustomerListByCompanyId(Q($users, 'company', 'id'), 2);
        $apply_basic_info['custList'] = $custList;//关联客户列表
        $apply_basic_info['supplierList'] = $supplierList;//关联供应商列表
        $apply_basic_info['BudetsCatemory']=$this->rpcRepository->getFlowCategory();//获取流水类别

        return $apply_basic_info;
    }

    /**
     * 通过财务id 获取分账列表
     * @param $financial_id
     */
    public function financeChilder($financial_id){
        if (!$financial_id ) {
            return returnJson($message = '参数id错误', $code =400);
        }
        $list=[];
        $data=$this->rpcRepository->getRecordList($financial_id);
        if($data['data']){
            $list=$data['data'];
        }
        $this->data=$list;
        return $this->returnApiJson();

    }
    public function editWorkflowShow($financial_id){
        if (!$financial_id ) {
            return returnJson($message = '参数id错误', $code =400);
        }
        $list=[];
        $finance=Financial::with('getFlow', 'financeDetail','loan_bill.getFinanceInfo','financePic','dept','financeOrder')
            ->where('id',$financial_id)->first();
        if($finance){
            $finance->loan_bill_id=$finance->loan_bill;
            $finance->flow_name = Q($finance, 'getFlow', 'flow_name');
            $finance->flow_no = Q($finance, 'getFlow', 'flow_no');
            if ($finance->status == Financial::FINANCIALS_STARUS_SUBMIT) {
                $finance->status_name = '待审批';
            } elseif ($finance->status == Financial::FINANCIALS_STARUS_CHECKING) {
                $finance->status_name = '审批中';
            } elseif ($finance->status == Financial::FINANCIALS_STARUS_CHECK_FINISH) {
                $finance->status_name = '待入账';
            } elseif ($finance->status == Financial::FINANCIALS_STARUS_PENDING_BUDGET) {
                $finance->status_name = '待收支';
            } elseif ($finance->status == Financial::FINANCIALS_STARUS_RECEIVED_BUDGET) {
                $finance->status_name = '已收支';
            } elseif ($finance->status == Financial::FINANCIALS_STARUS_WAITING_INVOICE) {
                $finance->status_name = '待发票';
            } elseif ($finance->status == Financial::FINANCIALS_STARUS_FINISH) {
                $finance->status_name = '已完成';
            } elseif ($finance->status == Financial::FINANCIALS_STARUS_REJUEST) {
                $finance->status_name = '拒绝';
            } elseif ($finance->status == Financial::FINANCIALS_STARUS_CANCEL) {
                $finance->status_name = '撤销';
            }

            unset($finance->company);
            $finance->dept;
            $finance->applicant_chinese_name = Q($finance, 'users', 'chinese_name');
            $finance->current_unit_id='';
            //客户
            if($finance->unittype=='内部单位'){
                $unitUsers=User::findById($finance->current_unit);
                $finance->current_unit = Q($unitUsers,'chinese_name');
                //$finance->current_unit_id = Q($unitUsers,'id');
            }else{
                if ($finance->current_unit) {
                    $customer = $this->rpcRepository->getCustomerById($finance->current_unit);
                    if ($customer) {
                        $finance->current_unit = $customer['cusname'];
                        $finance->current_unit_id = $customer['id'];
                    }
                }
            }

            //项目
            $finance->projects_title='';
            if ($finance->projects_id) {
                $miss = $this->rpcRepository->getProjectById($finance->projects_id);
                if ($miss) {
                    $finance->projects_id = $miss['id'];
                    $finance->projects_title = $miss['title'];

                }
            }
            //关联订单
            $finance->order_id=$finance->linked_order_name=$finance->linked_order_code='';
            if ($finance->linked_order != Financial::FINANCIALS_LINKED_ORDER_NO) {
                if(Q($finance,'financeOrder')->toArray()){
                    $finances=Q($finance,'financeOrder');
                    if($finances[0]->order_type==1){
                        $finance->linked_order_name='销售订单';
                        $finance->linked_order_code=$finances[0]->saleOrder->order_sn;
                        $finance->order_id=$finances[0]->saleOrder->id;
                    }elseif($finances[0]->order_type==2){
                        $finance->linked_order_name='采购订单';
                        $finance->linked_order_code=$finances[0]->purchaseOrder->code;
                        $finance->order_id=$finances[0]->saleOrder->id;
                    }
                    unset($finance->financeOrder);
                }
            }


            $list=$finance->toArray();
        }
        $this->data=$list;
        return $this->returnApiJson();
    }


}
