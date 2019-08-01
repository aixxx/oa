<?php

namespace App\Models;


use App\Models\PAS\Purchase\Purchase;
use App\Models\PAS\SaleOrder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class FinancialPic.
 *
 * @package namespace App\Models;
 */
class FinancialOrder extends Model
{
    //use SoftDeletes;

	protected $table = 'financial_order';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
		'order_type',
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
    //销售相关订单id
    public  function saleOrder(){
        return $this->hasOne(SaleOrder::class,'id', 'title')->select('id','order_sn');
    }
    //采购相关订单id
    public  function purchaseOrder(){
        return $this->hasOne(Purchase::class,'id', 'title')->select('id','code');
    }

}
