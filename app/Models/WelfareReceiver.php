<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Workflow\Entry;
/**
 * Class WelfareReceiver.
 *
 * @package namespace App\Models;
 */
class WelfareReceiver extends Model
{
    use SoftDeletes;

	protected $table = 'welfare_receiver';

    /**
     * @deprecated 福利领取状态
     */
    const WELFARE_RECEIVER_STATUS_PENDING_APPLY = -1;//待申请
    const WELFARE_RECEIVER_STATUS_PROCESSING = 1;//申请中
    const WELFARE_RECEIVER_STATUS_COMPLETED = 2;//申请通过
    const WELFARE_RECEIVER_STATUS_REJECT = 3;//申请拒绝
    const WELFARE_RECEIVER_YES_DRAW = 1;//未领取
    const WELFARE_RECEIVER_NO_DRAW = 2;//已领取

    public static $welfareReceiverStatus = [
        self::WELFARE_RECEIVER_STATUS_PROCESSING => '申请中',
        self::WELFARE_RECEIVER_STATUS_COMPLETED => '申请通过',
        self::WELFARE_RECEIVER_STATUS_REJECT => '申请拒绝',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
		'welfare_id',
        'minister',
        'user_id',
        'status',
        'is_draw',
        'reason'
	];
	/**
     * 不能被批量赋值的属性
     *
     * @var array
     */
    protected $guarded = [
        "id"
    ];
    protected $hidden = [
        'deleted_at'
    ];
    //领取人
    public function user(){
        return $this->hasOne(User::class,'id','user_id')->select('id','chinese_name');
    }


}
