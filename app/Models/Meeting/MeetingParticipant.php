<?php

namespace App\Models\Meeting;
   
use Illuminate\Database\Eloquent\Model;

class MeetingParticipant extends Model {

    protected $table = 'meeting_participant';


    protected $fillable = ['id',"user_id","m_id","signin","type","status"];
    public function lists(){
        return $this->hasMany(Meeting::class,'id','m_id')
            ->select(['id','title','code','start','end','position','status']);
    }
}