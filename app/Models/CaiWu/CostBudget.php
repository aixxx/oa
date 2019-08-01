<?php

namespace App\Models\CaiWu;

use App\Models\Workflow\Flow;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CostBudget extends Model
{
//    use SoftDeletes;

    protected $table = 'cost_budget';
	protected $connection = 'caiwudb';
    protected $primaryKey = 'id';

    public $timestamps  = false;

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'cost_project_id',
        'cost_organ_id',
        'user_id',
        'perm',
        'cost_department_id',
        'department_id',
        'title',
		'is_lock',
		'is_over',
		'inc',
		'exp',
		'status',
		'created_time',
		'update_time',
		
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
