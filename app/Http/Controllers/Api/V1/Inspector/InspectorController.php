<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/14
 * Time: 13:16
 */

namespace App\Http\Controllers\Api\V1\Inspector;

use Illuminate\Http\Request;
use App\Repositories\Inspector\InspectorRepository;
use App\Http\Controllers\Api\V1\BaseController;

class InspectorController extends BaseController
{
    /**
     * @var mixed
     */
    protected $respository;

    public function __construct()
    {
        $this->respository = app()->make(InspectorRepository::class);
    }

    //工作流
    public function workflowList()
    {
        return $this->respository->fetchWorkflowList();
    }

    public function showWorkflow(Request $request)
    {
        return $this->respository->workflowShow($request);
    }

    public function createWorkflow(Request $request)
    {
        return $this->respository->createWorkflow($request);
    }

    public function storeWorkflow(Request $request)
    {
        return $this->respository->storeWorkflow($request);
    }

    public function show(Request $request)
    {
        return $this->respository->show($request);
    }

    //审批人视角
    public function showAuditorWorkflow(Request $request)
    {
        return $this->respository->workflowAuthorityShow($request);
    }

    public function passWorkflow(Request $request)
    {
        return $this->respository->passWorkflow($request);
    }

    public function rejectWorkflow(Request $request)
    {
        return $this->respository->rejectWorkflow($request);
    }

    public function myShow(Request $request)
    {
        return $this->respository->myShow($request);
    }

    public function adminList(Request $request)
    {
        return $this->respository->adminList($request);
    }
}