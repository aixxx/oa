<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/15
 * Time: 14:38
 */

namespace App\Http\Controllers\Api\V1;

use App\Repositories\PositiveRepository;
use Illuminate\Http\Request;

class PositiveController extends BaseController
{
    /**
     * @var mixed
     */
    protected $repository;

    public function __construct()
    {
        $this->repository = app()->make(PositiveRepository::class);
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

    public function showWorkflow(Request $request)
    {
        return $this->repository->workflowShow($request);
    }

    public function storeWorkflow(Request $request)
    {
        return $this->repository->storeWorkflow($request);
    }



    /**
     * @deprecated 申请单
     * @param Request $request
     * @return mixed
     */
    public function show(Request $request)
    {
        return $this->repository->show($request);
    }

    public function wageShowApply(Request $request)
    {
        return $this->repository->wageShowApply($request);
    }

    public function wageShow(Request $request)
    {
        return $this->repository->wageShow($request);
    }
    public function storeWageWorkflow(Request $request)
    {
        return $this->repository->storeWageWorkflow($request);
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



    public function processQuery(Request $request)
    {
        return $this->repository->processQuery($request);
    }

    public function entryShow(Request $request)
    {
        return $this->repository->entryShow($request);
    }

    public function procShow(Request $request)
    {
        return $this->repository->procShow($request);
    }

    public function positiveApplyEntry(Request $request)
    {
        return $this->repository->positiveApplyEntry($request);
    }

    public function fetchPositiveWageEntry(Request $request)
    {
        return $this->repository->fetchPositiveWageEntry($request);
    }
}