<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class FinancialPic.
 *
 * @package namespace App\Models;
 */
class FinancialCustomer extends Model
{
    //use SoftDeletes;

	protected $table = 'financial_customer';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
		'customer_type',
        'title',
        'financial_id'
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

}
