<?php

namespace App\Http\Controllers\Api\V1\Assets;

use App\Http\Controllers\Api\V1\BaseController;
use App\Repositories\Assets\CorporateAssetsTransferRepository;
use App\Repositories\Assets\CorporateAssetsUseRepository;
use Illuminate\Http\Request;
use Exception;
use Response;
use Auth;

class CorporateAssetsTransferController extends BaseController
{
    /**
     * @var mixed
     */
    protected $repository;

    public function __construct()
    {
        $this->repository = app()->make(CorporateAssetsTransferRepository::class);
    }

    public function index()
    {
    }

    public function showform(Request $request)
    {
        return $this->repository->showCorporateAssetsTransferForm($request);
    }

    public function create(Request $request)
    {

        $user = Auth::user();
        return $this->repository->createCorporateAssetsTransferFlow($request, $user);
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
