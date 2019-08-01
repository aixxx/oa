<?php

namespace App\Http\Controllers\Api\V1\Executive;

use App\Http\Controllers\Api\V1\BaseController;
use App\Repositories\Executive\CarsAppointRepository;
use Request;

class CarsAppointController extends BaseController
{
    public $repository;
    //构造函数
    function __construct() {
        $this->repository = app()->make(CarsAppointRepository::class);
    }

    public function getList(){
        return $this->repository->getList(Request::all());
    }

    public function add(){
        return $this->repository->add(Request::all());
    }

    public function option(){
        return $this->repository->option(Request::all());
    }

}
