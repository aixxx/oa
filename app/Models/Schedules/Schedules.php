<?php

namespace App\Models\Schedules;

use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedules extends Model
{
    use SoftDeletes;
    
    protected $table = 'schedules';
    
    public $timestamps  = true;
    
    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'content',
        'all_day_yes',
        'end_at',
        'start_at',
        'send_type',
        'prompt_type',
        'repeat_type',
        'address',
        'create_schedule_user_id'
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    public function hasManyScheduleUsers(){
        return $this->hasMany(UserSchedules::class, 'schedule_id', 'id');
    }

    public function creator(){
        return $this->hasOne(User::class, 'id', 'create_schedule_user_id')->select(['chinese_name']);
    }


    public function reportInfo(){
        return $this->hasOne(Report::class, 'id', 'report_id')
            ->select(['content', 'id', 'created_at']);  //汇报的时间
    }
}
