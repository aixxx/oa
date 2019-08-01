<?php

namespace App\Http\Controllers\Api\V1;

use App\Constant\ConstFile;
use App\Exceptions\DiyException;
use App\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


abstract class ApiController extends Controller
{
    use ApiResponse;
    public function __construct(){

    }

    public abstract function run(Request $request);

    public function checkParam(Request $request, $rules=[], $messages= [])
    {
        $valicator = Validator::make($request->all(), $rules, $messages);
        if ($valicator->fails()) {
            $res = $valicator->errors()->first();
            throw  new DiyException($res, ConstFile::API_PARAM_ERROR);
        }
    }
}
