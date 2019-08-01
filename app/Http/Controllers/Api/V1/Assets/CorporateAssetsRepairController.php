<?php

namespace App\Http\Controllers\Api\V1\Assets;

use App\Http\Controllers\Api\V1\BaseController;
use App\Repositories\Assets\CorporateAssetsRepairRepository;
use App\Repositories\Assets\CorporateAssetsTransferRepository;
use App\Repositories\Assets\CorporateAssetsUseRepository;
use Illuminate\Http\Request;
use Exception;
use Response;
use Auth;

class CorporateAssetsRepairController extends BaseController
{
    /**
     * @var mixed
     */
    protected $repository;

    public function __construct()
    {
        $this->repository = app()->make(CorporateAssetsRepairRepository::class);
    }

    public function index()
    {
    }

    public function showform(Request $request)
    {
        return $this->repository->showCorporateAssetsRepairForm($request);
    }

    public function create(Request $request)
    {

        $user = Auth::user();
        return $this->repository->createCorporateAssetsRepairFlow($request, $user);
    }

    public function completed(Request $request)
    {
        $user = Auth::user();

        $id = $request->get('id');
        return $this->repository->repairCompleted($user, $id);
    }
}
