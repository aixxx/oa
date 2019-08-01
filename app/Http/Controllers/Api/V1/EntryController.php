<?php

namespace App\Http\Controllers\Api\V1;

use App\Constant\ConstFile;
use App\Exceptions\DiyException;
use App\Models\Message\CronPushRecord;
use App\Models\Message\Message;
use App\Models\OperateLog;
use App\Models\User;
use App\Models\Workflow\Entry;
use App\Models\Workflow\Flow;
use App\Models\Workflow\Proc;
use App\Models\Workflow\Workflow;
use App\Notifications\MessageNotification;
use App\Repositories\CronPushRecordRepository;
use App\Repositories\ReportRepository;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Repositories\EntryRepository;

class EntryController extends BaseController
{
    /**
     * @var mixed
     */
    protected $repository;

    public function __construct()
    {
        $this->repository = app()->make(EntryRepository::class);
    }

    public function toDoList()
    {
        return $this->repository->fetchToDo();
    }

    public function workflowList()
    {
        return $this->repository->fetchWorkflowList();
    }

    public function createWorkflow(Request $request)
    {
        return $this->repository->createWorkflow($request);
    }

    public function storeWorkflow(Request $request)
    {
        try{
            return $this->repository->storeWorkflow($request);
        }catch (\Exception $exception){
            return returnJson($exception->getMessage(),
                $exception->getCode());
        }
    }

    public function showWorkflow(Request $request)
    {
        return $this->repository->workflowShow($request);
    }

    //审批人视角
    public function showAuditorWorkflow(Request $request)
    {
        return $this->repository->workflowAuthorityShow($request);
    }

    public function passWorkflow(Request $request)
    {
        return $this->repository->passWorkflow($request);
    }

    public function rejectWorkflow(Request $request)
    {
        return $this->repository->rejectWorkflow($request);
    }


    public function fetchBasicInfo()
    {
        return $this->repository->fetchBasicInfo();
    }

    /**
     * 我的申请历史
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function history(Request $request)
    {
        $flow_id = $request->get('flow_id', 0);
        $status  = $request->get('status', '');
        $page    = $request->get('page', 1);
        $keyword = $request->get('keyword', '');
        $size    = $request->get('size', 20);
        $type    = $request->get('type', Flow::TYPE_ATTENTION);
        $user_id = $request->get('user_id');
        return $this->repository->history($page, intval($flow_id), $status, $keyword, $size, $type, $user_id);
    }

    /**
     * 撤销
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function cancel(Request $request)
    {
        try {


            $uid      = \Auth::id();
            $entry_id = $request->get('entry_id');
            $content  = $request->get('content');
            $entryObj = Entry::find($entry_id);
            //已经有审核的记录了 不让撤销
            $cnt = Proc::query()->where('entry_id', '=', $entry_id)
                ->where('status', '=', Proc::STATUS_PASSED)
                ->where('user_id', '!=', $entryObj->user_id)
                ->count();

            if ($cnt > 0) {
                return returnJson(ConstFile::API_RESPONSE_FAIL_MESSAGE,
                    ConstFile::API_RESPONSE_FAIL, ['已有审核记录，不能撤销操作']);
            }

            if (empty($content)) {
                $content = Carbon::now()->toDateString() . '用户:' . Auth::user()->chinese_name . '撤销了ID为' . $entry_id . '的流程申请';
            }
            $data = [
                'operate_user_id' => $uid,
                'action'          => 'cancel',
                'type'            => OperateLog::TYPE_WORKFLOW,
                'object_id'       => $entry_id,
                'object_name'     => $entryObj->title,
                'content'         => $content,
            ];
            OperateLog::query()->insert($data);
            $entryObj->status = Entry::STATUS_CANCEL;
            $res              = $entryObj->save();
            //
        } catch (\Exception $exception) {
            return returnJson($exception->getMessage(),
                $exception->getCode());
        }
        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE,
            ConstFile::API_RESPONSE_SUCCESS, $res);
    }

    /**
     * 催办
     * @param Request $request
     * @return array
     */
    public function urgeShow(Request $request)
    {
        $user_id = Auth::id();

        $entry_id = $request->get('entry_id');

        $procs_id = $request->get('procs_id');
        $entryObj = Entry::find($entry_id);
        //接受人
        $uids = $entryObj->procs->pluck('user_id');
//        $receivers = User::query()->whereIn('id',  $uids->toArray())->get();
        $proces = $entryObj->procs;
        foreach ($proces as $proc) {
            $uid = $proc->user_id;
            if ($uid == $user_id) {
                continue;
            }
            $data = [
                'receiver_id'     => $uid,
                'sender_id'       => $user_id,
                'content'         => $entryObj->title,
                'status'          => 0,
                'flag'            => 0,
                'sender_status'   => 0,
                'receiver_status' => 0,
                'remark'          => '催办',
                'type'            => Message::MESSAGE_TYPE_URGE,
                'relation_id'     => $proc->id,
            ];
            Message::query()->create($data);
        }
        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE,
            ConstFile::API_RESPONSE_SUCCESS, [true]);
    }

    /**
     * 保存催办
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function urgeSave(Request $request)
    {
        $procsId      = $request->get('procs_id');
        $receiverId   = $request->get('receiver_id');
        $noticeType   = $request->get('notice_type');
        $isSendTiming = $request->get('is_send_timing');
        $sendTime     = $request->get('send_time');

        $procsObj = Proc::find($procsId);

        $entryObj = $procsObj->entry;

        $noticeRepository    = app()->make(CronPushRecordRepository::class);
        $record              = new CronPushRecord();
        $record->push_at     = strtotime($sendTime);
        $record->type        = CronPushRecord::TYPE_URGE;
        $record->type_pid    = $procsId;
        $record->type_title  = $entryObj->title;
        $record->content     = $entryObj;
        $record->notice_type = $noticeType;
        $record->target_uids = $receiverId;
        $data                = $noticeRepository->insertRecord($record);
        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE,
            ConstFile::API_RESPONSE_SUCCESS, $data);
    }
}
