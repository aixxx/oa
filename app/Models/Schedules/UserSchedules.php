<?php

namespace App\Models\Schedules;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserSchedules extends Model
{
    use SoftDeletes;
    
    protected $table = 'user_schedules';
    
    public $timestamps  = true;
    
    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'create_schedule_user_id',
        'schedule_id',
        'content',
        'user_id',
        'create_schedule_user_name',
        'user_name',
        'prompt_type',
        'confirm_yes',
        'confirm_at',
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];
    
    protected $appends = [];

    public function hasOneSchedules()
    {
        return $this->hasOne('App\Models\Schedules\Schedules', 'id', 'schedule_id');
    }

    public function hasOneUser()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
}
