<?php

namespace App\Repositories;

use App\Constant\ConstFile;
use App\Models\Attendance\AnnualRule;
use App\Models\Message\CronPushRecord;

class AnnualRulesRepository extends ParentRepository
{
    protected $message = ConstFile::API_RESPONSE_SUCCESS_MESSAGE;
    protected $code = ConstFile::API_RESPONSE_SUCCESS;
    protected $data = [];


    public function model()
    {
        return AnnualRule::class;
    }

    /**
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function getList(){
        // 新增消息通知 推送
        $repository = app()->make(CronPushRecordRepository::class);
        $obj = new CronPushRecord();
        $obj->push_at = 12345678;  //推送的时间 单位：s
        $obj->type = CronPushRecordRepository::PUSH_TYPE_TASK;  //1、任务 2、日程
        $obj->type_pid = 2;  //任务主键id or 日程主键id
        $obj->type_title = '日程/任务';
        $obj->content = '您有一个任务：xxxx待处理'; // 推送内容
        $obj->target_uids = '1,2,3';//逗号分隔
        /* 以下非必填 */
        $obj->channel = 1;//推送频道（暂时没有用到）
        $obj->notice_type = CronPushRecordRepository::NOTICE_TYPE_INNER;  //推送渠道： 1、站内 2、手机
        $obj->times = 1; //执信次数
        $obj->push_times = 0; //已推送次数
        $obj->is_expire = 0;  //1、失效 0、有效
        $obj->remark = '';
        $res = $repository->insertRecord($obj);
        //事件：新增消息通知已阅读
        //操作：需要设置 推送失效/停止
        /**
         * @param $type int  推送类型 任务/日程。。。
         * @param $id  int  任务编号/日程编号
         */
        $res = $repository->modifyExpire(1,2);
        $this->data = $res;
        return $this->returnApiJson();
    }
}