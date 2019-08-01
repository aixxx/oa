<?php

namespace App\Http\Controllers\Api\V1\Assets;

use App\Http\Controllers\Api\V1\BaseController;
use App\Repositories\Assets\CorporateAssetsBorrowRepository;
use App\Repositories\Assets\CorporateAssetsReturnRepository;
use Illuminate\Http\Request;
use Exception;
use Response;
use Auth;

class CorporateAssetsReturnController extends BaseController
{
    /**
     * @var mixed
     */
    protected $repository;

    public function __construct()
    {
        $this->repository = app()->make(CorporateAssetsReturnRepository::class);
    }

    public function index()
    {
    }

    public function showform(Request $request)
    {
        return $this->repository->showCorporateAssetsReturnForm($request);
    }

    public function create(Request $request)
    {

        $user = Auth::user();
        return $this->repository->createCorporateAssetsReturnFlow($request, $user);
    }


}
