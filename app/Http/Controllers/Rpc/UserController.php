<?php

namespace App\Http\Controllers\Rpc;

use App\Models\Company;
use App\Models\Department;
use App\Models\Financial;
use App\Models\Message\Message;
use App\Models\Power\RolesUsers;
use App\Models\User;
use App\Models\Workflow\Flow;
use App\Models\Workflow\WorkflowRole;
use App\Models\Workflow\WorkflowRoleUser;
use App\Repositories\FinanceLogRepository;
use App\UserAccount\AccountLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use JWTAuth;
use App\Repositories\UsersRepository;
use App\Repositories\FinanceRepository;

class UserController extends HproseController
{

    public function login($username, $password)
    {
        $payload = [
            'email' => $username,
            'password' => $password
        ];
        $data = [];
        $token = JWTAuth::attempt($payload);
        if ($token) {
            $user = Auth::user();
            $user->company;
            $user->departments;
            $user->mobile = decrypt_no_user_exception($user->mobile);
            $data['user'] = $user->toArray();
            $data['token'] = $token;
            $data['code'] = 200;

        } else {
            $data['code'] = 400;
        }
        return $data;
    }
    public function getUsersByIds() {
        $users = User::get();

        $data = [];
        foreach ($users as $user) {
            $user->company;
            $user->departments;
            $user->mobile = decrypt_no_user_exception($user->mobile);
            $data[$user['id']] = $user->toArray();
        }
        return $data;
    }
    public function getUsersByIdsCache() {
        $repository = app()->make(UsersRepository::class);
        $users = User::get();
        $data = [];
        $u['user'] = [];
        foreach ($users as $user) {
            $dept_id = $repository->getDeptTopId($user);
            $detp = Department::where('id', $dept_id)->select('*')->first();
            $user->company;
            $user->departments;
            $user->top_detp = $detp->name;
            $user->top_detp_id = $detp->id;
            $user->mobile = decrypt_no_user_exception($user->mobile);
            $u['user'] = $user->toArray();
            $u['top_detp'] = $detp->name;
            $u['top_detp_id'] = $detp->id;
            $data[$user['id']] = $u;
        }
        return $data;
    }
    public function getUserById($id = '')
    {
        $data['user'] = [];
        if ($id) {
            $user = User::find($id);
            if(empty($user)) {
                return $data;
            }
            $user->company;
            $user->departments;
            $user->mobile = decrypt_no_user_exception($user->mobile);

            $data['user'] = $user->toArray();
            $data['permission'] = $this->getUserPower($user);
        }
        return $data;
    }

    public function getUserPower($user)
    {
        $rolesUser = RolesUsers::with('belongsToManyVueAction')->where('user_id', $user->id)->get();
        $result = [];
        if (!$rolesUser) {
            return $result;
        }
        $data = [];
        foreach ($rolesUser as $key => $val) {
            foreach ($val->belongsToManyVueAction as $k => $v) {
                $data[$key][$k]['id'] = $v->id;
                $data[$key][$k]['title'] = $v->title;
                $data[$key][$k]['vue_path'] = $v->vue_path;
            }
        }
        $result = array_reduce($data, function ($result, $value) {
            return array_merge($result, array_values($value));
        }, array());
        return array_column($result,'vue_path');
    }
    //获取部门
    public function dept($dept_id = '', $user_id = '')
    {
        $users = User::find($user_id);
        $repository = app()->make(UsersRepository::class);
        $detp = $repository->getAllDept($dept_id, $users);
        return $detp;
    }

    //获取一级部门数量
    public function deptCount($user_id = '')
    {
        $users = User::find($user_id);
        $repository = app()->make(UsersRepository::class);
        $dept_id = $repository->getDeptTopId($users);
        $detp = $repository->getAllDept($dept_id, $users);
        return count($detp);
    }
    // 获取下级部门数量 getChild
    public function allDeptCount($user_id = '')
    {
        $users = User::find($user_id);
        $repository = app()->make(UsersRepository::class);
        $dept_id = $repository->getDeptTopId($users);
        $detps = $repository->getChild($dept_id);
        return $detps;
    }
    // 获取下级部门数量 getChild
    public function allDeptChild($dept_id = '')
    {
        $repository = app()->make(UsersRepository::class);
        $detps = $repository->getChild($dept_id);
        return $detps;
    }
    //获取部门
    public function deptById($dept_id = '')
    {

        $users = Auth::user();
        $repository = app()->make(UsersRepository::class);
        $detp = $repository->getAllDept($dept_id, $users);
        return $detp;
    }
    public function getDeptById($dept_id = '')
    {
        $detp = Department::where('id', $dept_id)->select('*')->first();
        $data = [];
        if($detp){
            $data = $detp->toArray();
        }

        return $data;
    }

    //获取部门id获取下面的用户getTopPid
    public function getDeptParents($dept_id)
    {
        $repository = app()->make(UsersRepository::class);
        $data = Department::all();
        $parents = $repository->getTopPid($data->toArray(), $dept_id);
        $data = [];
        foreach ($parents as $key=>$parent) {
            if($key == 1) {
                $data = $parent;
                break;
            }
        }
        return $data;
    }

    //获取部门id获取下面的用户
    public function getUser($dept_id = '')
    {
        $repository = app()->make(UsersRepository::class);
        $users = $repository->getRpcChildUsers($dept_id);
        return $users;
    }



    //获取部门id获取公司id
    public function getCompanyId($dept_id = '')
    {
        $repository = app()->make(UsersRepository::class);
        $users = $repository->getRpcChildUsers($dept_id);
        return $users;
    }

    //获取所有组织架构，并且分层级输出
    public function getDepts($uid,$dept='',$keywords='')
    {
        $user = User::find($uid);
        $repository = app()->make(UsersRepository::class);
        $data = $repository->getDeptAllChild($user,$dept,$keywords);
        $data = array_values($data);
        return $data;

    }
    //获取所有组织架构，并且分层级输出
    public function getDeptsNotCache($uid,$dept='',$keywords='')
    {
        $user = User::find($uid);
        $repository = app()->make(UsersRepository::class);
        $data = $repository->getDeptAllChildForRpc($user,$dept,$keywords);return $data;
        $data = array_values($data);
        return $data;

    }
    //获取用户职务列表
    public function getPositionList()
    {
        $position = [];
        $position = User::select('id', 'position')->groupBy('position')->get()->toArray();
        return $position;
    }

    //通过id获取公司详情
    public function getCompanyDetail($id)
    {
        $company = Company::find($id);
        $companyDetail = [];
        if ($company) {
            $companyDetail = $company->toArray();
        }
        return $companyDetail;

    }

    public function getCompanyList()
    {
        $companyList = [];
        $companyList = Company::fetchCompanyList();
        return $companyList;
    }

    public function fetchFinanceUsers()
    {
        return User::leftJoin('assigned_roles', 'users.id', '=', 'assigned_roles.entity_id')
            ->leftJoin('roles', 'roles.id', '=', 'assigned_roles.role_id')
            ->where('status', User::STATUS_JOIN)
            ->where('roles.name', '=', 'finance_manager')
            ->select(
                'users.id',
                'users.chinese_name',
                'roles.name as roleName'
            )->orderBy('users.id', 'desc')->first()->toArray();
    }

    public function nowTime($uid)
    {
        $user = User::find($uid);
        return Q($user,'company','establishment');
    }
    //财务改变状态
    public function changeStatus($id, $status, $qishu=''){
        //$ret = AccountLog::Update($id, $qishu, $status);
        /*if (!$ret) {
            return false;
        }*/
        $repository = app()->make(FinanceRepository::class);
        return $repository->changeStatus($id, $status);
    }
    //财务报销状态
    public function flowList(){
        $items=[];
        $items = Flow::getFlowFinance(['fee_expense', 'finance_payment', 'finance_loan', 'finance_repayment', 'finance_receivables']);
        return $items;
    }
    //通过名称和用户id获取
    public function getUsersRole($company_id='1',$uid){
        $usersRoles=WorkflowRoleUser::where('user_id',$uid)->pluck('role_id')->toArray();
        $roles=WorkflowRole::whereIn('id',$usersRoles)->where('company_id',$company_id)->pluck('role_name')->toArray();

        return $roles;
    }
    //获取财务列表
    public function companyIndex($data){
        $repository = app()->make(FinanceRepository::class);
        $items=$repository->companyIndex($data);
        return $items;
    }
    //更新财务凭证数据
    public function updateFinancial($id,$data){
        $repository = app()->make(FinanceRepository::class);
        $res=$repository->updateCompanyFinancial($id,$data);
        return $res;
    }
    //财务明细
    public function financeShows($id){
        $repository = app()->make(FinanceRepository::class);
        $item=$repository->companyDetail($id);
        return $item;
    }
    //获取财务图片
    public function getFinancePic($ids){
        $repository = app()->make(FinanceRepository::class);
        return $repository->getPicInfo($ids);
    }
    //根据客户或者项目id获取财务审批列表
    public function getCustProList($data){
        $repository = app()->make(FinanceRepository::class);
        return $repository->getCustProList($data);
    }

    //获取顶级部门，一级部门和二级部门
    public function getFirstAndSecond($user_id){
        $repository = app()->make(UsersRepository::class);
        return $repository->getUserFirstAndSecond($user_id);
    }
    /*
     *'receiver_id' //接收者（申请人）
     * 'sender_id' //发送这（最后审批人）
     * 'content'//内容（审批title）
     * $relation_id //关联id
     * $type   14:项目审批 15:项目审批抄送 16:项目审批通过 17:任务审批 18:项目审批抄送 19:任务审批通过 20:任务汇报 21:任务催办 22:客户提醒
     */
    public function addMessage($receiver_id, $sender_id, $content, $relation_id, $type){
        $rt=Message::addMessage($receiver_id, $sender_id, $content, $relation_id, $type);
        if($rt){
            return true;
        }else{
            return false;
        }

    }

    //更新抵充借款单的数据
    public function updateBillFinancial($id,$data)
    {
        $repository = app()->make(FinanceRepository::class);
        return $repository->updateBillFinancial($id,$data);
    }
    //获取客户类型，客户单位等
    public function deptPlan($user_id){
        $users=User::find($user_id);
        $repository = app()->make(FinanceRepository::class);
        return $repository->deptPlan($users);
    }
    //数组改变财务状态

     public function changeStatusByIds($data){
         $repository = app()->make(FinanceRepository::class);
         return $repository->changeStatusByIds($data);
     }
     //公司财务应收应付
    public function accountReceivablePayable($search){
        $repository = app()->make(FinanceLogRepository::class);
        return $repository->accountReceivablePayable($search);
    }



}
