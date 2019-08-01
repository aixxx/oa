<?php

namespace App\Models\Task;

use App\Models\Task\MyTask;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskScore extends Model
{
    use SoftDeletes;

    protected $table = 'task_score';

    public $timestamps  = true;

    const DEFAULT_SCORE = 60;
    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'pid','score','user_id','my_task_id','admin_id',
        'created_at','updated_at','deleted_at',
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    protected $appends = [];
}
