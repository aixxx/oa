<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/19
 * Time: 10:47
 */

namespace App\Models\TotalAudit;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TotalAudit extends Model {

    use SoftDeletes;

    protected $table = 'total_audit';

    public $timestamps = true;

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'relation_id',
        'uid',
        'user_name',
        'status',
        'audit_time',
        'create_user_id',
        'is_success',
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
    ];
}
