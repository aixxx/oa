<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\BaseController;
use App\Repositories\Performance\PerformanceTemplateRepository;

use Request;
use Exception;
use Response;
use Auth;

class PerformanceController extends BaseController
{
	
	/**
     * @var mixed
     */
    protected $respository;

    public function __construct()
    {
        $this->repository = app()->make(PerformanceTemplateRepository::class);
    }
	
	/**
     * gaolu
     * 添加 修改 绩效模板
     */
    public function update()
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
     * 4-16 添加绩效模板 改版
     */
    public function setAdd()
    {
        $user = Auth::user();
        $uid = $user->id;
        $array = Request::input();
        $company_id = $user->company_id;
        //var_dump($array);die;
        $array['company_id']=$company_id;

        //returnJson($message='请输入正确内容',$code='500',$data=$array);die;

        return  $this->repository->setAddTow($uid,$array);

    }
    /**
     * gaolu
     *  绩效模板删除
     */
    public function del()
    {
        $user = Auth::user();
        $uid = $user->id;
        $array = Request::input();
        return  $this->repository->setdel($uid,$array['id']);

    }


    /**
     * gaolu
     * 绩效模板列表
     */
    public function lists()
    {
        $user = Auth::user();
        $uid = $user->id;
        $company_id = $user->company_id;
        $array = Request::input();
        //var_dump($array);die;
        return  $this->repository->getList($company_id,$array);

    }
    /**
     * gaolu
     * 考核对象数据列表
     */
    public function getObjectList()
    {
        $user = Auth::user();
        $uid=$user->id;
        return  $this->repository->getObjectList();

    }

    /**
     * gaolu
     * 参与绩效考核人列表
     */
    public function getUserList()
    {
        $user = Auth::user();
        $uid = $user->id;
        $company_id = $user->company_id;
        $array = Request::input();

        return  $this->repository->getUserList($company_id,$array);

    }

    /**
     * gaolu
     * 绩效模板详情
     */
    public function detail()
    {
        $user = Auth::user();
        $uid = $user->id;
        $id = Request::input('id');
        return  $this->repository->getInfoOne($uid,$id);
        //return  $this->repository->detail($uid,$id);

    }

    /**
     *  gaolu
     * 4-17 给所有人发绩效申请
     */
    public function setBatchApply()
    {
        return  $this->repository->setBatchApply();
    }
    /**
     *  gaolu
     * 4-17 被审核人的绩效申请列表
     */
    public function getMeApplyList()
    {
        $user = Auth::user();
        $uid = $user->id;
        return  $this->repository->getMeApplyList($uid);
    }

    /**
     *  gaolu
     * 4-17 被审核人的绩效申请列表
     */
    public function getMeApplyInfo()
    {
        $user = Auth::user();
        $uid = $user->id;
        $array = Request::input();
        return  $this->repository->getMeApplyInfo($uid,$array);
    }
    /**
     *  gaolu
     * 4-17 提交自评
     */
    public function setReview()
    {
        $user = Auth::user();
        $uid = $user->id;
        $array = Request::input();
        return  $this->repository->setReview($uid,$array);
    }
    /**
     *  gaolu
     * 4-18 部门绩效--列表接口(打分人)
     */
    public function getDpList()
    {
        $user = Auth::user();
        $uid = $user->id;
        $array = Request::input();
        return  $this->repository->getDpList($uid,$array);
    }
    /**
     *  gaolu
    * 4-18  绩效详情--打分界面 (打分人)
    */
    public function getDpScoring()
    {
        $user = Auth::user();
        $uid = $user->id;
        $array = Request::input();
        return  $this->repository->getDpScoring($uid,$array);
    }
    /**
     *  gaolu
     * 4-18  绩效打分完成界面--详情接口 (打分人)
     */
    public function getDpScoringEnd()
    {
        $user = Auth::user();
        $uid = $user->id;
        $array = Request::input();
        return  $this->repository->getDpScoringEnd($uid,$array);
    }
    /**
     *  gaolu
     * 4-18  绩效审核打分 (打分人)
     */
    public function setAuditScoring()
    {
        $user = Auth::user();
        $uid = $user->id;
        $array = Request::input();
        return  $this->repository->setAuditScoring($uid,$array);
    }
    /**
     *  gaolu
     * 4-19  绩效自评详情 (打分人)
     */
    public function getDpevaluate()
    {
        $user = Auth::user();
        $uid = $user->id;
        $array = Request::input();
        return  $this->repository->getDpevaluate($uid,$array);
    }
    /**
     *  gaolu
     * 4-19  绩效申请详情--执行中绩效,已完成（被审核人）
     */
    public function getScoringInfo()
    {
        $user = Auth::user();
        $uid = $user->id;
        $array = Request::input();
        return  $this->repository->getScoringInfo($uid,$array);
    }

    /**
     *  gaolu
     * 4-19  被审核人接受打分（绩效完成）
     */
    public function getAcceptScoring()
    {
        $user = Auth::user();
        $uid = $user->id;
        $array = Request::input();
        return  $this->repository->getAcceptScoring($uid,$array);
    }
    /**
     *  gaolu
     * 4-19  被审核人接受打分（绩效完成）
     */
    public function getInfoScoring()
    {
        $user = Auth::user();
        $uid = $user->id;
        $array = Request::input();
        return  $this->repository->getScoringInfo($uid,$array);
    }

    /*
     * 4-22   绩效审核驳回操作 --打分人
     * gaolu
     */
    public function setAuditReject(){
        $user = Auth::user();
        $uid = $user->id;
        $array = Request::input();
        return  $this->repository->setAuditReject($uid,$array);
    }
    /*
     * 4-22   绩效审核驳回操作 --打分人
     * gaolu
     */
    public function setRejectReview(){
        $user = Auth::user();
        $uid = $user->id;
        $array = Request::input();
        return  $this->repository->setRejectReview($uid,$array);
    }

    /*
    * 4-22   绩效驳回重新自评详情页面接口---被审核人
    * gaolu
    */
    public function getRejectInfo(){
        $user = Auth::user();
        $uid = $user->id;
        $array = Request::input();
        return  $this->repository->getRejectInfo($uid,$array);
    }
        /* PHP不使用速算扣除数计算个人所得税
         * @author 吴先成
         * @param float $salary 含税收入金额
         * @param float $deduction 保险等应当扣除的金额 默认值为0
         * @param float $threshold 起征金额 默认值为3500
         * @return float | false 返回值为应缴税金额 参数错误时返回false
         */
    public function tests(){
        $user = Auth::user();
        $uid = $user->id;
        $array = Request::input();
        $salary=intval($array['salary']);
        $deduction=intval($array['deduction']);
        return  $this->repository->getPersonalIncomeTax($salary,$deduction);
    }
    public function testss(){
        $user = Auth::user();
        $uid = $user->id;
        return  $this->repository->getSummary();
    }

    public function setUpdate(){
        $user = Auth::user();
        $uid = $user->id;
        return  $this->repository->setUpdate(1791,16);
    }
}
