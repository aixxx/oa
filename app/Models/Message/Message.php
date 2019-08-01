<?php

namespace App\Models\Message;

use App\Models\User;
use App\Models\Workflow\Proc;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Message\Message
 *
 * @property int $id
 * @property int $receiver_id 接收者
 * @property int $sender_id 发送者
 * @property string $content 内容
 * @property int|null $status 消息状态 0、普通 1、举报
 * @property int|null $flag 消息标签 0、普通 1、标星
 * @property int|null $read_status 阅读状态 1、已阅读
 * @property string|null $remark
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $sender_status 发送者状态1、删除2、标星。。
 * @property int|null $receiver_status 发送者状态1、删除2、标星。。
 * @property int|null $type 信息类型，0：普通，3：投票 1：任务 5：汇报 6：审批通过 7：审批驳回  8：催办  9:绩效消息
 * @property int $relation_id 关联ID
 * @property-read \App\Models\User $receiver
 * @property-read \App\Models\User $sender
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message\Message newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message\Message newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message\Message query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message\Message whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message\Message whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message\Message whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message\Message whereFlag($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message\Message whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message\Message whereReadStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message\Message whereReceiverId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message\Message whereReceiverStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message\Message whereRelationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message\Message whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message\Message whereSenderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message\Message whereSenderStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message\Message whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message\Message whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message\Message whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Message extends Model
{
    const SENDER_SYSTEM_DEFAULT = -1;

    const READ_STATUS_YES = 1;
    const READ_STATUS_NO = 0;

    public static $_read_status = [
        self::READ_STATUS_NO => '未阅读',
        self::READ_STATUS_YES => '已阅读',
    ];
    const MESSAGE_STATUS_ORDINARY = 0;
    const MESSAGE_STATUS_REPORT = 1;

    //3：投票 1：任务 4：工作流 5：汇报 6：审批通过 7：审批驳回  8：审核催办
    //  9:绩效自评页面 10:会议消息 11：抄送人  12:绩效待确认界面 13: 任务催办 23:会议参与人  24:任务奖惩 25子任务抄送
    //  14:项目审批 15:项目审批抄送 16:项目审批通过 17:任务审批 18:项目审批抄送 19:任务审批通过
    // 20:任务汇报 21:任务催办 22:客户提醒 26:客户任务 27：完善资料 28：申请转正 29：续签合同
    // 30:车辆指派通知
    const MESSAGE_TYPE_NORMAL = 0;    //0：普通
    const MESSAGE_TYPE_VOTE = 3;    //投票
    const MESSAGE_TYPE_TASK = 1;
    const MESSAGE_TYPE_WORK_FLOWS = 4;
    const MESSAGE_TYPE_REPORT = 5;
    const MESSAGE_TYPE_WORKFLOW_PASS = 6;
    const MESSAGE_TYPE_WORKFLOW_REJECT = 7;
    const MESSAGE_TYPE_URGE = 8;
    const MESSAGE_TYPE_ACHIEVEMENTS = 9;
    const MESSAGE_TYPE_MEETING = 10;
    const MESSAGE_TYPE_CC = 11;
    const MESSAGE_TYPE_ACHIEVEMENTS_ONE = 12;
    const MESSAGE_TYPE_TASK_URGE = 13;
    const MESSAGE_TYPE_USER_MEETING=23;
    const MESSAGE_TYPE_REWARDPUNISHMENT=24;
    const MESSAGE_TYPE_REWARDPUNISHMENT0NE=25;
    /************************项目******************************/
    const MESSAGE_TYPE_PROJECT_FLOW = 14;
    const MESSAGE_TYPE_PROJECT_FLOW_CC = 15;
    const MESSAGE_TYPE_PROJECT_FLOW_PASS = 16;
    /************************项目任务******************************/
    const MESSAGE_TYPE_PROJECT_TASK_FLOW = 17;
    const MESSAGE_TYPE_PROJECT_TASK_FLOW_CC = 18;
    const MESSAGE_TYPE_PROJECT_TASK_FLOW_PASS = 19;
    const MESSAGE_TYPE_PROJECT_TASK_REPORT = 20;
    const MESSAGE_TYPE_PROJECT_TASK_URGE = 21;
    const MESSAGE_TYPE_PROJECT_CUSTOMER_REMINDER = 22;
    const MESSAGE_TYPE_CUSTOMER_TASK = 26;
    const MESSAGE_IMPROVING_DATA = 27;
    const MESSAGE_TYPE_TURN_POSITIVE = 28;
    const MESSAGE_TYPE_CONTRACT = 29;
    const MESSAGE_CONTENT_VOTE = '投票已通过';
    //
    const MESSAGE_CARS_APPOINT = 30;


    protected $table = 'message';
    public $timestamps  = true;
    protected $hidden = ['deleted_at'];

    protected $fillable = ['receiver_id', 'sender_id', 'content', 'status', 'sender_status',
        'receiver_status', 'read_status', 'type', 'relation_id', 'title'
    ];

    /*
     * 审批通过 审批驳回 添加消息通知
     * type int 5 - 通过 6 - 驳回
     * */
    public static function addProc(Proc $proc, $type = 0){
        $data = [
            'receiver_id' => $proc->entry->user_id,//接收者（申请人）
            'sender_id' => $proc->user_id,//发送这（最后审批人）
            'content'=> $proc->entry->title,//内容（审批title）
            'type' => $type,		//4：审批全部通过 5：审批驳回
            'relation_id' => $type == self::MESSAGE_TYPE_URGE ? $proc->id : $proc->entry_id,//workflow_entries 的 id
        ];
        return self::query()->create($data);
    }

    /*
     * 手动组装数据，发送通知
     * type int
     * */
    public static function addMessage($receiver_id, $sender_id, $content, $relation_id, $type){
        $data = [
            'receiver_id' => $receiver_id,//接收者（申请人）
            'sender_id' => $sender_id,//发送这（最后审批人）
            'content'=> $content,//内容（审批title）
            'type' => $type,		//4：审批全部通过 5：审批驳回
            'relation_id' => $relation_id,		//workflow_entries 的 id
        ];
        return self::query()->create($data);
    }

    public function receiver(){
        return $this->hasOne(User::class, 'id', 'receiver_id')
            ->select(['id','avatar','chinese_name']);
    }

    public function sender(){
        return $this->hasOne(User::class, 'id','sender_id')->select(['id','avatar','chinese_name']);
    }
}
