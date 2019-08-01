<?php

namespace App\Models\CaiWu;

use App\Models\Workflow\Flow;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CostBudgetItemConditionSetting extends Model
{
//    use SoftDeletes;

    protected $table = 'cost_budget_item_condition_setting';
	protected $connection = 'caiwudb';
    protected $primaryKey = 'id';

    public $timestamps  = false;

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'category_id',
        'point_name',
        'settings',
        'limit_price',
        'is_control'
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
