<?php

namespace App\Http\Controllers\Api\V1\Salary;

use App\Constant\ConstFile;
use App\Http\Controllers\Api\V1\BaseController;
use App\Repositories\Salary\RewardPunishmentRepository;
use Request;
use Auth;

class RewardPunishmentController extends BaseController
{
    public $repository;
    //构造函数
    function __construct() {
        parent::__construct();
        $this->repository = app()->make(RewardPunishmentRepository::class);
    }
    public function add(){
        return $this->repository->add(Request::all());
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


    public function getMyInfo(){
        return $this->repository->getMyInfo();
    }

    public function delete(){
        $data = Request::all();
        $id = intval($data['id']);
        if (!$id) return returnJson('参数错误', ConstFile::API_RESPONSE_FAIL);
        return $this->repository->delete($id);
    }


    /*
     * 添加惩罚 - 任务
     * */
    public function addByTask(){
        $data = Request::all();
        return $this->repository->addByTask($data);
    }

    /*
     * 申诉 -
     * */
    public function RewardPunishmentAppeal(){
        $data = Request::all();
        return $this->repository->RewardPunishmentAppeal($data, Auth::id());
    }
}
