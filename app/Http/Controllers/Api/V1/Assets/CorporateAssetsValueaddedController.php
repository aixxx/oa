<?php

namespace App\Http\Controllers\Api\V1\Assets;

use App\Http\Controllers\Api\V1\BaseController;
use App\Repositories\Assets\CorporateAssetsValueaddedRepository;
use Illuminate\Http\Request;
use Exception;
use Response;
use Auth;

class CorporateAssetsValueaddedController extends BaseController
{
    /**
     * @var mixed
     */
    protected $repository;

    public function __construct()
    {
        $this->repository = app()->make(CorporateAssetsValueaddedRepository::class);
    }

    public function index()
    {
    }

    public function showform(Request $request)
    {
        return $this->repository->showCorporateAssetsValueaddedForm($request);
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        return $this->repository->createCorporateAssetsValueaddedFlow($request, $user);
    }


}
