<?php

namespace App\Http\Controllers\Api\V1\Assets;

use App\Http\Controllers\Api\V1\BaseController;
use App\Repositories\Assets\CorporateAssetsDepreciationRepository;
use Illuminate\Http\Request;
use Exception;
use Response;
use Auth;

class CorporateAssetsDepreciationController extends BaseController
{
    /**
     * @var mixed
     */
    protected $repository;

    public function __construct()
    {
        $this->repository = app()->make(CorporateAssetsDepreciationRepository::class);
    }

    public function index()
    {
    }

    public function showform(Request $request)
    {
        return $this->repository->showCorporateAssetsDepreciationForm($request);
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        return $this->repository->createCorporateAssetsDepreciationFlow($request, $user);
    }


}
