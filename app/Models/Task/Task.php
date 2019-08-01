<?php

namespace App\Models\Task;

use App\Models\MyTask\MyTask;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;

    protected $table = 'task';

    public $timestamps  = false;

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'info',
        'enclosure',
        'enclosure_img',
        'send_type',
        'deadline',
        'remind_time',
        'create_user_id',
        'send_time',
        'start_time',
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    protected $appends = [];

    public function hasManyMyTask(){
        return $this->hasMany(MyTask::class,'tid','id')->where('user_type',1);
    }
}
