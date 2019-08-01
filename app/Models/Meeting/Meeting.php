<?php

namespace App\Models\Meeting;
   
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model {

    protected $table = 'meeting';
    /**
     * status  1待审核 0 已拒绝   2 审核成功  3撤销
     * 会议结束   start 小于当前时间
     *
     */
    const API_STATUS_REFUSE =0;
    const API_STATUS_EXAMINE =1;
    const API_STATUS_SUCCESS =2;
    const API_STATUS_REVOKE =3;

    protected $fillable = ['id',"title","mr_id","describe","user_id",
        "host_id","start",'end','day','remind','meeting_file','number','repeat_type','send_type','status','meeting_summary'];

    //会议抄送人
    public function getParticipantType()
    {
        return $this->hasMany(MeetingParticipant::class,'m_id','id')->where('type',1)
            ->select(['m_id','chinese_name','type','signin','status']);
    }

    //会议任务
    public function getTask()
    {
        return $this->hasOne(MeetingTask::class,'m_id','id')
            ->select(['m_id','chinese_name','count','end']);
    }
}