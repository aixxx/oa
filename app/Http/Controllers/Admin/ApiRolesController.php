<?php

namespace App\Http\Controllers\Admin;

use DB;
use App;
use Response;
use App\Constant\ConstFile;
use UserFixException;
use DevFixException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Power\Roles;
use App\Models\Power\VueAction;
use App\Models\Power\RoutesRoles;

class ApiRolesController extends Controller
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
        $roles = Roles::with('belongsToManyVueAction')->orderBy('id', 'desc')->simplePaginate(ConstFile::PAGE_SIZE, ['*']);

        return view('admin.apiroles.index', compact('roles'));
    }

    public function create()
    {
        $roles = VueAction::with('hasManyChildren')->where('parent_id', 0)->get();
        return view('admin.apiroles.create', compact('roles', 'menu', 'titleMap'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $info = $request->all();
            $this->checkCreate($info);
            $roleData['title'] = $info['title'];
            $role = Roles::create($roleData);
            $roleRoute = [];
            if(isset($info['name_item']) && !empty($info['name_item']) ){
                foreach ($info['name_item'] as $k => $i) {
                    $roleRoute[$k]['title'] = $roleData['title'];
                    $roleRoute[$k]['action_id'] = $i;
                    $roleRoute[$k]['role_id'] = $role->id;
                }
                RoutesRoles::query()->insert($roleRoute);
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
        $roles = VueAction::with('hasManyChildren')->where('parent_id', 0)->get();
        $roleinfo = Roles::findOrFail($id);
        $routesRoles = $roleinfo->belongsToManyVueAction->toArray();
        $ids = array_column($routesRoles, 'id');
        return view('admin.apiroles.edit', compact('roleinfo', 'roles', 'ids'));
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $info = $request->all();
            $role = Roles::findOrFail($id);
            $this->checkEdit($info, $id);
            $roleData['title'] = $info['title'];
            $role->update($roleData);
            $roleRoute = [];
            if(isset($info['name_item']) && !empty($info['name_item']) ) {
                foreach ($info['name_item'] as $k => $i) {
                    $roleRoute[$k]['title'] = $roleData['title'];
                    $roleRoute[$k]['action_id'] = $i;
                    $roleRoute[$k]['role_id'] = $role->id;

                }
                RoutesRoles::query()->insert($roleRoute);
            }
            RoutesRoles::where('role_id', $id)->delete();

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
            $role = Roles::findOrFail($id);
            $role->delete();
            RoutesRoles::where('role_id', $id)->delete();
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


    private function fetchRoleByTitle($title)
    {
        return Roles::query()->where('title', $title)->first();
    }

    private function checkCreate($info)
    {
        if (empty($info['title'])) {
            throw new UserFixException('角色名称不能为空');
        }
        if ($this->fetchRoleByTitle($info['title'])) {
            throw new UserFixException('角色名称已经存在');
        }
    }

    private function checkEdit($info, $id)
    {
        if (empty($info['title'])) {
            throw new UserFixException('角色名称不能为空');
        }

        $record = $this->fetchRoleByTitle($info['title']);
        if ($record && ($record->id != $id)) {
            throw new UserFixException('角色名称已经存在');
        }

    }
}
