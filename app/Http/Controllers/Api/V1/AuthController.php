<?php

namespace App\Http\Controllers\Api\V1;

//use App\Http\Services\SmsTrait;
use App\Models\Company;
use App\Models\Contract\Contract;
use App\Models\Department;
use App\Models\DepartUser;
use App\Models\User;
use App\Models\UsersDetailInfo;
use App\Services\WorkflowUserService;
use Mockery\Exception;
use Tymon\JWTAuth\JWTAuth;
use Auth;
use Request;
use App\Repositories\UsersRepository;


class AuthController extends BaseController
{
    //use SmsTrait;
    protected $jwt;
    protected $repository;

    public function __construct(JWTAuth $jwt)
    {
        $this->repository = app()->make(UsersRepository::class);
        $this->jwt = $jwt;
    }

    //获取部门
    public function index()
    {
        $users = Auth::user();
        $dept_id = Request::get('id', '');
        $detp = $this->repository->getAllDept($dept_id, $users);

        return returnJson($message = 'ok', $code = 200, $detp);
    }

    //获取部门下面的用户
    public function getUser()
    {
        $dept_id = Request::input('id', '');
        $users = $this->repository->getDeptUsers($dept_id);
        return returnJson($message = 'ok', $code = 200, $users);
    }

    //获取所有组织架构，并且分层级输出
    public function getDept()
    {
        $user = Auth::user();
        $data = $this->repository->getDeptAllChild($user);
        $data = array_values($data);
        return returnJson($message = 'ok', $code = 200, $data);

    }

    //获取所有组织架构，并且分层级输出
    public function getDeptUsers()
    {
        $user = Auth::user();
        $data = $this->repository->getDeptAllChildUsers($user);
        $data = array_values($data);
        return returnJson($message = 'ok', $code = 200, $data);

    }

    /**
     * 有时候我们还可以直接通过用户对象实例创建token：
     *$user = User::first();
     *$token = JWTAuth::fromUser($user);
     */

    public function authenticate()
    {

        $mobile = Request::input('email', '');
        $code = Request::input('code', '');
        $user = User::where('mobile', $mobile)->first();
        if(!$user){
            return returnJson($message = '账号不存在', '400');
        }
        if($user->status!=User::STATUS_JOIN){
            return returnJson($message = '账号审批中...', '400');
        }
        if (!$mobile) {
            return returnJson($message = '手机号码不能为空', '400');
        }
        if (!preg_match("/^1[3456789]\d{9}$/", $mobile)) {
            return returnJson($message = '手机号码格式不正确', '400');
            //throw  new \Exception('手机号码格式不正确', '400');
        }
        if ($code) {

            $token = $this->jwt->fromUser($user);
        } else {
            $payload = [
                'mobile' => $mobile,
                'password' => Request::get('password')
            ];

            try {
                //验证用户是否存在，存在则颁发token，不存在，则不颁发token。
                if (!$token = $this->jwt->attempt($payload)) {
                    return returnJson($message = '用户名或密码错误', $code = 400);
                }

            } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
                return returnJson($message = 'token过期', $code = 400);
            } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
                return returnJson($message = '无效的token', $code = 400);
            } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
                return returnJson($message = $e->getMessage(), $code = 400);
            }

        }
        $data['token'] = $token;
        $users=Auth::user();
        $data['users']=$this->userInfo($users,$users->id);
        return returnJson($message = 'ok', $code = 200, $data);

    }

    public function me()
    {
        $users = Auth::user();
        return returnJson($message = 'ok', $code = 200, $users);
    }
    public function userInfo($users, $id){

            $user = User::where('id', '=', $id)
                ->first(['id', 'chinese_name', 'mobile', 'company_id', 'avatar', 'gender', 'position', 'work_name', 'join_at','status', 'work_address']);

            $userDetail = UsersDetailInfo::where('user_id', '=', $id)
                ->first(['born_time', 'address', 'user_status']);

            $user->mobile = isset($user->mobile) ? decrypt_no_user_exception($user->mobile) : "";
            $user->born_time = isset($userDetail->born_time) ? decrypt_no_user_exception($userDetail->born_time) : "";
            $user->address = isset($userDetail->address) ? decrypt_no_user_exception($userDetail->address) : "";
            $user->user_status = isset($userDetail->user_status) ? $this->repository->judge($userDetail->user_status) : "";

            $priDepart = DepartUser::with('department')->where('user_id', '=', $id)
                ->where('is_primary', '=', 1)
                ->first();
            if($user->status==-1){
                $user->firstDepartment=Q($priDepart,'department','name');
                $top=Department::find(1);
                $user->topDepartment =$top->name;
            }else{

                $tops =WorkflowUserService::fetchUserPrimaryDeptPath($id);
                $tops=explode('/',$tops);

                $user->topDepartment = $tops[0];
                $user->firstDepartment=isset($tops[1])?$tops[1]:$tops[0];
            }




//            /$user->tel = isset($tops->tel) ? $tops->tel : "";
            //获取部门
            $user->department = isset($priDepart->department) ? $priDepart->department->name : "";

            $array = Contract::where('user_id', $id)
                ->where('status', 2)
                ->orderBy('created_at', 'decs')
                ->select('id', 'user_id', 'version', 'probation', 'contract', 'entry_at', 'contract_end_at')
                ->get();
            if (!empty($array) && count($array) > 0) {
                $data = $array->toArray();
                foreach ($data as $val) {
                    if ($val['version'] == '1') {
                        $user->firstTime = $val['entry_at'];
                        $user->firstEndTime = $val['contract_end_at'];
                    }
                }
                if (count($data) > 0) {
                    $user->Time = $data[0]['entry_at'];
                    $user->EndTime = $data[0]['contract_end_at'];
                    if (!empty($user->join_at)) {
                        $probation = date("Y-m-d", strtotime("+" . $data[0]['probation'] . "months", strtotime($user->join_at)));
                        $user->probation = $user->join_at . '-' . $probation;
                        $user->positive = $probation . '-' . date("Y-m-d", strtotime("+" . $data[0]['contract'] . "years", strtotime($probation)));
                        $user->timeLimit = $data[0]['contract'];
                    }
                }
                $user->renew_count = count($data) - 1;
            } else {
                $user->firstTime = "";
                $user->firstEndTime = "";
                $user->Time = "";
                $user->EndTime = "";
                $user->probation = "";
                $user->positive = "";
                $user->timeLimit = "";
                $user->renew_count = "";
            }
            return $user;

    }

    public function fetchChildrenDepartmentsById()
    {
        $id = Request::get('id', Department::ROOT_DEPARTMENT_ID);
        $parentIds = Department::fetchAllParentId();
        $temp = Department::fetchDepartmentList($id);
        $data = [];
        if ($temp->isNotEmpty()) {
            $temp->each(function ($item, $key) use (& $data, $parentIds) {
                $data[$key]['id'] = $item->id;
                $data[$key]['name'] = $item->name;
                $data[$key]['has_child'] = false !== strpos($parentIds . ',', $item->id . ',');
            });
        }

        return returnJson($message = 'ok', $code = 200, $data);
    }

    public function fetchAllCompanies()
    {
        $data = Company::fetchCompanyList();
        return returnJson($message = 'ok', $code = 200, $data);
    }

    public function getAllDepartmentList()
    {
        $user = Auth::user();
        $data = $this->repository->getAllDepartmentList();

        return returnJson($message = 'ok', $code = 200, $data);
    }


    public function getAllDepartmentUserList()
    {
        $user = Auth::user();
        $data = $this->repository->getAllDepartmentUserList();

        return returnJson($message = 'ok', $code = 200, $data);
    }
}
