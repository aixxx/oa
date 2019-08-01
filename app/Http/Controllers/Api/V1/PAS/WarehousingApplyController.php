<?php

namespace App\Http\Controllers\Api\V1\PAS;

use App\Http\Controllers\Controller;
use App\Repositories\PAS\SupplierRepository;
use App\Repositories\PAS\WarehousingApplyRepository;
use Request;
use Exception;
use Response;
use Auth;

class WarehousingApplyController extends Controller
{
    /**
     *
     * @var UserRespository
     */
    protected $respository;


    //构造函数
    function __construct()
    {
        $this->repository = app()->make(WarehousingApplyRepository::class);
    }
    /*
     * 2019-05-08
     *
     */
    public function setAdd() {
        $user = Auth::user();
        $uid = $user->id;
        $array = Request::input();
        return $this->repository->setAdd($user,$array);
    }

    /*
     * 2019-05-08
     *
     */
    public function setUpdate() {
        $user = Auth::user();
        $uid = $user->id;
        $array = Request::input();
        return $this->repository->setUpdate($user,$array);
    }
    /*
     * 2019-05-11
     *
     */
    public function getInfo() {
        $user = Auth::user();
        $uid = $user->id;
        $array = Request::input();
        return $this->repository->getInfo($user,$array);
    }
    /*
     * 2019-05-08
     *
     */
    public function getCode() {
        $user = Auth::user();
        $uid = $user->id;
        return $this->repository->getCode();
    }
    /*
     * 2019-05-08
     *
     */
    public function getRelationInfo() {
        $user = Auth::user();
        $array = Request::input();
        return $this->repository->getRelationInfo($user,$array);
    }

    /*
    * 2019-05-08
    *
    */
    public function getList() {
        $user = Auth::user();
        $array = Request::input();
        return $this->repository->getList($user,$array);
    }
    /*
    * 2019-05-08
    *
    */
    public function getErr() {
        $user = Auth::user();
        $array = Request::input();
        return $this->repository->getErr($user,$array);
    }
    /*
    * 2019-05-08
    * 添加发货方式
    */
    public function setInvoiceAdd() {
        $user = Auth::user();
        $array = Request::input();
        return $this->repository->setInvoiceAdd($user,$array);
    }
}
