<?php

namespace App\Repositories;

use App\Constant\ConstFile;
use App\Models\Attendance\AnnualRule;
use App\Models\Message\CronPushRecord;

class CronPushRecordRepository extends ParentRepository
{
    const PUSH_TYPE_TASK = 1;
    const PUSH_TYPE_SCHEDULE =2;
    const NOTICE_TYPE_MOBILE =2;
    const NOTICE_TYPE_INNER =1;

    protected $message = ConstFile::API_RESPONSE_SUCCESS_MESSAGE;
    protected $code = ConstFile::API_RESPONSE_SUCCESS;
    protected $data = [];

    public function model()
    {
        return CronPushRecord::class;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getList(){
        return CronPushRecord::query()
            ->where('is_expire', '=', 0)
            ->where('push_at', '<=', time())
            ->get();
    }

    /**
     * insert demo
     * @param CronPushRecord $obj
     * @return bool
     */
    public function insertRecord(CronPushRecord $obj){
        $recordObj = CronPushRecord::query()
            ->where('type', '=', $obj->type)
            ->where('type_pid', '=', $obj->type_pid)
            ->first();
        if(empty($recordObj)){
            return $obj->save();
        }else{
            $recordObj->push_at = $obj->push_at;  //推送的时间 单位：s
            $recordObj->type = $obj->type;  //1、任务 2、日程
            $recordObj->type_pid = $obj->type_pid;  //1、任务主键id 2、日程主键id
            $recordObj->type_title = $obj->type_title;
            $recordObj->content = $obj->content; // 推送内容
            $recordObj->target_uids = $obj->target_uids;//逗号分隔
            /* 以下非必填 */
            $recordObj->diff_minute = $obj->diff_minute;//下一次推送间隔（分钟）
            $recordObj->channel = $obj->channel;//推送频道（暂时没有用到）
            $recordObj->notice_type = $obj->notice_type;  //推送渠道： 1、站内 2、手机
            $recordObj->times = $obj->times; //执信次数
            $recordObj->push_times = $obj->push_times; //执信次数
            $recordObj->is_expire = $obj->is_expire;  //1、失效 0、有效
            $recordObj->remark = $obj->remark;
            return $recordObj->save();
        }
    }

    /**
     * @param $type int  推送类型 任务/日程。。。
     * @param $id  int  任务编号/日程编号
     * @return bool
     */
    public function modifyExpire($type, $id){
        /** @var CronPushRecord $obj */
        $obj = CronPushRecord::query()
            ->where('push_type', '=', $type)
            ->where('push_type_pid', '=', $id)
            ->first();
        $obj->is_expire = 1;
        return $obj->save();
    }


}