<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Workflow\Entry;

/**
 * Class Welfare.
 *
 * @package namespace App\Models;
 */
class Welfare extends Model
{
    use SoftDeletes;

	protected $table = 'welfare';

    /**
     * @deprecated 福利相关状态
     */
    const WELFARE_STATUS_PROCESSING = 1;//审核中
    const WELFARE_STATUS_COMPLETED = 2;//审核通过
    const WELFARE_STATUS_DELETE = 3;//已删除
    const WELFARE_STATUS_UNDELETE = 4;//待删除

    public static $welfareStatus = [
        self::WELFARE_STATUS_PROCESSING => '审核中',
        self::WELFARE_STATUS_COMPLETED => '审核通过',
        self::WELFARE_STATUS_DELETE => '已删除',
        self::WELFARE_STATUS_UNDELETE => '待删除',
    ];

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
		'entries_id',
        'promoter',
        'title',
        'content',
        'condition_methods',
        'issuer',
        'startdate',
        'status',
        'enddate'
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
    public  function getEntry(){
        return $this->hasOne(Entry::class,'id', 'entries_id');
    }
    //获取发起人的用户id
    public function promoterUser(){
        return $this->hasOne(User::class,'id','promoter');
    }
    //获取福利发放人的id
    public function issuerUser(){
        return $this->hasOne(User::class,'id','issuer');
    }

}
