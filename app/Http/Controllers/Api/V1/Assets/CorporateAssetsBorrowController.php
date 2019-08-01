<?php

namespace App\Http\Controllers\Api\V1\Assets;

use App\Events\CorporateAssetsBorrowPassEvent;
use App\Http\Controllers\Api\V1\BaseController;
use App\Repositories\Assets\CorporateAssetsBorrowRepository;
use Illuminate\Http\Request;
use Auth;

class CorporateAssetsBorrowController extends BaseController
{
    /**
     * @var mixed
     */
    protected $repository;

    public function __construct()
    {
        $this->repository = app()->make(CorporateAssetsBorrowRepository::class);
    }

    public function index()
    {
    }

    public function showform(Request $request)
    {
        return $this->repository->showCorporateAssetsBorrowForm($request);
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        return $this->repository->createCorporateAssetsBorrowFlow($request, $user);
    }

    public function test()
    {
        event(new CorporateAssetsBorrowPassEvent(4791));
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
}
