<?php

namespace App\Http\Controllers\Api\V1\Attendance;

use App\Constant\ConstFile;
use App\Http\Controllers\Api\V1\BaseController;
use App\Models\Vacations\UserVacation;
use App\Models\Workflow\Flow;
use App\Models\Workflow\Workflow;
use App\Repositories\EntryRepository;
use App\Services\WorkflowUserService;
use Auth;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class VacationLeaveController extends BaseController {
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
    public function index(Request $request){
        try{
            $data = $this->run($request);
        }catch (Exception $exception){
            $data = [];
            if(config('app.debug')) {
                $data = [
                    'message' => $exception->getMessage(),
                    'code' => $exception->getCode(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'trace' => $exception->getTrace(),
                ];
            }
            return returnJson(ConstFile::API_RESPONSE_FAIL_MESSAGE, ConstFile::API_RESPONSE_FAIL, $data);
        }

//        $data['patch_more_than'] = 5;
        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE, ConstFile::API_RESPONSE_SUCCESS, $data);
    }

    /**
     * @param Request $request
     * @return array
     * @throws Exception
     * @internal param $item
     * @internal param $value
     */
    public function run(Request $request)
    {
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
        $flow = $flow->template;
        $temp_form = $flow->template_form;
        $vacationType = '';
        $userVacation = UserVacation::query()->where('user_id', '=', $user_id)->first();
        foreach ($temp_form as &$item) {
            if ($item->field == 'vacation_type') {
                $vacationType = explode("\r\n", $item->field_value);
                foreach ($vacationType as &$value) {
                    //当前用户的假期信息
                    if ($userVacation) {
                        $annual_time = intval($userVacation->annual_time);
                        $rest_time = intval($userVacation->rest_time);
                        if ($value == '年假') {
                            $value .= '(' . $annual_time . ')';
                        } elseif ($value == '调休') {
                            $value .= '(' . floor($rest_time / 60) . ')';
                        }
                    } else {
                        if ($value == '年假') {
                            $value .= '(0)';
                        } elseif ($value == '调休') {
                            $value .= '(0)';
                        }
                    }

                }
                $item->field_value = implode(',', $vacationType);
            }
        }
        $data = [
            'flow' => $flow,
            'user_id' => $user_id,
            'default_title' => $defaultTitle,
        ];
        return  $data;
    }
}
