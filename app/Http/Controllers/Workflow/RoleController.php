<?php

namespace App\Http\Controllers\Workflow;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Workflow\WorkflowRole;
use Illuminate\Http\Request;
use Response;
use UserFixException;

/**
 * Created by PhpStorm.
 * User: aike
 * Date: 2018/7/26
 * Time: 下午3:24
 */
class RoleController extends Controller
{
    protected $companyList;

    public function __construct()
    {
        $this->companyList = Company::getCompanyList()->pluck('name', 'id');
    }

    public function index()
    {
        return view('workflow.role.index')
            ->with(['roles' => WorkflowRole::getRoles(), 'companyList' => $this->companyList]);
    }

    public function create()
    {
        return view('workflow.role.create')->with(['companyList' => $this->companyList]);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $role = WorkflowRole::where('role_name', $data['role_name'])->first();
        if ($role) {
            throw new UserFixException('角色已经存在！请重新添加');
        }
        $data['company_id'] = $data['company_id'] ?? ['0'];
        $data['company_id'] = join($data['company_id'], ',');
        WorkflowRole::create($data);
        return redirect()->route('workflow.role.index')->with(['success' => 1, 'message' => '添加成功']);
    }

    public function destroy($id)
    {
        WorkflowRole::deleteById($id);
        return Response::json(['code' => 0, 'status' => 'success', 'message' => '删除成功']);
    }
}
