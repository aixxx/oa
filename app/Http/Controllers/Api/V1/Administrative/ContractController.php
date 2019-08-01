<?php

namespace App\Http\Controllers\Api\V1\Administrative;

use Illuminate\Http\Request;
use App\Repositories\Administrative\ContractRepository;
use App\Http\Controllers\Api\V1\BaseController;

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

    public function workflowList()
    {
        return $this->repository->fetchWorkflowList();
    }

    public function createWorkflow(Request $request)
    {
        return $this->repository->show($request);
    }


    public function storeWorkflow(Request $request)
    {
        return $this->repository->storeWorkflow($request);
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

    public function contractShow(Request $request){
        return $this->repository->contractShow($request);
    }

    public function contractSearch(Request $request){
        return $this->repository->contractSearch($request);
    }

    public function workflowShows(Request $request){
        return $this->repository->workflowShows($request);
    }
}
