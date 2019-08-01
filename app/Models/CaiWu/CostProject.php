<?php

namespace App\Models\CaiWu;

use App\Models\Workflow\Flow;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CostProject extends Model
{
//    use SoftDeletes;

    protected $table = 'cost_project';
	protected $connection = 'caiwudb';
    protected $primaryKey = 'id';

    public $timestamps  = false;

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'cost_organ_id',
        'cost_department_id',
        'department_id',
        'title',
        'user_id',
        'is_over',
        'perm',
		'status',
		'company_id',
		'exp',
		'inc',
		'created_time',
		'update_time'
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
