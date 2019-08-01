<?php

namespace App\Http\Controllers\User;

use App\Http\Requests\PendingUserRequest;
use App\Models\Attendance\AttendanceWorkClass;
use App\Models\Company;
use App\Models\Department;
use App\Models\DepartUser;
use App\Models\PendingUser;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\Controller;
use Mockery\CountValidator\Exception;
use UserFixException;
use Illuminate\Support\Facades\Hash;
use DB;

class PendingUsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('afterlog')->only('store', 'update', 'delete');
    }

    public function index()
    {
        $pendingUsers = User::with('company')->Where('status', '=', User::STATUS_PENDING_JOIN)->orderby('id', SORT_DESC)->paginate(30);
        $totalnum = $pendingUsers->count();
        $primarynum = $this->getPrimaryUserNum($pendingUsers);
        return view('user.pending-users.index', compact('pendingUsers', 'totalnum', 'primarynum'));
    }

    /**
     *获取记录总数
     * @param $model
     *
     * @return mixed
     */
    private function getPrimaryUserNum($model)
    {
        $num = $model->where('primary', '=', 1)->count();
        return $num;
    }

    /**
     * 删除待入职员工
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        $userId = $request->get('id');
        $desStatus = User::destroy($userId);
        if ($desStatus) {
            return response()->json(['status' => 'success', 'messages' => "删除成功"]);
        } else {
            return response()->json(['status' => 'failed', 'messages' => "删除失败"]);
        }
    }

    /**
     * 更新待入职员工
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */

    public function update(Request $request)
    {
        $user = $request->all();
        $updateData['chinese_name'] = trim($user['chinese_name']);
        $updateData['company_id'] = trim($user['company_id']);
        if (!isset($user['id']) || !($pendingUsers = User::findOrFail($user['id']))) {
            throw new Exception('不存在的用户');
        }

        $pendingUsers->fill($updateData);
        if ($pendingUsers->save()) {
            return redirect()->route('pendingusers.index', ['id' => $user['id']])->with('updateSuccess', '用户信息更新成功！');
        } else {
            return redirect()->route('pendingusers.edit', ['id' => $user['id']])->with('updateFail', '用户信息更新失败！');
        }
    }

    /**
     * 创建待入职员工
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(PendingUserRequest $request)
    {
        $user = $request->all();
        try {
            $checkuser = User::where('email', '=', strtolower(trim($user['email'])))->lockForUpdate()->count();
            if ($checkuser) {
                throw new UserFixException("系统唯一账号已存在请重新填写！");
            }

            DB::transaction(function () use ($user) {
                $insertData['email'] = trim($user['email']);
                $insertData['chinese_name'] = trim($user['chinese_name']);
                $insertData['password'] = Hash::make($user['password']);
                $insertData['status'] = User::STATUS_PENDING_JOIN;
                $insertData['company_id'] = trim($user['company_id']);
                $pendingUsers = new User();
                $pendingUsers->fill($insertData);
                $pendingUsers->save();
                User::grantPlainUser($pendingUsers->id);//用户需要有默认的plain_user权限才能使用基本的申请功能
            });
        } catch (\Exception $e) {
            $messages = $e->getMessage();
            return back()->with('storeError', $messages)->withInput();
        }
        return redirect()->route('pendingusers.index')->with('storeSuccess', "创建成功！");
    }

    /**
     * 待入职转入职
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function join($id)
    {
        $user = PendingUser::findOrFail($id);
        $user->department_path = Department::getDeptPath($user->department_id);
        $departments = Department::all();
        $companies = Company::where('status', '<>', Company::STATUS_DELETE)->get();

        $newEmployeeNum = User::genUniqueNum();
        //部门领导信息
        $deptLeader = DepartUser::with('department')->where('user_id', '=', 0)->get();
        $workClass = AttendanceWorkClass::orderBy('type')->get();

        $allUsers = User::getAll();
        return view('user.users.join',
            compact('user', 'companies', 'departments', 'newEmployeeNum', 'deptLeader', 'allUsers', 'workClass'));
    }

    /**
     * 创建待入职员工
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function create()
    {
        $departments = Department::all();
        $companies = Company::where('status', '<>', Company::STATUS_DELETE)->get();
        return view('user.pending-users.create', compact('pendingUsers', 'departments', 'companies'));
    }

    /**
     * 编辑待入职员工列表
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $companies = Company::where('status', '<>', Company::STATUS_DELETE)->get();
        return view('user.pending-users.edit', compact('companies', 'user'));
    }

    /**
     * 详情页
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        $user = PendingUser::findOrFail($id);
        if (isset($user)) {
            $departmentInfo = Department::where('id', '=', $user->department_id)->first();
            $user->department_name = $departmentInfo ? $departmentInfo->name : '';

            $companyInfo = Company::where('id', '=', $user->company_id)->first();
            $user->company_name = $companyInfo ? $companyInfo->name : '';
        }

        return view('user.pending-users.show', compact('user'));
    }
}
