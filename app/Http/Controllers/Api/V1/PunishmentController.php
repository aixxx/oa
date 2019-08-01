<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\BaseController;
use App\Repositories\PunishmentTemplateRepository;
use Request;
use Exception;
use Response;
use Auth;

class PunishmentController extends BaseController
{
    /**
     * @var mixed
     */
    protected $respository;

    public function __construct()
    {
        $this->repository = app()->make(PunishmentTemplateRepository::class);
    }

    /**
     * gaolu
     * 添加 修改 惩罚模板
     */
    public function setAdd()
    {
        $user = Auth::user();
        $uid = $user->id;
        $array = Request::input();
        $company_id = $user->company_id;
        $array['company_id']=$company_id;
        if(!isset($array['id']) || empty($array['id'])){
            return  $this->repository->setAdd($uid,$array);
        }else{
            return  $this->repository->setedit($uid,$array);
        }
        //var_dump($array);die;
    }

    /**
     * gaolu
     * 删除迟到惩罚数据
     */
    public function setDel()
    {
        $user = Auth::user();
        $uid = $user->id;
        $array = Request::input();
        return  $this->repository->setDel($uid,$array);
    }
    /**
     * gaolu
     * 惩罚模板详情
     */
    public function getInfo()
    {
        $user = Auth::user();
        $uid = $user->id;
        $array = Request::input();
        $company_id = $user->company_id;
        $array['company_id']=$company_id;
        //var_dump( $array);die;
        return  $this->repository->getInfo($uid,$array);
    }
    /**
    * gaolu
    * 计算金额
    */
    public function punishmentCalculation()
    {
        $user = Auth::user();
        $uid = $user->id;
        $company_id = $user->company_id;
        return  $this->repository->punishmentCalculation(1791,'2019-05');
    }
    public function setOvertimePay()
    {
        $user = Auth::user();
        $array = Request::input();
        return  $this->repository->setOvertimePay($user,$array);
    }
    public function getOvertimePay()
    {
        $user = Auth::user();
        $array = Request::input();
        return  $this->repository->getOvertimePay($user,$array);
    }
    public function getOvertimePayInfo()
    {
        $user = Auth::user();
        $array = Request::input();
        return  $this->repository->getOvertimePayInfo($user,$array);
    }
    public function setLatePay()
    {
        $user = Auth::user();
        $array = Request::input();
        return  $this->repository->setLatePay($user,$array);
    }
    public function getLatePayList()
    {
        $user = Auth::user();
        $array = Request::input();
        return  $this->repository->getLatePayList($user,$array);
    }

    public function setAbsenteeism()
    {
        $user = Auth::user();
        $array = Request::input();
        return  $this->repository->setAbsenteeism($user,$array);
    }
    public function getAbsenteeismList()
    {
        $user = Auth::user();
        $array = Request::input();
        return  $this->repository->getAbsenteeismList($user,$array);
    }
    public function setLeave()
    {
        $user = Auth::user();
        $array = Request::input();
        return  $this->repository->setLeave($user,$array);
    }
    public function setLeaveList()
    {
        $user = Auth::user();
        $array = Request::input();
        return  $this->repository->setLeaveList($user,$array);
    }
}
