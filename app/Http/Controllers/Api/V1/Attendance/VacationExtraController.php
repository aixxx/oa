<?php

namespace App\Http\Controllers\Api\V1\Attendance;

use App\Constant\ConstFile;
use App\Http\Controllers\Api\V1\BaseController;
use App\Models\AttendanceApi\AttendanceApiAnomaly;
use App\Models\Workflow\Flow;
use App\Models\Workflow\Flowlink;
use App\Models\Workflow\Workflow;
use App\Repositories\EntryRepository;
use App\Services\WorkflowUserService;
use Auth;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Http\Request;

class VacationExtraController extends BaseController {
    protected  $repository;

    public function __construct()
    {
        parent::__construct();
        $this->repository = app()->make(EntryRepository::class);
        $this->middleware('auth:api');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function save(Request $request){

        DB::beginTransaction();

        $flow_id = $request->get('flow_id');
        $flow = Flow::findById($flow_id);
        $flow->checkCanApply(); // 校验是否可以提交该流程的申请,不可提交申请的话,创建或者修改申请单,也没有意义
        $uid = Auth::id();
        $tpl = $request->get('tpl');

        $res = AttendanceApiAnomaly::overtimeValidator($uid, $tpl['begin_time'], $tpl['end_time']);
        if($res['code'] == ConstFile::API_RESPONSE_FAIL){
            return returnJson(ConstFile::API_RESPONSE_FAIL_MESSAGE, ConstFile::API_RESPONSE_FAIL, $res);
        }

        $entry = $this->repository->updateOrCreateEntry($request, 0); // 创建或更新申请单
        if ($entry->isInHand()) {
            $flow_link = Flowlink::firstStepLink($entry->flow_id);
            //进程初始化
            (new Workflow())->setFirstProcessAuditor($entry, $flow_link);
        }
        $entry->save();
        DB::commit();

        $data = ['entry' => $entry->toArray()];
        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE, ConstFile::API_RESPONSE_SUCCESS, $data);
    }
}
