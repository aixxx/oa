<?php

namespace App\Models\Message;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

/**
 * App\Models\Message\CronPushRecord
 *
 * @property int $id
 * @property int $push_at 推送执行时间
 * @property string $content 推送内容
 * @property int $channel 推送频道
 * @property int $notice_type 推送渠道： 1、站内 2、手机
 * @property int $times 推送频率
 * @property int $is_expire 是否失效 1、失效 0、有效
 * @property string $target_uids 推送对象
 * @property string $remark 备注
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $type 目标类型： 1、任务 2、日程
 * @property int $type_pid 目标类型的主键ID
 * @property string $type_title 目标类型描述 任务/日程
 * @property int|null $push_times 已推送次数
 * @property int|null $diff_minute 下一次执行间隔分钟数
 * @mixin \Eloquent
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message\CronPushRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message\CronPushRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message\CronPushRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message\CronPushRecord whereChannel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message\CronPushRecord whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message\CronPushRecord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message\CronPushRecord whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message\CronPushRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message\CronPushRecord whereIsExpire($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message\CronPushRecord whereNoticeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message\CronPushRecord wherePushAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message\CronPushRecord wherePushTimes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message\CronPushRecord whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message\CronPushRecord whereTargetUids($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message\CronPushRecord whereTimes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message\CronPushRecord whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message\CronPushRecord whereTypePid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message\CronPushRecord whereTypeTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message\CronPushRecord whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message\CronPushRecord whereDiffMinute($value)
 */
class CronPushRecord extends Model
{
    use Notifiable;

    const NOTICE_TYPE_APP = 1;
    const NOTICE_TYPE_MOBILE = 2;
    public static $_notice_type = [
        self::NOTICE_TYPE_APP => '应用内',
        self::NOTICE_TYPE_MOBILE => '手机',
    ];

    const TYPE_DAY = 1;
    const TYPE_TASK = '任务';
    const TYPE_URGE = '催办';
    public static $_types = [
        self::TYPE_DAY => '日程',
        self::TYPE_TASK => '任务',
        self::TYPE_URGE => '催办',
    ];

    //
    protected $table ='message_cron_push_records';

}
