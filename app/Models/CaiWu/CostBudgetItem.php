<?php

namespace App\Models\CaiWu;

use App\Models\Workflow\Flow;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CostBudgetItem extends Model
{
//    use SoftDeletes;

    protected $table = 'cost_budget_item';
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
        'cost_budget_id',
        'cost_project_id',
        'cost_department_id',
        'company_id',
        'category_id',
        'amount',
		'is_lock',
		'is_over',
		'personnel_count',
		'status'
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
