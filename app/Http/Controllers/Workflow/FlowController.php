<?php

namespace App\Http\Controllers\Workflow;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use App\Models\Workflow\Entry;
use App\Models\Workflow\Flow;
use App\Models\Workflow\Flowlink;
use App\Models\Workflow\FlowType;
use App\Models\Workflow\Process;
use App\Models\Workflow\Template;
use App\Models\Workflow\Workflow;
use App\Models\Workflow\TemplateForm;
use App\Models\Workflow\ProcessVar;
use Carbon\Carbon;
use Guzzle\Iterator\FilterIterator;
use Illuminate\Http\Request;
use Auth;
use File;
use Log;
use DevFixException;
use UserFixException;
use App\Services\Common\FileService;
use Illuminate\Http\Response;
use DB;

class FlowController extends Controller
{
    private $operateLog;

    private $departments;
    private $users;

    public function __construct()
    {
        $this->operateLog  = \app()->make('operatelog');
        $this->departments = Department::fetchLeafDepartmentsWithAllPath();
        $this->users       = User::getAll()->pluck('chinese_name', 'id');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $flows             = Flow::orderBy('is_abandon', 'asc')
            ->orderBy('is_publish', 'asc')
            ->orderBy('id', 'desc')
            ->get();
        $flow_leader_links = Flow::FLOW_LEADER_LINK_MAP;

        return view('workflow.flow.index')->with(compact('flows', 'flow_leader_links'));
    }

    public function export(Request $request)
    {
        $allItems = $request->input('checkedAllItems');
        Log::info(Carbon::today()->toDateString() . '导出流程id为' . $allItems);
        $fileName = 'workflow-export' . Carbon::today()->toDateString() . '.json';
        try {
            $flowIds = explode(',', $allItems);
            if (empty($allItems)) {
                throw new UserFixException('请至少选中一个流程');
            }

            $data = [];
            foreach ($flowIds as $flowId) {
                $data[$flowId] = $this->fetchData($flowId);
            }

            (new FileService('json', $fileName, json_encode($data)))->export();
        } catch (UserFixException $e) {
            Log::error($e->getMessage());
            throw new UserFixException($e->getMessage());
        } catch (DevFixException $e) {
            Log::error($e->getMessage());
            throw new DevFixException($e->getMessage());
        }
        Log::info(Carbon::today()->toDateString() . '导出流程成功');
    }


    public function import(Request $request)
    {
        DB::beginTransaction();
        try {
            $path = $request->file('import-file')->path();
            $data = collect(json_decode(File::get($path), true));
            if ($data->isEmpty()) {
                Log::error(sprintf('获取数据为空,请检查文件%s', $path));
                throw new DevFixException(sprintf('获取数据为空,请检查文件%s', $path));
            }
            foreach ($data as $k => $d) {
                $this->importData($d);
            }
            DB::commit();
        } catch (UserFixException $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return response()->json(['status' => 'failed', 'messages' => $e->getMessage()]);
        } catch (DevFixException $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return response()->json(['status' => 'failed', 'messages' => $e->getMessage()]);
        }
        return response()->json(['status' => 'success', 'messages' => '流程导入成功']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $templates         = Template::get();
        $flow_types        = FlowType::get();
        $flow_leader_links = Flow::FLOW_LEADER_LINK_MAP;

        return view(
            'workflow.flow.create',
            [
                'users'       => $this->users,
                'departments' => $this->departments,
            ]
        )->with(
            compact(
                'templates',
                'flow_types',
                'flow_leader_links'
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'flow_name'   => 'required',
            'template_id' => 'required',
        ]);

        $data = $request->all();
        foreach ($data as &$item) {
            if (is_null($item)) {
                $item = '';
            }
        }

        $data['can_view_users']       = !empty($data['users_ids']) ? json_encode($data['users_ids']) : null;
        $data['can_view_departments'] = !empty($data['departments_ids']) ? json_encode($data['departments_ids']) : null;
        Flow::create($data);
        return redirect()->route('workflow.flow.index')->with(['success' => 1, 'message' => '添加成功']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $flow = Flow::findOrFail($id);

        return view('workflow.flow.show')->with(compact('flow'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function design(Request $request, $id)
    {
        $flow = Flow::findOrFail($id);

        return view('workflow.flow.design')->with(compact('flow'));
    }

    public function edit(Request $request, $id)
    {
        $flow              = Flow::findOrFail($id);
        $templates         = Template::get();
        $flow_types        = FlowType::get();
        $flow_leader_links = Flow::FLOW_LEADER_LINK_MAP;
        $can_view_users    = !empty($flow->can_view_users) ?
            json_decode($flow->can_view_users) : [];

        $can_view_departments = !empty($flow->can_view_departments) ?
            json_decode($flow->can_view_departments) : [];

        return view(
            'workflow.flow.edit',
            [
                'users'       => $this->users,
                'departments' => $this->departments,
            ]
        )->with(compact(
            'flow',
            'templates',
            'flow_types',
            'flow_leader_links',
            'can_view_users',
            'can_view_departments'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $flow = Flow::findOrFail($id);
        $data = $request->all();

        foreach ($data as &$item) {
            if (is_null($item)) {
                $item = '';
            }
        }
        $data['can_view_users']       = !empty($data['users_ids']) ? json_encode($data['users_ids']) : null;
        $data['can_view_departments'] = !empty($data['departments_ids']) ? json_encode($data['departments_ids']) : null;

        $flow->update($data);

        return redirect()->route('workflow.flow.index')->with(['success' => 1, 'message' => '更新成功']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $flow = Flow::findOrFail($id);

        if (Entry::where('flow_id', $flow->id)->first()) {
            return response()->json([
                'error' => 1,
                'msg'   => '该流程已经被使用，不能删除',
            ]);
        }

        if (Process::where('child_flow_id', $flow->id)->first()) {
            return response()->json([
                'error' => 1,
                'msg'   => '该流程已经被使用，不能删除',
            ]);
        }

        $flow->process()->delete();
        $flow->process_var()->delete();
        $flow->delete();

        return response()->json([
            'error' => 0,
            'msg'   => '流程删除成功',
        ]);

    }

    public function publish(Request $request)
    {
        try {
            $flow_id = $request->input('flow_id', 0);
            $flow    = Flow::findOrFail($flow_id);

            if (Flowlink::where(['flow_id' => $flow->id, 'type' => 'Condition'])->count() <= 1) {
                return response()->json([
                    'status_code' => 1,
                    'message'     => '发布失败，至少两个步骤',
                ]);
            }

            if (Flowlink::where(['flow_id' => $flow->id, 'type' => 'Condition', 'next_process_id' => -1])->count() >
                1
            ) {
                return response()->json([
                    'status_code' => 1,
                    'message'     => '发布失败，有步骤没有连线',
                ]);
            }

            if (!Flowlink::whereHas('process', function ($query) {
                $query->where('position', 0);
            })->where('flow_id', $flow_id)->first()
            ) {
                return response()->json([
                    'status_code' => 1,
                    'message'     => '发布失败，请设置起始步骤',
                ]);
            }

            // if(!Flowlink::whereHas('process',function($query){
            //     $query->where('position',9);
            // })->first()){
            //     return response()->json([
            //         'status_code'=>1,
            //         'message'=>'发布失败，请设置结束步骤'
            //     ]);
            // }

            $flowlinks = Flowlink::where(['flow_id' => $flow->id, 'type' => 'Condition'])
                ->whereHas('process', function ($query) {
                    $query->where('position', '!=', 0);
                })
                ->get();

            foreach ($flowlinks as $v) {
                if (!Flowlink::where(['flow_id' => $flow->id, 'process_id' => $v->process_id])
                    ->where('type', '!=', 'Condition')
                    ->whereHas('process', function ($query) {
                        $query->where('position', '!=', 0);
                    })
                    ->first()
                ) {
                    return response()->json([
                        'status_code' => 1,
                        'message'     => '发布失败，请给设置步骤审批权限',
                    ]);
                }
            }

            $flow->is_publish = 1;
            $flow->save();

            return response()->json([
                'status_code' => 0,
                'message'     => '发布成功',
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->with(['status_code' => 1, 'message' => $e->getMessage()]);
        }
    }

    public function cloneNewVersion(Request $request)
    {
        try {
            $flow_id = $request->input('id', 0);
            $res     = Workflow::cloneFlowNewVersion($flow_id);
            $this->operateLog->save([
                'operate_user_id' => Auth::id(),
                'action'          => 'clone_new_version',
                'type'            => '',
                'object_id'       => $res['old']->id,
                'object_name'     => $res['old']->flow_name,
                'content'         => json_encode([
                    'new' => $res['new']->toArray(),
                    'old' => $res['old']->toArray(),
                ]),
            ]);

            return response()->json([
                'status_code' => 0,
                'status'      => 'success',
                'message'     => '发布新版本成功',
            ]);
        } catch (\Exception $e) {
            Workflow::errLog('FlowCloneNewVersion', $e->getMessage() . $e->getTraceAsString());

            return response()->json([
                'status_code' => 1,
                'status'      => 'error',
                'message'     => '发布新版本失败',
            ]);
        }
    }

    /**
     * 设置废弃状态
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function setAbandon(Request $request)
    {
        try {
            $flow_id       = $request->input('flow_id', 0);
            $abandon_state = $request->input('abandon_state');
            Flow::setAbandonState($flow_id, $abandon_state);

            return response()->json([
                'status_code' => 0,
                'status'      => 'success',
                'message'     => '设置成功',
            ]);
        } catch (\Exception $e) {
            Workflow::errLog('FlowSetAbandon', $e->getMessage() . $e->getTraceAsString());

            return response()->json([
                'status_code' => 1,
                'status'      => 'error',
                'message'     => '设置失败',
            ]);
        }
    }

    /**
     * @param $row
     *
     * @return int|mixed
     */
    private function importData($row)
    {
        $templateExist = Template::whereTemplateName($row['template_name'])->first();

        if ($templateExist) {
            throw new UserFixException(sprintf('%s流程模版已经存在', $row['template_name']));
        }

        $template = Template::create(['template_name' => $row['template_name']]);

        collect($row['template_form'])->each(function ($item, $key) use ($template) {
            $item['template_id'] = $template->id;
            $this->deleteBeforeTime($item);
            TemplateForm::create($item);
        });

        $flowType = FlowType::firstOrCreate(['type_name' => $row['type_name']]);
        $flowData = $row['flow'];
        $this->deleteBeforeTime($flowData);
        $flowData['template_id'] = $template->id;
        $flowData['type_id']     = $flowType->id;
        $flow                    = Flow::create($flowData);

        Flow::setAbandonState($flow->id, $flowData['is_abandon']);

        $processIds = [];
        collect($row['workflow_process'])->each(function ($item, $key) use ($flow, $row, &$processIds) {
            $item['flow_id'] = $flow->id;
            $itemId          = $item['id'];
            $this->deleteBeforeTime($item);
            $process             = Process::create($item);
            $processIds[$itemId] = $process->id;
        });

        collect($row['workflow_process_var'])->transform(function ($item, $key) use ($flow, $processIds) {
            if (isset($processIds[$item['process_id']])) {
                $item['flow_id']    = $flow->id;
                $item['process_id'] = $processIds[$item['process_id']];
                $this->deleteBeforeTime($item);
                ProcessVar::create($item);
            }
        });

        collect($row['flow_links'])->each(function ($item, $key) use ($flow, $processIds) {
            $item['flow_id'] = $flow->id;
            $this->deleteBeforeTime($item);

            if (isset($processIds[$item['process_id']])) {
                $item['process_id'] = $processIds[$item['process_id']];
            }

            if ((isset($processIds[$item['next_process_id']]))) {
                $item['next_process_id'] = $processIds[$item['next_process_id']];
            }

            Flowlink::create($item);
        });

        $arr['list'] = collect(json_decode($flow->jsplumb, true)['list'])
            ->transform(function ($it, $k) use ($processIds, $flow) {
                $it['flow_id'] = $flow->id;
                $it['id']      = $processIds[$it['id']];
                if (!empty($it['process_to'])) {
                    $it['process_to'] = $this->fetchProcessToVar($it['process_to'], $processIds);
                }

                return $it;
            })->all();

        $arr['total'] = json_decode($flow->jsplumb, true)['total'];
        $flow->update(['jsplumb' => json_encode($arr)]);

        return $flow->id;
    }

    private function fetchProcessToVar($processTo, $processIds)
    {
        if (false !== strpos($processTo, ',')) {
            $info      = explode(',', $processTo);
            $processTo = collect($info)->transform(function ($it, $k) use ($processIds) {
                return $processIds[$it];
            })->all();

            return join($processTo, ',');
        }

        return $processIds[$processTo];
    }

    private function deleteBeforeTime(&$item)
    {
        unset($item['id']);
        unset($item['created_at']);
        unset($item['updated_at']);
    }

    private function fetchData($flowId)
    {
        $data = [];
        $flow = Flow::with('template', 'type', 'process', 'process_var')->find($flowId);

        if (!$flow) {
            throw new UserFixException(sprintf('流程ID%s不存在', $flowId));
        }

        if (!$flow->template) {
            throw new UserFixException(sprintf('流程ID为%s的模版不存在', $flowId));
        }

        $data['template_name'] = $flow->template->template_name;
        $data['template_form'] = TemplateForm::where('template_id', $flow->template->id)
            ->get((new TemplateForm())->fillable)
            ->toArray();
        $data['type_name']     = $flow->type->type_name;

        $data['workflow_process']     = $flow->process->toArray();
        $data['workflow_process_var'] = $flow->process_var->toArray();
        $data['flow']                 = Flow::findOrFail($flowId)->toArray();
        $data['flow_links']           = Flowlink::where('flow_id', $flowId)->get()->toArray();

        return $data;
    }
}
