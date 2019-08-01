<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\MessageLog
 *
 * @property int $id 主键
 * @property string $template_key 模板键值
 * @property string $push_type 推送类型
 * @property string $sent_content_md5 消息内容摘要
 * @property string $sent_to 发送用户
 * @property string $sent_cc 抄送用户
 * @property int $sent_status 状态：0（未发送），1（发送成功），-1（发送失败）
 * @property string $sent_at 发送时间
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MessageLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MessageLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MessageLog wherePushType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MessageLog whereSentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MessageLog whereSentCc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MessageLog whereSentContentMd5($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MessageLog whereSentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MessageLog whereSentTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MessageLog whereTemplateKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MessageLog whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MessageLog extends Model
{
    public $timestamps = false;

    protected $table = "message_log";

    protected $primaryKey = 'id';

    protected $fillable = [
        'template_key',
        'push_type',
        'sent_content_md5',
        'sent_to',
        'sent_cc',
        'sent_status',
        'sent_at',
        'created_at',
    ];

    protected $guarded = ['id', 'template_updated_at'];
}
