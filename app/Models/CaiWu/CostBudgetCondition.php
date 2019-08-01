<?php

namespace App\Models\CaiWu;

use App\Models\Workflow\Flow;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CostBudgetCondition extends Model
{
//    use SoftDeletes;

    protected $table = 'cost_budget_condition';
	protected $connection = 'caiwudb';
    protected $primaryKey = 'id';

    public $timestamps  = false;

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'des_id',
        'title',
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
