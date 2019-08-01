<?php

namespace App\Http\Controllers\Api\V1\Salary;

use App\Http\Controllers\Api\V1\BaseController;
use App\Repositories\Complain\ComplainRepository;
use Illuminate\Http\Request;

class ComplainController extends BaseController
{
    /**
     * @var mixed
     */
    protected $repository;

    public function __construct()
    {
        parent::__construct();
        $this->repository = app()->make(ComplainRepository::class);
    }

    public function showForm()
    {
        return $this->repository->showForm();
    }

    public function store(Request $request)
    {
        return $this->repository->storeWorkflow($request);
    }

    public function show(Request $request)
    {
        return $this->repository->workflowShow($request);
    }

    //审批人视角
    public function AuditorShow(Request $request)
    {
        return $this->repository->workflowAuthorityShow($request);
    }
}
