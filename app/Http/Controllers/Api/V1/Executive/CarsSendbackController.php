<?php

namespace App\Http\Controllers\Api\V1\Executive;

use App\Constant\ConstFile;
use App\Http\Controllers\Api\V1\BaseController;
use App\Repositories\Executive\CarsSendbackRepository;
use App\Services\AttendanceApi\CountsService;
use Request;

class CarsSendbackController extends BaseController
{
    public $repository;
    //构造函数
    function __construct() {
        $this->repository = app()->make(CarsSendbackRepository::class);
    }

    public function getList(){
        return $this->repository->getList(Request::all());
    }

    public function add(){
        return $this->repository->add(Request::all());
    }

}
