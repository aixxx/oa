<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/9
 * Time: 13:21
 */

namespace App\Http\Controllers\Api\V1\Intelligence;

use Illuminate\Http\Request;
use App\Repositories\Intelligence\IntelligenceRepository;
use App\Http\Controllers\Api\V1\BaseController;

class IntelligenceController extends BaseController
{
    /**
     * @var mixed
     */
    protected $respository;

    public function __construct()
    {
        $this->respository = app()->make(IntelligenceRepository::class);
    }

    public function intelligenceTypeAdd(Request $request)
    {
        return $this->respository->intelligenceTypeAdd($request);
    }

    public function intelligenceTypeEdit(Request $request)
    {
        return $this->respository->intelligenceTypeEdit($request);
    }

    public function intelligenceTypeDelete(Request $request)
    {
        return $this->respository->intelligenceTypeDelete($request);
    }

    public function intelligenceTypeShow()
    {
        return $this->respository->intelligenceTypeShow();
    }

    public function IntelligenceAdd(Request $request)
    {
        return $this->respository->IntelligenceAdd($request);
    }

    public function IntelligenceShow(Request $request)
    {
        return $this->respository->IntelligenceShow($request);
    }

    public function intelligenceClaim(Request $request)
    {
        return $this->respository->intelligenceClaim($request);
    }

    public function intelligenceConsent(Request $request)
    {
        return $this->respository->intelligenceConsent($request);
    }

    public function intelligenceRefused(Request $request)
    {
        return $this->respository->intelligenceRefused($request);
    }

    public function intelligenceAdminShow(Request $request)
    {
        return $this->respository->intelligenceAdminShow($request);
    }

    public function intelligenceMyShow(Request $request)
    {
        return $this->respository->intelligenceMyShow($request);
    }

    public function intelligenceAdminDetails(Request $request)
    {
        return $this->respository->intelligenceAdminDetails($request);
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

}
