<?php

namespace App\Http\Controllers\Api\V1\PAS;

use App\Http\Controllers\Controller;
use App\Repositories\PAS\PurchaseRepository;
use Request;
use Auth;

class PurchaseController extends Controller
{
    /**
     *
     * @var UserRespository
     */
    protected $respository;


    //构造函数
    function __construct()
    {
        $this->repository = app()->make(PurchaseRepository::class);
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
    public function getPurchaseInfo() {
        $user = Auth::user();
        $array = Request::input();
        return $this->repository->getPurchaseInfo($user,$array);
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
    public function getTrialPurchaseInfo() {
        $user = Auth::user();
        $array = Request::input();
        return $this->repository->getTrialPurchaseInfo($user,$array);
    }
    public function getPurchaseInfoOne() {
        $user = Auth::user();
        $array = Request::input();
        return $this->repository->getPurchaseInfoOne($user,$array);
    }
    public function getPayableMoney() {
        $user = Auth::user();
        $array = Request::input();
        return $this->repository->getPayableMoney($user,$array);
    }
    public function getOrderList() {
        $user = Auth::user();
        $array = Request::input();
        return $this->repository->getOrderList($user,$array);
    }
    public function getUniversalCode() {
        $user = Auth::user();
        $array = Request::input();
        return $this->repository->getUniversalCode($user,$array);
    }
    public function getPurchaseList() {
        $user = Auth::user();
        $array = Request::input();
        return $this->repository->getPurchaseList($user,$array);
    }
}
