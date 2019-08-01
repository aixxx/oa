<?php

namespace App\Models\MyTask;

use App\Models\Comments\TotalComment;
use App\Models\Department;
use App\Models\Task\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Salary\RewardPunishment;

class MyTask extends Model
{
    use SoftDeletes;

    protected $table = 'my_task';

    public $timestamps  = false;

    //任务状态（-1拒绝,0默认,1待确认,2待办理,3待评价,4完成）
    const STATUS_REFUSE = -1;
    const STATUS_DEFAULT = 0;
    const STATUS_WAITING_FOR_PROCESSING = 1;
    const STATUS_WAITING_FOR_HANDLE = 2;
    const STATUS_WAITING_FOR_COMMENT = 3;
    const STATUS_OVER = 4;

    //用户类型（1接收人，2抄送人，3部门下的项目）
    const USER_TYPE_RECEIVE = 1;
    const USER_TYPE_CC = 2;
    const USER_TYPE_DEPARTMENT_PROJECT = 3;
    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'tid','uid','pid','parent_id','level','parent_ids',
        'temp_id','type_name','status','user_type','create_user_id',
        'accept_time','finish_time','start_time','end_time','comment_time',
        'deleted_at','created_at','updated_at','content',
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    protected $appends = [];

    public function task(){
        return $this->hasOne(Task::class, 'id', 'tid');
    }

    public function totalcomment(){
        return $this->hasOne(TotalComment::class, 'relation_id', 'id')
            ->where('type', 1);
    }


    public function user(){
        return $this->hasOne(User::class, 'id', 'create_user_id');
    }

    public function userByUid(){
        return $this->hasOne(User::class, 'id', 'uid');
    }

    /*
     * 关联处罚
     * */
    public function punishment(){
        return $this->hasMany(RewardPunishment::class, 'task_id', 'id');
    }

    /*
     * 关联子任务
     * */
    public function child(){
        return $this->hasMany(MyTask::class, 'parent_id', 'id')->groupBy('temp_id');
    }
}
