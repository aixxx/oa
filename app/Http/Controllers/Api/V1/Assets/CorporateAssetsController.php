<?php

namespace App\Http\Controllers\Api\V1\Assets;

use App\Constant\ConstFile;
use App\Http\Controllers\Api\V1\BaseController;
use App\Repositories\Assets\CorporateAssetsRepository;
use Illuminate\Http\Request;
use Exception;
use Response;
use Auth;

class CorporateAssetsController extends BaseController
{
    /**
     * @var mixed
     */
    protected $repository;

    public function __construct()
    {
        $this->repository = app()->make(CorporateAssetsRepository::class);
    }

    public function index()
    {
    }

    public function create(Request $request)
    {
        $all = $request->all();
        $user = Auth::user();
        return $this->repository->created($all, $user);
    }

    /**
     * @description 获取资产列表
     * @param Request $request
     * @return false|\Illuminate\Http\JsonResponse|string
     */
    public function lists(Request $request)
    {
        $user = Auth::user();
        $status = $request->get('status');
        $attr = $request->get('attr');
        $cat = $request->get('cat');
        $department_id = $request->get('department_id');
        return $this->repository->fetchList($user, $status, $attr, $cat, $department_id);
    }

    /**
     * @description 获取资产详情
     * @param Request $request
     * @return false|\Illuminate\Http\JsonResponse|string
     */
    public function show(Request $request)
    {
        $id = $request->get('id');
        $user = Auth::user();
        return $this->repository->fetchInfo($user, $id);
    }

    /**
     *
     * @description 获取管理记录
     * @param Request $request
     * @return false|\Illuminate\Http\JsonResponse|string
     */
    public function management(Request $request)
    {
        $user = Auth::user();
        $type = $request->get('type');
        $keywords = $request->get('keywords');
        return $this->repository->management($user, $type, $keywords);
    }

    /**
     * @description 获取单据审批记录
     * @param Request $request
     * @return false|\Illuminate\Http\JsonResponse|string
     */
    public function processquery(Request $request)
    {
        $tab = $request->get('tab');
        $except = $request->except('tab');
        $user = Auth::user();
        return $this->repository->processQuery($user, $tab, $request);
    }

    /**
     * @description 获取单号
     * @param Request $request
     * @return false|\Illuminate\Http\JsonResponse|string
     */
    public function getnum(Request $request)
    {
        $act = $request->get('act');
        return $this->repository->getNum($act);
    }

    /**
     * @description 资产统计
     * @param Request $request
     * @return false|\Illuminate\Http\JsonResponse|string
     */
    public function report(Request $request)
    {
        $departmentId = $request->get('did');
        try {
            $data = $this->repository->report($departmentId);
        } catch (Exception $e) {
            return returnJson($e->getMessage(), $e->getCode());
        }
        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE, ConstFile::API_RESPONSE_SUCCESS, $data);
    }
}
