<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/9
 * Time: 13:08
 */

namespace App\Models\Intelligence;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Intelligence extends Model
{
    protected $table = "intelligence";
    use SoftDeletes;

    const STATUS_DEAFT = -1;
    const STATUS_ONGOING ='进行中';
    const STATUS_COMPLETE = 1;
    public static $type = [
        self::STATUS_DEAFT => '草稿',
        self::STATUS_COMPLETE => '已完成',
    ];

    protected $fillable = [
        'class_id',
        'title',
        'user_id',
        'demand',
        'targetData',
        'img_url',
        'video_url',
        'file_url',
        'audio_url',
        'startTime',
        'endTime',
        'cost',
        'state',
        'classified',
        'userNum',
        'participation',

    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    /**
     * 类型
     * @return $this
     */
    public function hasOneType()
    {
        return $this->hasOne('App\Models\Intelligence\IntelligenceType', 'class_id', 'class_id');
    }

    public function hasManyUser()
    {
        return $this->hasMany('App\Models\Intelligence\IntelligenceUsers', 'inte_id', 'id');
    }
}