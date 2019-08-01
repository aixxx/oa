<?php

namespace App\Models\PAS;

use Illuminate\Database\Eloquent\Model;

class SaleReturnOrder extends Model
{
    protected $table = 'pas_sale_return_orders';
    protected $guarded = [];


    /*
     * 关联退货单商品
     * */
    public function return_goods(){
        return $this->hasMany('App\Models\PAS\SaleReturnOrderGoods', 'return_order_id', 'id');
    }

    /*
     * 关联退货入库单
     * */
    public function return_in_order(){
        return $this->hasMany('App\Models\PAS\SaleReturnInWarehouse', 'return_order_id', 'id')->with('in_goods');
    }


}
