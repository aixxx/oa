<?php

namespace App\Models\Meeting;
   
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class MeetingRoom extends Model {
    /**
     * status  1待审核   2审核成功    3已驳回   0撤销
     */
    protected $table = 'meeting_room';


    protected $fillable = ['id',"title","code","position","configure","number","start",'end','remarks','status','user_id'];

    public function config(){
        return $this->hasMany(MeetingRoomConfig::class,'mr_id','id')
            ->select(['mr_id','config_id']);
    }
    public function userInfo(){
        return $this->hasOne(User::class,'id','user_id')
            ->select(['id','chinese_name',]);
    }

}