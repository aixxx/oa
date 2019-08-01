<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class FinancialDetail.
 *
 * @package namespace App\Models;
 */
class FinancialDetail extends Model
{
    use SoftDeletes;

	protected $table = 'financial_detail';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
		'financial_id',
        'money',
        'title',
        'projects_id',
        'reason',
        'repayment_date',
        'projects_condition',
        'limit_price',
        'is_control'
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
        'deleted_at'
    ];
    public function getProjectsConditionAttribute($value)
    {
        return json_decode($value,true);
    }

}
