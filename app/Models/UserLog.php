<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2018/6/26
 * Time: 11:00
 */

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserLog
 *
 * @property int $id
 * @property int|null $target_user_id 目标人
 * @property int|null $operate_user_id 操作人
 * @property string|null $action 动作
 * @property string|null $init_data 原数据
 * @property string|null $target_data 目标数据
 * @property string|null $extra 扩展信息（JSON 数据）
 * @property string|null $note 备注信息
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int|null $type 信息类型
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserLog whereExtra($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserLog whereInitData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserLog whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserLog whereOperateUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserLog whereTargetData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserLog whereTargetUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserLog whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserLog whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string|null $init_json_data 原始json数据
 * @property string|null $target_json_data 变化后json数据
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserLog whereInitJsonData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserLog whereTargetJsonData($value)
 */
class UserLog extends Model
{
    protected $table = "user_log";

    protected $fillable = [
        'target_user_id','operate_user_id','action','init_data',
        'target_data','extra','note','type','init_json_data','target_json_data'
    ];
}