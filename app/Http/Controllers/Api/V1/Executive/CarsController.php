<?php

namespace App\Http\Controllers\Api\V1\Executive;

use App\Constant\ConstFile;
use App\Http\Controllers\Api\V1\BaseController;
use App\Repositories\Executive\CarsRepository;
use Request;

class CarsController extends BaseController
{
    public $repository;
    //构造函数
    function __construct() {
        $this->repository = app()->make(CarsRepository::class);
    }
    public function add(){
        $data = Request::all();
        if(isset($data['id']) && intval($data['id'])){
            return $this->repository->updated($data, $data['id']);
        }else{
            return $this->repository->add($data);
        }
    }

    public function getList(){
        return $this->repository->getList(Request::all());
    }

    public function getInfo(){
        $data = Request::all();
        $id = intval($data['id']);
        if (!$id) return returnJson('参数错误', ConstFile::API_RESPONSE_FAIL);
        return $this->repository->getInfo($id);
    }

    public function delete(){
        $data = Request::all();
        $id = intval($data['id']);
        if (!$id) return returnJson('参数错误', ConstFile::API_RESPONSE_FAIL);
        return $this->repository->delete($id);
    }


}
