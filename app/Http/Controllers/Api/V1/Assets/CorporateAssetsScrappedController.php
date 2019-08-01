<?php

namespace App\Http\Controllers\Api\V1\Assets;

use App\Http\Controllers\Api\V1\BaseController;
use App\Repositories\Assets\CorporateAssetsScrappedRepository;
use Illuminate\Http\Request;
use Exception;
use Response;
use Auth;

class CorporateAssetsScrappedController extends BaseController
{
    /**
     * @var mixed
     */
    protected $repository;

    public function __construct()
    {
        $this->repository = app()->make(CorporateAssetsScrappedRepository::class);
    }

    public function index()
    {
    }

    public function showform(Request $request)
    {
        return $this->repository->showCorporateAssetsScrappedForm($request);
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        return $this->repository->createCorporateAssetsScrappedFlow($request, $user);
    }


}
