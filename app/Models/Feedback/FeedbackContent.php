<?php

namespace App\Models\Feedback;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Feedback\FeedbackContent
 *
 * @property int $id 自动编号
 * @property int|null $tid 反馈类型id
 * @property string|null $title 标题
 * @property string|null $content 内容
 * @property int|null $way 2：匿名反馈，3：实名反馈
 * @property string $publish_time 发布时间
 * @property int|null $status 2：未回复，3：已回复未读，4：回复已读
 * @property int|null $uid 发布人员id
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $image 图片附件
 * @property string|null $video 视频附件
 * @property string|null $audio 音频附件
 * @property string|null $other_file 文件附件
 * @mixin \Eloquent
 */
class FeedbackContent extends Model
{
    //use SoftDeletes;
    //type 类型 1情报，2举报，3投诉，4申诉
    const TYPE_INTELLIGENCE = 1;
    const TYPE_REPORT = 2;
    const TYPE_COMPLAIN = 3;
    const TYPE_APPEAL = 4;

    //2：匿名反馈，3：实名反馈
    const WAY_ANONYMOUS = 2;
    const WAY_REALNAME = 3;

    //状态 2：未回复，3：已回复未读，4：回复已读
    const STATUS_UNANSWERED = 2;
    const STATUS_ANSWERED_UNREAD = 3;
    const STATUS_ANSWERED_READ = 4;

    //关联类型 1- 评分 2-奖惩
    const RELATION_SCORE = 1;
    const RELATION_REWARD_PUNISHMENT = 2;
    
    protected $table = 'feedback_content';
    
    public $timestamps  = true;
    
    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'tid',
        'title',
        'content',
        'way',
        'publish_time',
        'status',
        'image',
        'video',
        'audio',
        'uid',
        'other_file',
        'created_at',
        'updated_at',
        'deleted_at',
        'relation_type',
        'relation_id',
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
