<?php

namespace App\Http\Controllers\Api\V1\PAS;

use App\Http\Controllers\Controller;
use App\Repositories\PAS\SupplierRepository;
use Request;
use Exception;
use Response;
use Auth;

class SupplierController extends Controller
{
    /**
     *
     * @var UserRespository
     */
    protected $respository;


    //构造函数
    function __construct()
    {
        $this->repository = app()->make(SupplierRepository::class);
    }
    /*
     * 2019-05-08
     * 添加供应商
     */
    public function setAdd() {
        $user = Auth::user();
        $uid = $user->id;
        $array = Request::input();
        return $this->repository->setAdd($user,$array);
    }
    /*
     * 2019-05-08
     * 添加供应商
     */
    public function setUpdate() {
        $user = Auth::user();
        $uid = $user->id;
        $array = Request::input();
        return $this->repository->setUpdate($user,$array);
    }
    /*
     * 2019-05-08
     * 获取供应商编号
     */
    public function getCode() {
        $user = Auth::user();
        $uid = $user->id;
        return $this->repository->getCode();
    }
    /*
     * 2019-05-08
     *  获取中文字符的首字母
     */
    public function chTowarr() {
        $user = Auth::user();
        $array = Request::input();
        return $this->repository->chTowarr($array);
    }

    /*
     * 2019-05-08
     *  获取供应商详情
     */
    public function getInfo() {
        $user = Auth::user();
        $array = Request::input();
        return $this->repository->getInfo( $user , $array );
    }
    /*
     * 2019-05-08
     *  获取供应商列表
     */
    public function getList() {
        $user = Auth::user();
        $array = Request::input();
        return $this->repository->getList( $user , $array );
    }

    /*
     * 2019-05-08
     *  获取供应商列表
     */
    public function getListOne() {
        $user = Auth::user();
        $array = Request::input();
        return $this->repository->getListOne( $user ,$array );
    }
    /*
     * 2019-05-08
     *  获取供应商列表
     */
    public function Statistical() {
        $user = Auth::user();
        $array = Request::input();
        return $this->repository->Statistical( $user , $array );
    }
    /*
     * 2019-05-08
     *  获取供应商列表
     */
    public function ProcurementStatistics() {
        $user = Auth::user();
        $array = Request::input();
        return $this->repository->ProcurementStatistics( $user , $array );
    }
}
