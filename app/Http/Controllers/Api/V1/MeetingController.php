<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\BaseController;
use App\Repositories\Meeting\MeetingRepository;

use Request;
use Exception;
use Response;
use Auth;

class MeetingController extends BaseController
{

    /**
     * @var mixed
     */
    protected $respository;

    public function __construct()
    {
        $this->repository = app()->make(MeetingRepository::class);
    }

    /**
     * gaolu 4-24
     * 添加 添加会议室
     */
    public function setAdd()
    {
        $user = Auth::user();
        $uid = $user->id;
        $array = Request::input();
        return $this->repository->setAddOne($uid, $array);
    }

    /**
     * gaolu 4-24
     * 添加 会议室列表搜索
     */
    public function getList()
    {
        $user = Auth::user();
        $uid = $user->id;
        $array = Request::input();
        return $this->repository->getList($uid, $array);
    }

    /*
     * 4-26
     * gaolu
     * 会议添加
     */
    public function meetingAdd()
    {
        $user = Auth::user();
        $uid = $user->id;
        $array = Request::input();
        return $this->repository->meetingAdd($uid, $array);
    }

    /*
     * 4-26
     * gaolu
     * 会议添加
     */
    public function getMeetingList()
    {
        $user = Auth::user();
        $uid = $user->id;
        $array = Request::input();
        return $this->repository->getMeetingList($uid, $array);
    }

    /*
     * 4-26
     * gaolu
     * 会议添加
     */
    public function getPlusList()
    {
        $user = Auth::user();
        $uid = $user->id;
        $array = Request::input();
        return $this->repository->getPlusList($uid, $array);
    }
    /*
     * 4-27
     * gaolu
     * 审核通过
     */
    public function passMeeting(Request $request)
    {
        $user = Auth::user();
        $uid = $user->id;
        $array = Request::input();
        return $this->repository->passMeeting($array);
    }
    /*
     * 4-27
     * gaolu
     * 审核不通过
     */
    public function rejectMeeting()
    {
        $user = Auth::user();
        $uid = $user->id;
        $array = Request::input();
        return $this->repository->rejectMeeting($array);
    }
    /*
     * 4-27
     * gaolu
     * 审核人的会议详情接口
     */
    public function meetingReviewedInfo()
    {
        $user = Auth::user();
        $uid = $user->id;
        $array = Request::input();
        return $this->repository->meetingReviewedInfo($uid,$array);
    }
    /*
     * 4-28
     * gaolu
     * 参与人的会议详情接口
     */
    public function meetingInfo()
    {
        $user = Auth::user();
        $uid = $user->id;
        $array = Request::input();
        return $this->repository->meetingInfo($uid,$array);
    }
    /*
     * 4-28
     * gaolu
     * 会议签到
     */
    public function setMeetingSigin()
    {
        $user = Auth::user();
        $uid = $user->id;
        $array = Request::input();
        return $this->repository->setMeetingSigin($uid,$array);
    }
    /*
     * 4-28
     * gaolu
     * 会议任务列表
     */
    public function getTaskList()
    {
        $user = Auth::user();
        $uid = $user->id;
        $array = Request::input();
        return $this->repository->getTaskList($uid,$array);
    }
    /*
     * 4-28
     * gaolu
     * 会议室详情
     */
    public function getMeetingToomInfo()
    {
        $user = Auth::user();
        $uid = $user->id;
        $array = Request::input();
        return $this->repository->getMeetingToomInfo($uid,$array);
    }
    /*
     * 4-28
     * gaolu
     * 会议室某个月预约会议的天数
     */
    public function monthList()
    {
        $user = Auth::user();
        $uid = $user->id;
        $array = Request::input();
        return $this->repository->monthList($uid,$array);
    }
    /*
     * 4-28
     * gaolu
     * 会议室某一天所有预约的会议
     */
    public function getDayList()
    {
        $user = Auth::user();
        $uid = $user->id;
        $array = Request::input();
        return $this->repository->getDayList($uid,$array);
    }
    /*
     * 4-28
     * gaolu
     * 预约会议根据会议开始时间来获取会议结束时间
     */
    public function getEndTime()
    {
        $user = Auth::user();
        $uid = $user->id;
        $array = Request::input();
        return $this->repository->getEndTime($uid,$array);
    }
    /*
     * 4-29
     * gaolu
     * 新建会议室设备配置列表数据接口
     */
    public function getConfigureList()
    {
        $user = Auth::user();
        $uid = $user->id;
        $array = Request::input();
        return $this->repository->getConfigureList($uid,$array);
    }
    /*
     * 4-29
     * gaolu
     * 新建会议室设备配置列表数据接口
     */
    public function remindTime()
    {
        $user = Auth::user();
        $uid = $user->id;
        $array = Request::input();
        return $this->repository->remindTime($uid);
    }
    /*
     * 4-29
     * gaolu
     * 会议发布人给未签到的人进行提醒
     */
    public function setNosigninRemind()
    {
        $user = Auth::user();
        $uid = $user->id;
        $array = Request::input();
        return $this->repository->setNosigninRemind($uid,$array);
    }
    /*
     * 4-30
     * gaolu
     * 会议发布人添加会议纪要
     */
    public function setMeetingSummary()
    {
        $user = Auth::user();
        $uid = $user->id;

        $array = Request::input();
        return $this->repository->setMeetingSummary($uid,$array);
    }
    /*
     * 4-30
     * gaolu
     * 会议发布人添加会议纪要
     */
    public function meetingMeInfo()
    {
        $user = Auth::user();
        $uid = $user->id;
        $array = Request::input();
        return $this->repository->meetingMeInfo($uid,$array);
    }
    /*
        * 4-30
        * gaolu
        * 公章管理元获取公章类型列表
        */
    public function setSummary()
    {
        $user = Auth::user();
        $array = Request::input();
        return $this->repository->setSummary($user,$array);
    }

    /*
    * 4-30
    * gaolu
    * 公章管理元获取公章类型列表
    */
    public function getSealsType()
    {
        $user = Auth::user();
        return $this->repository->getSealsType($user);
    }

    /*
    * 4-30
    * gaolu
    * 公章管理元获取公章列表
    */
    public function getSeals()
    {
        $user = Auth::user();
        $array = Request::input();
        return $this->repository->getSeals($user,$array);
    }
    /*
    * 4-30
    * gaolu
    * 会议撤销
    */
    public function withdraw()
    {
        $user = Auth::user();
        $array = Request::input();
        return $this->repository->withdraw($user,$array);
    }
}