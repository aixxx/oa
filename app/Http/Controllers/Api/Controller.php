<?php

namespace App\Http\Controllers\Api;

use App\Http\Helpers\ApiResponse;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use ApiResponse, AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}