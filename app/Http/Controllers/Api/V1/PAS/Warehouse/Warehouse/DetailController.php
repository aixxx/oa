<?php

namespace App\Http\Controllers\Api\V1\PAS\Warehouse\Warehouse;

use App\Exceptions\DiyException;
use App\Http\Controllers\Api\V1\ApiController;
use App\Models\PAS\Warehouse\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class DetailController extends ApiController
{


    public function run(Request $request)
    {
        // TODO: Implement run() method.
        $id = $request->get('id');
        $obj = Warehouse::find($id);
        return $obj;
    }
}
