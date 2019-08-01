<?php

namespace App\Http\Controllers\Admin;

use DB;
use App;
use Response;
use UserFixException;
use DevFixException;
use App\Constant\ConstFile;
use Illuminate\Http\Request;
use Silber\Bouncer\Database\Role;
use Silber\Bouncer\Database\Ability;
use App\Http\Controllers\Controller;
use App\Models\Position\Position;
use App\Models\User;
use App\Models\Power\Roles;
use App\Models\Power\RolesUsers;

class UserController extends Controller
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

        $result = User::with(['primaryDepartUser', 'primaryDepartUser.department', 'company'])
            ->where("status", User::STATUS_JOIN)
            ->paginate(ConstFile::PAGE_SIZE);
        return view('admin.user.index', compact('result'));
    }

    public function create()
    {
        $position = Position::with('belongsToManyRoles')->orderBy('id', 'desc')->simplePaginate(ConstFile::PAGE_SIZE, ['*']);

        return view('admin.position.create', compact('position'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $info = $request->all();

            $this->checkCreate($info);
            $role      = Role::create($info);
            $abilities = [];
            foreach ($info as $k => $i) {
                if (false !== strpos($k, 'name_item_')) {
                    $abilities = array_merge($abilities, $i);
                }
            }

            $role->allow($abilities);
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

        $roles     = Roles::all();
        $user      = User::findOrFail($id);
        $rolesUser = $user->belongsToManyRoles->toArray();
        $ids       = array_column($rolesUser, 'role_id');

        return view('admin.user.edit', compact('roles', 'user', 'ids'));
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $info = $request->all();
            if (!isset($info['name_item'])) {
                RolesUsers::where('user_id', $id)->delete();
                return Response::json(['code' => 0, 'status' => 'success', 'message' => '修改成功']);
            }
            //普通用户角色禁止修改
            $roleRoute = [];
            foreach ($info['name_item'] as $k => $i) {
                $roleRoute[$k]['user_id'] = $id;
                $roleRoute[$k]['role_id'] = $i;

            }
            RolesUsers::where('user_id', $id)->delete();
            RolesUsers::query()->insert($roleRoute);

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

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $role = Role::findOrFail($id);
            if ('plain_user' == $role->name) {//普通用户角色是每个用户都有的角色，不能删除。
                throw new UserFixException('本角色为基础角色，不能删除');
            }
            $role->delete();
            DB::commit();
            return Response::json(['code' => 0, 'status' => 'success', 'message' => '删除成功']);
        } catch (UserFixException $e) {
            DB::rollBack();
            return Response::json(['code' => $e->getCode(), 'status' => 'failed', 'message' => $e->getMessage()]);
        } catch (DevFixException $e) {
            DB::rollBack();
            report($e);
            return Response::json(['code' => $e->getCode(), 'status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    private function fetchRoleByName($name)
    {
        return Role::query()->where('name', $name)->first();
    }

    private function fetchRoleByTitle($title)
    {
        return Role::query()->where('title', $title)->first();
    }

    private function checkCreate($info)
    {
        if (empty($info['title'])) {
            throw new UserFixException('角色名称不能为空');
        }

        if (empty($info['name'])) {
            throw new UserFixException('角色代码不能为空');
        }

        if ($this->fetchRoleByTitle($info['title'])) {
            throw new UserFixException('角色名称已经存在');
        }

        if ($this->fetchRoleByName($info['name'])) {
            throw new UserFixException('角色代码已经存在');
        }
    }

    private function checkEdit($info, $id)
    {
        if (empty($info['title'])) {
            throw new UserFixException('角色名称不能为空');
        }

        if (empty($info['name'])) {
            throw new UserFixException('角色代码不能为空');
        }

        $record = $this->fetchRoleByTitle($info['title']);
        if ($record && ($record->id != $id)) {
            throw new UserFixException('角色名称已经存在');
        }

        $record = $this->fetchRoleByName($info['name']);
        if ($record && ($record->id != $id)) {
            throw new UserFixException('角色代码已经存在');
        }
    }
}
