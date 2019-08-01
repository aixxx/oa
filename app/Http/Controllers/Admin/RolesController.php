<?php

namespace App\Http\Controllers\Admin;

use DB;
use App;
use Response;
use UserFixException;
use DevFixException;
use Illuminate\Http\Request;
use Silber\Bouncer\Database\Role;
use Silber\Bouncer\Database\Ability;
use App\Http\Controllers\Controller;

class RolesController extends Controller
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
        $roles = Role::all()->paginate(30);
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $abilities = Ability::all();
        $constant  = config('constant');
        $menu      = $constant['menu'];
        $titleMap  = $constant['titleMap'];
        return view('admin.roles.create', compact('abilities', 'menu', 'titleMap'));
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
        $abilities     = Ability::all();
        $constant      = config('constant');
        $menu          = $constant['menu'];
        $titleMap      = $constant['titleMap'];
        $role          = Role::findOrFail($id);
        $roleAbilities = $role->getAbilities()->pluck('id', 'name')->toArray();
        $ids           = array_values($roleAbilities);
        return view('admin.roles.edit', compact('role', 'abilities', 'ids', 'menu', 'titleMap'));
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $info = $request->all();
            $role = Role::findOrFail($id);
            $this->checkEdit($info, $id);

            //普通用户角色禁止修改
            if ('plain_user' == $role->name && ($info['name'] != 'plain_user')) {
                throw new UserFixException('本角色为基础角色，不能修改');
            }

            $role->update($info);
            foreach ($role->getAbilities() as $ability) {
                $role->disallow($ability->name);
            }

            $abilities = [];
            foreach ($info as $k => $i) {
                if (false !== strpos($k, 'name_item_')) {
                    $abilities = array_merge($abilities, $i);
                }
            }
            $role->allow($abilities);
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
