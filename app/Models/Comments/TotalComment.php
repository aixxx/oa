<?php

namespace App\Models\Comments;

use App\Models\Task\TaskScore;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Comments\TotalComment
 *
 * @property int $id 自动编号
 * @property int|null $type 类型(1任务,2投票,3反馈,4审批,5出差,6加班,7请假,8外勤,9补卡,10汇报,11工作流,12公文)
 * @property int $relation_id 关联id
 * @property int $uid 用户id
 * @property string $comment_text 文字评论
 * @property string|null $comment_img 图片评论
 * @property string|null $comment_field 评价附件
 * @property string $comment_time 评价时间
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @mixin \Eloquent
 */
class TotalComment extends Model
{
    //类型(1任务,2投票,3反馈,4审批,5出差,6加班,7请假,8外勤,9补卡,10汇报,12公文)
    //13 - 我接受的任务 完成时自评
    const TYPE_TASK = 1;
    const TYPE_VOTE = 2;
    const TYPE_FEEDBACK = 3;
    const TYPE_AUDIT = 4;
    const TYPE_BUSINESS_TRIP = 5;
    const TYPE_EXTRA = 6;
    const TYPE_LEAVE = 7;
    const TYPE_OUTSIDE = 8;
    const TYPE_PATCH = 9;
    const TYPE_REPORT = 10;
    const TYPE_WORKFLOW = 11;
    const TYPE_DOCUMENT = 12;
    const TYPE_MY_TASK_HANDLE_COMMENT = 13;
    public static $_type = [
        self::TYPE_TASK => '任务',
        self::TYPE_VOTE => '投票',
        self::TYPE_FEEDBACK => '反馈',
        self::TYPE_AUDIT => '审批',
        self::TYPE_BUSINESS_TRIP => '出差',
        self::TYPE_EXTRA => '加班',
        self::TYPE_LEAVE => '请假',
        self::TYPE_OUTSIDE => '外勤',
        self::TYPE_PATCH => '补卡',
        self::TYPE_REPORT => '汇报',
        self::TYPE_WORKFLOW => '工作流',
        self::TYPE_DOCUMENT => '公文',
        self::TYPE_MY_TASK_HANDLE_COMMENT => '我接受的任务完成时自评',
    ];

    protected $table = 'total_comment';

    protected $fillable = [
        'type',
        'relation_id',
        'uid',
        'comment_text',
        'comment_img',
        'comment_field',
        'comment_time',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function user(){
        return $this->hasOne(User::class, 'id');
    }

    public function taskscore(){
        return $this->hasOne(TaskScore::class, 'pid', 'id');
    }
}
