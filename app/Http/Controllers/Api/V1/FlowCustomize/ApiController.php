<?php
namespace App\Http\Controllers\Api\V1\FlowCustomize;

use App\Constant\ConstFile;
use App\Http\Controllers\Api\V1\Administrative\ContractController;
use App\Http\Controllers\Api\V1\BaseController;
use App\Models\Comments\TotalComment;
use App\Models\Workflow\Entry;
use App\Models\Workflow\Proc;
use App\Models\Workflow\Workflow;
use App\Repositories\Administrative\ContractRepository;
use App\Services\AuthUserShadowService;
use Request;
use DB;
use Exception;

class ApiController extends BaseController
{
    /*
     * 我的申请
     * $id entry_id
     * */
    public function apiFlowCustomize(){
        try {
            $id = (int)Request::get('id');
            if(!$id) return returnJson('参数错误', ConstFile::API_RESPONSE_FAIL);

            $flow = Entry::find($id);
            $flow_no = Q($flow,'flow','flow_no');

            $authAuditor = new AuthUserShadowService();
            $entry = Entry::findUserEntry($authAuditor->id(), $id);
            $showData = DB::table($flow_no)->where('entrise_id', $id)->first();
            $list = [
                'show_data' => $showData,
                'processes' => $this->fetchEntryProcess($entry)
            ];
            return returnJson('', ConstFile::API_RESPONSE_SUCCESS, $list);
        } catch (Exception $e) {
            return returnJson($e->getMessage(), $e->getCode());
        }
    }

    public function fetchEntryProcess(Entry $entry)
    {
        $processes = (new Workflow())->getProcs($entry);
        if (empty($processes)) {
            throw new Exception('流程没有配置审批节点');
        }
        $processAuditors = $temp = [];
        foreach ($processes as $process) {
            $temp['process_name'] = $process->process_name;
            $temp['auditor_name'] = '';
            $temp['approval_content'] = $process->proc ? $process->proc->content : '';

            if ($process->proc && $process->proc->auditor_name) {
                $temp['auditor_name'] = $process->proc->auditor_name;
            } elseif ($process->proc && $process->proc->user_name) {
                $temp['auditor_name'] = $process->proc->user_name;
            } else {
                $temp['auditor_name'] = $process->auditors;
            }

            $temp['status'] = $process->proc ? $process->proc->status : '';
            $temp['status_name'] = '';

            if ($process->proc && $process->proc->status == Proc::STATUS_REJECTED) {
                $temp['status_name'] = '驳回';
            } elseif ($process->proc && $process->proc->status == Proc::STATUS_PASSED) {
                $temp['status_name'] = '完成';
            } else {
                $temp['status_name'] = '待处理';
            }
            $processAuditors[] = $temp;
        }

        return $processAuditors;
    }

    /*
     * 我的审核
     * $id process_id
     * */
    public function apiFlowCustomizeAuditorFlowShow(){
        try {
            $id = (int)Request::get('id');
            if(!$id) return returnJson('参数错误', ConstFile::API_RESPONSE_FAIL);

            $authAuditor = new AuthUserShadowService();

            $process = Proc::findUserProcAllStatus($authAuditor->id(), $id);
            /** @var Entry $entry */
            $entry = Entry::findOrFail($process->entry_id);
            $flow_no = Q($entry,'flow','flow_no');
            $showData = DB::table($flow_no)->where('entrise_id', $entry->id)->first();

            $comments = TotalComment::query()
                ->where('type', '=', $entry->flow_id)
                ->where('entry_id', '=', $id)
                ->with(['user'])
                ->get();
            $list = [
                'show_data' => $showData,
                'processes' => $this->fetchEntryProcess($entry),
                'proc' => $process->toArray(),
                'procs_id' => $entry->procsFirstNode()->id,
                'comment_for_end' => $comments,   // 整个流程完成之后的评论
            ];

            return returnJson('', ConstFile::API_RESPONSE_SUCCESS, $list);
        } catch (Exception $e) {
            return returnJson($e->getMessage(), $e->getCode());
        }
    }
}
