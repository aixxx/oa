<?php

namespace App\Http\Controllers\Api\V1\Salary;

use App\Constant\ConstFile;
use App\Http\Controllers\Api\V1\BaseController;
use App\Repositories\Salary\ScoreRepository;
use Request;

class ScoreController extends BaseController
{
    public $repository;
    //构造函数
    function __construct() {
        parent::__construct();
        $this->repository = app()->make(ScoreRepository::class);
    }
    public function getList(){
        return $this->repository->getList(Request::all());
    }

    public function getListInfo(){
        return $this->repository->getListInfo(Request::all());
    }

}
