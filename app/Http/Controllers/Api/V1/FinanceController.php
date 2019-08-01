<?php

namespace App\Http\Controllers\Api\V1;

use App\Repositories\FinanceRepository;
use Illuminate\Http\Request;
use Auth;

class FinanceController extends BaseController
{
    /**
     * @var FinanceRepository
     */
    protected $repository;

    public function __construct()
    {
        $this->repository = app()->make(FinanceRepository::class);
    }
    //我的财务首页
    public function index(){
        return $this->repository->financeIndex();
    }
    //我的财务状态列表
    public function toDoList()
    {
        return $this->repository->fetchToDo();
    }
    //集团财务
    public function companyIndex(Request $request){
        $all=$request->all();
        return $this->repository->companyIndex($all);
    }
    //集团财务明细
    public function companyDetail(Request $request){
        $id=$request->input('id');
        $data=$this->repository->companyDetail($id);
        return returnJson($message = 'ok', $code = 200, $data);

    }

    //财务相关：5个创建列表，支持其他相关创建列表
    public function workflowList(Request $request)
    {
        return $this->repository->fetchWorkflowList($request);
    }
    //创建审批单
    public function createWorkflow(Request $request)
    {
        return $this->repository->createWorkflow($request);
    }
    //保存审批单
    public function storeWorkflow(Request $request)
    {
        return $this->repository->storeWorkflow($request);
    }
    //编辑审批单
    public function editWorkflow(Request $request){
        return $this->repository->editWorkflow($request);
    }
    //申请人查看详情
    public function showWorkflow(Request $request)
    {
        return $this->repository->workflowShow($request);
    }
    //我提交的，待我审批的，我审批过的
    public function processQuery(Request $request){
        return $this->repository->processQuery($request);
    }
    //通过预算单id查出项目列表
    public function budgets(Request $request){
        return $this->repository->projectsByBudgetsId($request);
    }
    //通过项目id获取条件选项
    public function budgetsItems(Request $request){
        return $this->repository->getBudgetsItemId($request);
    }
    //我审批过的
    public function myAudited(Request $request){
        return $this->repository->myAudited($request);
    }

    //审批中的
   /* public function myProcs(Request $request){
        return $this->repository->myProcs($request);
    }*/

    //审批人视角
    public function showAuditorWorkflow(Request $request)
    {
        return $this->repository->workflowAuthorityShow($request);
    }
    //审批通过
    public function passWorkflow(Request $request)
    {
        return $this->repository->passWorkflow($request);
    }
    //审批拒绝
    public function rejectWorkflow(Request $request)
    {
        return $this->repository->rejectWorkflow($request);
    }
    //待生成凭证
    public function voucher(Request $request){
        return $this->repository->voucher($request);
    }
    //财务改变状态
    public function changeStatus(Request $request){
        return $this->repository->changeStatus($request);
    }
    //通过组织id获取所有的部门
    public  function getFinanceDept(Request $request){

        $company_id=$request->input('id','');
        return $this->repository->getFinanceDept($company_id);
    }

    //撤销
    public function updateFinancial(Request $request){
        $data=[];
        $data['status']=$request->input('status','');
        $data['reasons']=$request->input('reasons','');
        $id=$request->input('id');
        //unset($data['id']);
       return $this->repository->updateCompanyFinancial($id,$data);
    }
    public function getLimitPrice(Request $request){
        $data=$request->all();
        return $this->repository->getLimitPrice($data['data']);

    }
    //初始状态
    public function initStatus(Request $request){
        $init_status=$request->input('init_status');
        return returnJson($message='ok', $code = 200,['init_status'=>$init_status] );
    }
    //获取银行类型列表
    public function getFlowAccount(Request $request){
        $type_name=$request->input('account_type','现金账户');

        return $this->repository->getFlowAccount($type_name);
    }

    //部长专属-经营计划
    public function deptPlan(Request $request){
        $users=Auth::user();
        return $this->repository->deptPlan($users);
    }
    //财务下的分账列表
    public function childer(Request $request){
        $financial_id=$request->input('id','');
        return $this->repository->financeChilder($financial_id);
    }
    //驳回编辑
    public function editWorkflowShow(Request $request){
        $financial_id=$request->input('financial_id','');
        return $this->repository->editWorkflowShow($financial_id);
    }

}
