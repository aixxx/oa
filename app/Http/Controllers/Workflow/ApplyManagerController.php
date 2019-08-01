<?php

namespace App\Http\Controllers\Workflow;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Workflow\Entry;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Workflow\Flow;
use Response;
use Log;
use Auth;
use App;
use DB;
use DevFixException;
use UserFixException;

class ApplyManagerController extends Controller
{
    protected $operateLog;

    public function __construct()
    {
        $this->operateLog = App::make('operatelog');
    }

    public function index(Request $request)
    {
        $searchData            = $request->toArray();
        $flows                 = Flow::getFlowsOfNo(); // 流程列表
        $entries               = Entry::getAllApplyEntries($searchData);
        $entryEntriesStatusMap = Entry::STATUS_MAP; // 申请单状态map

        $compact = [
            'flows',
            'entryProcStatusMap',
            'entries',
            'entryEntriesStatusMap',
            'searchData',
        ];
        return view(
            'workflow.manager.index',
            compact($compact)
        );
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $entry = Entry::find($id);
            //合同申请撤销的时候同步更新合同表中对应记录的状态
            if ($entry->flow->flow_no == Entry::WORK_FLOW_NO_CONTRACT_APPLY) {
                Contract::updateContractStatusByEntryId($id, Contract::CONTRACT_STATUS_CANCEL);
            }

            $data = [
                'operate_user_id' => auth()->id(),
                'action'          => 'cancel_workflow',
                'type'            => 'cancel',
                'object_id'       => $id,
                'object_name'     => $id,
                'content'         => Carbon::now()->toDateString() . '用户:' . Auth::user()->chinese_name . '撤销了ID为' . $id . '的流程申请',
            ];

            $this->operateLog->save($data);
            if (Entry::deleteEntry($id)) {
                $message = Carbon::now()->toDateString() . '用户:' . Auth::user()->chinese_name . '撤销了ID为' . $id . '的流程申请';
                DB::commit();
                Log::info($message);
                return Response::json(['code' => 0, 'status' => 'success', 'message' => '撤销成功']);
            } else {
                DB::rollBack();
                return Response::json(['code' => 0, 'status' => 'success', 'message' => '撤销失败']);
            }
        } catch (DevFixException $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return Response::json(['code' => 0, 'status' => 'success', 'message' => '撤销失败']);
        } catch (UserFixException $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return Response::json(['code' => 0, 'status' => 'success', 'message' => '撤销失败']);
        }
    }
}
