<?php

namespace App\Models\CaiWu;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FlowAccount extends Model
{
//    use SoftDeletes;

    protected $table = 'flow_account';
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
        'code',
        'name',
        'use_coin',
        'subject_id',
        'create_time',
        'create_uid',
        'sof_id',
        'status',
        'account',
        'department_id'
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    protected $appends = [];
}
