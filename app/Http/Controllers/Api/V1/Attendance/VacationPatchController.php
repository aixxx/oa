<?php

namespace App\Http\Controllers\Api\V1\Attendance;

use App\Constant\ConstFile;
use App\Http\Controllers\Api\V1\BaseController;
use App\Models\Vacations\VacationPatchRecord;
use App\Models\Workflow\Flow;
use App\Models\Workflow\Workflow;
use App\Repositories\EntryRepository;
use App\Services\WorkflowUserService;
use Auth;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class VacationPatchController extends BaseController {
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
     * @throws Exception
     */
    public function index(Request $request){

        $canSeeFlowIds = WorkflowUserService::fetchUserCanSeeWorkflowIds(Auth::id());
        $flow_id = $request->get('flow_id', 0);
        $flow_id = (int)$flow_id;
        if ($flow_id < 0) {
            throw new Exception(sprintf('无效的流程ID:%s', $flow_id));
        }

        if (!$canSeeFlowIds->contains($flow_id)) {
            throw new Exception('当前流程不可用');
        }

        $flow = Flow::publish()->findOrFail($flow_id);
        $user_id = Auth::id();
        Workflow::generateHtml($flow->template, null, null, $user_id);
        $defaultTitle = $flow->flow_name . (new Carbon())->toDateString();
        $total = 5;//每月可补卡次数
        $day = date('Y-m');
        //获取当月补卡次数
        $cnt = VacationPatchRecord::query()
            ->where('uid', '=', $user_id)
            ->whereBetween('created_at', [ $day. '-00 00:00:00', $day. '-00 23:59:59'])
            ->count();

        $data = [
            'flow' => $flow->template->toArray(),
            'user_id' => $user_id,
            'default_title' => $defaultTitle,
            'already_count' => $cnt,
            'residue' => 5-$cnt,
        ];
//        $data['patch_more_than'] = 5;
        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE, ConstFile::API_RESPONSE_SUCCESS, $data);
    }
}
