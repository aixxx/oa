<?php

namespace App\Http\Controllers\Api\V1;

use App\Repositories\ContractRepository;
use Illuminate\Http\Request;
use Auth;

class ContractController extends BaseController
{
    /**
     * @var mixed
     */
    protected $repository;

    public function __construct()
    {
        $this->repository = app()->make(ContractRepository::class);
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
        $user = Auth::user();
        return $this->repository->storeWorkflow($request, $user);
    }

    /**
     * @description 申请人视角
     * @param Request $request
     * @return mixed
     */
    public function showWorkflow(Request $request)
    {
        return $this->repository->workflowShow($request);
    }

    //审批人视角
    public function showAuditorWorkflow(Request $request)
    {
        return $this->repository->workflowAuthorityShow($request);
    }

    /**
     * @description 通过审批
     * @param Request $request
     * @return mixed
     */
    public function passWorkflow(Request $request)
    {
        return $this->repository->passWorkflow($request);
    }

    /**
     * @description 驳回审批
     * @param Request $request
     * @return mixed
     */
    public function rejectWorkflow(Request $request)
    {

        //\Event::fire(new ContractRejectEvent(4757));
        return $this->repository->rejectWorkflow($request);
    }

    /**
     * @description 历史审批
     * @param Request $request
     * @return mixed
     */
    public function history(Request $request)
    {
        $flow_id = $request->get('flow_id', 0);
        return $this->repository->history(intval($flow_id));
    }


    /**
     * @deprecated 申请单
     * @param Request $request
     * @return mixed
     */
    public function show(Request $request)
    {
        return $this->repository->showPendingUsersForm($request);
    }

    public function hrlist(Request $request)
    {
        $user = Auth::user();
        return $this->repository->hrList($request, $user);
    }

    public function getperformanceid()
    {
        $user = Auth::user();
        return $this->repository->getPerformanceId($user);
    }

    public function fetchContract(Request $request)
    {

        $entry_id = $request->entry_id;
        return $this->repository->fetchContract($entry_id);
    }

    public function getuserstatustatol()
    {
        $user = Auth::user();
        return $this->repository->getUserStatus($user);
    }
}
