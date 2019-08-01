<?php

namespace App\Models\Meeting;
   
use App\Models\Basic\BasicOaOption;
use Illuminate\Database\Eloquent\Model;

class MeetingRoomConfig extends Model {

    protected $table = 'meeting_room_config';


    protected $fillable = ['id',"mr_id","config_id",'status'];

    public function meetingRoom(){
        return $this->hasOne(MeetingRoom::class, 'id', 'mr_id')
            ->select(['id','title','code','position','number','start','end','remarks','status','created_at']);
    }

    public function configName(){
        return $this->hasOne(BasicOaOption::class,'id','config_id')
            ->select(['id','title']);
    }
}