<?php

namespace App\Models\Meeting;
   
use Illuminate\Database\Eloquent\Model;

class MeetingTask extends Model {

    protected $table = 'meeting_task';


    protected $fillable = ['id',"user_id","count","end","m_id","status"];

}