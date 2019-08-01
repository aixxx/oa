<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2018/7/11
 * Time: 15:22
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\OperateLog
 *
 * @property int $id
 * @property int|null $operate_user_id 操作员工ID
 * @property string|null $action 动作
 * @property string|null $type 类型
 * @property int|null $object_id 对象ID
 * @property string|null $object_name 对象名称
 * @property string|null $content 内容
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OperateLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OperateLog whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OperateLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OperateLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OperateLog whereObjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OperateLog whereObjectName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OperateLog whereOperateUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OperateLog whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OperateLog whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class OperateLog extends Model
{
    const TYPE_WORKFLOW = 'workflow';
    const TYPE_USER = 'user';


    protected $table = "operate_log";

    protected $fillable = [
        'operate_user_id',
        'action',
        'type',
        'object_id',
        'object_name',
        'content',
    ];

    public static function joinLogData($operateId, $action, $type, $object_id, $object_name, $content)
    {
        $data = [
            'operate_user_id' => $operateId,
            'action'          => $action,
            'type'            => $type,
            'object_id'       => $object_id,
            'object_name'     => $object_name,
            'content'         => json_encode($content),
        ];
        return $data;
    }
}
