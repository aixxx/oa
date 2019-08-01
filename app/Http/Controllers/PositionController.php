<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use DB;
use App;
use Illuminate\Database\Query\Builder;
use Response;
use UserFixException;
use DevFixException;
use App\Constant\ConstFile;
use Illuminate\Http\Request;
use Silber\Bouncer\Database\Role;
use Silber\Bouncer\Database\Ability;
use App\Http\Controllers\Controller;
use App\Models\Position\Position;
use App\Models\Power\Roles;
use App\Models\Power\PositionsRoles;
use App\Models\Position\PositionDepartment;
use App\Models\Department;

class PositionController extends Controller
{
    protected $operateLog;

    public function __construct()
    {
        $this->middleware('afterlog')->only('store', 'update', 'destroy');
    }

    public function index(Request $request)
    {
            $deptId = $request->get('deptId');
            $position = Position::getList($deptId);
            $dept = Department::find($deptId);
            return view('position.index', compact('position','dept'));
    }

    public function create(Request $request)
    {
        $roles = Roles::all();
        $deptId = $request->get('deptId');
        $dept = Department::find($deptId);
        return view('position.create', compact('roles', 'dept'));
    }

    public function store(Request $request)
    {
        try {
            //验证
            $this->validate($request, [
                'deptId' => 'bail|required|numeric',
                'name' => 'bail|required|max:191',
                'is_leader' => 'bail|required|numeric|in:0,1',
            ]);

            DB::beginTransaction();
                $info = $request->all();
                Position::create($info);
            DB::commit();
            return Response::json(['code' => 0, 'status' => 'success', 'message' => '创建成功']);
        } catch (UserFixException $e) {
            DB::rollBack();
            return Response::json(['code' => $e->getCode(), 'status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $roles = Roles::all();
        $info = Position::query()->find($id);
        $myRoles = PositionsRoles::query()->where('position_id', $id)->pluck('role_id')->all();
        $dept = $info->hasOneDept->hasOneDepartment;
        return view('position.edit', compact('roles', 'dept', 'info', 'myRoles'));
    }

    public function update(Request $request, $id)
    {
        //验证
        $this->validate($request, [
            'deptId' => 'bail|required|numeric',
            'name' => 'bail|required|max:191',
            'is_leader' => 'bail|required|numeric|in:0,1',
        ]);

        try {
            $id = intval($id);
            if(!$id)
                return Response::json(['code' => -1, 'status' => 'failed', 'message' => 'ID格式错误']);

            $info = $request->all();
            //编辑职务
            $position = Position::query()->find($id);
            if(empty($position))
                return Response::json(['code' => -1, 'status' => 'failed', 'message' => '数据不存在']);
            DB::beginTransaction();
            $position->name = $info['name'];
            $position->is_leader = $info['is_leader'];
            //更新职位
            $position->save();
            //重置职务角色
            PositionsRoles::query()->where('position_id', $id)->delete();
            if(isset($info['roles'])) {
                $roles = [];
                foreach ($info['roles'] as $v) {
                    $roles[] = [
                        'position_id' => $position->id,
                        'role_id' => $v,
                        'created_at' => Carbon::now()->toDateTimeString(),
                        'updated_at' => Carbon::now()->toDateTimeString(),
                    ];
                }
                //增加角色关联
                if ($roles)
                    PositionsRoles::query()->insert($roles);
            }
            DB::commit();
            return Response::json(['code' => 0, 'status' => 'success', 'message' => '修改成功']);
        }catch (UserFixException $e) {
            DB::rollBack();
            return Response::json(['code' => $e->getCode(), 'status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function destroy(Request $request)
    {
        $id = intval($request->get('id'));
        $deptId = intval($request->get('deptId'));

        if(!$id || !$deptId)
            return Response::json(['code' => '-1', 'status' => 'failed', 'message' => '参数格式错误']);
        try {
            $dept = PositionDepartment::query()
                ->where('department_id', $deptId)
                ->where('position_id', $id)
                ->first();
            if(empty($dept))
                return Response::json(['code' => '-1', 'status' => 'failed', 'message' => '部门-职务 数据错误']);

            $position = Position::query()->find($id);
            if(empty($position))
                return Response::json(['code' => '-1', 'status' => 'failed', 'message' => 'ID不存在']);
            DB::beginTransaction();
            //职务逻辑删除
            /** @var Builder $position */
            $position->delete();
            //关联 职务 部门 逻辑删除
            /** @var Builder $dept */
            $dept->delete();
            //关联 职务 角色 逻辑删除
            PositionsRoles::query()->where('position_id', $id)->delete();
            DB::commit();
            return Response::json(['code' => 0, 'status' => 'success', 'message' => '删除成功']);
        } catch (UserFixException $e) {
            DB::rollBack();
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
            throw new UserFixException('职位名称不能为空');
        }

        if ($this->fetchRoleByTitle($info['title'])) {
            throw new UserFixException('职位名称已经存在');
        }
    }

    private function checkEdit($info, $id)
    {
        if (empty($info['title'])) {
            throw new UserFixException('职位名称不能为空');
        }

        $record = $this->fetchRoleByTitle($info['title']);
        if ($record && ($record->id != $id)) {
            throw new UserFixException('职位名称已经存在');
        }
    }
}
