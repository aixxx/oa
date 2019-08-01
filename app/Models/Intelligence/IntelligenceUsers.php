<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/9
 * Time: 13:18
 */

namespace App\Models\Intelligence;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IntelligenceUsers extends Model
{
    protected $table = "intelligence_users";
    use SoftDeletes;

    const STATUS_CLAIM = 1;
    const STATUS_ASSIGN = 2;

    const STATUS_AUDIT_IN = 1;//审批中
    const STATUS_COMPLETE = 2;//已完成
    const STATUS_REFUSED = 3;//已拒绝

    protected $fillable = [
        'user_id',
        'inte_id',
        'attribute',
        'state',
        'reason',
        'inte_content',
        'inte_demand',
        'file_upload',
        'time',
        'bank',
        'reason',
        'auditstate',
        'entry_id',
    ];

    protected $hidden = [
        'created_at',
        'deleted_at',
    ];

    /**
     * 类型
     * @return $this
     */
    public function hasOneInte()
    {
        return $this->hasOne('App\Models\Intelligence\Intelligence', 'id', 'inte_id');
    }

    /**
     * 类型
     * @return $this
     */
    public function hasOneUser()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

}