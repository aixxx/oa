<?php

namespace App\Http\Controllers\Workflow;

use App\Models\User;
use App\Models\Workflow\AuthorizeAgent;
use App\Models\Workflow\Flow;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Response;

class AuthorizeAgentController extends Controller
{
    public function index()
    {
        $agents = AuthorizeAgent::getUserAgents([Auth::id()]);
        return view('workflow.authorize_agent.index')->with(compact('agents'));
    }

    public function create()
    {
        $workflow_flows  = Flow::findFlowNos();
        $select_form[''] = '授权所有流程';
        foreach ($workflow_flows as $workflow_flow) {
            $select_form[$workflow_flow->flow_no] = $workflow_flow->flow_name;
        }
        return view('workflow.authorize_agent.create')->with(compact('select_form'));
    }

    public function store(Request $request)
    {
        $data                         = $this->validate($request, [
            'flow_no'               => 'max:255',
            'agent_user_id'         => 'required',
            'authorize_valid_begin' => 'required',
            'authorize_valid_end'   => 'required',
        ]);
        $data['authorizer_user_id']   = Auth::id();
        $data['authorizer_user_name'] = Auth::user()->chinese_name;
        $data['agent_user_name']      = User::findOrFail($data['agent_user_id'])->chinese_name;
        $data['authorize_valid_end']  = $data['authorize_valid_end'] . " 23:59:59";
        if (is_null($data['flow_no'])) {
            $data['flow_no'] = '';
        }
        AuthorizeAgent::create($data);
        return Response::json(['code' => 0, 'success' => 'success', 'message' => '添加成功']);
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        AuthorizeAgent::deleteUserAgent(Auth::id(), $id);
        return Response::json(['code' => 0, 'status' => 'success', 'message' => '删除成功']);
    }
}
