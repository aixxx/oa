<?php

namespace App\Http\Controllers\Rpc;

use App\Repositories\Assets\CorporateAssetsRepository;

class AssetsController extends HproseController
{
    public function report($departmentId = 0)
    {
        $repository = app()->make(CorporateAssetsRepository::class);

        return $repository->report($departmentId);
    }
}
