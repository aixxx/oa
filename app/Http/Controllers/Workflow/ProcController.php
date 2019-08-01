<?php

namespace App\Http\Controllers\Workflow;

use App\Http\Controllers\Controller;
use App\Models\Workflow\Workflow;
use App\Services\AuthUserShadowService;
use Illuminate\Http\Request;

use App\Models\Workflow\Entry;
use App\Models\Workflow\Proc;
use Illuminate\Support\Facades\DB;


class ProcController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $entry_id = $request->input('entry_id', 0);
        $entry    = Entry::findOrFail($entry_id);

        if ($entry->pid > 0) {
            $entry_id = $entry->pid;
        }

        $procs = Proc::select(DB::raw("min(id) id,entry_id,process_id,process_name,GROUP_CONCAT(user_name) user_name,auditor_name,status,content,max(updated_at) updated_at"))
            ->with('entry')
            ->where(['entry_id' => $entry_id])
            ->groupBy('process_id', 'concurrence', 'circle')
            ->orderBy('id', 'ASC')
            ->get();

        return view('workflow.proc.index')->with(compact('procs'));
    }

    public function children(Request $request)
    {
        $entry_id = $request->input('entry_id', 0);

        $procs = Proc::select(DB::raw("GROUP_CONCAT(id) id,entry_id,process_id,process_name,GROUP_CONCAT(emp_name) emp_name,auditor_name,status,content,created_at"))
            ->with('entry')
            ->where(['entry_id' => $entry_id])
            ->groupBy('process_id', 'concurrence', 'circle')
            ->orderBy('id', 'ASC')
            ->get();

        return view('proc.index')->with(compact('procs'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
        //
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
        $authAuditor = new AuthUserShadowService();
        $proc        = Proc::findUserProcAllStatus($authAuditor->id(), $id);

        $entry = Entry::findOrFail($proc->entry_id);

        if ($entry->pid > 0) {
            $form_html = $entry->parent_entry->flow->template ? Workflow::generateHtml($entry->parent_entry->flow->template, $entry->parent_entry) : '';
        } else {
            $form_html = $entry->flow->template ? Workflow::generateHtml($entry->flow->template, $entry) : '';
        }

        //申请进程
        $processes      = (new Workflow())->getProcs($entry);
        $processes_html = Workflow::generateProcessHtml($processes, $entry);

        return view('workflow.proc.show')->with(compact('proc', 'entry', 'form_html', 'processes_html'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
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
        //
    }

    public function pass(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            (new Workflow())->passWithNotify($id);
            DB::commit();
            return response()->json(['code' => 1, 'status' => 'success', 'message' => '提交成功']);
        } catch (\Exception $e) {
            DB::rollback();
            report($e);
            return response()->json(['code' => 0, 'status' => 'success', 'message' => $e->getMessage()]);
        }
    }

    public function passAndNext(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            (new Workflow())->passWithNotify($id);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            report($e);
            return response()->json(['code' => 0, 'status' => 'success', 'message' => $e->getMessage()]);
        }
        $authAuditor = new AuthUserShadowService();
        $entries     = Entry::getTodoEntry($authAuditor->id());
        if (!$entries->isEmpty() && isset($entries->first()->procs->first()->id)) {
            $message  = '你还有' . count($entries) . '条待审批';
            $redirect = route("workflow.proc.show", $entries->first()->procs->first()->id);
        } else {
            $message  = '你已全部审批完';
            $redirect = '';
        }
        return response()->json(['code' => 1, 'status' => 'success', 'message' => $message, 'redirect' => $redirect]);
    }

    public function unPassAndNext(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            (new Workflow())->reject($id, $request->input('content', ''));
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            report($e);
            return response()->json(['code' => 0, 'status' => 'success', 'message' => $e->getMessage()]);
        }
        $authAuditor = new AuthUserShadowService();
        $entries     = Entry::getTodoEntry($authAuditor->id());
        if (!$entries->isEmpty() && isset($entries->first()->procs->first()->id)) {
            $message  = '你还有' . count($entries) . '条待审批';
            $redirect = route("workflow.proc.show", $entries->first()->procs->first()->id);
        } else {
            $message  = '你已全部审批完';
            $redirect = '';
        }
        return response()->json(['code' => 1, 'status' => 'success', 'message' => $message, 'redirect' => $redirect]);
    }

    public function pass_all(Request $request)
    {
        $ids = $request->input('ids', []);
        foreach ($ids as $id) {
            DB::beginTransaction();
            try {
                (new Workflow())->passWithNotify($id);
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                report($e);
                return response()->json(['code' => 0, 'status' => 'success', 'message' => $e->getMessage()]);
            }
        }
        return response()->json(['code' => 1, 'status' => 'success', 'message' => '全部审批成功']);
    }

    public function unpass(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            (new Workflow())->reject($id, $request->input('content', ''));
            DB::commit();
            return response()->json(['code' => 1, 'status' => 'success', 'message' => '提交成功']);
        } catch (\Exception $e) {
            DB::rollback();
            report($e);
            return response()->json(['code' => 0, 'status' => 'success', 'message' => $e->getMessage()]);
        }
    }

    public function unpass_all(Request $request)
    {
        $ids = $request->input('ids', []);
        foreach ($ids as $id) {
            DB::beginTransaction();
            try {
                (new Workflow())->reject($id, $request->input('content', '批量拒绝'));
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                report($e);
                return response()->json(['code' => 0, 'status' => 'success', 'message' => $e->getMessage()]);
            }
        }
        return response()->json(['code' => 1, 'status' => 'success', 'message' => '全部拒绝成功']);
    }
}
