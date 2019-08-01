<?php

namespace App\Models\CaiWu;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CaiWuFlow extends Model
{
//    use SoftDeletes;

    protected $table = 'flow';
	protected $connection = 'caiwudb';
    protected $primaryKey = 'id';

    public $timestamps  = false;

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'class_id',
        'money',
        'abstract',
        'flow_time',
        'account_id',
        'assitsmx_id',
        'assitsmx_type',
        'settle_id',
        'bill_num',
        'sof_id',
        'create_uid',
        'audit_uid',
        'audit_status',
        'audit_remark',
        'create_time',
        'sof_year',
        'sof_round',
        'audit_time',
        'update_time',
        'status',
        'department_id',
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    protected $appends = [];
    public function getFlowClass(){
        return $this->hasOne(FlowClass::class,'id','class_id')
            ->where('status',1)->select('name','id');
    }
}
