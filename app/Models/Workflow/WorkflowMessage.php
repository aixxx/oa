<?php

namespace App\Models\Workflow;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Workflow\WorkflowMessage
 *
 * @property int $id
 * @property string|null $title 标题
 * @property string|null $content 内容
 * @property string $type 消息类型
 * @property string|null $sender 发送方
 * @property string|null $receiver 接收方
 * @property string|null $carbon_copy 抄送方
 * @property string $status 消息状态
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\WorkflowMessage whereCarbonCopy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\WorkflowMessage whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\WorkflowMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\WorkflowMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\WorkflowMessage whereReceiver($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\WorkflowMessage whereSender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\WorkflowMessage whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\WorkflowMessage whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\WorkflowMessage whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\WorkflowMessage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class WorkflowMessage extends Model
{
    const MESSAGE_TYPE_MAIL = 'mail';
    const MESSAGE_TYPE_WECHAT = 'wechat';
    const MESSAGE_TYPE_SYSTEM = 'system';

    const MESSAGE_STATUS_UNFINISHED = 'unfinished';
    const MESSAGE_STATUS_FINISHED = 'finished';

    public static $statusList = [
        self::MESSAGE_STATUS_UNFINISHED,
        self::MESSAGE_STATUS_FINISHED,
    ];

    public static $typeList = [
        self::MESSAGE_TYPE_MAIL,
        self::MESSAGE_TYPE_SYSTEM,
        self::MESSAGE_TYPE_WECHAT,
    ];

    protected $table = "workflow_messages";
    protected $fillable = ['type', 'sender', 'receiver', 'cc', 'title', 'content'];
}
