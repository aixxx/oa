<?php
/**
 * Created by PhpStorm.
 * User: lizhennan
 * Date: 2018/8/20
 * Time: 下午5:24
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SealChangeLog
 *
 * @property int $id
 * @property int $change_seal_id 印章id
 * @property string $change_entry_id 流程编号
 * @property int $change_lend_user_id 出借人
 * @property int $change_receive_user_id 接收人
 * @property int $change_status 0-流转中，1-持有中
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SealChangeLog whereChangeEntryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SealChangeLog whereChangeLendUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SealChangeLog whereChangeReceiveUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SealChangeLog whereChangeSealId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SealChangeLog whereChangeStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SealChangeLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SealChangeLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SealChangeLog whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SealChangeLog extends Model
{
    //印章持有状态
    const SEAL_HOLD = 1;//持有中
    const SEAL_FLOWING = 0;//流转中
    public $fillable=[
        'change_seal_id',//印章id
        'change_entry_id',//流程编号
        'change_lend_user_id',//出借者
        'change_receive_user_id',//接收者
        'change_status',//0-流转中，1-持有中
    ];

    /**
     * 根据印章Id查询最新流转记录
     * @param $sealId
     * @return mixed
     */
    public static function getBySealId($sealId)
    {
        $changeLog = self::where('change_seal_id', $sealId)->orderBy('created_at', 'desc')->first();
        return $changeLog;
    }

}