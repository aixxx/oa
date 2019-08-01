<?php
namespace App\Http\Controllers\Workflow;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Workflow\WorkflowRole;
use App\Models\Workflow\WorkflowRoleUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use UserFixException;
/**
 * Created by PhpStorm.
 * User: aike
 * Date: 2018/7/26
 * Time: 下午6:18
 */

class RoleUserController extends Controller
{
    public function index(Request $request)
    {
        $roleId     = $request->input('role_id'); // 角色id
        $roleUsers  = WorkflowRoleUser::getRoleUsersByRole($roleId);
        return view('workflow.role_user.index')->with(compact('roleUsers', 'roleId'));
    }

    public function create(Request $request)
    {
        $roleIdSelected = $request->input('role_id'); // 角色id
        $roles          = WorkflowRole::getRoles();
        $rolesMap       = [];
        foreach ($roles as $role) {
            $rolesMap[$role->id] = $role->role_name;
        }
        return view('workflow.role_user.create')->with(compact('roles', 'rolesMap', 'roleIdSelected'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $roleUser=WorkflowRoleUser::getByRoleAndUser($data['role_id'], $data['user_id']);
        if ($roleUser){
            throw new UserFixException('该角色已存在该用户');
        }
        $data['user_chinese_name']         = User::findById($data['user_id'])->chinese_name;
        $data['creater_user_id']           = Auth::id();
        $data['creater_user_chinese_name'] = Auth::user()->name;

        WorkflowRoleUser::create($data);
        return redirect()->route('workflow.role_user.index', ['role_id' => $data['role_id']])->with(['success' => 1, 'message' => '添加成功']);
    }


    public function destroy($id)
    {
        WorkflowRoleUser::deleteById($id);
        return Response::json(['code' => 0, 'status' => 'success', 'message' => '删除成功']);
    }
}
