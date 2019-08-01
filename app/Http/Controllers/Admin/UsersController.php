<?php

namespace App\Http\Controllers\Admin;

use DB;
use App;
use Response;
use App\Models\User;
use DevFixException;
use UserFixException;
use Illuminate\Http\Request;
use Silber\Bouncer\Database\Role;
use App\Http\Controllers\Controller;

class UsersController extends Controller
{
    protected $operateLog;

    public function __construct()
    {
        $this->middleware('adminAuth:admin');
        $this->operateLog = App::make('operatelog');
        $this->middleware('afterlog:admin')->only('store', 'update', 'destroy');
    }

    public function index()
    {
        $users = $this->fetchAdminUsers()->simplePaginate(30, ['*']);
        $links = str_replace(['&amp;laquo;', '&amp;raquo;'], ['<<', '>>'], $users->links());
        return view('admin.users.index', compact('users', 'links'));
    }

    public function create()
    {
        $users = $this->fetchPlainUsers();
        $roles = Role::get()->pluck('name', 'id');
        return view('admin.users.create', compact('roles', 'users'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $user  = User::findOrFail($request->input('user_id'));
            $roles = $request->input('roles');

            if (empty($user)) {
                throw new UserFixException('用户不存在');
            }

            if (empty($roles)) {
                throw new UserFixException('角色不能为空');
            }

            $plainUserRole = $this->fetchRoleByName('plain_user');

            if (empty($plainUserRole)) {
                throw new UserFixException('系统错误：不存在普通用户角色');
            }
            $roles = array_merge($roles, [$plainUserRole->id]);

            foreach ($roles as $role) {
                $user->assign($role);
            }

            DB::commit();
            return Response::json(['code' => 0, 'status' => 'success', 'message' => '创建成功']);
        } catch (UserFixException $e) {
            DB::rollBack();
            return Response::json(['code' => $e->getCode(), 'status' => 'failed', 'message' => $e->getMessage()]);
        } catch (DevFixException $e) {
            DB::rollBack();
            report($e);
            return Response::json(['code' => $e->getCode(), 'status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $roles = Role::get()->pluck('name', 'name');
        $user  = User::findOrFail($id);
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $user  = User::findOrFail($id);
            $roles = $request->input('roles');

            if (empty($user)) {
                throw new UserFixException('用户不存在');
            }

            if (empty($roles)) {
                throw new UserFixException('角色不能为空');
            }

            foreach ($user->roles as $role) {
                $user->retract($role);
            }

            $plainUserRole = $this->fetchRoleByName('plain_user');

            if (empty($plainUserRole)) {
                throw new UserFixException('系统错误：不存在普通用户角色');
            }

            $roles = array_merge($roles, [$plainUserRole->id]);

            if ($request->input('roles')) {
                foreach ($roles as $role) {
                    $user->assign($role);
                }
            }
            DB::commit();
            return Response::json(['code' => 0, 'status' => 'success', 'message' => '修改成功']);
        } catch (UserFixException $e) {
            DB::rollBack();
            return Response::json(['code' => $e->getCode(), 'status' => 'failed', 'message' => $e->getMessage()]);
        } catch (DevFixException $e) {
            DB::rollBack();
            report($e);
            return Response::json(['code' => $e->getCode(), 'status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    /**
     * 获取管理员
     * 管理员的定义：在职，拥有除了普通员工权限之外还有别的权限
     * @return $this
     */
    private function fetchAdminUsers()
    {
        return User::leftJoin('assigned_roles', 'users.id', '=', 'assigned_roles.entity_id')
            ->leftJoin('roles', 'roles.id', '=', 'assigned_roles.role_id')
            ->where('status', User::STATUS_JOIN)
            ->where('roles.name', '<>', 'plain_user')
            ->whereNotNull('entity_id')
            ->select([
                'users.id',
                'users.name',
                'employee_num',
                'chinese_name',
                'english_name',
                'position',
                'entity_id',
            ])->distinct('id')
            ->orderBy('id', 'desc');
    }

    /**
     * 获取普通员工
     * 普通员工的定义：在职，只有一个普通员工角色
     * @return \Illuminate\Support\Collection
     */
    private function fetchPlainUsers()
    {
        return User::leftJoin('assigned_roles', 'users.id', '=', 'assigned_roles.entity_id')
            ->where('status', User::STATUS_JOIN)
            ->select(
                'users.id',
                'users.name',
                DB::raw('COUNT(role_id)')
            )->groupBy(['users.name'])
            ->having(DB::raw('COUNT(role_id)'), '1')
            ->orderBy('users.name')
            ->pluck('users.name', 'users.id');
    }

    private function fetchRoleByName($name)
    {
        return Role::query()->where('name', $name)->first();
    }
}
