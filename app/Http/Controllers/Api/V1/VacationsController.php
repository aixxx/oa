<?php
namespace App\Http\Controllers\Api\V1;

use App\Constant\ConstFile;
use App\Http\Controllers\Api\V1\BaseController;
use App\Models\Vacations\CompanyVacation;
use App\Repositories\VacationsRepository;

use App\Repositories\UsersRepository;
use Auth;

use Illuminate\Foundation\Console\Presets\React;
use Illuminate\Http\Request;

class VacationsController extends BaseController
{
    protected $repository;

    //构造函数
    public function __construct() {
        parent::__construct();
        $this->repository = app()->make(VacationsRepository::class);
    }

    //录入员工假期信息
    public function adduservaca()
    {
        return $user = $this->repository->adduservacations();
    }

    //获取我的各种假期时长
    public function getmyleaves()
    {
        $userinfo = Auth::user();
        $user_id=$userinfo->id;
        return $user = $this->repository->getmyleave($user_id);
    }

    //根据公司id获取请假模板字段
    public function gettem()
    {
        $userinfo = Auth::user();
        $userRespository = app()->make(UsersRepository::class);
        $company = $userRespository->getAllDept('',$userinfo); // 此时为数组
        $arr['user_id'] = $userinfo->id;
        $arr['c_id'] = $company['id'];

        return $user = $this->repository->gettembycomid($arr);
    }

    //根据假期类型获取员工的该假期剩余时长
    public function getutimes()
    {
        $userinfo = Auth::user();
        $user_id=$userinfo->id;

        return $user = $this->repository->getusertimes($user_id);
    }

    //申请请假
    public function rabsence()
    {
        $userinfo = Auth::user();
        $userRespository = app()->make(UsersRepository::class);
        $company = $userRespository->getAllDept('',$userinfo); // 此时为数组
        $department = $userRespository->getCurrentDept($userinfo); // 此时为数组

        $arr['user_id'] = $userinfo->id;
        $arr['c_id'] = $company['id'];     //公司id
        $arr['bm_id'] = $department['id']; //部门id

        return $user = $this->repository->absence($arr);
    }

    //请假申请记录列表
    public function leavelist()
    {
        $userinfo = Auth::user();
        $user_id=$userinfo->id;
        return $user = $this->repository->leastatuslist($user_id);
    }

    //请假单详情页
    public function leavedetail()
    {
        return $user = $this->repository->leave_detail();
    }

    //请假审批操作
    public function leaappoperation()
    {
        $userinfo = Auth::user();
        $user_id=$userinfo->id;

        return $user = $this->repository->leaappoper($user_id);
    }

    //撤销
    public function cxrevocation()
    {
        $userinfo = Auth::user();
        $user_id=$userinfo->id;

        return $user = $this->repository->revocation($user_id);
    }


    //假期管理信息-人事视角
    public function leave_management_list(){
        $userinfo = Auth::user();
        $user_id=$userinfo->id;
        //获取公司id
//        $getbranchid = app()->make(UserRespository::class);
//        $c_id = $getbranchid->getAllDept('',$user_id);
        $arr['c_id'] = $userinfo->departments->first()->id;
        return $user = $this->repository->leave_management_list($arr);
    }

    //查看假期详情-人事视角
    public function leavetypedetail(){
        $userinfo = Auth::user();
        $user_id=$userinfo->id;
        //获取公司id
//        $getbranchid = app()->make(UserRespository::class);
//        $c_id = $getbranchid->getAllDept('',$user_id);
        $arr['c_id'] = $userinfo->departments->first()->id;
        return $user = $this->repository->leavetypedetail($arr);
    }

    //添加编辑假期规则
    public function addupleaverule(){
        $userinfo = Auth::user();
        $user_id = $userinfo->id;
        //获取公司id
//        $getbranchid = app()->make(UserRespository::class);
//        $c_id = $getbranchid->getAllDept('',$user_id);
        $arr['c_id'] = $userinfo->departments->first()->id;
        return $user = $this->repository->addupleaverule($arr);
    }

    /***
     * 增加修改假期
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function modifyVacation(Request $request){
        $vacationId = $request->get('vacation_id');
        $costUnit = $request->get('cost_unit');  //最小请假单位
        $leaveType = $request->get('leave_type');  //请假时长方式
        $isBalance = $request->get('is_balance');  //是否启用余额
        $balanceType = $request->get('balance_type');  //余额发放形式
        $perCnt = $request->get('per_count');  //每人发放形式
        $expireTime = $request->get('expire_time');  //使用规则
        $isAddExpire = $request->get('is_add_expire');  //是否支持延长有效期
        $addTime = $request->get('add_time');  //可以延长的天数
        $leaveStartType = $request->get('leave_start_type');  //新员工何时可以请假
        $discountSalary = $request->get('discount_salary', 100);  //工资折扣

        $data = [
            'cost_unit' => $costUnit,
            'leave_type' => $leaveType,
            'is_balance' => $isBalance,
            'balance_type' => $balanceType,
            'per_count' => $perCnt,
            'expire_time' => $expireTime,
            'is_add_expire' => $isAddExpire,
            'add_time' => $addTime,
            'leave_start_type' => $leaveStartType,
            'discount_salary' => $discountSalary,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        if(empty($vacationId)){
            $data = CompanyVacation::query()->create($data);
        }else{
            CompanyVacation::query()->where('id', '=', $vacationId)->update($data);
        }
        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE, ConstFile::API_RESPONSE_SUCCESS, $data);
    }

    /**
     * 假期详情
     */
    public function detailVacation(Request $request){

        $id = $request->get('id');

        $vacationObj = CompanyVacation::query()->find($id);

        $user = Auth::user();
        $company = $user->company;
        $companyId = $company->id;
        if($vacationObj->company_id != $companyId){
            return returnJson(ConstFile::API_RESPONSE_FAIL_MESSAGE, ConstFile::API_RESPONSE_FAIL, []);
        }
        $vacationObj->cost_unit_type_str = CompanyVacation::$_cost_unit_type[$vacationObj->cost_unit_type];
        $vacationObj->leave_type_str = CompanyVacation::$_leave_type[$vacationObj->leave_type];
        $vacationObj->balance_type_str = CompanyVacation::$_balance_type[$vacationObj->balance_type];
        $vacationObj->expire_time_str = CompanyVacation::$_expire_time_type[$vacationObj->expire_time];
        $vacationObj->leave_start_type_str = CompanyVacation::$_leave_start_type[$vacationObj->leave_start_type];
        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE, ConstFile::API_RESPONSE_SUCCESS, $vacationObj);
    }

    /**
     * 假期列表  // CompanyVacation 需要换成 => VacationRule  类写重了
     * @deprecated
     */
    public function listVacation(){
        $user = Auth::user();
        $company = $user->company;
        $companyId = $company->id;
        $companyList = CompanyVacation::query()->where('company_id', '=', $companyId)->get();
        foreach ($companyList as $key => &$value){
            /** @var CompanyVacation $value */
            $value->cost_unit_type_str = CompanyVacation::$_cost_unit_type[$value->cost_unit_type];
            $value->leave_type_str = CompanyVacation::$_leave_type[$value->leave_type];
            $value->balance_type_str = CompanyVacation::$_balance_type[$value->balance_type];
            $value->expire_time_str = CompanyVacation::$_expire_time_type[$value->expire_time];
            $value->leave_start_type_str = CompanyVacation::$_leave_start_type[$value->leave_start_type];
        }
    }

}