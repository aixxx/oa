<?php

namespace App\Http\Controllers\Api\V1\Attendance;

use App\Constant\ConstFile;
use App\Http\Controllers\Api\V1\BaseController;
use App\Models\Vacations\UserVacation;
use App\Models\Workflow\Flow;
use App\Models\Workflow\Flowlink;
use App\Models\Workflow\Workflow;
use App\Repositories\EntryRepository;
use App\Services\WorkflowUserService;
use Auth;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class VacationLeaveSaveController extends BaseController {
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
        $this->repository->checkRequest($request);

        $flow_id = $request->get('flow_id');
        $flow = Flow::findById($flow_id);
        $flow->checkCanApply(); // 校验是否可以提交该流程的申请,不可提交申请的话,创建或者修改申请单,也没有意义
        $entry = $this->repository->updateOrCreateEntry($request, 0); // 创建或更新申请单

        if ($entry->isInHand()) {
            $flow_link = Flowlink::firstStepLink($entry->flow_id);
            //进程初始化
            (new Workflow())->setFirstProcessAuditor($entry, $flow_link);
        }
        $entry->save();

        $user_id = Auth::id();
        $vacationType = $request->get('vacation_type');
//        $userVacation = UserVacation::query()->where('user_id', $user_id)->first();
//        if($vacationType == '年假'){
//            $tplArr = $request->get('tpl');
//            $userVacation->decrement('annual_time', $tplArr['time_sub_by_hour']);  //调休的时间
//            $userVacation->save();
//        }else if($vacationType == '调休'){
//            $tplArr = $request->get('tpl');
//            $userVacation->decrement('rest_time', $tplArr['time_sub_by_hour']);  //调休的时间
//            $userVacation->save();
//        }
        return  ['entry' => $entry->toArray()];
    }
}
