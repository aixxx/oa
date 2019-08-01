<?php

namespace App\Http\Controllers\Api\V1\PAS;

use App\Http\Controllers\Controller;
use App\Repositories\PAS\PurchaseRepository;
use App\Repositories\PAS\ReturnOrderRepository;
use Request;
use Auth;

class ReturnOrderController extends Controller
{
    /**
     *
     * @var UserRespository
     */
    protected $respository;


    //构造函数
    function __construct()
    {
        $this->repository = app()->make(ReturnOrderRepository::class);
    }
    public function getCode() {
        $user = Auth::user();
        return $this->repository->getCode();
    }

    public function setAdd() {
        $user = Auth::user();
        $array = Request::input();
        return $this->repository->setAdd($user,$array);
    }

    public function setUpdate() {
        $user = Auth::user();
        $array = Request::input();
        return $this->repository->setUpdate($user,$array);
    }

    public function getWeList() {
        $user = Auth::user();
        $array = Request::input();
        return $this->repository->getWeList($user,$array);
    }
    public function getInfo() {
        $user = Auth::user();
        $array = Request::input();
        return $this->repository->getInfo($user,$array);
    }
    public function delCost() {
        $user = Auth::user();
        $array = Request::input();
        return $this->repository->delCost($user,$array);
    }
    public function withdraw() {
        $user = Auth::user();
        $array = Request::input();
        return $this->repository->withdraw($user,$array);
    }
    public function getInfoTow() {
        $user = Auth::user();
        $array = Request::input();
        return $this->repository->getInfoTow($user,$array);
    }
    public function getInfoOne() {
        $user = Auth::user();
        $array = Request::input();
        return $this->repository->getInfoOne($user,$array);
    }
    public function getRelationInfo() {
        $user = Auth::user();
        $array = Request::input();
        return $this->repository->getRelationInfo($user,$array);
    }
}
