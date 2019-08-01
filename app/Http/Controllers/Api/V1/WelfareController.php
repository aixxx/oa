<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\BaseController;

use App\Repositories\Welfare\WelfareRepository;
use Illuminate\Http\Request;
use Exception;
use Response;
use Auth;

class WelfareController extends BaseController
{

    /**
     * @var WelfareRepository
     */
    protected $respository;

    public function __construct()
    {
        $this->repository = app()->make(WelfareRepository::class);
    }

    /**
     * 福利列表-创建人
     * @return personList
     */
    public function index()
    {
        $user = Auth::user();
        $uid = $user->id;

        return $this->repository->getList($uid);
    }
    /**
     * 福利列表-创建人
     * @return
     */
    public function personList()
    {
        $user = Auth::user();
        $uid = $user->id;
        return $this->repository->personList($uid);
    }


    /**
     * 发起福利
     */

    public function create(Request $request){
        return $this->repository->createWorkflow($request);
    }

    /**
     * 保存福利
     */
   public function save(Request $request){
       return $this->repository->updateFlow($request);
   }
    /**
     * 发起人查看
     */
    public function show(Request $request){
        return $this->repository->workflowShow($request);
    }
    /**
     * 获取福利领取资格的人
     */
    public function personShow(Request $request){
        return $this->repository->personShow($request);
    }
    /**
     *
     * 领取人列表
     */
    public function receiverList(Request $request){
        return $this->repository->receiverList($request);
    }
    /**
     * 福利申请
     */
    public function apply(Request $request){
        return $this->repository->apply($request);
    }


   //审批人视角查看
    public function showAuditor(Request $request)
    {
        return $this->repository->workflowAuthorityShow($request);
    }
    //审批通过
    public function pass(Request $request){
        return $this->repository->passWorkflow($request);
    }

    public function reject(Request $request)
    {
        return $this->repository->rejectWorkflow($request);
    }

}
